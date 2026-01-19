<?php
/**
 * Lookup Reservation by QR Code
 * Called when admin scans a reservation QR code
 * 
 * QR Format: LibraryAI|RES-XXX-YYYY|BookTitle
 * 
 * Returns reservation details + current status
 */

require_once 'db.php';

setHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Method not allowed');
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['qr_code'])) {
        respond(false, 'QR code is required');
    }

    $qrCode = trim($data['qr_code']);

    // Parse QR code: LibraryAI|RES-XXX-YYYY|BookTitle
    $parts = explode('|', $qrCode);

    if (count($parts) < 2) {
        respond(false, 'Invalid QR code format');
    }

    // Check if it's a reservation QR
    $reservationId = null;
    foreach ($parts as $part) {
        if (strpos($part, 'RES-') === 0) {
            // Extract reservation number from RES-XXX-YYYY format
            // We'll search by this pattern
            $reservationId = $part;
            break;
        }
    }

    if (!$reservationId) {
        respond(false, 'Not a reservation QR code', ['is_book_qr' => true]);
    }

    $conn = getConnection();

    // Find reservation by the reservation reference or by matching pattern
    // First try to find by reference if we have a reference column
    $sql = "SELECT r.*, 
                   b.title as book_title, b.author as book_author, b.cover_url,
                   u.name as user_name, u.email as user_email, u.phone as user_phone,
                   l.name as library_name
            FROM reservations r
            LEFT JOIN books b ON r.book_id = b.id
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN libraries l ON r.library_id = l.id
            WHERE r.id = ? OR r.id LIKE ?
            ORDER BY r.created_at DESC
            LIMIT 1";

    // Extract the number from RES-XXX-YYYY
    $resNumber = preg_replace('/[^0-9]/', '', $reservationId);
    $searchPattern = '%' . $resNumber . '%';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $resNumber, $searchPattern);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Try searching by book title if included in QR
        $bookTitle = isset($parts[2]) ? trim($parts[2]) : null;
        if ($bookTitle) {
            $sql2 = "SELECT r.*, 
                           b.title as book_title, b.author as book_author, b.cover_url,
                           u.name as user_name, u.email as user_email, u.phone as user_phone,
                           l.name as library_name
                    FROM reservations r
                    LEFT JOIN books b ON r.book_id = b.id
                    LEFT JOIN users u ON r.user_id = u.id
                    LEFT JOIN libraries l ON r.library_id = l.id
                    WHERE b.title LIKE ?
                    ORDER BY r.created_at DESC
                    LIMIT 1";
            $titlePattern = '%' . $bookTitle . '%';
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("s", $titlePattern);
            $stmt2->execute();
            $result = $stmt2->get_result();
        }
    }

    if ($result->num_rows === 0) {
        respond(false, 'Reservation not found');
    }

    $reservation = $result->fetch_assoc();

    // Determine what actions are available based on status
    $actions = [];
    switch (strtolower($reservation['status'])) {
        case 'pending':
            $actions = ['approve', 'reject'];
            break;
        case 'approved':
            $actions = ['pickup']; // Mark as picked up
            break;
        case 'picked_up':
        case 'active':
            $actions = ['return']; // Mark as returned
            break;
        case 'returned':
        case 'completed':
            $actions = []; // No actions, completed
            break;
        case 'rejected':
        case 'cancelled':
            $actions = []; // No actions
            break;
        default:
            $actions = [];
    }

    // Get pickup date from either pickup_date or reservation_date column
    $pickupDate = $reservation['pickup_date'] ?? $reservation['reservation_date'] ?? null;

    respond(true, 'Reservation found', [
        'reservation' => [
            'id' => (int) $reservation['id'],
            'status' => $reservation['status'],
            'book_id' => (int) $reservation['book_id'],
            'book_title' => $reservation['book_title'],
            'book_author' => $reservation['book_author'],
            'book_cover' => $reservation['cover_url'],
            'user_id' => (int) $reservation['user_id'],
            'user_name' => $reservation['user_name'],
            'user_email' => $reservation['user_email'],
            'user_phone' => $reservation['user_phone'],
            'library_name' => $reservation['library_name'],
            'pickup_date' => $pickupDate,
            'time_slot' => $reservation['time_slot'] ?? 'N/A',
            'due_date' => $reservation['due_date'] ?? null,
            'created_at' => $reservation['created_at']
        ],
        'available_actions' => $actions,
        'is_reservation' => true
    ]);

    $conn->close();

} catch (Exception $e) {
    error_log("Reservation Lookup Error: " . $e->getMessage());
    respond(false, 'Server error: ' . $e->getMessage());
}
?>