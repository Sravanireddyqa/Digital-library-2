<?php
/**
 * Update ALL Books with Publisher, Published Date, and Pages
 * Run: http://localhost/digitallibrary_API/fix_all_books.php
 */

require_once 'db.php';

try {
    $conn = getConnection();

    // Ensure columns exist
    $conn->query("ALTER TABLE books ADD COLUMN IF NOT EXISTS publisher VARCHAR(255) DEFAULT NULL");
    $conn->query("ALTER TABLE books ADD COLUMN IF NOT EXISTS published_date VARCHAR(50) DEFAULT NULL");
    $conn->query("ALTER TABLE books ADD COLUMN IF NOT EXISTS pages INT DEFAULT 0");

    // Get all books
    $result = $conn->query("SELECT id, title, author, category FROM books");

    $updatedCount = 0;

    while ($book = $result->fetch_assoc()) {
        $id = $book['id'];
        $title = $book['title'];
        $category = $book['category'] ?? 'General';

        // Generate publisher based on category
        $publishers = [
            'Fiction' => ['Penguin Classics', 'Harper Perennial', 'Vintage Books', 'Scribner'],
            'Romance' => ['Penguin Books', 'Harlequin', 'Avon Books', 'Berkley'],
            'Horror' => ['Viking Press', 'Doubleday', 'Cemetery Dance', 'Tor Books'],
            'Fantasy' => ['Tor Books', 'Del Rey', 'Ace Books', 'DAW Books'],
            'Sci-Fi' => ['Ace Books', 'Tor Books', 'Del Rey', 'Orbit Books'],
            'Mystery' => ['Minotaur Books', 'Putnam', 'St. Martin\'s Press', 'Bantam'],
            'Thriller' => ['Grand Central', 'Putnam', 'Little Brown', 'Simon & Schuster'],
            'Biography' => ['Simon & Schuster', 'Random House', 'Crown Publishing', 'Knopf'],
            'Memoir' => ['Random House', 'Knopf', 'Penguin Press', 'Harper'],
            'Self-Help' => ['Avery', 'Hay House', 'Portfolio', 'Penguin Life'],
            'Programming' => ['O\'Reilly Media', 'Addison-Wesley', 'Manning', 'No Starch Press'],
            'Science' => ['MIT Press', 'Oxford University Press', 'Princeton University Press', 'Norton'],
            'Psychology' => ['Little Brown', 'Crown Publishing', 'Penguin Books', 'Basic Books'],
            'Non-Fiction' => ['Harper', 'Random House', 'Crown Publishing', 'Knopf'],
            'Young Adult' => ['Scholastic', 'Katherine Tegen Books', 'Disney Hyperion', 'Penguin Teen'],
            'History' => ['W. W. Norton', 'Vintage', 'Penguin Press', 'Basic Books'],
            'Economics' => ['William Morrow', 'Harper Business', 'Crown Business', 'Portfolio'],
            'Finance' => ['Plata Publishing', 'Crown Business', 'Portfolio', 'Random House']
        ];

        // Select random publisher from category or use default
        $categoryPublishers = $publishers[$category] ?? ['Penguin Random House', 'HarperCollins', 'Simon & Schuster'];
        $publisher = $categoryPublishers[array_rand($categoryPublishers)];

        // Generate random published date between 1990 and 2023
        $year = rand(1990, 2023);
        $month = rand(1, 12);
        $day = rand(1, 28);
        $publishedDate = sprintf("%04d-%02d-%02d", $year, $month, $day);

        // Generate pages based on category
        $pagesRanges = [
            'Fiction' => [250, 500],
            'Romance' => [280, 420],
            'Horror' => [300, 600],
            'Fantasy' => [350, 800],
            'Sci-Fi' => [280, 500],
            'Mystery' => [280, 450],
            'Thriller' => [300, 480],
            'Biography' => [350, 700],
            'Memoir' => [250, 400],
            'Self-Help' => [200, 350],
            'Programming' => [300, 800],
            'Science' => [250, 500],
            'Psychology' => [250, 400],
            'Non-Fiction' => [300, 500],
            'Young Adult' => [300, 500],
            'History' => [400, 700],
            'Economics' => [280, 400],
            'Finance' => [280, 380]
        ];

        $range = $pagesRanges[$category] ?? [200, 400];
        $pages = rand($range[0], $range[1]);

        // Update the book
        $stmt = $conn->prepare("UPDATE books SET publisher = ?, published_date = ?, pages = ? WHERE id = ?");
        $stmt->bind_param("ssii", $publisher, $publishedDate, $pages, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $updatedCount++;
        }
        $stmt->close();
    }

    $conn->close();

    echo "<h1 style='color: green;'>✅ ALL Books Updated!</h1>";
    echo "<p>📚 Updated: <strong>$updatedCount</strong> books</p>";
    echo "<p>All books now have:</p>";
    echo "<ul>";
    echo "<li>✅ Publisher name</li>";
    echo "<li>✅ Published date</li>";
    echo "<li>✅ Page count</li>";
    echo "</ul>";
    echo "<p><a href='get_books.php'>View All Books</a></p>";

} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>