<?php
/**
 * Extend Due Dates - Daily Cron Job
 * Run this daily at midnight via cron job or Windows Task Scheduler
 * 
 * Windows Task: schtasks /create /tn "LibraryDueDateExtension" /tr "php C:\xampp\htdocs\digitallibrary_API\extend_due_dates.php" /sc daily /st 00:01
 * Linux Cron: 1 0 * * * php /var/www/html/digitallibrary_API/extend_due_dates.php
 */

require_once 'db.php';
require_once 'notification_helper.php';

// For CLI execution
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
}

try {
    $conn = getConnection();
    $today = date('Y-m-d');

    // Check if today is in library_closure table
    $checkClosure = $conn->prepare("SELECT id, reason FROM library_closure WHERE closed_date = ?");
    $checkClosure->bind_param("s", $today);
    $checkClosure->execute();
    $closureResult = $checkClosure->get_result();

    if ($closureResult->num_rows === 0) {
        // Today is not a closed day
        $result = [
            'success' => true,
            'message' => 'Today is not a library closure day. No action needed.',
            'date' => $today,
            'is_closed' => false,
            'due_dates_extended' => 0
        ];

        outputResult($result);
        $conn->close();
        exit;
    }

    $closureData = $closureResult->fetch_assoc();
    $closureReason = $closureData['reason'];
    $checkClosure->close();

    // Today is a closed day - extend due dates
    $newDueDate = date('Y-m-d', strtotime($today . ' +1 day'));
    $displayNewDate = date('d M Y', strtotime($newDueDate));

    // Find all approved reservations with due_date = today
    $query = $conn->query("
        SELECT r.id, r.user_id, r.due_date, b.title as book_title
        FROM reservations r
        JOIN books b ON r.book_id = b.id
        WHERE r.status = 'approved'
        AND r.due_date = '$today'
    ");

    $extendedCount = 0;
    $usersNotified = [];

    while ($row = $query->fetch_assoc()) {
        $reservationId = $row['id'];
        $userId = $row['user_id'];
        $bookTitle = $row['book_title'];

        // Update due_date
        $updateResult = $conn->query("UPDATE reservations SET due_date = '$newDueDate' WHERE id = $reservationId");

        if ($updateResult) {
            $extendedCount++;

            // Send notification to user
            $title = "📅 Due Date Extended";
            $message = "Library was closed today ($closureReason). Your due date for '$bookTitle' has been automatically extended to $displayNewDate.";
            $notifData = [
                'type' => 'due_date_extended',
                'reservation_id' => strval($reservationId),
                'book_title' => $bookTitle,
                'old_due_date' => $today,
                'new_due_date' => $newDueDate,
                'reason' => 'library_closed',
                'closure_reason' => $closureReason
            ];

            sendNotificationToUser($userId, $title, $message, $notifData, $conn);
            $usersNotified[] = $userId;

            echo "Extended due date for reservation #$reservationId (User: $userId, Book: $bookTitle)\n";
        }
    }

    $result = [
        'success' => true,
        'message' => "Library closure processed for $today",
        'date' => $today,
        'is_closed' => true,
        'closure_reason' => $closureReason,
        'due_dates_extended' => $extendedCount,
        'new_due_date' => $newDueDate,
        'users_notified' => count(array_unique($usersNotified))
    ];

    outputResult($result);
    $conn->close();

} catch (Exception $e) {
    error_log("Extend Due Dates Error: " . $e->getMessage());
    $error = ['success' => false, 'message' => $e->getMessage()];
    outputResult($error);
}

function outputResult($result)
{
    if (php_sapi_name() !== 'cli') {
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else {
        echo "\n=== Library Closure Due Date Extension ===\n";
        echo "Date: " . $result['date'] . "\n";
        echo "Is Closed: " . ($result['is_closed'] ? 'Yes' : 'No') . "\n";
        if ($result['is_closed']) {
            echo "Reason: " . ($result['closure_reason'] ?? 'N/A') . "\n";
            echo "Due Dates Extended: " . $result['due_dates_extended'] . "\n";
            echo "New Due Date: " . ($result['new_due_date'] ?? 'N/A') . "\n";
            echo "Users Notified: " . ($result['users_notified'] ?? 0) . "\n";
        }
        echo "===========================================\n";
    }
}
?>