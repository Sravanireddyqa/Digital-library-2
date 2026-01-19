<?php
/**
 * Add Programming Language Books
 * Run: http://localhost/digitallibrary_API/add_programming_books.php
 */

require_once 'db.php';

$books = [
    // C Programming Books
    ['The C Programming Language', 'Brian W. Kernighan', 'Programming', '9780131103627', 4.8, 399, 5, 'The definitive C programming book by the creators of C. Learn C from the masters.', 'https://covers.openlibrary.org/b/isbn/9780131103627-M.jpg', 'Prentice Hall', '1988-03-22', 272],
    ['C Programming: A Modern Approach', 'K. N. King', 'Programming', '9780393979503', 4.7, 549, 4, 'Comprehensive guide to C programming with modern practices.', 'https://covers.openlibrary.org/b/isbn/9780393979503-M.jpg', 'W. W. Norton', '2008-04-01', 832],
    ['Head First C', 'David Griffiths', 'Programming', '9781449399917', 4.5, 449, 5, 'A brain-friendly guide to C programming.', 'https://covers.openlibrary.org/b/isbn/9781449399917-M.jpg', 'O\'Reilly Media', '2012-04-20', 632],
    ['C in Depth', 'S.K. Srivastava', 'Programming', '9788176567558', 4.4, 349, 6, 'Learn C programming in depth with practical examples.', 'https://covers.openlibrary.org/b/isbn/9788176567558-M.jpg', 'BPB Publications', '2009-01-01', 456],
    ['Let Us C', 'Yashavant Kanetkar', 'Programming', '9788176567893', 4.6, 299, 8, 'India\'s most popular C programming book for beginners.', 'https://covers.openlibrary.org/b/isbn/9788176567893-M.jpg', 'BPB Publications', '2016-11-01', 492],

    // C++ Programming Books
    ['The C++ Programming Language', 'Bjarne Stroustrup', 'Programming', '9780321958327', 4.7, 599, 4, 'The definitive C++ reference by the creator of C++.', 'https://covers.openlibrary.org/b/isbn/9780321958327-M.jpg', 'Addison-Wesley', '2013-05-19', 1376],
    ['C++ Primer', 'Stanley B. Lippman', 'Programming', '9780321714114', 4.6, 549, 5, 'Comprehensive introduction to C++ programming.', 'https://covers.openlibrary.org/b/isbn/9780321714114-M.jpg', 'Addison-Wesley', '2012-08-16', 976],
    ['Effective C++', 'Scott Meyers', 'Programming', '9780321334879', 4.8, 449, 4, '55 specific ways to improve your C++ programs.', 'https://covers.openlibrary.org/b/isbn/9780321334879-M.jpg', 'Addison-Wesley', '2005-05-12', 320],
    ['C++ For Dummies', 'Stephen R. Davis', 'Programming', '9781118823774', 4.3, 349, 6, 'Easy introduction to C++ programming for beginners.', 'https://covers.openlibrary.org/b/isbn/9781118823774-M.jpg', 'For Dummies', '2014-04-21', 456],
    ['Beginning C++', 'Ivor Horton', 'Programming', '9781484200087', 4.5, 499, 5, 'From novice to professional in C++ programming.', 'https://covers.openlibrary.org/b/isbn/9781484200087-M.jpg', 'Apress', '2014-10-08', 784],

    // Java Programming Books
    ['Head First Java', 'Kathy Sierra', 'Programming', '9780596009205', 4.7, 449, 6, 'A brain-friendly guide to Java programming.', 'https://covers.openlibrary.org/b/isbn/9780596009205-M.jpg', 'O\'Reilly Media', '2005-02-09', 688],
    ['Effective Java', 'Joshua Bloch', 'Programming', '9780134685991', 4.9, 549, 4, 'Best practices for Java programming by Google engineer.', 'https://covers.openlibrary.org/b/isbn/9780134685991-M.jpg', 'Addison-Wesley', '2018-01-06', 416],
    ['Java: The Complete Reference', 'Herbert Schildt', 'Programming', '9781260440232', 4.6, 649, 5, 'Comprehensive Java reference guide.', 'https://covers.openlibrary.org/b/isbn/9781260440232-M.jpg', 'McGraw-Hill', '2018-12-12', 1248],
    ['Core Java Volume I', 'Cay S. Horstmann', 'Programming', '9780135166307', 4.7, 599, 4, 'Fundamentals of Java programming.', 'https://covers.openlibrary.org/b/isbn/9780135166307-M.jpg', 'Prentice Hall', '2018-08-27', 928],
    ['Java in 24 Hours', 'Rogers Cadenhead', 'Programming', '9780672337024', 4.4, 349, 6, 'Learn Java basics in 24 hours.', 'https://covers.openlibrary.org/b/isbn/9780672337024-M.jpg', 'Sams Publishing', '2017-10-05', 432],

    // Python Programming Books
    ['Python Crash Course', 'Eric Matthes', 'Programming', '9781593279288', 4.8, 449, 7, 'A hands-on, project-based introduction to Python programming.', 'https://covers.openlibrary.org/b/isbn/9781593279288-M.jpg', 'No Starch Press', '2019-05-03', 544],
    ['Learning Python', 'Mark Lutz', 'Programming', '9781449355739', 4.5, 549, 5, 'Powerful object-oriented programming with Python.', 'https://covers.openlibrary.org/b/isbn/9781449355739-M.jpg', 'O\'Reilly Media', '2013-06-12', 1648],
    ['Automate the Boring Stuff with Python', 'Al Sweigart', 'Programming', '9781593279929', 4.7, 399, 6, 'Practical programming for total beginners.', 'https://covers.openlibrary.org/b/isbn/9781593279929-M.jpg', 'No Starch Press', '2019-11-12', 592],
    ['Fluent Python', 'Luciano Ramalho', 'Programming', '9781491946008', 4.8, 549, 4, 'Clear, concise, and effective Python programming.', 'https://covers.openlibrary.org/b/isbn/9781491946008-M.jpg', 'O\'Reilly Media', '2015-08-20', 792],
    ['Python for Data Analysis', 'Wes McKinney', 'Programming', '9781491957660', 4.6, 499, 5, 'Data wrangling with Pandas, NumPy, and IPython.', 'https://covers.openlibrary.org/b/isbn/9781491957660-M.jpg', 'O\'Reilly Media', '2017-10-05', 550],

    // JavaScript Books
    ['JavaScript: The Definitive Guide', 'David Flanagan', 'Programming', '9781491952023', 4.6, 549, 5, 'Master the world\'s most popular programming language.', 'https://covers.openlibrary.org/b/isbn/9781491952023-M.jpg', 'O\'Reilly Media', '2020-05-14', 706],
    ['Eloquent JavaScript', 'Marijn Haverbeke', 'Programming', '9781593279509', 4.7, 399, 6, 'A modern introduction to JavaScript programming.', 'https://covers.openlibrary.org/b/isbn/9781593279509-M.jpg', 'No Starch Press', '2018-12-04', 472],
    ['You Don\'t Know JS', 'Kyle Simpson', 'Programming', '9781491924464', 4.8, 349, 7, 'Deep dive into JavaScript core mechanisms.', 'https://covers.openlibrary.org/b/isbn/9781491924464-M.jpg', 'O\'Reilly Media', '2015-12-27', 278],

    // Ruby Books
    ['The Ruby Programming Language', 'David Flanagan', 'Programming', '9780596516178', 4.5, 449, 4, 'Comprehensive guide to Ruby programming.', 'https://covers.openlibrary.org/b/isbn/9780596516178-M.jpg', 'O\'Reilly Media', '2008-01-25', 446],
    ['Learn Ruby the Hard Way', 'Zed Shaw', 'Programming', '9780321884992', 4.4, 349, 5, 'Learn Ruby through practical exercises.', 'https://covers.openlibrary.org/b/isbn/9780321884992-M.jpg', 'Addison-Wesley', '2014-12-01', 320],

    // Go Language Books
    ['The Go Programming Language', 'Alan Donovan', 'Programming', '9780134190440', 4.8, 499, 4, 'Authoritative guide to Go programming.', 'https://covers.openlibrary.org/b/isbn/9780134190440-M.jpg', 'Addison-Wesley', '2015-10-26', 400],
    ['Go in Action', 'William Kennedy', 'Programming', '9781617291784', 4.6, 449, 5, 'Practical approach to Go programming.', 'https://covers.openlibrary.org/b/isbn/9781617291784-M.jpg', 'Manning', '2015-11-04', 264],

    // Swift Books  
    ['Swift Programming', 'Matthew Mathias', 'Programming', '9780135264201', 4.5, 449, 4, 'Learn iOS development with Swift.', 'https://covers.openlibrary.org/b/isbn/9780135264201-M.jpg', 'Big Nerd Ranch', '2020-12-10', 480],

    // Kotlin Books
    ['Kotlin in Action', 'Dmitry Jemerov', 'Programming', '9781617293290', 4.7, 499, 4, 'Complete guide to Kotlin for Android and JVM.', 'https://covers.openlibrary.org/b/isbn/9781617293290-M.jpg', 'Manning', '2017-02-19', 360],

    // Rust Books
    ['The Rust Programming Language', 'Steve Klabnik', 'Programming', '9781718500440', 4.8, 499, 4, 'The official book on Rust programming.', 'https://covers.openlibrary.org/b/isbn/9781718500440-M.jpg', 'No Starch Press', '2019-08-06', 560],
];

