<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';
?>

<main class="info-page">
    <section class="info-header">
        <h1>About NUST Nav</h1>
        <p>Your digital guide to navigating the Namibia University of Science and Technology campus.</p>
    </section>

    <section class="info-content">
        <div class="info-card">
            <h2>Our Mission</h2>
            <p>Starting university can be overwhelming. Between finding lecture halls, locating the library, and discovering where to grab a bite, first-year students often struggle to find their way around campus.</p>
            <p><strong>NUST Nav</strong> was created to solve this problem. Instead of relying on confusing campus maps or asking strangers for directions, our web application provides clear, step-by-step walking routes from the campus gates directly to your destination.</p>
        </div>

        <div class="info-card">
            <h2>How It Works</h2>
            <ul class="info-list">
                <li><strong>Select Your Start Point:</strong> Choose which campus gate you are entering from.</li>
                <li><strong>Choose Your Destination:</strong> Select the building or facility you need to find.</li>
                <li><strong>Follow the Steps:</strong> Our system retrieves pre-verified, student-written directions from our database to guide you safely to your destination.</li>
            </ul>
        </div>

        <div class="info-card">
            <h2>Project Background</h2>
            <p>This application was developed as part of the <strong>Web Application Development (WAD621S)</strong> group assignment at NUST. It demonstrates the use of HTML, CSS, JavaScript, PHP, and MySQL to create a dynamic, database-driven web application.</p>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>