<?php
$page_title = "Khan Level Management";
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$conn = getDbConnection();
$message = '';
$error = '';

// Handle APPROVE Grading
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $grading_id = (int)$_GET['approve'];
    $stmt = $conn->prepare("UPDATE khan_gradings SET status = 'approved', approved_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $grading_id);
    
    if ($stmt->execute()) {
        // Update user's khan level
        $grading = $conn->query("SELECT user_id, khan_level FROM khan_gradings WHERE id = $grading_id")->fetch_assoc();
        if ($grading) {
            $update_user = $conn->prepare("UPDATE users SET khan_level = ? WHERE id = ?");
            $khan_name = "Khan " . $grading['khan_level'];
            $update_user->bind_param("si", $khan_name, $grading['user_id']);
            $update_user->execute();
            $update_user->close();
        }
        
        $message = "Khan grading approved and user level updated!";
    } else {
        $error = "Failed to approve grading.";
    }
    $stmt->close();
}

// Handle REJECT Grading
if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $grading_id = (int)$_GET['reject'];
    $stmt = $conn->prepare("UPDATE khan_gradings SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $grading_id);
    
    if ($stmt->execute()) {
        $message = "Khan grading rejected.";
    } else {
        $error = "Failed to reject grading.";
    }
    $stmt->close();
}

// Handle ADD Manual Grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grading'])) {
    $user_id = (int)$_POST['user_id'];
    $khan_level = (int)$_POST['khan_level'];
    $examiner_id = $_SESSION['user_id'];
    $notes = trim($_POST['notes']);
    
    $stmt = $conn->prepare("INSERT INTO khan_gradings (user_id, khan_level, examiner_id, status, notes, grading_date, approved_at, created_at) VALUES (?, ?, ?, 'approved', ?, NOW(), NOW(), NOW())");
    $stmt->bind_param("iiis", $user_id, $khan_level, $examiner_id, $notes);
    
    if ($stmt->execute()) {
        // Update user's khan level
        $khan_name = "Khan " . $khan_level;
        $update = $conn->prepare("UPDATE users SET khan_level = ? WHERE id = ?");
        $update->bind_param("si", $khan_name, $user_id);
        $update->execute();
        $update->close();
        
        $message = "Khan level assigned successfully!";
    } else {
        $error = "Failed to assign Khan level: " . $stmt->error;
    }
    $stmt->close();
}

