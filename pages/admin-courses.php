<?php
$page_title = "Course Management";
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$conn = getDbConnection();
$message = '';
$error = '';

// Handle DELETE Course
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = "Course deleted successfully!";
    } else {
        $error = "Failed to delete course.";
    }
    $stmt->close();
}

// Handle DELETE Module
if (isset($_GET['delete_module']) && is_numeric($_GET['delete_module'])) {
    $module_id = (int)$_GET['delete_module'];
    $stmt = $conn->prepare("DELETE FROM course_modules WHERE id = ?");
    $stmt->bind_param("i", $module_id);
    
    if ($stmt->execute()) {
        $message = "Module deleted successfully!";
    }
    $stmt->close();
}

// Handle ADD/EDIT Course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_course'])) {
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $title = trim($_POST['title']);
    $slug = strtolower(str_replace(' ', '-', trim($_POST['slug'])));
    $description = trim($_POST['description']);
    $course_type = $_POST['course_type'];
    $level = trim($_POST['level']);
    $duration = trim($_POST['duration']);
    $price = floatval($_POST['price']);
    $status = $_POST['status'];
    
    if (empty($title) || empty($slug) || empty($course_type)) {
        $error = "Title, slug, and course type are required!";
    } else {
        if ($course_id > 0) {
            $stmt = $conn->prepare("UPDATE courses SET title=?, slug=?, description=?, course_type=?, level=?, duration=?, price=?, status=? WHERE id=?");
            $stmt->bind_param("ssssssdsi", $title, $slug, $description, $course_type, $level, $duration, $price, $status, $course_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (title, slug, description, course_type, level, duration, price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssssssds", $title, $slug, $description, $course_type, $level, $duration, $price, $status);
        }
        
        if ($stmt->execute()) {
            $message = $course_id > 0 ? "Course updated successfully!" : "Course created successfully!";
            if ($course_id == 0) {
                $course_id = $stmt->insert_id;
            }
        } else {
            $error = "Failed to save course: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle ADD Module Content
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_module'])) {
    $course_id = (int)$_POST['course_id'];
    $module_title = trim($_POST['module_title']);
    $module_description = trim($_POST['module_description']);
    $content_type = $_POST['content_type'];
    $content_url = trim($_POST['content_url']);
    $order_num = (int)$_POST['order_num'];
    
    $stmt = $conn->prepare("INSERT INTO course_modules (course_id, title, description, content_type, content_url, order_number, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssi", $course_id, $module_title, $module_description, $content_type, $content_url, $order_num);
    
    if ($stmt->execute()) {
        $message = "Module added successfully!";
    } else {
        $error = "Failed to add module: " . $stmt->error;
    }
    $stmt->close();
}

// Get course to edit
$edit_course = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_course = $result->fetch_assoc();
    $stmt->close();
}

// Get course modules if viewing/editing
$course_modules = [];
if ($edit_course) {
    $stmt = $conn->prepare("SELECT * FROM course_modules WHERE course_id = ? ORDER BY order_number ASC");
    $stmt->bind_param("i", $edit_course['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $course_modules[] = $row;
    }
    $stmt->close();
}

// Get all courses
$courses = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");

include '../includes/header.php';
?>

<style>
.admin-page {
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

.content-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.module-item {
    background: var(--color-bg-light);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 3px solid var(--color-secondary);
    transition: all 0.3s ease;
}

.module-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateX(5px);
}

.content-type-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-video { background: #e3f2fd; color: #1565c0; }
.type-pdf { background: #ffebee; color: #c62828; }
.type-image { background: #f3e5f5; color: #7b1fa2; }
.type-youtube { background: #ffebee; color: #d32f2f; }
.type-text { background: #e8f5e9; color: #2e7d32; }
</style>

<div class="admin-page">
    <div class="container">
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Course Management</h1>
                    <p style="color: var(--color-text-light);">Create courses and manage content modules</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Course Form -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                <?php echo $edit_course ? 'Edit Course' : 'Create New Course'; ?>
            </h3>

            <form method="POST" action="">
                <?php if ($edit_course): ?>
                    <input type="hidden" name="course_id" value="<?php echo $edit_course['id']; ?>">
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Course Title *</label>
                        <input type="text" name="title" class="form-input" required
                               value="<?php echo $edit_course ? htmlspecialchars($edit_course['title']) : ''; ?>"
                               placeholder="e.g., Beginner Muayboran Training">
                    </div>

                    <div class="form-group">
                        <label class="form-label">URL Slug *</label>
                        <input type="text" name="slug" class="form-input" required
                               value="<?php echo $edit_course ? htmlspecialchars($edit_course['slug']) : ''; ?>"
                               placeholder="beginner-course">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" rows="4" placeholder="Course description..."><?php echo $edit_course ? htmlspecialchars($edit_course['description']) : ''; ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Course Type *</label>
                        <select name="course_type" class="form-select" required>
                            <option value="student" <?php echo ($edit_course && $edit_course['course_type'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="coach" <?php echo ($edit_course && $edit_course['course_type'] === 'coach') ? 'selected' : ''; ?>>Coach</option>
                            <option value="teacher" <?php echo ($edit_course && $edit_course['course_type'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                            <option value="referee" <?php echo ($edit_course && $edit_course['course_type'] === 'referee') ? 'selected' : ''; ?>>Referee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Level</label>
                        <input type="text" name="level" class="form-input" placeholder="Beginner"
                               value="<?php echo $edit_course ? htmlspecialchars($edit_course['level']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-input" placeholder="12 weeks"
                               value="<?php echo $edit_course ? htmlspecialchars($edit_course['duration']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Price (USD)</label>
                        <input type="number" name="price" class="form-input" step="0.01" min="0"
                               value="<?php echo $edit_course ? $edit_course['price'] : '0'; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo ($edit_course && $edit_course['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                        <option value="active" <?php echo ($edit_course && $edit_course['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="archived" <?php echo ($edit_course && $edit_course['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="save_course" class="btn btn-primary">
                        <?php echo $edit_course ? 'Update Course' : 'Create Course'; ?>
                    </button>
                    <?php if ($edit_course): ?>
                        <a href="admin-courses.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Course Modules (Only show when editing) -->
        <?php if ($edit_course): ?>
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                    Course Content Modules
                </h3>

                <!-- Add Module Form -->
                <form method="POST" style="background: var(--color-bg-light); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <input type="hidden" name="course_id" value="<?php echo $edit_course['id']; ?>">

                    <h4 style="margin-bottom: 1rem;">Add New Module</h4>

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Module Title *</label>
                            <input type="text" name="module_title" class="form-input" required placeholder="e.g., Introduction to Mae Mai">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Content Type *</label>
                            <select name="content_type" class="form-select" required>
                                <option value="video">Video File</option>
                                <option value="youtube">YouTube Link</option>
                                <option value="pdf">PDF Document</option>
                                <option value="image">Image</option>
                                <option value="text">Text Content</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Order Number</label>
                            <input type="number" name="order_num" class="form-input" value="<?php echo count($course_modules) + 1; ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Description</label>
                        <textarea name="module_description" class="form-textarea" rows="2" placeholder="Module description..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Content URL/Path *</label>
                        <input type="text" name="content_url" class="form-input" required 
                               placeholder="YouTube URL, file path, or content...">
                        <small style="color: var(--color-text-light); font-size: 0.875rem;">
                            For videos: YouTube URL or file path | For PDFs/Images: upload to /assets/courses/ folder
                        </small>
                    </div>

                    <button type="submit" name="add_module" class="btn btn-primary">Add Module</button>
                </form>

                <!-- List Modules -->
                <?php if (count($course_modules) > 0): ?>
                    <h4 style="margin-bottom: 1rem;">Existing Modules (<?php echo count($course_modules); ?>)</h4>
                    <?php foreach ($course_modules as $module): ?>
                        <div class="module-item">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div style="flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                        <span style="background: var(--color-primary); color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                            <?php echo $module['order_number']; ?>
                                        </span>
                                        <h4 style="margin: 0;"><?php echo htmlspecialchars($module['title']); ?></h4>
                                        <span class="content-type-badge type-<?php echo $module['content_type']; ?>">
                                            <?php echo strtoupper($module['content_type']); ?>
                                        </span>
                                    </div>
                                    <p style="color: var(--color-text-light); margin: 0.5rem 0; margin-left: 46px;">
                                        <?php echo htmlspecialchars($module['description']); ?>
                                    </p>
                                    <p style="font-size: 0.875rem; color: var(--color-text-light); margin-left: 46px;">
                                        <strong>URL:</strong> <?php echo htmlspecialchars($module['content_url']); ?>
                                    </p>
                                </div>
                                <a href="?delete_module=<?php echo $module['id']; ?>&edit=<?php echo $edit_course['id']; ?>" 
                                   onclick="return confirm('Delete this module?');"
                                   style="background: var(--color-primary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; padding: 2rem; color: var(--color-text-light);">
                        No modules added yet. Add your first module above.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Courses List -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">All Courses</h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--color-bg-light);">
                        <tr>
                            <th style="padding: 1rem; text-align: left;">Title</th>
                            <th style="padding: 1rem; text-align: left;">Type</th>
                            <th style="padding: 1rem; text-align: left;">Level</th>
                            <th style="padding: 1rem; text-align: left;">Duration</th>
                            <th style="padding: 1rem; text-align: left;">Price</th>
                            <th style="padding: 1rem; text-align: left;">Status</th>
                            <th style="padding: 1rem; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses->num_rows > 0): ?>
                            <?php while ($course = $courses->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: 1rem; font-weight: 600;"><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td style="padding: 1rem;"><?php echo ucfirst($course['course_type']); ?></td>
                                    <td style="padding: 1rem;"><?php echo $course['level'] ?: '-'; ?></td>
                                    <td style="padding: 1rem;"><?php echo $course['duration'] ?: '-'; ?></td>
                                    <td style="padding: 1rem;">$<?php echo number_format($course['price'], 2); ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="background: <?php echo $course['status'] === 'active' ? '#4caf50' : '#9e9e9e'; ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">
                                            <?php echo strtoupper($course['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="?edit=<?php echo $course['id']; ?>" style="background: var(--color-secondary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; margin-right: 0.5rem;">Edit</a>
                                        <a href="?delete=<?php echo $course['id']; ?>" onclick="return confirm('Delete this course?');" style="background: var(--color-primary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--color-text-light);">No courses found. Create your first course above.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
