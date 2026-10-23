<?php
// dashboard.php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch user's favourite venues (We will add the ability to save favourites later)
$stmtFav = $pdo->prepare("
    SELECT v.* 
    FROM favourites f 
    JOIN venues v ON f.venue_id = v.venue_id 
    WHERE f.user_id = ?
");
$stmtFav->execute([$user_id]);
$favourites = $stmtFav->fetchAll();
?>

<main class="dashboard-page">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
        <p>Logged in.</p>
        
        <?php if ($user['role'] === 'admin'): ?>
            <a href="admin/index.php" class="btn-primary" style="background: #4dabf7; color: white; margin-top: 15px; display: inline-block;">Go to Admin Panel</a>
        <?php endif; ?>
    </div>

    <section class="dashboard-content">
        <div class="dashboard-section">
            <h2>Your Favourite Buildings</h2>
            <?php if (count($favourites) > 0): ?>
                <ul>
                    <?php foreach ($favourites as $fav): ?>
                        <li>
                            <a href="venue_details.php?id=<?php echo $fav['venue_id']; ?>">
                                <?php echo htmlspecialchars($fav['name']); ?> (<?php echo htmlspecialchars($fav['nickname']); ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You haven't saved any favourites yet. <a href="venues.php">Browse buildings</a> and save them!</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-section">
            <h2>Quick Links</h2>
            <ul class="quick-links">
                <li><a href="find_route.php">📍 Find a Route</a></li>
                <li><a href="venues.php">🏢 Browse All Buildings</a></li>
                <li><a href="contact.php">✉️ Contact Support</a></li>
            </ul>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>