<?php

require_once '../includes/db_connect.php';
require_once '../includes/admin_auth.php';

$error = '';
$success = '';

//DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM routes WHERE route_id = ?");
    if ($stmt->execute([$delete_id])) {
        $success = "Route deleted successfully (along with its steps).";
    } else {
        $error = "Failed to delete route.";
    }
}

// ADD ROUTE & STEPS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
    $start_id = $_POST['start_venue_id'];
    $end_id = $_POST['end_venue_id'];
    $time = (int)$_POST['estimated_minutes'];
    $steps_text = trim($_POST['steps']);

    if ($start_id == $end_id) {
        $error = "Start point and destination cannot be the same.";
    } elseif (empty($steps_text)) {
        $error = "You must provide at least one step.";
    } else {
        try {
    
            $pdo->beginTransaction();

            // insert Route
            $stmt = $pdo->prepare("INSERT INTO routes (start_venue_id, end_venue_id, estimated_minutes, is_bidirectional) VALUES (?, ?, ?, TRUE)");
            $stmt->execute([$start_id, $end_id, $time]);
            $new_route_id = $pdo->lastInsertId();

         
            $steps_array = explode("\n", $steps_text);
            $step_order = 1;

            $stmtStep = $pdo->prepare("INSERT INTO route_steps (route_id, step_order, instruction) VALUES (?, ?, ?)");
            
            foreach ($steps_array as $line) {
                $instruction = trim($line);
                if (!empty($instruction)) { // Skip empty lines
                    $stmtStep->execute([$new_route_id, $step_order, $instruction]);
                    $step_order++;
                }
            }

            $pdo->commit();
            $success = "Route and steps added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to add route: " . $e->getMessage();
        }
    }
}

//get routes by Venue names
$routesQuery = "
    SELECT r.*, 
           v1.name AS start_name, v1.nickname AS start_nick,
           v2.name AS end_name, v2.nickname AS end_nick
    FROM routes r
    JOIN venues v1 ON r.start_venue_id = v1.venue_id
    JOIN venues v2 ON r.end_venue_id = v2.venue_id
    ORDER BY r.route_id DESC
";
$routes = $pdo->query($routesQuery)->fetchAll();

//get all venues
$venues = $pdo->query("SELECT * FROM venues ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Routes - Admin</title>
    <link rel="stylesheet" href="/navig/css/admin.css">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="manage_venues.php">Manage Venues</a></li>
                <li><a href="manage_routes.php" class="active">Manage Routes</a></li>
                <li><a href="../index.php">← Back to Website</a></li>
                <li><a href="../logout.php" style="color: #ff6b6b;">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <header class="admin-header">
            <h1>Manage Routes</h1>
        </header>

        <?php if ($error): ?>
            <div class="alert error" style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success" style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;"><?php echo $success; ?></div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            
            <div class="table-container" style="flex: 2; min-width: 500px; overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Start Point</th>
                            <th>Destination</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $r): ?>
                            <tr>
                                <td>#<?php echo $r['route_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($r['start_name']); ?></strong><br><small style="color:#666;"><?php echo htmlspecialchars($r['start_nick']); ?></small></td>
                                <td><strong><?php echo htmlspecialchars($r['end_name']); ?></strong><br><small style="color:#666;"><?php echo htmlspecialchars($r['end_nick']); ?></small></td>
                                <td><?php echo $r['estimated_minutes']; ?> min</td>
                                <td class="actions">
                                    <a href="manage_routes.php?delete_id=<?php echo $r['route_id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Delete this route and all its steps?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container" style="flex: 1; min-width: 300px; padding: 20px;">
                <h2 style="color: #1D3557; margin-bottom: 20px;">Add New Route</h2>
                <form action="manage_routes.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                    
                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Start Point *</label>
                        <select name="start_venue_id" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="">-- Select Venue --</option>
                            <?php foreach ($venues as $v): ?>
                                <option value="<?php echo $v['venue_id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Destination *</label>
                        <select name="end_venue_id" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="">-- Select Venue --</option>
                            <?php foreach ($venues as $v): ?>
                                <option value="<?php echo $v['venue_id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Estimated Minutes *</label>
                        <input type="number" name="estimated_minutes" required min="1" value="5" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>

                    <div>
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Step-by-Step Directions *</label>
                        <small style="display:block; margin-bottom: 5px; color:#666;">Put each step on a new line.</small>
                        <textarea name="steps" required rows="8" placeholder="Enter the main gate.&#10;Walk straight past the Admin Block.&#10;Turn left at the yellow benches." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <input type="hidden" name="add_route" value="1">
                    <button type="submit" class="btn-primary" style="margin-top: 10px; cursor: pointer;">Save Route</button>
                </form>
            </div>

        </div>
    </main>
</div>

</body>
</html>