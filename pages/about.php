<?php
$page_title = "About Us";
include '../includes/header.php';
?>

<style>
    /* Scoped Styles for About Page */
    .about-hero {
        min-height: 400px;
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../assets/images/mt1.jpg') center / cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-radius: 12px;
        color: #fff;
        margin-bottom: 4rem;
        padding: 2rem;
    }

    .story-grid {
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 4rem; 
        align-items: center;
        margin-bottom: 5rem;
    }

    .oath-section {
        background-color: #f9f9f9;
        padding: 4rem 2rem;
        border-radius: 15px;
        margin: 4rem 0;
    }

    .icon-feature {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
    }

    .membership-highlight {
        border-left: 5px solid var(--color-primary);
        background: #fff;
        padding: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    @media (max-width: 768px) {
        .story-grid { grid-template-columns: 1fr; gap: 2rem; }
        .about-hero { min-height: 300px; }
    }
</style>

<div class="container">
    <header class="about-hero">
        <p class="section-subtitle" style="text-transform: uppercase; letter-spacing: 2px;">Our Heritage</p>
        <h1 class="hero-title" style="font-size: clamp(2rem, 5vw, 3.5rem); text-shadow: 0 4px 10px rgba(0,0,0,0.5);">
            Oriental Muayboran Academy
        </h1>
        <p class="section-description" style="max-width: 600px; font-size: 1.2rem;">
            Inheriting and promoting the authentic traditional warfare system of ancient Siam.
        </p>
    </header>

    <section class="story-grid">
        <div class="story-content">
            <h2 style="color: var(--color-primary); margin-bottom: 1.5rem; font-size: 2.5rem;">The Path of OMA</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text-light);">
                Established in <strong>2016</strong> and rooted in profound Siamese martial arts heritage since <strong>2007</strong>, OMA represents the pinnacle of traditional Thai combat.
            </p>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--color-text-light); margin-top: 1rem;">
                From <strong>La Trinidad, Benguet</strong> to our headquarters in <strong>Quezon City</strong> (2018), we carry forward the sacred teachings of <strong>Great Grandmaster Sane Tubthimtong</strong> through <strong>Ajarn Brendaley Tarnate</strong>.
            </p>
        </div>
        <div class="story-image" style="aspect-ratio: 4/5; background: url('../assets/images/rusha.png') center / cover no-repeat; border-radius: 12px; box-shadow: var(--shadow-lg);"></div>
    </section>

    <section class="card-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div class="card" style="border-top: 5px solid #b22222;">
            <h3 class="card-title">Our Vision</h3>
            <p>A united community of Muaythai Boran practitioners who passionately embody the core values of OMA, empowered to contribute to a peaceful and progressive humanity.</p>
        </div>
        <div class="card" style="border-top: 5px solid #b22222;">
            <h3 class="card-title">Our Mission</h3>
            <ul style="padding-left: 1.2rem; line-height: 1.6;">
                <li>Institutionalize the curriculum of Grandmaster Sane Tubthimtong.</li>
                <li>Equip members with high-standard knowledge for self-sufficiency.</li>
                <li>Solidify strong kinship among all Kru in the Philippines.</li>
                <li>Produce well-rounded, champion-quality combative athletes.</li>
            </ul>
        </div>
    </section>

    <section class="oath-section">
        <div class="text-center" style="margin-bottom: 3rem;">
            <p class="section-subtitle">Our Principles</p>
            <h2 class="section-title">Core Values & Ethical Oath</h2>
        </div>
        <div class="card-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div class="card"><h4>Respect & Honor</h4><p>I will respect everyone, especially my family, mentors, and myself, never bringing disgrace to the name of Muaythai Boran.</p></div>
            <div class="card"><h4>Loyalty & Truth</h4><p>I will be loyal to my motherland, standing fearlessly to protect honor, truth, and justice.</p></div>
            <div class="card"><h4>Conviction</h4><p>I will live by my principles, stand for the greater good, and hold myself responsible for my actions.</p></div>
            <div class="card"><h4>Self-Control</h4><p>I will maintain unwavering self-discipline and self-control under any circumstance.</p></div>
            <div class="card"><h4>Righteousness</h4><p>I will use my skills to protect and defend what is right, never to boast or cause harm.</p></div>
            <div class="card" style="background: var(--color-primary); color: #fff;">
                <h4>The Pledge</h4>
                <p><em>"These I pledge."</em><br>A commitment to personal excellence and the service of others.</p>
            </div>
        </div>
    </section>

    <section style="margin-bottom: 5rem;">
        <div class="text-center" style="margin-bottom: 3rem;">
            <p class="section-subtitle">Structured Progression</p>
            <h2 class="section-title">The Khan System</h2>
        </div>
        <div class="card-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div class="card text-center">
                <span class="icon-feature">📚</span>
                <h3>Constructivist Learning</h3>
                <p>Mastery through core fundamentals, elevating to complex application.</p>
            </div>
            <div class="card text-center">
                <span class="icon-feature">🥊</span>
                <h3>Nakmuay (Khan 1-10)</h3>
                <p>Ten levels for students to ensure technical proficiency before promotion.</p>
            </div>
            <div class="card text-center">
                <span class="icon-feature">🥇</span>
                <h3>Mastership (Khan 11-16)</h3>
                <p>Advanced instructor and mastership certification led by certified Kru.</p>
            </div>
        </div>
    </section>

    <section style="max-width: 900px; margin: 0 auto 5rem auto;">  
        <div class="card">
            <h3>Our Facilities</h3>
            <p>Our Quezon City academy features a full-sized ring, heavy bags, moving targets, and a safe environment conducive to deep learning.</p>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>