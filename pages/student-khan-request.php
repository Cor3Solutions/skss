<?php
$page_title = "Khan Grading Request";
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDbConnection();
$message = '';
$error = '';

// Handle grading request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_grading'])) {
    $khan_level = (int)$_POST['khan_level'];
    $notes = trim($_POST['notes']);
    
    // Check if already has pending request
    $check = $conn->query("SELECT id FROM khan_gradings WHERE user_id = $user_id AND status = 'pending'")->fetch_assoc();
    
    if (!$check) {
        $stmt = $conn->prepare("INSERT INTO khan_gradings (user_id, khan_level, notes, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $user_id, $khan_level, $notes);
        
        if ($stmt->execute()) {
            $message = "Khan grading request submitted successfully! Please wait for admin approval.";
        } else {
            $error = "Failed to submit request.";
        }
        $stmt->close();
    } else {
        $error = "You already have a pending grading request!";
    }
}

// Get user's current khan level
$user = $conn->query("SELECT khan_level FROM users WHERE id = $user_id")->fetch_assoc();
$current_khan = $user['khan_level'] ?: 'Not Assigned';

// Get grading history
$history = $conn->query("SELECT * FROM khan_gradings WHERE user_id = $user_id ORDER BY created_at DESC");

include '../includes/header.php';
?>

<style>
.khan-page {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.khan-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}
</style>

<div class="khan-page">
    <div class="container" style="max-width: 900px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">Khan Grading Request</h1>
            <a href="dashboard.php" class="btn btn-outline">← Back</a>
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

        <div class="khan-card">
            <div style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); padding: 2rem; border-radius: 8px; color: white; text-align: center; margin-bottom: 2rem;">
                <h2 style="font-size: 3rem; margin: 0;"><?php echo htmlspecialchars($current_khan); ?></h2>
                <p style="opacity: 0.9; margin-top: 0.5rem;">Your Current Khan Level</p>
            </div>

            <h3 style="margin-bottom: 1.5rem;">Request Grading for Next Level</h3>

            <form method="POST">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Request Level *</label>
                    <select name="khan_level" class="form-select" required>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?php echo $i; ?>">Khan <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Notes / Comments</label>
                    <textarea name="notes" class="form-textarea" rows="4" placeholder="Add any notes about your training or readiness..."></textarea>
                </div>

                <button type="submit" name="request_grading" class="btn btn-primary">
                    Submit Grading Request
                </button>
            </form>
        </div>

        <div class="khan-card">
            <h3 style="margin-bottom: 1.5rem;">Grading History</h3>

            <?php if ($history->num_rows > 0): ?>
                <?php while ($record = $history->fetch_assoc()): ?>
                    <div style="border-left: 4px solid <?php 
                        echo $record['status'] === 'approved' ? '#4caf50' : 
                             ($record['status'] === 'rejected' ? '#f44336' : '#ff9800'); 
                    ?>; padding: 1rem; background: var(--color-bg-light); border-radius: 4px; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4 style="margin: 0 0 0.5rem 0;">Khan <?php echo $record['khan_level']; ?></h4>
                                <p style="font-size: 0.875rem; color: var(--color-text-light); margin: 0;">
                                    Requested: <?php echo date('M d, Y', strtotime($record['created_at'])); ?>
                                </p>
                            </div>
                            <span style="background: <?php 
                                echo $record['status'] === 'approved' ? '#4caf50' : 
                                     ($record['status'] === 'rejected' ? '#f44336' : '#ff9800'); 
                            ?>; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                <?php echo strtoupper($record['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--color-text-light); padding: 2rem;">
                    No grading history yet
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php';
?>
