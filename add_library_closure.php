<?php
/**
 * Add Library Closure API
 * Admin marks library as closed for a specific date
 * 
 * POST: { "closed_date": "2026-01-07", "reason": "Public Holiday", "admin_id": 1 }
 */

require_once 'db.php';
require_once 'notification_helper.php';

setHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Method not allowed');
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $closedDate = $data['closed_date'] ?? null;
    $reason = $data['reason'] ?? 'Library Closed';
    $adminId = $data['admin_id'] ?? null;

    if (!$closedDate) {
        respond(false, 'closed_date is required');
    }

    // Validate date format
    $dateObj = DateTime::createFromFormat('Y-m-d', $closedDate);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $closedDate) {
        respond(false, 'Invalid date format. Use YYYY-MM-DD');
    }

    $conn = getConnection();

    // Auto-create library_closure table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS library_closure (
        id INT AUTO_INCREMENT PRIMARY KEY,
        closed_date DATE NOT NULL UNIQUE,
        reason VARCHAR(255) NOT NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_closed_date (closed_date)
    )");

    // Check if date already exists
    $checkStmt = $conn->prepare("SELECT id FROM library_closure WHERE closed_date = ?");
    $checkStmt->bind_param("s", $closedDate);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        respond(false, 'This date is already marked as closed');
    }
    $checkStmt->close();

    // Insert closure
    $stmt = $conn->prepare("INSERT INTO library_closure (closed_date, reason, created_by) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $closedDate, $reason, $adminId);

    if ($stmt->execute()) {
        // Format date for display
        $displayDate = date('d M Y', strtotime($closedDate));

        // Broadcast notification to all users
        $title = "📢 Library Closed on $displayDate";
        $message = $reason;
        $notifData = [
            'type' => 'library_closed',
            'closed_date' => $closedDate,
            'reason' => $reason,
            'sender' => 'admin'
        ];

        broadcastToAllUsers($title, $message, $notifData, $conn);

        // Immediately extend due dates for this closure date if it's today
        $today = date('Y-m-d');
        if ($closedDate === $today) {
            $extended = extendDueDatesForDate($conn, $closedDate);
        } else {
            $extended = 0;
        }

        respond(true, 'Library closure added successfully', [
            'closed_date' => $closedDate,
            'reason' => $reason,
            'notification_sent' => true,
            'due_dates_extended' => $extended
        ]);
    } else {
        respond(false, 'Failed to add closure: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Add Library Closure Error: " . $e->getMessage());
    respond(false, 'Server error: ' . $e->getMessage());
}

/**
 * Extend due dates for reservations due on a closed date
 */
function extendDueDatesForDate($conn, $closedDate)
{
    // Find all approved reservations with due_date = closedDate
    $query = $conn->prepare("
        SELECT r.id, r.user_id, r.due_date, b.title as book_title
        FROM reservations r
        JOIN books b ON r.book_id = b.id
        WHERE r.status = 'approved'
        AND r.due_date = ?
    ");
    $query->bind_param("s", $closedDate);
    $query->execute();
    $result = $query->get_result();

    $extendedCount = 0;
    $newDueDate = date('Y-m-d', strtotime($closedDate . ' +1 day'));
    $displayNewDate = date('d M Y', strtotime($newDueDate));

    while ($row = $result->fetch_assoc()) {
        // Update due_date
        $updateStmt = $conn->prepare("UPDATE reservations SET due_date = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newDueDate, $row['id']);

        if ($updateStmt->execute()) {
            $extendedCount++;

            // Notify user
            $userId = $row['user_id'];
            $bookTitle = $row['book_title'];

            $title = "📅 Due Date Extended";
            $message = "Library was closed today. Your due date for '$bookTitle' has been extended to $displayNewDate.";
            $notifData = [
                'type' => 'due_date_extended',
                'reservation_id' => strval($row['id']),
                'book_title' => $bookTitle,
                'old_due_date' => $closedDate,
                'new_due_date' => $newDueDate,
                'reason' => 'library_closed'
            ];

            sendNotificationToUser($userId, $title, $message, $notifData, $conn);
        }
        $updateStmt->close();
    }

    $query->close();
    return $extendedCount;
}
?>