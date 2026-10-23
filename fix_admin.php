<?php
require_once 'includes/db_connect.php';

// The email of the admin account
$email = 'admin@nust.na';
// The new password we want to use
$new_password = 'password123';

// Create the secure hash
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the database
$stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE email = ?");
if ($stmt->execute([$hashed_password, $email])) {
    echo "<h1>Success!</h1>";
    echo "<p>The admin account has been reset.</p>";
    echo "<p>Email: <strong>$email</strong></p>";
    echo "<p>Password: <strong>$new_password</strong></p>";
    echo "<a href='login.php'>Go to Login Page</a>";
} else {
    echo "Something went wrong.";
}
?>