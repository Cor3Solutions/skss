<?php
$page_title = "My Profile";
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDbConnection();
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $country = trim($_POST['country']);
    $emergency_contact = trim($_POST['emergency_contact']);
    $emergency_phone = trim($_POST['emergency_phone']);
    
    $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, city=?, country=?, emergency_contact=?, emergency_phone=? WHERE id=?");
    $stmt->bind_param("sssssssi", $name, $phone, $address, $city, $country, $emergency_contact, $emergency_phone, $user_id);
    
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['user_name'] = $name;
    }
    $stmt->close();
}

// Get user data
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

include '../includes/header.php';
?>

<style>
.profile-page {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.profile-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}
</style>

<div class="profile-page">
    <div class="container" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">My Profile</h1>
            <a href="dashboard.php" class="btn btn-outline">← Back</a>
        </div>

        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <form method="POST">
                <h3 style="margin-bottom: 1.5rem; color: var(--color-primary);">Personal Information</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-input" required value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email (Cannot be changed)</label>
                        <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Current Khan Level</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['khan_level'] ?: 'Not Assigned'); ?>" disabled>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-textarea" rows="2"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-input" value="<?php echo htmlspecialchars($user['city']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-input" value="<?php echo htmlspecialchars($user['country']); ?>">
                    </div>
                </div>

                <h3 style="margin-bottom: 1.5rem; color: var(--color-primary);">Emergency Contact</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact" class="form-input" value="<?php echo htmlspecialchars($user['emergency_contact']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_phone" class="form-input" value="<?php echo htmlspecialchars($user['emergency_phone']); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php';
?>
