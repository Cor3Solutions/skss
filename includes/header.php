<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detect if we're in root or pages directory
$isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$basePath = $isInPages ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Oriental Muayboran Academy</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0&autoLogAppEvents=1"></script>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo $basePath; ?>index.php" class="logo">
                    <img src="<?php echo $basePath; ?>assets/images/oma.png" alt="Oriental Muayboran Academy"
                        class="logo-img" style="height: 70px; width: auto;">
                </a>

                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <nav class="main-nav" id="mainNav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo $basePath; ?>index.php" class="nav-link">Home</a>
                        </li>

                        <li class="nav-item has-dropdown">
                            <a href="<?php echo $basePath; ?>pages/about.php" class="nav-link">About Us</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo $basePath; ?>pages/history.php">History</a></li>
                                <li><a href="<?php echo $basePath; ?>pages/lineage.php">Lineage</a></li> 
                                <li><a href="<?php echo $basePath; ?>pages/khan-members.php">Khan/Members</a></li> 
                            </ul>
                        </li>

                        <li class="nav-item has-dropdown">
                            <a href="<?php echo $basePath; ?>pages/course.php" class="nav-link">Courses</a>
                        </li>

                        <li class="nav-item has-dropdown">
                            <a href="#" class="nav-link">Become a Member</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo $basePath; ?>pages/khan-grading.php">Khan Grading Structure</a>
                                </li> 
                                <li><a href="<?php echo $basePath; ?>pages/membership-benefits.php">Membership
                                        Benefits</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $basePath; ?>pages/contact.php" class="nav-link">Contact Us</a>
                        </li>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a href="<?php echo $basePath; ?>pages/dashboard.php" class="nav-link nav-cta">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $basePath; ?>pages/logout.php" class="nav-link">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="<?php echo $basePath; ?>pages/login.php" class="nav-link nav-cta">Sign In</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">