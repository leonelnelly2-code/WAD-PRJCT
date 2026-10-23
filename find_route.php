<?php

require_once 'includes/db_connect.php';
require_once 'includes/header.php';


$stmtStart = $pdo->query("SELECT * FROM venues WHERE is_start_point = TRUE ORDER BY name ASC");
$start_points = $stmtStart->fetchAll();

$stmtDest = $pdo->query("SELECT * FROM venues WHERE is_start_point = FALSE ORDER BY name ASC");
$destinations = $stmtDest->fetchAll();

$route_steps = [];
$error_msg = '';


// function to reverse directions
function reverse_instruction($text) {
    $replacements = [
        'left' => 'right',
        'right' => 'left',
        'up' => 'down',
        'down' => 'up',
        'enter' => 'exit',
        'exit' => 'enter',
        'past' => 'before',
        'before' => 'past',
        'opposite' => 'opposite',
        'next to' => 'next to'
    ];

    foreach ($replacements as $search => $replace) {
        $text = preg_replace('/\b' . $search . '\b/i', $replace, $text);
    }

    $text = str_ireplace('from the', 'to the', $text);

    $text = ucfirst($text);

    return $text;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_id = $_POST['start_point'];
    $end_id = $_POST['destination'];

    if ($start_id == $end_id) {
        $error_msg = "Start point and destination cannot be the same.";
    } else {

        $stmt = $pdo->prepare("
            SELECT r.route_id, r.start_venue_id, r.estimated_minutes 
            FROM routes r 
            WHERE r.start_venue_id = ? AND r.end_venue_id = ?
        ");
        $stmt->execute([$start_id, $end_id]);
        $route = $stmt->fetch();

        if ($route) {
   
            $stmtSteps = $pdo->prepare("SELECT instruction FROM route_steps WHERE route_id = ? ORDER BY step_order ASC");
            $stmtSteps->execute([$route['route_id']]);
            $route_steps = $stmtSteps->fetchAll();

            if ($route['start_venue_id'] == $start_id) {
               
            } else {
              
                $route_steps = array_reverse($route_steps);
               
                foreach ($route_steps as &$step) {
                    $step['instruction'] = reverse_instruction($step['instruction']);
                }
                unset($step);
            }
            
        } else {
            $error_msg = "No direct route found between these two locations. Try choosing a gate as your start point, or a building as your destination.";
        }
    }
}
?>

<main class="route-page">
    <header class="route-header">
        <h1>Find Your Way</h1>
        <p>Select where you are and where you want to go to get step-by-step directions.</p>
    </header>

    <section class="route-form-card">
        <form action="find_route.php" method="POST">
            <div class="form-group">
                <label for="start_point">Where are you now?</label>
                <select name="start_point" id="start_point" required>
                    <option value="">-- Select Start Point --</option>
                    <?php foreach ($start_points as $sp): ?>
                        <option value="<?php echo $sp['venue_id']; ?>" <?php echo (isset($_POST['start_point']) && $_POST['start_point'] == $sp['venue_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sp['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="destination">Where do you want to go?</label>
                <select name="destination" id="destination" required>
                    <option value="">-- Select Destination --</option>
                    <?php foreach ($destinations as $dest): ?>
                        <option value="<?php echo $dest['venue_id']; ?>" <?php echo (isset($_POST['destination']) && $_POST['destination'] == $dest['venue_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dest['name']); ?> (<?php echo htmlspecialchars($dest['nickname']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-primary">Get Directions</button>
        </form>
    </section>

    <!-- Output Section -->
    <?php if ($error_msg): ?>
        <div class="alert error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($route_steps)): ?>
        <section class="directions-result">
            <h2>Your Directions</h2>
            <p class="route-time">Estimated walking time: <strong><?php echo $route['estimated_minutes']; ?> minutes</strong></p>
            
            <ol class="step-list">
                <?php foreach ($route_steps as $index => $step): ?>
                    <li>
                        <span class="step-number"><?php echo $index + 1; ?></span>
                        <p><?php echo htmlspecialchars($step['instruction']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>