// Get pending gradings
$pending = $conn->query("
    SELECT kg.*, u.name as student_name, u.email, u.khan_level as current_level,
           e.name as examiner_name
    FROM khan_gradings kg
    JOIN users u ON kg.user_id = u.id
    LEFT JOIN users e ON kg.examiner_id = e.id
    WHERE kg.status = 'pending'
    ORDER BY kg.created_at DESC
");

// Get approved gradings
$approved = $conn->query("
    SELECT kg.*, u.name as student_name, u.email, u.khan_level as current_level,
           e.name as examiner_name
    FROM khan_gradings kg
    JOIN users u ON kg.user_id = u.id
    LEFT JOIN users e ON kg.examiner_id = e.id
    WHERE kg.status = 'approved'
    ORDER BY kg.approved_at DESC
    LIMIT 20
");

// Get all users for manual assignment
$users = $conn->query("SELECT id, name, email, khan_level, role FROM users WHERE role IN ('student', 'coach', 'teacher') ORDER BY name ASC");

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

.grading-card {
    background: var(--color-bg-light);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--color-secondary);
    transition: all 0.3s ease;
}

.grading-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.khan-badge {
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.875rem;
}
</style>

<div class="admin-page">
    <div class="container">
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Khan Level Management</h1>
                    <p style="color: var(--color-text-light);">Manage Khan grading requests and student promotions</p>
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

        <!-- Manual Khan Assignment -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                Assign Khan Level Manually
            </h3>

            <form method="POST" style="background: var(--color-bg-light); padding: 1.5rem; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: 2fr 1fr 3fr; gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Select Student *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Choose a student...</option>
                            <?php 
                            $users->data_seek(0);
                            while ($user = $users->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['name']); ?> 
                                    (<?php echo $user['khan_level'] ?: 'No Khan'; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Khan Level *</label>
                        <select name="khan_level" class="form-select" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>">Khan <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-input" placeholder="Optional notes...">
                    </div>
                </div>

                <button type="submit" name="add_grading" class="btn btn-primary">Assign Khan Level</button>
            </form>
        </div>

        <!-- Pending Grading Requests -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                Pending Grading Requests (<?php echo $pending->num_rows; ?>)
            </h3>

            <?php if ($pending->num_rows > 0): ?>
                <?php while ($grading = $pending->fetch_assoc()): ?>
                    <div class="grading-card">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                    <h4 style="margin: 0; font-size: 1.25rem;"><?php echo htmlspecialchars($grading['student_name']); ?></h4>
                                    <span class="khan-badge">
                                        Khan <?php echo $grading['khan_level']; ?>
                                    </span>
                                </div>
                                <p style="color: var(--color-text-light); margin: 0.5rem 0; font-size: 0.875rem;">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($grading['email']); ?> |
                                    <strong>Current Level:</strong> <?php echo $grading['current_level'] ?: 'None'; ?>
                                </p>
                                <p style="color: var(--color-text-light); margin: 0.5rem 0; font-size: 0.875rem;">
                                    <strong>Requested:</strong> <?php echo date('M d, Y', strtotime($grading['created_at'])); ?>
                                    <?php if ($grading['examiner_name']): ?>
                                        | <strong>Examiner:</strong> <?php echo htmlspecialchars($grading['examiner_name']); ?>
                                    <?php endif; ?>
                                </p>
                                <?php if ($grading['notes']): ?>
                                    <p style="background: white; padding: 0.75rem; border-radius: 4px; margin-top: 0.5rem; font-size: 0.875rem;">
                                        <strong>Notes:</strong> <?php echo htmlspecialchars($grading['notes']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="?approve=<?php echo $grading['id']; ?>" 
                                   onclick="return confirm('Approve this grading and update student level?');"
                                   style="background: #4caf50; color: white; padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                                    ✓ Approve
                                </a>
                                <a href="?reject=<?php echo $grading['id']; ?>" 
                                   onclick="return confirm('Reject this grading request?');"
                                   style="background: var(--color-primary); color: white; padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                                    ✗ Reject
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 3rem; color: var(--color-text-light);">
                    No pending grading requests
                </p>
            <?php endif; ?>
        </div>

        <!-- Recent Approved Gradings -->
        <div class="content-card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary); font-size: 1.5rem;">
                Recent Approved Gradings
            </h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--color-bg-light);">
                        <tr>
                            <th style="padding: 1rem; text-align: left;">Student</th>
                            <th style="padding: 1rem; text-align: left;">Khan Level</th>
                            <th style="padding: 1rem; text-align: left;">Examiner</th>
                            <th style="padding: 1rem; text-align: left;">Approved Date</th>
                            <th style="padding: 1rem; text-align: left;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($approved->num_rows > 0): ?>
                            <?php while ($grading = $approved->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($grading['student_name']); ?></div>
                                        <div style="font-size: 0.875rem; color: var(--color-text-light);"><?php echo htmlspecialchars($grading['email']); ?></div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span class="khan-badge">Khan <?php echo $grading['khan_level']; ?></span>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($grading['examiner_name'] ?: 'Manual Assignment'); ?></td>
                                    <td style="padding: 1rem; color: var(--color-text-light);">
                                        <?php echo date('M d, Y', strtotime($grading['approved_at'])); ?>
                                    </td>
                                    <td style="padding: 1rem; font-size: 0.875rem;">
                                        <?php echo $grading['notes'] ? htmlspecialchars(substr($grading['notes'], 0, 50)) . '...' : '-'; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: var(--color-text-light);">
                                    No approved gradings yet
                                </td>
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
