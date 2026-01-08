<?php
$page_title = "Manage ";
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle">Admin Panel</p>
            <h1 class="section-title">Manage </h1>
            <p class="section-description"> management coming soon</p>
        </div>
        
        <div class="card">
            <div style="text-align: center; padding: 4rem 2rem;">
                <div style="font-size: 5rem; margin-bottom: 1rem;">🚧</div>
                <h2 style="margin-bottom: 1rem;">Under Construction</h2>
                <p style="color: var(--color-text-light); margin-bottom: 2rem;">
                    The memberships management feature is currently being developed.
                </p>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
