<?php
$page_title = "Manage Messages";
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$conn = getDbConnection();
$message = '';

// Handle DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $message = "Message deleted successfully!";
    }
    $stmt->close();
}

// Handle MARK AS READ
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $read_id = (int)$_GET['read'];
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $read_id);
    $stmt->execute();
    $stmt->close();
}

// Handle MARK AS REPLIED
if (isset($_GET['replied']) && is_numeric($_GET['replied'])) {
    $replied_id = (int)$_GET['replied'];
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
    $stmt->bind_param("i", $replied_id);
    $stmt->execute();
    $stmt->close();
}

// Get all messages
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");

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

.message-item {
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--color-secondary);
    transition: all 0.3s ease;
}

.message-new {
    background: #fffef0;
    border-left-color: var(--color-primary);
}

.message-read {
    background: var(--color-bg-light);
    opacity: 0.7;
}

.message-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-new { background: #ff9800; color: white; }
.status-read { background: #2196f3; color: white; }
.status-replied { background: #4caf50; color: white; }
.status-archived { background: #9e9e9e; color: white; }
</style>

<div class="admin-page">
    <div class="container">
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Contact Messages</h1>
                    <p style="color: var(--color-text-light);">View and manage contact form submissions</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <?php if ($messages->num_rows > 0): ?>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                    <div class="message-item <?php echo $msg['status'] === 'new' ? 'message-new' : 'message-read'; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                    <h3 style="color: var(--color-primary); margin: 0;">
                                        <?php echo htmlspecialchars($msg['subject']); ?>
                                    </h3>
                                    <span class="status-badge status-<?php echo $msg['status']; ?>">
                                        <?php echo strtoupper($msg['status']); ?>
                                    </span>
                                </div>
                                <p style="color: var(--color-text-light); font-size: 0.875rem; margin: 0.5rem 0;">
                                    <strong>From:</strong> <?php echo htmlspecialchars($msg['name']); ?> 
                                    (<?php echo htmlspecialchars($msg['email']); ?>)
                                    <?php if ($msg['phone']): ?>
                                        | <strong>Phone:</strong> <?php echo htmlspecialchars($msg['phone']); ?>
                                    <?php endif; ?>
                                </p>
                                <p style="color: var(--color-text-light); font-size: 0.875rem; margin: 0;">
                                    <strong>Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($msg['created_at'])); ?>
                                </p>
                            </div>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php if ($msg['status'] === 'new'): ?>
                                    <a href="?read=<?php echo $msg['id']; ?>" 
                                       style="background: #2196f3; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; white-space: nowrap;">
                                        Mark Read
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($msg['status'] !== 'replied'): ?>
                                    <a href="?replied=<?php echo $msg['id']; ?>" 
                                       style="background: #4caf50; color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; white-space: nowrap;">
                                        Mark Replied
                                    </a>
                                <?php endif; ?>
                                
                                <a href="?delete=<?php echo $msg['id']; ?>" 
                                   onclick="return confirm('Delete this message?');"
                                   style="background: var(--color-primary); color: white; padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.875rem; white-space: nowrap;">
                                    Delete
                                </a>
                            </div>
                        </div>
                        
                        <div style="background: white; padding: 1rem; border-radius: 4px; border-left: 3px solid var(--color-secondary); margin-bottom: 1rem;">
                            <p style="white-space: pre-wrap; line-height: 1.6; margin: 0;"><?php echo htmlspecialchars($msg['message']); ?></p>
                        </div>
                        
                        <div>
                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                               class="btn btn-outline" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                📧 Reply via Email
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                    <h3>No Messages Yet</h3>
                    <p style="color: var(--color-text-light); margin-top: 1rem;">
                        Contact form submissions will appear here
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
