<?php
$page_title = "Contact Us";
require_once '../config/database.php';

$success = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

        if ($stmt->execute()) {
            $success = 'Thank you for your message! We will get back to you soon.';
            // Clear form
            $_POST = array();
        } else {
            $error = 'Failed to send message. Please try again.';
        }

        $stmt->close();
        $conn->close();
    }
}

include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header text-center" style="
    min-height: 320px;
    background:
      linear-gradient(rgba(0,0,0,0.35), rgba(0,0,0,0.35)),
      url('../assets/images/omaa.jpg')
 center / cover no-repeat;
    padding: 4rem 2rem;
    border-radius: 12px;
    color: #fff;
">

            <p class="section-subtitle">Get In Touch</p>
             <h1 class="hero-title" style="color:#ffffff; text-shadow: 0 4px 8px rgba(202, 19, 19, 0.6);">
            Contact Us
        </h1>
            <p class="section-description">
                Have questions about our programs or membership? We're here to help.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-top: 3rem;">
            <!-- Contact Information -->
            <div>
                <h2 style="margin-bottom: 2rem;">Contact Information</h2>

                <div style="margin-bottom: 2rem;">
                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--color-bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            📍
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Address</h4>
                            <p style="color: var(--color-text-light);">
                                Oriental Muayboran Academy<br>
                                240 Rosal St., Pingkian 3, Pasong Tamo, Quaezon City<br>
                                Philippines
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--color-bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            📧
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Email</h4>
                            <p style="color: var(--color-text-light);">
                                <a href="mailto:orientalmuayboranacademy@gmail.com" style="color: var(--color-text-light);">
                                    orientalmuayboranacademy@gmail.com
                                </a>
                            </p>
                        </div>
                    </div>

                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                        <div
                            style="width: 50px; height: 50px; background: var(--color-bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            📱
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem;">Phone</h4>
                            <p style="color: var(--color-text-light);">
                                <a href="tel:+639605667175" style="color: var(--color-text-light);">
                                    +63 960 566 7175
                                </a>
                            </p>
                        </div>
                    </div>

                </div>

                <div style="margin-top: 3rem;">
                    <h3 style="margin-bottom: 1rem;">Follow Us</h3>
                    <div class="social-links">
                        <a href="#"
                            style="width: 50px; height: 50px; border: 2px solid var(--color-border); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 1rem; transition: var(--transition-base);">
                            F
                        </a>
                        
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div>
                <?php if ($error): ?>
                    <div class="alert alert-error"
                        style="background: #ffebee; color: var(--color-primary); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"
                        style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" data-validate>
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Enter your name"
                            required
                            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input"
                            placeholder="your.email@example.com" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" id="subject" name="subject" class="form-input"
                            placeholder="What is this regarding?" required
                            value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label">Message *</label>
                        <textarea id="message" name="message" class="form-textarea"
                            placeholder="Tell us how we can help you"
                            required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>

                    <button type="submit" name="send_message" class="btn btn-primary" style="width: 100%;">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<style>
    @media (max-width: 768px) {
        section>div>div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 2rem !important;
        }
    }
</style>