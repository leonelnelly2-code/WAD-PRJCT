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
    
      <!-- links to our external csss-->
    <?php 
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if ($current_page == 'index.php' || $current_page == '') {
        echo '<link rel="stylesheet" href="/navig/css/home.css">';
    } elseif ($current_page == 'register.php') {
        echo '<link rel="stylesheet" href="/navig/css/register.css">';
    } elseif ($current_page == 'login.php') {
        echo '<link rel="stylesheet" href="/navig/css/home.css">';
    } elseif ($current_page == 'find_route.php') {
        echo '<link rel="stylesheet" href="/navig/css/route.css">';
    } elseif ($current_page == 'venues.php' || $current_page == 'venue_details.php' || $current_page == 'dashboard.php' || $current_page == 'about.php' || $current_page == 'contact.php') {
        echo '<link rel="stylesheet" href="/navig/css/directory.css">';
    } else {
        echo '<link rel="stylesheet" href="/navig/css/home.css">';
    }
    ?>
    

    <style>
        .site-header { background-color: #1D3557; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif; }
        .site-header a { color: white; text-decoration: none; font-weight: bold; }
        .site-nav ul { list-style: none; display: flex; gap: 15px; margin: 0; padding: 0; }
        .site-nav a:hover { color: #F2A900; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="logo">
            <a href="/navig/index.php">NUST NAV</a>
        </div>
        <nav class="site-nav">
            <ul>
                <li><a href="/navig/index.php">Home</a></li>
                <li><a href="/navig/venues.php">Buildings</a></li>
                <li><a href="/navig/find_route.php">Find a Route</a></li>
                <li><a href="/navig/about.php">About</a></li>
                <li><a href="/navig/contact.php">Contact</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/navig/dashboard.php">Dashboard</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="/navig/admin/index.php" style="color: #4dabf7;">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="/navig/logout.php" style="color: #ff6b6b;">Logout</a></li>
                <?php else: ?>
                    <li><a href="/navig/login.php">Login</a></li>
                    <li><a href="/navig/register.php" style="background: #F2A900; color: #171717; padding: 5px 10px; border-radius: 5px;">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        