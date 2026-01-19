<?php
/**
 * Debug Category Mismatch
 * Run: http://localhost/digitallibrary_API/debug_categories.php
 */

require_once 'db.php';

echo "<h2>Category Debug Tool</h2>";

try {
    $conn = getConnection();

    // Show all unique categories in database
    echo "<h3>1. Categories in Database:</h3>";
    $result = $conn->query("SELECT category, COUNT(*) as count FROM books GROUP BY category ORDER BY count DESC");

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Category (exact)</th><th>Count</th><th>First Few Chars Hex</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $cat = $row['category'];
        $hex = bin2hex(substr($cat, 0, 10));
        echo "<tr><td>" . htmlspecialchars($cat) . "</td><td>" . $row['count'] . "</td><td>$hex</td></tr>";
    }
    echo "</table>";

    // Test category filter
    echo "<h3>2. Test Category Filter:</h3>";
    $testCategories = ['Fiction', 'fiction', 'FICTION', 'Fantasy', 'Science', 'Programming'];

    foreach ($testCategories as $cat) {
        // Exact match
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM books WHERE category = ?");
        $stmt->bind_param("s", $cat);
        $stmt->execute();
        $exactCount = $stmt->get_result()->fetch_assoc()['cnt'];

        // Case-insensitive match
        $stmt2 = $conn->prepare("SELECT COUNT(*) as cnt FROM books WHERE LOWER(category) = LOWER(?)");
        $stmt2->bind_param("s", $cat);
        $stmt2->execute();
        $caseInsensitiveCount = $stmt2->get_result()->fetch_assoc()['cnt'];

        $match = $exactCount == $caseInsensitiveCount ? "✅" : "❌ MISMATCH!";
        echo "Testing '$cat': Exact=$exactCount, Case-insensitive=$caseInsensitiveCount $match<br>";
    }

    // Check get_books.php version
    echo "<h3>3. Check get_books.php:</h3>";
    $getBooksContent = file_get_contents(__DIR__ . '/get_books.php');
    if (strpos($getBooksContent, 'LOWER(category) = LOWER(?)') !== false) {
        echo "✅ get_books.php has case-insensitive matching";
    } else {
        echo "❌ get_books.php is OLD VERSION - needs to be updated!";
        echo "<br><b>Solution:</b> Copy the updated get_books.php from your Android project to this folder.";
    }

    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>