<?php
require_once 'includes/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/header.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: venues.php");
    exit;
}

$venue_id = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT v.*, p.file_path AS photo 
    FROM venues v 
    LEFT JOIN photos p ON v.venue_id = p.venue_id AND p.is_primary = TRUE 
    WHERE v.venue_id = ?
");
$stmt->execute([$venue_id]);
$venue = $stmt->fetch();

if (!$venue) {
    header("Location: venues.php");
    exit;
}

$stmtCat = $pdo->prepare("
    SELECT c.name 
    FROM categories c 
    JOIN venue_categories vc ON c.category_id = vc.category_id 
    WHERE vc.venue_id = ?
");
$stmtCat->execute([$venue_id]);
$categories = $stmtCat->fetchAll();
?>

<main class="details-page">
    <div class="back-link-container">
        <a href="venues.php" class="back-link">← Back to Directory</a>
    </div>

    <section class="details-card">
        <!-- image section -->
        <div class="details-image">
            <?php if ($venue['photo']): ?>
                <img src="<?php echo htmlspecialchars($venue['photo']); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>">
            <?php else: ?>
                <div class="no-image">No photo available</div>
            <?php endif; ?>
        </div>

        <!-- info section -->
        <div class="details-info">
            <h1><?php echo htmlspecialchars($venue['name']); ?></h1>
            <h2 class="details-nickname"><?php echo htmlspecialchars($venue['nickname']); ?></h2>
            
            <div class="categories">
                <?php foreach ($categories as $cat): ?>
                    <span class="category-badge"><?php echo htmlspecialchars($cat['name']); ?></span>
                <?php endforeach; ?>
            </div>

            <p class="details-description"><?php echo htmlspecialchars($venue['short_description']); ?></p>

            <div class="details-landmark">
                <strong>How to spot it:</strong>
                <p><?php echo htmlspecialchars($venue['landmark_description']); ?></p>
            </div>

            <?php if ($venue['is_start_point']): ?>
                <div class="gate-notice">
                    🚪 This is a campus gate. It is a valid starting point for navigation.
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php

                $stmtFavCheck = $pdo->prepare("SELECT 1 FROM favourites WHERE user_id = ? AND venue_id = ?");
                $stmtFavCheck->execute([$_SESSION['user_id'], $venue_id]);
                $isFavourited = $stmtFavCheck->fetchColumn();
                ?>
                
                <?php if ($isFavourited): ?>
                    <a href="toggle_favourite.php?id=<?php echo $venue_id; ?>" class="btn-secondary" style="background: #ff6b6b; color: white; display: block; text-align: center; padding: 12px; border-radius: 5px; text-decoration: none; margin-bottom: 15px; font-weight: bold;">❤️ Remove from Favourites</a>
                <?php else: ?>
                    <a href="toggle_favourite.php?id=<?php echo $venue_id; ?>" class="btn-secondary" style="background: #eee; color: #333; display: block; text-align: center; padding: 12px; border-radius: 5px; text-decoration: none; margin-bottom: 15px; font-weight: bold; border: 1px solid #ccc;">🤍 Save to Favourites</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align: center; margin-bottom: 15px; font-size: 14px; color: #666;">
                    <a href="login.php" style="color: #1D3557; font-weight: bold;">Log in</a> to save this building.
                </p>
            <?php endif; ?>

            <a href="find_route.php" class="btn-primary">Get Directions Here</a>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>