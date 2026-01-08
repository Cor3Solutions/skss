<?php
$page_title = "Membership Overview";
include '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <p class="section-subtitle">Join Us</p>
            <h1 class="section-title">Membership Overview</h1>
            <p class="section-description">
                Become part of the global Muayboran community
            </p>
        </div>
        
        <div style="max-width: 900px; margin: 3rem auto;">
            <div class="card" style="margin-bottom: 3rem;">
                <h3 style="color: var(--color-primary); margin-bottom: 1rem;">Why Become a Member?</h3>
                <p style="color: var(--color-text-light);">
                    Membership in Oriental Muayboran Academy connects you to an authentic lineage of 
                    traditional Thai martial arts. You'll gain access to certified instruction, official 
                    Khan grading, international recognition, and a worldwide community of practitioners.
                </p>
            </div>
        </div>
        
        <div class="card-grid">
            <div class="card">
                <h3 class="card-title">Student Membership</h3>
                <p style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;">$299/year</p>
                <ul style="list-style: none; padding: 0; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ Access to student courses</li>
                    <li style="padding: 0.5rem 0;">✓ Khan grading eligibility</li>
                    <li style="padding: 0.5rem 0;">✓ Training materials</li>
                    <li style="padding: 0.5rem 0;">✓ Member certificate</li>
                    <li style="padding: 0.5rem 0;">✓ Event participation</li>
                </ul>
                <a href="register.php" class="btn btn-outline" style="width: 100%;">Join Now</a>
            </div>
            
            <div class="card" style="border: 2px solid var(--color-primary);">
                <h3 class="card-title">Instructor Membership</h3>
                <p style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;">$899/year</p>
                <ul style="list-style: none; padding: 0; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ All student benefits</li>
                    <li style="padding: 0.5rem 0;">✓ Instructor certification</li>
                    <li style="padding: 0.5rem 0;">✓ Teaching materials</li>
                    <li style="padding: 0.5rem 0;">✓ Advanced seminars</li>
                    <li style="padding: 0.5rem 0;">✓ School affiliation rights</li>
                </ul>
                <a href="register.php" class="btn btn-primary" style="width: 100%;">Join Now</a>
            </div>
            
            <div class="card">
                <h3 class="card-title">Academy Membership</h3>
                <p style="font-size: 2rem; font-weight: 600; color: var(--color-primary); margin: 1rem 0;">$1,499/year</p>
                <ul style="list-style: none; padding: 0; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ All instructor benefits</li>
                    <li style="padding: 0.5rem 0;">✓ Academy certification</li>
                    <li style="padding: 0.5rem 0;">✓ Master teacher support</li>
                    <li style="padding: 0.5rem 0;">✓ Curriculum resources</li>
                    <li style="padding: 0.5rem 0;">✓ Business guidance</li>
                </ul>
                <a href="register.php" class="btn btn-outline" style="width: 100%;">Join Now</a>
            </div>
        </div>
        
        <div style="max-width: 700px; margin: 4rem auto; text-align: center;">
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem;">Ready to Join?</h2>
            <p style="font-size: 1.15rem; line-height: 1.8; color: var(--color-text-light); margin-bottom: 2rem;">
                Start your journey with Oriental Muayboran Academy today and become part of an 
                authentic martial arts lineage.
            </p>
            <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Register Now</a>
            <a href="contact.php" class="btn btn-outline">Contact Us</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
