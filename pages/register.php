<?php
$page_title = "Register";
require_once '../config/database.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $conn = getDbConnection();
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'An account with this email already exists';
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role, created_at) VALUES (?, ?, ?, ?, 'student', NOW())");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $phone);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now sign in.';
                // Clear form fields
                $_POST = array();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="section-header text-center">
                <p class="section-subtitle">Join Us</p>
                <h1 class="section-title">Create Account</h1>
                <p class="section-description">Begin your Muayboran journey with us</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                    <?php echo htmlspecialchars($success); ?>
                    <a href="login.php" style="text-decoration: underline; margin-left: 0.5rem;">Sign in now</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate>
                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Enter your full name"
                        required
                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
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
                    <label for="phone" class="form-label">Phone Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-input" 
                        placeholder="+1 (555) 123-4567"
                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Minimum 8 characters"
                        required
                    >
                    <small style="color: var(--color-text-light); font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Password must be at least 8 characters long
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        placeholder="Re-enter your password"
                        required
                    >
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: start; gap: 0.5rem; font-size: 0.95rem;">
                        <input type="checkbox" name="terms" required style="width: auto; margin-top: 0.25rem;">
                        <span>
                            I agree to the <a href="/pages/terms.php" style="color: var(--color-primary);">Terms of Service</a> 
                            and <a href="/pages/privacy.php" style="color: var(--color-primary);">Privacy Policy</a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">
                    Create Account
                </button>
                
                <p style="text-align: center; margin-top: 1.5rem; color: var(--color-text-light);">
                    Already have an account? 
                    <a href="login.php" style="color: var(--color-primary); font-weight: 500;">Sign in</a>
                </p>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>