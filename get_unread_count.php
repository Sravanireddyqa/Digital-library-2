<?php
/**
 * Get Unread Count API
 * Returns the count of unread notifications for a user
 */

require_once 'db.php';

setHeaders();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    respond(false, 'Method not allowed');
}

try {
    $conn = getConnection();

    // Check if notifications table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($tableCheck->num_rows == 0) {
        respond(true, 'No notifications', ['unread_count' => 0]);
    }

    // Validate user_id parameter
    if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
        respond(false, 'Missing required parameter: user_id');
    }

    $user_id = (int) $_GET['user_id'];

    // Count unread notifications
    $stmt = $conn->prepare("SELECT COUNT(*) as unread_count 
                            FROM notifications 
                            WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    respond(true, 'Unread count fetched successfully', [
        'unread_count' => (int) $row['unread_count']
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Get Unread Count Error: " . $e->getMessage());
    respond(false, 'Server error: ' . $e->getMessage());
}
?>