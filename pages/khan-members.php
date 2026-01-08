<?php
$page_title = "Khan Members";
include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <p class="section-subtitle">Our Community</p>
            <h1 class="section-title">Khan Members</h1>
            <p class="section-description">
                Certified practitioners advancing through the Khan grading system
            </p>
        </div>
        
        <div style="max-width: 900px; margin: 3rem auto;">
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--color-primary); margin-bottom: 1rem;">What is Khan?</h3>
                <p style="color: var(--color-text-light);">
                    The Khan system is the traditional ranking structure in Muayboran, similar to belt 
                    levels in other martial arts. It represents a practitioner's knowledge, skill, and 
                    dedication to the art. Each Khan level requires mastery of specific techniques, forms, 
                    and philosophical understanding.
                </p>
            </div>
            
            <div class="section-header" style="margin-top: 3rem;">
                <h2 class="section-title">Members by Level</h2>
            </div>
            
            <div class="card-grid">
                <div class="card">
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div style="width: 80px; height: 80px; background: var(--color-bg-light); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            ⚪
                        </div>
                    </div>
                    <h3 class="card-title" style="text-align: center;">Khan 1-3</h3>
                    <p class="card-description" style="text-align: center;">
                        <strong>Beginner Level</strong><br>
                        Students learning foundational techniques
                    </p>
                </div>
                
                <div class="card">
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div style="width: 80px; height: 80px; background: var(--color-secondary); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            🟡
                        </div>
                    </div>
                    <h3 class="card-title" style="text-align: center;">Khan 4-6</h3>
                    <p class="card-description" style="text-align: center;">
                        <strong>Intermediate Level</strong><br>
                        Practitioners developing advanced skills
                    </p>
                </div>
                
                <div class="card">
                    <div style="text-align: center; margin-bottom: 1rem;">
                        <div style="width: 80px; height: 80px; background: var(--color-primary); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white;">
                            🔴
                        </div>
                    </div>
                    <h3 class="card-title" style="text-align: center;">Khan 7-10</h3>
                    <p class="card-description" style="text-align: center;">
                        <strong>Advanced Level</strong><br>
                        Masters and instructors
                    </p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 4rem;">
                <h2 style="margin-bottom: 1rem;">Ready to Join Our Members?</h2>
                <p style="font-size: 1.15rem; color: var(--color-text-light); margin-bottom: 2rem;">
                    Start your journey in the Khan grading system today
                </p>
                <a href="khan-grading.php" class="btn btn-primary" style="margin-right: 1rem;">View Grading Structure</a>
                <a href="register.php" class="btn btn-outline">Register Now</a>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
