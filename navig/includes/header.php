<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Navigation - NUST</title>
        <!-- Link to our external CSS -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
        <link rel="stylesheet" href="css/home.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) == 'register.php'): ?>
        <link rel="stylesheet" href="css/register.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) == 'login.php'): ?>
        <link rel="stylesheet" href="css/login.css">
    <?php endif; ?>
</head>
<body>
    <header>
        <div class="logo">
            <h1>NUST Nav</h1>
        </div>
        <nav>
            <ul>
                <li><a href="/navig/index.php">Home</a></li>
                <li><a href="/navig/venues.php">Buildings</a></li>
                <li><a href="/navig/find_route.php">Find a Route</a></li>
                <li><a href="/navig/about.php">About</a></li>
                <li><a href="/navig/contact.php">Contact</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- show if logged in -->
                    <li><a href="/navig/dashboard.php">Dashboard</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="/navig/admin/index.php" class="admin-link">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="/navig/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <!-- show if not logged in -->
                    <li><a href="/navig/login.php">Login</a></li>
                    <li><a href="/navig/register.php" class="btn-register">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>

    