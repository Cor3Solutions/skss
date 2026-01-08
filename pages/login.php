<?php
$page_title = "Sign In";
require_once '../config/database.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
        
        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 500px; margin: 0 auto;">
            <div class="section-header text-center">
                <p class="section-subtitle">Member Access</p>
                <h1 class="section-title">Sign In</h1>
                <p class="section-description">Access your member dashboard and course materials</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="your.email@example.com"
                        required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem;">
                        <input type="checkbox" name="remember" style="width: auto;">
                        Remember me
                    </label>
                    <a href="forgot-password.php" style="color: var(--color-primary); font-size: 0.95rem;">Forgot password?</a>
                </div>
                
                <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">
                    Sign In
                </button>
                
                <p style="text-align: center; margin-top: 1.5rem; color: var(--color-text-light);">
                    Don't have an account? 
                    <a href="register.php" style="color: var(--color-primary); font-weight: 500;">Register here</a>
                </p>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>