<?php
$page_title = "Manage Users";
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$conn = getDbConnection();
$message = '';
$error = '';

// Handle DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    // Don't allow deleting yourself
    if ($delete_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $message = "User deleted successfully!";
        } else {
            $error = "Failed to delete user.";
        }
        $stmt->close();
    } else {
        $error = "You cannot delete your own account!";
    }
}

// Handle ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $membership_status = $_POST['membership_status'];
    $khan_level = trim($_POST['khan_level']);
    $password = $_POST['password'];
    
    if (empty($name) || empty($email) || empty($role)) {
        $error = "Name, email, and role are required!";
    } else {
        if ($user_id > 0) {
            // UPDATE existing user
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, role=?, membership_status=?, khan_level=? WHERE id=?");
                $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $role, $membership_status, $khan_level, $user_id);
            } else {
                // Update without password change
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, membership_status=?, khan_level=? WHERE id=?");
                $stmt->bind_param("sssssi", $name, $email, $role, $membership_status, $khan_level, $user_id);
            }
            
            if ($stmt->execute()) {
                $message = "User updated successfully!";
            } else {
                $error = "Failed to update user: " . $stmt->error;
            }
        } else {
            // INSERT new user
            if (empty($password)) {
                $error = "Password is required for new users!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, membership_status, khan_level, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssssss", $name, $email, $hashed_password, $role, $membership_status, $khan_level);
                
                if ($stmt->execute()) {
                    $message = "User created successfully!";
                } else {
                    $error = "Failed to create user: " . $stmt->error;
                }
            }
        }
        
        if (isset($stmt)) $stmt->close();
    }
}

// Get user to edit (if editing)
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_user = $result->fetch_assoc();
    $stmt->close();
}

// Get all users
$users_query = "SELECT id, name, email, role, membership_status, khan_level, created_at FROM users ORDER BY created_at DESC";
$users = $conn->query($users_query);

include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle">Admin Panel</p>
            <h1 class="section-title">Manage Users</h1>
            <p class="section-description">Create, edit, and delete user accounts</p>
        </div>
        
        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 4px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 4px; margin-bottom: 2rem;">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Add/Edit User Form -->
        <div class="card" style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary);">
                <?php echo $edit_user ? 'Edit User' : 'Add New User'; ?>
            </h3>
            
            <form method="POST" action="">
                <?php if ($edit_user): ?>
                    <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            required
                            value="<?php echo $edit_user ? htmlspecialchars($edit_user['name']) : ''; ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required
                            value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>"
                        >
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="role" class="form-label">Role *</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="student" <?php echo ($edit_user && $edit_user['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                            <option value="coach" <?php echo ($edit_user && $edit_user['role'] === 'coach') ? 'selected' : ''; ?>>Coach</option>
                            <option value="teacher" <?php echo ($edit_user && $edit_user['role'] === 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                            <option value="referee" <?php echo ($edit_user && $edit_user['role'] === 'referee') ? 'selected' : ''; ?>>Referee</option>
                            <option value="admin" <?php echo ($edit_user && $edit_user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="membership_status" class="form-label">Membership Status</label>
                        <select id="membership_status" name="membership_status" class="form-select">
                            <option value="pending" <?php echo ($edit_user && $edit_user['membership_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="active" <?php echo ($edit_user && $edit_user['membership_status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($edit_user && $edit_user['membership_status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            <option value="expired" <?php echo ($edit_user && $edit_user['membership_status'] === 'expired') ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="khan_level" class="form-label">Khan Level</label>
                        <input 
                            type="text" 
                            id="khan_level" 
                            name="khan_level" 
                            class="form-input" 
                            placeholder="e.g., Khan 1"
                            value="<?php echo $edit_user ? htmlspecialchars($edit_user['khan_level']) : ''; ?>"
                        >
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="password" class="form-label">
                        Password <?php echo $edit_user ? '(leave blank to keep current)' : '*'; ?>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter password"
                        <?php echo !$edit_user ? 'required' : ''; ?>
                    >
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
                    </button>
                    
                    <?php if ($edit_user): ?>
                        <a href="admin-users.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Users List -->
        <div class="card">
            <h3 style="margin-bottom: 1.5rem; color: var(--color-primary);">All Users</h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                            <th style="padding: 1rem; font-family: var(--font-display);">Name</th>
                            <th style="padding: 1rem; font-family: var(--font-display);">Email</th>
                            <th style="padding: 1rem; font-family: var(--font-display);">Role</th>
                            <th style="padding: 1rem; font-family: var(--font-display);">Khan</th>
                            <th style="padding: 1rem; font-family: var(--font-display);">Status</th>
                            <th style="padding: 1rem; font-family: var(--font-display);">Joined</th>
                            <th style="padding: 1rem; font-family: var(--font-display); text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users->num_rows > 0): ?>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="background: var(--color-bg-light); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo $user['khan_level'] ?: '-'; ?></td>
                                    <td style="padding: 1rem;">
                                        <?php
                                        $status_color = [
                                            'active' => '#4caf50',
                                            'pending' => '#ff9800',
                                            'inactive' => '#9e9e9e',
                                            'expired' => '#f44336'
                                        ];
                                        $color = $status_color[$user['membership_status']] ?? '#9e9e9e';
                                        ?>
                                        <span style="background: <?php echo $color; ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">
                                            <?php echo ucfirst($user['membership_status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; color: var(--color-text-light); font-size: 0.875rem;">
                                        <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="?edit=<?php echo $user['id']; ?>" style="background: var(--color-secondary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; margin-right: 0.5rem;">
                                            Edit
                                        </a>
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <a href="?delete=<?php echo $user['id']; ?>" 
                                               onclick="return confirm('Are you sure you want to delete this user?');"
                                               style="background: var(--color-primary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem;">
                                                Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: var(--color-text-light);">
                                    No users found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
            <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
        </div>
    </div>
</section>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
