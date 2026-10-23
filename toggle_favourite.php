<?php
require_once 'includes/db_connect.php';
session_start();

// Make sure user is logged in and venue ID is provided
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $venue_id = $_GET['id'];

    // Check if it already exists
    $stmt = $pdo->prepare("SELECT 1 FROM favourites WHERE user_id = ? AND venue_id = ?");
    $stmt->execute([$user_id, $venue_id]);
    
    if ($stmt->fetchColumn()) {
        // It exists, so remove it
        $stmtDelete = $pdo->prepare("DELETE FROM favourites WHERE user_id = ? AND venue_id = ?");
        $stmtDelete->execute([$user_id, $venue_id]);
    } else {
        // It doesn't exist, so add it
        $stmtInsert = $pdo->prepare("INSERT INTO favourites (user_id, venue_id) VALUES (?, ?)");
        $stmtInsert->execute([$user_id, $venue_id]);
    }
}

// Send them back to the details page they were on
header("Location: venue_details.php?id=" . $venue_id);
exit;
?>