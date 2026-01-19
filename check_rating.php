<?php
/**
 * Check if user has already rated a book
 * GET: check_rating.php?user_id=1&book_id=1
 */

require_once 'db.php';

setHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    respond(false, 'Use GET method');
}

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$bookId = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;

if ($userId == 0 || $bookId == 0) {
    respond(false, 'user_id and book_id required');
}

try {
    $conn = getConnection();

    // Check if rating exists
    $stmt = $conn->prepare("SELECT id, rating, review, created_at FROM ratings WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $userId, $bookId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $rating = $result->fetch_assoc();
        respond(true, 'Rating found', [
            'already_rated' => true,
            'rating' => (int) $rating['rating'],
            'review' => $rating['review'],
            'rated_at' => $rating['created_at']
        ]);
    } else {
        respond(true, 'Not rated yet', [
            'already_rated' => false
        ]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Check Rating Error: " . $e->getMessage());
    respond(false, 'Server error');
}
?>