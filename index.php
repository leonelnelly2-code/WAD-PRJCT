<?php

require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['home_login'])) {
    $identity = trim($_POST['identity']);
    $password = $_POST['password'];

    if (empty($identity) || empty($password)) {
        $login_error = "Please enter both email and password.";
    } else {
        // Check if user exists by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$identity]);
        $user = $stmt->fetch();


        if ($user && ($password === $user['password_hash'] || password_verify($password, $user['password_hash']))) {
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
            $login_error = "Invalid email or password.";
        }
    }
}
?>

<main>
    <div class="main">

        <section class="home-content">
            <div class="home-copy">
                <p class="eyebrow">YOUR CAMPUS. YOUR COMMUNITY.</p>
                <h1>Make campus life <span>easier.</span></h1>
                <p class="home-description">
                    Campus Connect is a student-friendly online hub designed to help NUST students find
                    useful campus information in one convenient place. Discover campus locations, explore
                    student resources, and connect with the information you need to make everyday campus
                    life simpler, more organised, and more enjoyable.
                </p>
                <div class="home-actions">
                    <a class="primary-action" href="find_route.php">Find a Route</a>
                    <a class="secondary-action" href="register.php">Create an Account</a>
                </div>
                <div class="home-features" aria-label="Website features">
                    <span>Campus information</span>
                    <span>Helpful resources</span>
                    <span>Student-focused</span>
                </div>
            </div>

            <!-- logiin form -->
            <aside class="home-login-card" aria-labelledby="home-login-title">
                <div class="login-icon" aria-hidden="true">↗</div>
                <h2 id="home-login-title">Welcome back</h2>
                <p class="login-subtitle">Log in to continue to Campus Connect.</p>
                
                <?php if ($login_error): ?>
                    <p style="color: #ff6b6b; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 5px; font-size: 13px;"><?php echo $login_error; ?></p>
                <?php endif; ?>

                <form class="home-login-form" action="index.php" method="POST">
                    <label for="home-identity">Email or username</label>
                    <input id="home-identity" name="identity" type="text" placeholder="Enter your email" required>

                    <label for="home-password">Password</label>
                    <input id="home-password" name="password" type="password" placeholder="Enter your password" required>

                    <div class="login-options">
                        <label class="remember-option"><input type="checkbox" name="remember"> Remember me</label>
                        <a href="login.php">Go to full login page?</a>
                    </div>
                   
                    <input type="hidden" name="home_login" value="1">
                    <button class="home-login-submit" type="submit">Log In</button>
                </form>
                <p class="login-register-prompt">New to Campus Connect? <a href="register.php">Create an account</a></p>
            </aside>
        </section>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>