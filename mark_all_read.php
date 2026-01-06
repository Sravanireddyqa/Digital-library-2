<?php
/**
 * Mark All Notifications as Read API
 * Marks all notifications as read for a specific user
 */

require_once 'db.php';

setHeaders();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Method not allowed');
}

try {
    $conn = getConnection();

    $input = getInput();

    // Validate required fields
    if (!isset($input['user_id']) || empty($input['user_id'])) {
        respond(false, 'Missing required field: user_id');
    }

    $user_id = (int) $input['user_id'];

    // Update all notifications for the user to mark as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $count = $stmt->affected_rows;
        respond(true, "Marked $count notifications as read", ['count' => $count]);
    } else {
        respond(false, 'Failed to mark notifications as read: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Mark All Read Error: " . $e->getMessage());
    respond(false, 'Server error: ' . $e->getMessage());
}
?>