<?php
require_once '../includes/db_connect.php';
require_once '../includes/admin_auth.php';

$error = '';
$success = '';

// DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM venues WHERE venue_id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "Venue deleted successfully.";
    } else {
        $error = "Failed to delete venue.";
    }
}

// ADD VENUE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_venue'])) {
    $name = trim($_POST['name']);
    $nickname = trim($_POST['nickname']);
    $desc = trim($_POST['description']);
    $landmark = trim($_POST['landmark']);
    $is_start = isset($_POST['is_start_point']) ? 1 : 0;

    if (empty($name) || empty($desc) || empty($landmark)) {
        $error = "Name, Description, and Landmark are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO venues (name, nickname, short_description, landmark_description, is_start_point) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $nickname, $desc, $landmark, $is_start])) {
            $success = "New venue added successfully!";
        } else {
            $error = "Failed to add venue.";
        }
    }
}

// 3. GET ALL VENUES
$stmt = $pdo->query("SELECT * FROM venues ORDER BY name ASC");
$venues = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Venues - Admin</title>
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
            <h1>Manage Venues</h1>
        </header>

        <?php if ($error): ?>
            <div class="alert error" style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success" style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            <div class="table-container" style="flex: 2; min-width: 500px;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Nickname</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($venues as $v): ?>
                            <tr>
                                <td>#<?php echo $v['venue_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($v['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($v['nickname']); ?></td>
                                <td>
                                    <?php if ($v['is_start_point']): ?>
                                        <span class="badge gate">Gate</span>
                                    <?php else: ?>
                                        <span class="badge building">Building</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="manage_venues.php?delete_id=<?php echo $v['venue_id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this venue?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container" style="flex: 1; min-width: 300px; padding: 20px;">
                <h2 style="color: #1D3557; margin-bottom: 20px;">Add New Venue</h2>
                <form action="manage_venues.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                    
                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Venue Name *</label>
                        <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    
                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Nickname</label>
                        <input type="text" name="nickname" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Short Description *</label>
                        <input type="text" name="description" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Landmark Description *</label>
                        <input type="text" name="landmark" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: bold;">
                            <input type="checkbox" name="is_start_point"> Is this a Gate/Start Point?
                        </label>
                    </div>

                    <input type="hidden" name="add_venue" value="1">
                    <button type="submit" class="btn-primary" style="margin-top: 10px; cursor: pointer;">Save Venue</button>
                </form>
            </div>

        </div>
    </main>
</div>

</body>
</html>