<?php
// register.php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$message = '';
$message_class = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];

    // 1. Server-side validation
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_class = "error";
    } else {
        // 2. Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $message = "This email is already registered.";
            $message_class = "error";
        } else {
            // 3. Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insert into database (Role is 'student' by default)
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'student')");
            if ($stmt->execute([$full_name, $email, $hashed_password])) {
                $message = "Account created successfully! You can now <a href='login.php'>log in</a>.";
                $message_class = "success";
            } else {
                $message = "Something went wrong. Please try again.";
                $message_class = "error";
            }
        }
    }
}
?>

<main class="registration-page">
    <header class="registration-header">
        <a class="logo" href="index.php" style="text-decoration:none;">NUST NAV</a>
        <nav aria-label="Main navigation">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="venues.php">Buildings</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section class="registration-card" aria-labelledby="registration-title">
        <h1 id="registration-title">Create an Account</h1>
        <p class="registration-intro">Register below to get started.</p>

        <?php if ($message): ?>
            <div style="padding: 10px; margin-bottom: 15px; border-radius: 5px; 
                <?php echo $message_class === 'success' ? 'background: rgba(42, 150, 75, 0.22); border: 1px solid #73d69a;' : 'background: rgba(180, 35, 24, 0.22); border: 1px solid #ff8a80;'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form class="registration-form" action="register.php" method="POST">
            <label for="fullName">Full name</label>
            <input id="fullName" name="fullName" type="text" placeholder="Enter your full name" required>

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" placeholder="Enter your email address" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" minlength="8" placeholder="At least 8 characters" required>

            <label for="confirmPassword">Confirm password</label>
            <input id="confirmPassword" name="confirmPassword" type="password" minlength="8" placeholder="Enter your password again" required>

            <label class="terms">
                <input id="terms" name="terms" type="checkbox" required>
                <span>I agree to the terms and conditions.</span>
            </label>

            <button class="registration-submit" type="submit">Create Account</button>
            <p class="registration-login">Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>