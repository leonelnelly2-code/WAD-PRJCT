<?php
// login.php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity']);
    $password = $_POST['password'];

    if (empty($identity) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$identity]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<main class="auth-page">
    <header class="auth-header">
        <a class="home-logo" href="index.php">NUST NAV</a>
        <nav class="auth-nav" aria-label="Main navigation">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <section class="auth-card" aria-labelledby="login-title">
        <div class="auth-mark" aria-hidden="true">↗</div>
        <p class="eyebrow auth-eyebrow">GOOD TO SEE YOU AGAIN</p>
        <h1 id="login-title">Log in to your account</h1>
        <p class="auth-description">Access campus information and keep your student experience connected in one place.</p>

        <?php if ($error): ?>
            <p style="color: #ff6b6b; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 5px; font-size: 13px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form class="auth-form" action="login.php" method="POST">
            <label for="login-identity">Email or username</label>
            <input id="login-identity" name="identity" type="text" placeholder="Enter your email" required>

            <label for="login-password">Password</label>
            <input id="login-password" name="password" type="password" placeholder="Enter your password" required minlength="6">

            <div class="login-options">
                <label class="remember-option"><input type="checkbox" name="remember"> Remember me</label>
                <a href="#" id="forgot-password-link">Forgot password?</a>
            </div>
            <button class="home-login-submit" type="submit">Log In</button>
        </form>
        <p class="login-register-prompt">Don't have an account? <a href="register.php">Register here</a></p>
        <a class="back-home-link" href="index.php">← Back to homepage</a>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>