try {
    $conn = getConnection();

    $insertCount = 0;
    $skipCount = 0;

    foreach ($books as $book) {
        // Check if book already exists
        $check = $conn->prepare("SELECT id FROM books WHERE isbn = ? OR title = ?");
        $check->bind_param("ss", $book[3], $book[0]);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $skipCount++;
            continue;
        }
        $check->close();

        // Insert book with all fields
        $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, rating, price, stock, description, cover_url, publisher, published_date, pages, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param(
            "ssssddisssis",
            $book[0],   // title
            $book[1],   // author
            $book[2],   // category
            $book[3],   // isbn
            $book[4],   // rating
            $book[5],   // price
            $book[6],   // stock
            $book[7],   // description
            $book[8],   // cover_url
            $book[9],   // publisher
            $book[10],  // published_date
            $book[11]   // pages
        );

        if ($stmt->execute()) {
            $insertCount++;
        }
        $stmt->close();
    }

    $conn->close();

    echo "<h1 style='color: green;'>✅ Programming Books Added!</h1>";
    echo "<p>📚 Inserted: <strong>$insertCount</strong> new programming books</p>";
    echo "<p>⏭️ Skipped (already exist): <strong>$skipCount</strong> books</p>";
    echo "<h3>Books Added:</h3>";
    echo "<ul>";
    echo "<li>📘 <strong>C Programming</strong> - 5 books (K&R, Let Us C, etc.)</li>";
    echo "<li>📗 <strong>C++ Programming</strong> - 5 books (Stroustrup, Effective C++, etc.)</li>";
    echo "<li>📙 <strong>Java Programming</strong> - 5 books (Head First Java, Effective Java, etc.)</li>";
    echo "<li>📕 <strong>Python Programming</strong> - 5 books (Python Crash Course, Fluent Python, etc.)</li>";
    echo "<li>📒 <strong>JavaScript</strong> - 3 books (Eloquent JS, You Don't Know JS, etc.)</li>";
    echo "<li>📓 <strong>Ruby, Go, Swift, Kotlin, Rust</strong> - 7 books</li>";
    echo "</ul>";
    echo "<p><a href='get_books.php'>View All Books</a></p>";

} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>