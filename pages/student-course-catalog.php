<?php
$page_title = "Course Catalog";
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$conn = getDbConnection();
$message = '';

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    $course_id = (int)$_POST['course_id'];
    
    // Check if already enrolled
    $check = $conn->prepare("SELECT id FROM course_enrollments WHERE user_id = ? AND course_id = ?");
    $check->bind_param("ii", $user_id, $course_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        // Enroll student
        $stmt = $conn->prepare("INSERT INTO course_enrollments (user_id, course_id, enrollment_date, status) VALUES (?, ?, NOW(), 'enrolled')");
        $stmt->bind_param("ii", $user_id, $course_id);
        
        if ($stmt->execute()) {
            $message = "Successfully enrolled in course!";
        }
        $stmt->close();
    } else {
        $message = "You are already enrolled in this course!";
    }
    $check->close();
}

// Get all active courses for student's role
$courses = $conn->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM course_modules WHERE course_id = c.id) as module_count,
           (SELECT COUNT(*) FROM course_enrollments WHERE course_id = c.id AND user_id = $user_id) as is_enrolled
    FROM courses c
    WHERE c.status = 'active' AND c.course_type = 'student'
    ORDER BY c.created_at DESC
");

include '../includes/header.php';
?>

<style>
.student-page {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.page-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border-left: 4px solid var(--color-primary);
}

.course-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.course-thumbnail {
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 4rem;
}

.course-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.course-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.badge-beginner { background: #e3f2fd; color: #1565c0; }
.badge-intermediate { background: #fff3e0; color: #e65100; }
.badge-advanced { background: #f3e5f5; color: #7b1fa2; }
.badge-enrolled { background: #e8f5e9; color: #2e7d32; }
</style>

<div class="student-page">
    <div class="container">
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Course Catalog</h1>
                    <p style="color: var(--color-text-light);">Browse and enroll in available courses</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($courses->num_rows > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <div class="course-card">
                        <div class="course-thumbnail">
                            📚
                        </div>

                        <div class="course-content">
                            <div>
                                <?php if ($course['is_enrolled'] > 0): ?>
                                    <span class="course-badge badge-enrolled">✓ Enrolled</span>
                                <?php else: ?>
                                    <span class="course-badge badge-<?php echo strtolower($course['level'] ?: 'beginner'); ?>">
                                        <?php echo $course['level'] ?: 'All Levels'; ?>
                                    </span>
                                <?php endif; ?>

                                <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--color-primary);">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </h3>

                                <p style="color: var(--color-text-light); font-size: 0.875rem; margin-bottom: 1rem; line-height: 1.6;">
                                    <?php echo htmlspecialchars(substr($course['description'], 0, 120)); ?>...
                                </p>

                                <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                                    <div>
                                        <strong>📖 <?php echo $course['module_count']; ?></strong> Lessons
                                    </div>
                                    <?php if ($course['duration']): ?>
                                        <div>
                                            <strong>⏱️ <?php echo htmlspecialchars($course['duration']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($course['price'] > 0): ?>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">
                                        $<?php echo number_format($course['price'], 2); ?>
                                    </div>
                                <?php else: ?>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--color-secondary); margin-bottom: 1rem;">
                                        FREE
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div style="margin-top: auto;">
                                <?php if ($course['is_enrolled'] > 0): ?>
                                    <a href="student-course-view.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                                        Continue Learning
                                    </a>
                                <?php else: ?>
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" name="enroll_course" class="btn btn-secondary" style="width: 100%;">
                                            Enroll Now
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="course-card" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">📚</div>
                <h3 style="margin-bottom: 1rem;">No Courses Available</h3>
                <p style="color: var(--color-text-light);">
                    Check back later for new courses!
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php';
?>
