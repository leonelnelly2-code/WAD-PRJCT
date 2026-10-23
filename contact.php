<?php

require_once 'includes/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';

$message_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {

        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

        $stmt = $pdo->prepare("INSERT INTO reports (user_id, message) VALUES (?, ?)");
        if ($stmt->execute([$user_id, "From: $name ($email) - $message"])) {
            $message_status = "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>Thank you! Your message has been sent to the administration.</div>";
        } else {
            $message_status = "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>Sorry, there was an error sending your message.</div>";
        }
    } else {
        $message_status = "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>Please fill in all fields.</div>";
    }
}
?>

<main class="info-page">
    <section class="info-header">
        <h1>Contact Us</h1>
        <p>Have a question, found a wrong route, or need to report a missing building? Let us know.</p>
    </section>

    <section class="contact-grid">
        <div class="info-card">
            <h2>Get in Touch</h2>
            <p>If you notice that a route is blocked, a building has moved, or you just want to give us feedback, fill out the form.</p>
            
            <div style="margin-top: 20px;">
                <p><strong>📍 Address:</strong><br>13 Jackson Kaujeua Street, Windhoek, Namibia</p>
                <p><strong>✉️ Email:</strong><br>support@nustnav.na</p>
                <p><strong>📞 Phone:</strong><br>+264 61 207 2052</p>
            </div>
        </div>

        <div class="info-card">
            <h2>Send a Message</h2>
            
            <?php echo $message_status; ?>

            <form action="contact.php" method="POST" class="contact-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Your name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Your email">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required placeholder="How can we help you?"></textarea>
                </div>

                <button type="submit" class="btn-primary">Send Message</button>
            </form>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>