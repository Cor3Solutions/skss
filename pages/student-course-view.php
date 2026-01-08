<?php
$page_title = "Course View";
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = getDbConnection();

// Check if user is enrolled
$enrollment = $conn->prepare("SELECT * FROM course_enrollments WHERE user_id = ? AND course_id = ?");
$enrollment->bind_param("ii", $user_id, $course_id);
$enrollment->execute();
$enrolled = $enrollment->get_result()->fetch_assoc();
$enrollment->close();

if (!$enrolled) {
    header('Location: student-course-catalog.php');
    exit();
}

// Get course details
$course_query = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$course_query->bind_param("i", $course_id);
$course_query->execute();
$course = $course_query->get_result()->fetch_assoc();
$course_query->close();

// Get modules
$modules = $conn->query("
    SELECT cm.*, 
           COALESCE(cp.completed, 0) as is_completed
    FROM course_modules cm
    LEFT JOIN course_progress cp ON cm.id = cp.module_id AND cp.user_id = $user_id
    WHERE cm.course_id = $course_id
    ORDER BY cm.order_number ASC
");

// Handle module completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $module_id = (int)$_POST['module_id'];
    
    $stmt = $conn->prepare("
        INSERT INTO course_progress (user_id, course_id, module_id, completed, completed_at) 
        VALUES (?, ?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()
    ");
    $stmt->bind_param("iii", $user_id, $course_id, $module_id);
    $stmt->execute();
    $stmt->close();
    
    // Update overall progress
    $total_modules = $conn->query("SELECT COUNT(*) as count FROM course_modules WHERE course_id = $course_id")->fetch_assoc()['count'];
    $completed_modules = $conn->query("SELECT COUNT(*) as count FROM course_progress WHERE user_id = $user_id AND course_id = $course_id AND completed = 1")->fetch_assoc()['count'];
    $progress = $total_modules > 0 ? ($completed_modules / $total_modules) * 100 : 0;
    
    $update_progress = $conn->prepare("UPDATE course_enrollments SET progress = ? WHERE user_id = ? AND course_id = ?");
    $update_progress->bind_param("dii", $progress, $user_id, $course_id);
    $update_progress->execute();
    $update_progress->close();
    
    header("Location: student-course-view.php?id=$course_id");
    exit();
}

// Get selected module (or first one)
$selected_module_id = isset($_GET['module']) ? (int)$_GET['module'] : 0;
if ($selected_module_id == 0 && $modules->num_rows > 0) {
    $modules->data_seek(0);
    $first = $modules->fetch_assoc();
    $selected_module_id = $first['id'];
    $modules->data_seek(0);
}

$selected_module = null;
if ($selected_module_id > 0) {
    $module_query = $conn->prepare("
        SELECT cm.*, COALESCE(cp.completed, 0) as is_completed
        FROM course_modules cm
        LEFT JOIN course_progress cp ON cm.id = cp.module_id AND cp.user_id = ?
        WHERE cm.id = ?
    ");
    $module_query->bind_param("ii", $user_id, $selected_module_id);
    $module_query->execute();
    $selected_module = $module_query->get_result()->fetch_assoc();
    $module_query->close();
}

include '../includes/header.php';
?>

<style>
.course-viewer {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.video-container {
    background: #000;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 16/9;
    margin-bottom: 2rem;
}

.video-container iframe {
    width: 100%;
    height: 100%;
}

.module-sidebar {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    max-height: 600px;
    overflow-y: auto;
}

.module-item {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.module-item:hover {
    background: var(--color-bg-light);
}

.module-item.active {
    background: var(--color-bg-light);
    border-color: var(--color-primary);
}

.module-item.completed {
    background: #e8f5e9;
}

.content-viewer {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
</style>

<div class="course-viewer">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p style="color: var(--color-text-light);">Progress: <?php echo round($enrolled['progress']); ?>%</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
            <!-- Main Content Area -->
            <div>
                <?php if ($selected_module): ?>
                    <!-- Content Display -->
                    <?php if ($selected_module['content_type'] === 'youtube'): ?>
                        <?php
                        // Extract YouTube video ID
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $selected_module['content_url'], $matches);
                        $youtube_id = $matches[1] ?? '';
                        ?>
                        <div class="video-container">
                            <iframe src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    <?php elseif ($selected_module['content_type'] === 'video'): ?>
                        <div class="video-container">
                            <video controls style="width: 100%; height: 100%;">
                                <source src="../<?php echo htmlspecialchars($selected_module['content_url']); ?>" type="video/mp4">
                                Your browser does not support video playback.
                            </video>
                        </div>
                    <?php elseif ($selected_module['content_type'] === 'pdf'): ?>
                        <div class="content-viewer">
                            <iframe src="../<?php echo htmlspecialchars($selected_module['content_url']); ?>" style="width: 100%; height: 600px; border: none;"></iframe>
                        </div>
                    <?php elseif ($selected_module['content_type'] === 'image'): ?>
                        <div class="content-viewer" style="text-align: center;">
                            <img src="../<?php echo htmlspecialchars($selected_module['content_url']); ?>" style="max-width: 100%; border-radius: 8px;">
                        </div>
                    <?php else: ?>
                        <div class="content-viewer">
                            <div style="white-space: pre-wrap;"><?php echo htmlspecialchars($selected_module['content_url']); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Module Info -->
                    <div class="content-viewer">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
                            <div>
                                <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($selected_module['title']); ?></h2>
                                <?php if ($selected_module['description']): ?>
                                    <p style="color: var(--color-text-light); line-height: 1.6;">
                                        <?php echo htmlspecialchars($selected_module['description']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!$selected_module['is_completed']): ?>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="module_id" value="<?php echo $selected_module['id']; ?>">
                                    <button type="submit" name="mark_complete" class="btn btn-primary">
                                        ✓ Mark Complete
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="background: #4caf50; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600;">
                                    ✓ Completed
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Navigation Buttons -->
                        <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--color-border);">
                            <?php
                            // Get previous and next modules
                            $prev_module = $conn->query("SELECT id, title FROM course_modules WHERE course_id = $course_id AND order_number < {$selected_module['order_number']} ORDER BY order_number DESC LIMIT 1")->fetch_assoc();
                            $next_module = $conn->query("SELECT id, title FROM course_modules WHERE course_id = $course_id AND order_number > {$selected_module['order_number']} ORDER BY order_number ASC LIMIT 1")->fetch_assoc();
                            ?>
                            
                            <?php if ($prev_module): ?>
                                <a href="?id=<?php echo $course_id; ?>&module=<?php echo $prev_module['id']; ?>" class="btn btn-outline">
                                    ← Previous Lesson
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($next_module): ?>
                                <a href="?id=<?php echo $course_id; ?>&module=<?php echo $next_module['id']; ?>" class="btn btn-primary" style="margin-left: auto;">
                                    Next Lesson →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="content-viewer" style="text-align: center; padding: 4rem 2rem;">
                        <div style="font-size: 5rem; margin-bottom: 1rem;">📚</div>
                        <h3>Select a lesson to begin</h3>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Module Sidebar -->
            <div class="module-sidebar">
                <h3 style="margin-bottom: 1rem; font-size: 1.25rem;">Course Lessons</h3>
                
                <?php if ($modules->num_rows > 0): ?>
                    <?php 
                    $modules->data_seek(0);
                    while ($module = $modules->fetch_assoc()): 
                    ?>
                        <a href="?id=<?php echo $course_id; ?>&module=<?php echo $module['id']; ?>" 
                           class="module-item <?php echo $module['id'] == $selected_module_id ? 'active' : ''; ?> <?php echo $module['is_completed'] ? 'completed' : ''; ?>"
                           style="text-decoration: none; color: inherit; display: block;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="flex-shrink: 0;">
                                    <?php if ($module['is_completed']): ?>
                                        <span style="color: #4caf50; font-size: 1.25rem;">✓</span>
                                    <?php else: ?>
                                        <span style="color: var(--color-text-light);">○</span>
                                    <?php endif; ?>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                        <?php echo htmlspecialchars($module['title']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--color-text-light);">
                                        <?php echo strtoupper($module['content_type']); ?>
                                        <?php if ($module['duration']): ?>
                                            • <?php echo $module['duration']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--color-text-light); padding: 2rem;">
                        No lessons available yet
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php';
?>
