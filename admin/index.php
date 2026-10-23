<?php
require_once '../includes/db_connect.php';
require_once '../includes/admin_auth.php';

$venueCount = $pdo->query("SELECT COUNT(*) FROM venues")->fetchColumn();
$routeCount = $pdo->query("SELECT COUNT(*) FROM routes")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - NUST Nav</title>
    <link rel="stylesheet" href="/navig/css/admin.css">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="manage_venues.php">Manage Venues</a></li>
                <li><a href="manage_routes.php">Manage Routes</a></li>
                <li><a href="../index.php">← Back to Website</a></li>
                <li><a href="../logout.php" style="color: #ff6b6b;">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            
            <div class="table-container" style="padding: 30px; text-align: center;">
                <h3 style="color: #666; margin-bottom: 10px;">Total Venues</h3>
                <p style="font-size: 36px; font-weight: bold; color: #1D3557;"><?php echo $venueCount; ?></p>
                <a href="manage_venues.php" style="color: #F2A900; text-decoration: none; font-weight: bold;">Manage Venues →</a>
            </div>

            <div class="table-container" style="padding: 30px; text-align: center;">
                <h3 style="color: #666; margin-bottom: 10px;">Total Routes</h3>
                <p style="font-size: 36px; font-weight: bold; color: #1D3557;"><?php echo $routeCount; ?></p>
                <a href="manage_routes.php" style="color: #F2A900; text-decoration: none; font-weight: bold;">Manage Routes →</a>
            </div>

            <div class="table-container" style="padding: 30px; text-align: center;">
                <h3 style="color: #666; margin-bottom: 10px;">Registered Users</h3>
                <p style="font-size: 36px; font-weight: bold; color: #1D3557;"><?php echo $userCount; ?></p>
            </div>

        </div>
    </main>
</div>

</body>
</html>