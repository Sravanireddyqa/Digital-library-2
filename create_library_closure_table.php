<?php
/**
 * Create Library Closure Table
 * One-time setup script
 */

require_once 'db.php';

setHeaders();

try {
    $conn = getConnection();

    // Create library_closure table
    $sql = "CREATE TABLE IF NOT EXISTS library_closure (
        id INT AUTO_INCREMENT PRIMARY KEY,
        closed_date DATE NOT NULL UNIQUE,
        reason VARCHAR(255) NOT NULL,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_closed_date (closed_date)
    )";

    if ($conn->query($sql)) {
        respond(true, 'library_closure table created successfully');
    } else {
        respond(false, 'Failed to create table: ' . $conn->error);
    }

    $conn->close();

} catch (Exception $e) {
    respond(false, 'Error: ' . $e->getMessage());
}
?>