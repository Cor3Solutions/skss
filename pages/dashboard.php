<?php
$page_title = "Dashboard";
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Get database connection
$conn = getDbConnection();

// Get dashboard statistics based on role
if ($user_role === 'admin') {
    // Admin stats
    $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
    $total_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
    $total_coaches = $conn->query("SELECT COUNT(*) as count FROM users WHERE role IN ('coach', 'teacher')")->fetch_assoc()['count'];
    $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
    $pending_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE membership_status = 'pending'")->fetch_assoc()['count'];
    $active_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE membership_status = 'active'")->fetch_assoc()['count'];
    $total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'")->fetch_assoc()['count'];
    $total_enrollments = $conn->query("SELECT COUNT(*) as count FROM course_enrollments")->fetch_assoc()['count'];
} else {
    // Student/Coach stats
    $my_courses = $conn->query("SELECT COUNT(*) as count FROM course_enrollments WHERE user_id = $user_id")->fetch_assoc()['count'];
}

include '../includes/header.php';
?>

<style>
:root {
    --color-text-light: #000000;
}
.admin-dashboard {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.dashboard-header {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 3rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0.5rem 0;
    font-family: var(--font-display);
}

.stat-label {
    color: #000000;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.management-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border-left: 4px solid var(--color-primary);
    height: 100%;
}

.management-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-left-color: var(--color-secondary);
}

.management-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: white;
}

.btn-manage {
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    color: white;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin-top: 1rem;
}

.btn-manage:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(139, 0, 0, 0.3);
}

.activity-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.activity-table table {
    width: 100%;
    border-collapse: collapse;
}

.activity-table thead {
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    color: white;
}

.activity-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 1px;
}

.activity-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--color-border);
}

.activity-table tbody tr:hover {
    background: var(--color-bg-light);
}

.badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success { background: #e8f5e9; color: #2e7d32; }
.badge-warning { background: #fff3e0; color: #e65100; }
.badge-info { background: #e3f2fd; color: #1565c0; }
.badge-danger { background: #ffebee; color: #c62828; }

.quick-action {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action:hover {
    border-color: var(--color-secondary);
    transform: translateY(-3px);
}
</style>

<?php if ($user_role === 'admin'): ?>
    <!-- PROFESSIONAL ADMIN DASHBOARD -->
    <div class="admin-dashboard">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin-bottom: 0.5rem;">Administration Panel</p>
                        <h1 style="font-size: 2.5rem; margin: 0;">Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>
                        <p style="opacity: 0.8; margin-top: 0.5rem;">Manage and oversee academy operations</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; opacity: 0.8;">Last login</p>
                        <p style="font-size: 1.25rem; font-weight: 600;"><?php echo date('M d, Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Statistics Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        👥
                    </div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        🎓
                    </div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-label">Students</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        👨‍🏫
                    </div>
                    <div class="stat-value"><?php echo $total_coaches; ?></div>
                    <div class="stat-label">Instructors</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        📚
                    </div>
                    <div class="stat-value"><?php echo $total_courses; ?></div>
                    <div class="stat-label">Courses</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        📝
                    </div>
                    <div class="stat-value"><?php echo $total_enrollments; ?></div>
                    <div class="stat-label">Enrollments</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%);">
                        💬
                    </div>
                    <div class="stat-value"><?php echo $total_messages; ?></div>
                    <div class="stat-label">New Messages</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                        ⏳
                    </div>
                    <div class="stat-value"><?php echo $pending_members; ?></div>
                    <div class="stat-label">Pending Approval</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                        ✅
                    </div>
                    <div class="stat-value"><?php echo $active_members; ?></div>
                    <div class="stat-label">Active Members</div>
                </div>
            </div>

            <!-- Management Section -->
            <div style="margin-bottom: 3rem;">
                <h2 style="font-family: var(--font-display); font-size: 2rem; margin-bottom: 2rem; color: var(--color-primary);">
                    System Management
                </h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <!-- User Management -->
                    <div class="management-card">
                        <div class="management-icon">👥</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">User Management</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            Complete control over user accounts. Create, edit, delete users. Manage roles and permissions.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ Create new users</li>
                            <li>✓ Edit user details</li>
                            <li>✓ Assign roles & permissions</li>
                            <li>✓ Manage Khan levels</li>
                        </ul>
                        <a href="admin-users.php" class="btn-manage">Manage Users</a>
                    </div>

                    <!-- Course Management -->
                    <div class="management-card">
                        <div class="management-icon">📚</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Course Management</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            Full course creation and management. Upload videos, PDFs, images. Organize content and modules.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ Create & edit courses</li>
                            <li>✓ Upload videos (YouTube/Files)</li>
                            <li>✓ Manage PDFs & documents</li>
                            <li>✓ Organize course modules</li>
                        </ul>
                        <a href="admin-courses.php" class="btn-manage">Manage Courses</a>
                    </div>

                    <!-- Khan Level Management -->
                    <div class="management-card">
                        <div class="management-icon">🏅</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Khan Level System</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            Manage the Khan grading system. Approve promotions, track progress, issue certificates.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ Review grading requests</li>
                            <li>✓ Approve promotions</li>
                            <li>✓ Issue certificates</li>
                            <li>✓ Track student progress</li>
                        </ul>
                        <a href="admin-khan-levels.php" class="btn-manage">Manage Khan Levels</a>
                    </div>

                    <!-- Content Library -->
                    <div class="management-card">
                        <div class="management-icon">📁</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Content Library</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            Central repository for all media files. Upload, organize, and manage training materials.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ Upload videos & images</li>
                            <li>✓ Manage PDF documents</li>
                            <li>✓ Organize by category</li>
                            <li>✓ Link to YouTube</li>
                        </ul>
                        <a href="admin-content-library.php" class="btn-manage">Manage Content</a>
                    </div>

                    <!-- Messages & Inquiries -->
                    <div class="management-card">
                        <div class="management-icon">💬</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Messages & Inquiries</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            View and respond to contact form submissions. Manage student inquiries and support tickets.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ View new messages</li>
                            <li>✓ Reply to inquiries</li>
                            <li>✓ Mark as resolved</li>
                            <li>✓ Archive old messages</li>
                        </ul>
                        <a href="admin-messages.php" class="btn-manage">View Messages</a>
                    </div>

                    <!-- Events & Seminars -->
                    <div class="management-card">
                        <div class="management-icon">📅</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Events & Seminars</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem;">
                            Create and manage events, workshops, seminars, and competitions. Track registrations.
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 1rem; font-size: 0.875rem; color: var(--color-text-light);">
                            <li>✓ Create events</li>
                            <li>✓ Manage registrations</li>
                            <li>✓ Track attendance</li>
                            <li>✓ Send notifications</li>
                        </ul>
                        <a href="admin-events.php" class="btn-manage">Manage Events</a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div>
                <h2 style="font-family: var(--font-display); font-size: 2rem; margin-bottom: 2rem; color: var(--color-primary);">
                    Recent Activity
                </h2>

                <div class="activity-table">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent = $conn->query("SELECT name, email, role, membership_status, created_at FROM users ORDER BY created_at DESC LIMIT 10");
                            if ($recent->num_rows > 0):
                                while ($row = $recent->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div style="font-size: 0.875rem; color: var(--color-text-light);"><?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td>New Registration</td>
                                <td><span class="badge badge-info"><?php echo ucfirst($row['role']); ?></span></td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'active' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'inactive' => 'badge-danger'
                                    ];
                                    ?>
                                    <span class="badge <?php echo $badge_class[$row['membership_status']] ?? 'badge-info'; ?>">
                                        <?php echo ucfirst($row['membership_status']); ?>
                                    </span>
                                </td>
                                <td style="color: var(--color-text-light);">
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3rem; color: var(--color-text-light);">
                                    No recent activity
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($user_role === 'coach' || $user_role === 'teacher'): ?>
    <!-- COACH/TEACHER DASHBOARD (Same as before) -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <p class="section-subtitle">Instructor Panel</p>
                <h1 class="section-title">Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
                <p class="section-description">Manage your classes and students</p>
            </div>
            
            <div class="card-grid" style="margin-bottom: 3rem;">
                <div class="card">
                    <div class="card-icon">👨‍🎓</div>
                    <h3 class="card-title">My Students</h3>
                    <p class="card-description" style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;">0</p>
                    <a href="coach-students.php" class="btn btn-outline" style="margin-top: 0.5rem;">View Students</a>
                </div>
                
                <div class="card">
                    <div class="card-icon">📚</div>
                    <h3 class="card-title">My Courses</h3>
                    <p class="card-description" style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;"><?php echo $my_courses; ?></p>
                    <a href="coach-courses.php" class="btn btn-outline" style="margin-top: 0.5rem;">Manage Courses</a>
                </div>
                
                <div class="card">
                    <div class="card-icon">📅</div>
                    <h3 class="card-title">Schedule</h3>
                    <p class="card-description" style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;">0</p>
                    <a href="coach-schedule.php" class="btn btn-outline" style="margin-top: 0.5rem;">View Schedule</a>
                </div>
            </div>
            
            <div class="section-header">
                <h2 class="section-title">Quick Actions</h2>
            </div>
            
            <div class="card-grid">
                <div class="card">
                    <h3 class="card-title">Grade Students</h3>
                    <p class="card-description">Submit Khan grading results and recommendations</p>
                    <a href="coach-grading.php" class="btn btn-outline" style="margin-top: 1rem;">Grade Students</a>
                </div>
                
                <div class="card">
                    <h3 class="card-title">Course Materials</h3>
                    <p class="card-description">Upload and manage training materials</p>
                    <a href="coach-materials.php" class="btn btn-outline" style="margin-top: 1rem;">Manage Materials</a>
                </div>
                
                <div class="card">
                    <h3 class="card-title">Attendance</h3>
                    <p class="card-description">Track student attendance</p>
                    <a href="coach-attendance.php" class="btn btn-outline" style="margin-top: 1rem;">Take Attendance</a>
                </div>
                
                <div class="card">
                    <h3 class="card-title">My Profile</h3>
                    <p class="card-description">Update instructor profile</p>
                    <a href="profile.php" class="btn btn-outline" style="margin-top: 1rem;">Edit Profile</a>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- PROFESSIONAL STUDENT DASHBOARD -->
    <div class="admin-dashboard">
        <div class="dashboard-header">
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="opacity: 0.9; margin-bottom: 0.5rem;">Student Portal</p>
                        <h1 style="font-size: 2.5rem; margin: 0;">Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>
                        <p style="opacity: 0.8; margin-top: 0.5rem;">Continue your Muayboran journey</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; opacity: 0.8;">Last login</p>
                        <p style="font-size: 1.25rem; font-weight: 600;"><?php echo date('M d, Y'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Student Statistics -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        📚
                    </div>
                    <div class="stat-value"><?php echo $my_courses; ?></div>
                    <div class="stat-label">My Courses</div>
                </div>

                <?php
                // Get student's current khan level
                $student_data = $conn->query("SELECT khan_level FROM users WHERE id = $user_id")->fetch_assoc();
                $current_khan = $student_data['khan_level'] ?: 'Not Assigned';
                ?>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        🏅
                    </div>
                    <div class="stat-value" style="font-size: 1.75rem;"><?php echo htmlspecialchars($current_khan); ?></div>
                    <div class="stat-label">Current Khan Level</div>
                </div>

                <?php
                // Get total completed modules
                $completed = $conn->query("SELECT COUNT(*) as count FROM course_progress WHERE user_id = $user_id AND completed = 1")->fetch_assoc()['count'];
                ?>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        ✅
                    </div>
                    <div class="stat-value"><?php echo $completed; ?></div>
                    <div class="stat-label">Completed Lessons</div>
                </div>

                <?php
                // Get attendance percentage
                $attendance = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present FROM attendance WHERE user_id = $user_id")->fetch_assoc();
                $attendance_pct = $attendance['total'] > 0 ? round(($attendance['present'] / $attendance['total']) * 100) : 0;
                ?>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        📊
                    </div>
                    <div class="stat-value"><?php echo $attendance_pct; ?>%</div>
                    <div class="stat-label">Attendance Rate</div>
                </div>
            </div>

            <!-- My Courses Section -->
            <div style="margin-bottom: 3rem;">
                <h2 style="font-family: var(--font-display); font-size: 2rem; margin-bottom: 2rem; color: var(--color-primary);">
                    My Enrolled Courses
                </h2>

                <?php
                $enrolled_courses = $conn->query("
                    SELECT c.*, ce.enrollment_date, ce.progress, ce.status
                    FROM course_enrollments ce
                    JOIN courses c ON ce.course_id = c.id
                    WHERE ce.user_id = $user_id
                    ORDER BY ce.enrollment_date DESC
                ");
                ?>

                <?php if ($enrolled_courses->num_rows > 0): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                        <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
                            <div class="management-card">
                                <div style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); height: 150px; margin: -2rem -2rem 1rem -2rem; border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                    📚
                                </div>
                                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <p style="color: var(--color-text-light); margin-bottom: 1rem; font-size: 0.875rem;">
                                    <?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...
                                </p>
                                
                                <!-- Progress Bar -->
                                <div style="margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-size: 0.875rem; font-weight: 600;">Progress</span>
                                        <span style="font-size: 0.875rem; font-weight: 600;"><?php echo round($course['progress']); ?>%</span>
                                    </div>
                                    <div style="background: var(--color-bg-light); height: 8px; border-radius: 4px; overflow: hidden;">
                                        <div style="background: linear-gradient(90deg, var(--color-secondary), var(--color-primary)); height: 100%; width: <?php echo $course['progress']; ?>%; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="student-course-view.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;">
                                        Continue Learning
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="content-card" style="text-align: center; padding: 4rem 2rem;">
                        <div style="font-size: 5rem; margin-bottom: 1rem;">📚</div>
                        <h3 style="margin-bottom: 1rem;">No Enrolled Courses</h3>
                        <p style="color: var(--color-text-light); margin-bottom: 2rem;">
                            Start your Muayboran journey by enrolling in a course!
                        </p>
                        <a href="student-course-catalog.php" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">
                            Browse Available Courses
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div style="margin-bottom: 3rem;">
                <h2 style="font-family: var(--font-display); font-size: 2rem; margin-bottom: 2rem; color: var(--color-primary);">
                    Quick Actions
                </h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <a href="student-course-catalog.php" class="quick-action" style="text-decoration: none;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📖</div>
                        <h4 style="margin-bottom: 0.5rem; color: var(--color-primary);">Browse Courses</h4>
                        <p style="font-size: 0.875rem; color: var(--color-text-light);">Explore available training programs</p>
                    </a>

                    <a href="student-schedule.php" class="quick-action" style="text-decoration: none;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
                        <h4 style="margin-bottom: 0.5rem; color: var(--color-primary);">Class Schedule</h4>
                        <p style="font-size: 0.875rem; color: var(--color-text-light);">View upcoming training sessions</p>
                    </a>

                    <a href="student-khan-request.php" class="quick-action" style="text-decoration: none;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🏅</div>
                        <h4 style="margin-bottom: 0.5rem; color: var(--color-primary);">Khan Grading</h4>
                        <p style="font-size: 0.875rem; color: var(--color-text-light);">Request level promotion</p>
                    </a>

                    <a href="student-profile.php" class="quick-action" style="text-decoration: none;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">👤</div>
                        <h4 style="margin-bottom: 0.5rem; color: var(--color-primary);">My Profile</h4>
                        <p style="font-size: 0.875rem; color: var(--color-text-light);">Update personal information</p>
                    </a>
                </div>
            </div>

            <!-- Upcoming Events/Classes -->
            <div>
                <h2 style="font-family: var(--font-display); font-size: 2rem; margin-bottom: 2rem; color: var(--color-primary);">
                    Upcoming Events
                </h2>

                <?php
                $upcoming_events = $conn->query("
                    SELECT e.* FROM events e
                    WHERE e.start_date >= NOW() AND e.status = 'upcoming'
                    ORDER BY e.start_date ASC
                    LIMIT 3
                ");
                ?>

                <?php if ($upcoming_events->num_rows > 0): ?>
                    <div style="display: grid; gap: 1rem;">
                        <?php while ($event = $upcoming_events->fetch_assoc()): ?>
                            <div class="content-card" style="display: flex; gap: 2rem; align-items: center;">
                                <div style="background: linear-gradient(135deg, var(--color-secondary), var(--color-primary)); width: 80px; height: 80px; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; flex-shrink: 0;">
                                    <div style="font-size: 1.5rem; font-weight: 700;"><?php echo date('d', strtotime($event['start_date'])); ?></div>
                                    <div style="font-size: 0.875rem;"><?php echo date('M', strtotime($event['start_date'])); ?></div>
                                </div>
                                <div style="flex: 1;">
                                    <h4 style="margin-bottom: 0.5rem; font-size: 1.125rem;"><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <p style="color: var(--color-text-light); font-size: 0.875rem; margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($event['description']); ?>
                                    </p>
                                    <p style="font-size: 0.875rem;">
                                        <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['start_date'])); ?> |
                                        <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                                    </p>
                                </div>
                                <a href="student-event-register.php?id=<?php echo $event['id']; ?>" class="btn btn-outline" style="white-space: nowrap;">
                                    Register
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="content-card" style="text-align: center; padding: 3rem 2rem;">
                        <p style="color: var(--color-text-light);">No upcoming events at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
