<?php
$page_title = "Home";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-background">
        <img src="assets/images/omaa.jpg" alt="Muayboran Training">
        <div class="hero-overlay"></div>
    </div>

    <div class="hero-content">
        <h2 class="hero-subtitle"
            style="text-shadow: 0 2px 4px rgba(255,255,255,0.6); font-weight: bold; font-size: 2em;">
            Sit Kru Sane
        </h2>

        <h1 class="hero-title" style="color:#ffffff; text-shadow: 0 4px 8px rgba(22, 22, 22, 0.6);">
            Oriental Muayboran Academy
        </h1>

        <h3 class="hero-description" style="color:#ffffff; text-shadow: 0 2px 4px rgba(255,255,255,0.6);">
            An embodiment of martial tradition and discipline.<br>
            Student of Teacher Sane – Preserving ancient Thai martial arts.
        </h3>

        <div class="hero-buttons">
            <a href="pages/membership-overview.php" class="btn btn-primary">Become a Member</a>
            <a href="pages/about.php" class="btn btn-primary">Learn More</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle">Who We Are</p>
            <h2 class="section-title">The Path of Muayboran</h2>
            <p class="section-description">
                Oriental Muay Boran Academy (OMA) is a sanctuary for ancient Siamese warfare,
                rooting its practice in the profound lineage of Great Grandmaster Sane Tubthimtong.
            </p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-icon">⚔️</div>
                <h3 class="card-title">Authentic Heritage</h3>
                <p class="card-description">
                    Our curriculum, developed by Ajarn Brendaley Tarnate, preserves the
                    traditional warfare systems, weaponry (Krabi Krabong), and cultural rituals.
                </p>
            </div>

            <div class="card">
                <div class="card-icon">📈</div>
                <h3 class="card-title">The Khan System</h3>
                <p class="card-description">
                    A structured 16-level progression based on constructivist learning,
                    guiding students from fundamental mastery to international mastership.
                </p>
            </div>

            <div class="card">
                <div class="card-icon">🧘</div>
                <h3 class="card-title">Mindful Growth</h3>
                <p class="card-description">
                    Beyond physical striking, we integrate meditation and Thai philosophy
                    to cultivate discipline, humility, and a grounded spirit.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <div class="section-header">

            <p class="section-subtitle" style="color: yellow;">Academic Progression</p>
            <h2 class="section-title" style="color: #8b0000; text-shadow: 2px 2px 4px rgba(255,255,255,0.8);">Training
                Programs</h2>
            <h3 class="section-description">
                From Khan 1 to Mastership, our programs are designed to transform
                practitioners into guardians of the art.
            </h3>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3 class="card-title">Nakmuay (Student)</h3>
                <p class="card-description">
                    Levels Khan 1–10. Focuses on the "Eight Limbs," footwork,
                    traditional forms (Ram Muay), and foundational defense.
                </p>
                <a href="pages/course-student.php" class="btn btn-outline" style="
         margin-top: 1rem;
         color: #ffffff;
         border: 2px solid #ffffff;
         background: transparent;
       " onmouseover="this.style.background='#ffffff'; this.style.color='rgb(149,44,44)'"
                    onmouseout="this.style.background='transparent'; this.style.color='#ffffff'">
                    View Syllabus
                </a>
            </div>

            <div class="card">
                <h3 class="card-title">Kru (Instructor)</h3>
                <p class="card-description">
                    Levels Khan 11–16. Advanced mastership training for those
                    called to teach and preserve the Sit Kru Sane lineage.
                </p>
                <a href="pages/course-coach.php" class="btn btn-outline" style="
         margin-top: 1rem;
         color: #ffffff;
         border: 2px solid #ffffff;
         background: transparent;
       " onmouseover="this.style.background='#ffffff'; this.style.color='rgb(149,44,44)'"
                    onmouseout="this.style.background='transparent'; this.style.color='#ffffff'">
                    Instructor Path
                </a>
            </div>

            <div class="card">
                <h3 class="card-title">Krabi Krabong</h3>
                <p class="card-description">
                    The specialized study of Thai weaponry, an essential branch
                    of the traditional OMA curriculum.
                </p>

            </div>

        </div>
    </div>
</section>

<!-- facebook sdk -->
<section class="section">
    <div class="container">

        <div class="section-header">
            <p class="section-subtitle">Follow Us</p>

        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <div class="fb-page" data-href="https://www.facebook.com/OrientalMuayboranAcademy" data-tabs="timeline"
                data-width="" data-height="700" data-small-header="false" data-adapt-container-width="true"
                data-hide-cover="false" data-show-facepile="true">
                <blockquote cite="https://www.facebook.com/OrientalMuayboranAcademy" class="fb-xfbml-parse-ignore"><a
                        href="https://www.facebook.com/OrientalMuayboranAcademy">Oriental Muayboran Academy</a>
                </blockquote>
            </div>
        </div>
    </div>
</section>
<section class="section bg-light">
    <div class="container">
        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <div class="card" style="border-top: 4px solid #b22222;">
                <h3 class="card-title">Our Vision</h3>
                <p class="card-description">
                    A united community of Muaythai Boran practitioners who passionately embody the core values of OMA,
                    empowered to contribute to a peaceful and progressive humanity.
                </p>
            </div>

            <div class="card" style="border-top: 4px solid #b22222;">
                <h3 class="card-title">Our Mission</h3>
                <ul class="card-description" style="text-align: left; padding-left: 1rem; list-style-type: disc;">
                    <li>Institutionalize the curriculum of Grandmaster Sane Tubthimtong.</li>
                    <li>Equip members with high-standard knowledge for self-sufficiency.</li>
                    <li>Solidify strong kinship among all Kru in the Philippines.</li>
                    <li>Produce well-rounded, champion-quality combative athletes.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle">Our Principles</p>
            <h2 class="section-title">Core Values & Ethical Oath</h2>
            <p class="section-description">
                To solemnly teach others and live by the sacred ethical oath of Muaythai Boran.
            </p>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3 class="card-title">Respect & Honor</h3>
                <p class="card-description">
                    I will respect everyone, especially my family, mentors, and myself, ensuring I never bring disgrace
                    to the name of Muaythai Boran.
                </p>
            </div>

            <div class="card">
                <h3 class="card-title">Loyalty & Truth</h3>
                <p class="card-description">
                    I will be loyal and true to my motherland, standing fearlessly to protect honor, truth, and justice.
                </p>
            </div>

            <div class="card">
                <h3 class="card-title">Conviction</h3>
                <p class="card-description">
                    I will live by my principles, stand for the greater good, and hold myself solely responsible for all
                    my actions.
                </p>
            </div>

            <div class="card">
                <h3 class="card-title">Self-Control</h3>
                <p class="card-description">
                    I will maintain unwavering self-discipline and self-control under any circumstance or pressure.
                </p>
            </div>

            <div class="card">
                <h3 class="card-title">Righteousness</h3>
                <p class="card-description">
                    I will use my skills and knowledge to protect and defend what is right, never using them to boast or
                    cause harm.
                </p>
            </div>

            <div class="card">
                <h3 class="card-title">The Pledge</h3>
                <p class="card-description">
                    <em>"These I pledge."</em><br>
                    Living the OMA values is a commitment to personal excellence and the service of others.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="section bg-light">
    <div class="container">
        <div class="section-header">
            <p class="section-subtitle">Get In Touch</p>
            <h2 class="section-title">Have Questions?</h2>
            <p class="section-description">
                We're here to help you begin or continue your Muayboran journey.
                Reach out to learn more about our programs.
            </p>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="pages/contact.php" class="btn btn-primary">Contact Us</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>