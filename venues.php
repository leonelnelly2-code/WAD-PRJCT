<?php

require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// getall categories for dropdown filter
$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmtCat->fetchAll();

// search and filter
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "
    SELECT v.*, p.file_path AS photo 
    FROM venues v 
    LEFT JOIN photos p ON v.venue_id = p.venue_id AND p.is_primary = TRUE 
    WHERE v.is_start_point = FALSE
";

$params = [];

if (!empty($search_term)) {
    $sql .= " AND (v.name LIKE ? OR v.nickname LIKE ? OR v.short_description LIKE ?)";
    $search_wildcard = "%" . $search_term . "%";
    $params[] = $search_wildcard;
    $params[] = $search_wildcard;
    $params[] = $search_wildcard;
}

// if user selected a category
if (!empty($category_id)) {
    $sql .= " AND v.venue_id IN (SELECT venue_id FROM venue_categories WHERE category_id = ?)";
    $params[] = $category_id;
}

$sql .= " ORDER BY v.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$venues = $stmt->fetchAll();
?>

<main class="directory-page">
    <header class="directory-header">
        <h1>Explore Campus Buildings</h1>
        <p>Search for a building or filter by category.</p>
    </header>

    <!-- search and filter form -->
    <section class="search-filter-section">
        <form action="venues.php" method="GET" class="search-form">
            <div class="search-input-group">
                <input type="text" name="search" placeholder="Search by name, nickname, or description..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit" class="btn-search">🔍 Search</button>
            </div>
            
            <div class="filter-group">
                <label for="category">Filter by Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </section>

    <section class="venue-grid">
        <?php if (empty($venues)): ?>
            <div class="no-results">
                <h3>No buildings found.</h3>
                <p>Try adjusting your search terms or filters.</p>
                <a href="venues.php" class="btn-primary" style="text-decoration:none; padding: 10px 20px; border-radius: 5px;">Clear Filters</a>
            </div>
        <?php else: ?>
            <?php foreach ($venues as $venue): ?>
                <div class="venue-card">
                    <div class="venue-image">
                        <?php if ($venue['photo']): ?>
                            <img src="<?php echo htmlspecialchars($venue['photo']); ?>" alt="<?php echo htmlspecialchars($venue['name']); ?>">
                        <?php else: ?>
                            <div class="no-image">No photo available</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="venue-info">
                        <h2><?php echo htmlspecialchars($venue['name']); ?></h2>
                        <p class="venue-nickname"><?php echo htmlspecialchars($venue['nickname']); ?></p>
                        <p class="venue-desc"><?php echo htmlspecialchars($venue['short_description']); ?></p>
                        
                        <a href="venue_details.php?id=<?php echo $venue['venue_id']; ?>" class="btn-view">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>