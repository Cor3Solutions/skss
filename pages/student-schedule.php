<?php
$page_title = "My Schedule";
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/header.php';
?>

<style>
.schedule-page {
    background: linear-gradient(135deg, #f8f6f3 0%, #ffffff 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.schedule-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
</style>

<div class="schedule-page">
    <div class="container" style="max-width: 1000px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem;">My Training Schedule</h1>
            <a href="dashboard.php" class="btn btn-outline">← Back</a>
        </div>

        <div class="schedule-card" style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 5rem; margin-bottom: 1rem;">📅</div>
            <h3 style="margin-bottom: 1rem;">Schedule Feature Coming Soon</h3>
            <p style="color: var(--color-text-light); margin-bottom: 2rem;">
                Training schedule and class booking will be available here.
            </p>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
