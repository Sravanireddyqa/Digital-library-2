<?php
/**
 * Add 50 Popular Books with Cover URLs
 * Run once: http://localhost/digitallibrary_API/add_50_books.php
 */

require_once 'db.php';

$books = [
    // Fiction Classics
    ['To Kill a Mockingbird', 'Harper Lee', 'Fiction', '9780061120084', 4.8, 299, 5, 'A gripping tale of racial injustice in the American South.', 'https://covers.openlibrary.org/b/isbn/9780061120084-M.jpg'],
    ['Pride and Prejudice', 'Jane Austen', 'Fiction', '9780141439518', 4.6, 199, 8, 'A witty exploration of love and social standing in Regency England.', 'https://covers.openlibrary.org/b/isbn/9780141439518-M.jpg'],
    ['1984', 'George Orwell', 'Fiction', '9780451524935', 4.7, 249, 6, 'A dystopian masterpiece about totalitarian control.', 'https://covers.openlibrary.org/b/isbn/9780451524935-M.jpg'],
    ['The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '9780743273565', 4.5, 229, 7, 'A tale of wealth, love, and the American Dream.', 'https://covers.openlibrary.org/b/isbn/9780743273565-M.jpg'],
    ['Jane Eyre', 'Charlotte Bronte', 'Fiction', '9780141441146', 4.6, 259, 4, 'A passionate and independent heroine finds love.', 'https://covers.openlibrary.org/b/isbn/9780141441146-M.jpg'],
    ['Wuthering Heights', 'Emily Bronte', 'Fiction', '9780141439556', 4.4, 219, 5, 'A dark tale of love and revenge on the moors.', 'https://covers.openlibrary.org/b/isbn/9780141439556-M.jpg'],
    ['The Catcher in the Rye', 'J.D. Salinger', 'Fiction', '9780316769488', 4.3, 279, 6, 'A rebellious teen navigates New York City.', 'https://covers.openlibrary.org/b/isbn/9780316769488-M.jpg'],
    ['Lord of the Flies', 'William Golding', 'Fiction', '9780399501487', 4.2, 239, 5, 'Boys stranded on an island descend into savagery.', 'https://covers.openlibrary.org/b/isbn/9780399501487-M.jpg'],
    ['Animal Farm', 'George Orwell', 'Fiction', '9780451526342', 4.5, 179, 8, 'A satirical allegory about power and corruption.', 'https://covers.openlibrary.org/b/isbn/9780451526342-M.jpg'],
    ['Brave New World', 'Aldous Huxley', 'Fiction', '9780060850524', 4.4, 269, 5, 'A future society controlled by pleasure and conditioning.', 'https://covers.openlibrary.org/b/isbn/9780060850524-M.jpg'],

    // Fantasy & Sci-Fi
    ['Harry Potter and the Sorcerer\'s Stone', 'J.K. Rowling', 'Fantasy', '9780590353427', 4.9, 399, 10, 'A young wizard discovers his magical heritage.', 'https://covers.openlibrary.org/b/isbn/9780590353427-M.jpg'],
    ['The Hobbit', 'J.R.R. Tolkien', 'Fantasy', '9780547928227', 4.8, 349, 7, 'Bilbo Baggins embarks on an unexpected adventure.', 'https://covers.openlibrary.org/b/isbn/9780547928227-M.jpg'],
    ['The Lord of the Rings', 'J.R.R. Tolkien', 'Fantasy', '9780618640157', 4.9, 599, 5, 'An epic quest to destroy the One Ring.', 'https://covers.openlibrary.org/b/isbn/9780618640157-M.jpg'],
    ['Dune', 'Frank Herbert', 'Sci-Fi', '9780441172719', 4.7, 449, 4, 'A desert planet holds the key to the universe.', 'https://covers.openlibrary.org/b/isbn/9780441172719-M.jpg'],
    ['The Hunger Games', 'Suzanne Collins', 'Sci-Fi', '9780439023481', 4.6, 329, 8, 'A girl fights for survival in a deadly competition.', 'https://covers.openlibrary.org/b/isbn/9780439023481-M.jpg'],
    ['Ender\'s Game', 'Orson Scott Card', 'Sci-Fi', '9780812550702', 4.5, 299, 6, 'A child prodigy trains to save humanity.', 'https://covers.openlibrary.org/b/isbn/9780812550702-M.jpg'],
    ['The Chronicles of Narnia', 'C.S. Lewis', 'Fantasy', '9780066238500', 4.7, 499, 5, 'Children discover a magical world through a wardrobe.', 'https://covers.openlibrary.org/b/isbn/9780066238500-M.jpg'],
    ['A Game of Thrones', 'George R.R. Martin', 'Fantasy', '9780553573404', 4.6, 449, 4, 'Noble families vie for the Iron Throne.', 'https://covers.openlibrary.org/b/isbn/9780553573404-M.jpg'],
    ['The Martian', 'Andy Weir', 'Sci-Fi', '9780553418026', 4.7, 349, 6, 'An astronaut stranded on Mars fights to survive.', 'https://covers.openlibrary.org/b/isbn/9780553418026-M.jpg'],
    ['Ready Player One', 'Ernest Cline', 'Sci-Fi', '9780307887436', 4.5, 329, 5, 'A virtual reality treasure hunt for ultimate power.', 'https://covers.openlibrary.org/b/isbn/9780307887436-M.jpg'],

    // Mystery & Thriller
    ['The Girl with the Dragon Tattoo', 'Stieg Larsson', 'Mystery', '9780307454546', 4.5, 379, 4, 'A journalist and hacker investigate a cold case.', 'https://covers.openlibrary.org/b/isbn/9780307454546-M.jpg'],
    ['Gone Girl', 'Gillian Flynn', 'Thriller', '9780307588371', 4.4, 299, 6, 'A wife disappears on her anniversary.', 'https://covers.openlibrary.org/b/isbn/9780307588371-M.jpg'],
    ['The Da Vinci Code', 'Dan Brown', 'Thriller', '9780307474278', 4.3, 349, 7, 'A murder leads to ancient secrets.', 'https://covers.openlibrary.org/b/isbn/9780307474278-M.jpg'],
    ['Sherlock Holmes', 'Arthur Conan Doyle', 'Mystery', '9780140439083', 4.8, 299, 5, 'The legendary detective solves impossible cases.', 'https://covers.openlibrary.org/b/isbn/9780140439083-M.jpg'],
    ['Murder on the Orient Express', 'Agatha Christie', 'Mystery', '9780062693662', 4.6, 249, 6, 'Poirot investigates murder on a train.', 'https://covers.openlibrary.org/b/isbn/9780062693662-M.jpg'],
    ['The Silent Patient', 'Alex Michaelides', 'Thriller', '9781250301697', 4.5, 329, 5, 'A woman shoots her husband and never speaks again.', 'https://covers.openlibrary.org/b/isbn/9781250301697-M.jpg'],
    ['The Girl on the Train', 'Paula Hawkins', 'Thriller', '9781594634024', 4.3, 279, 6, 'A woman witnesses something shocking from her train.', 'https://covers.openlibrary.org/b/isbn/9781594634024-M.jpg'],
    ['Big Little Lies', 'Liane Moriarty', 'Mystery', '9780399587191', 4.4, 299, 5, 'Secrets unravel at a school trivia night.', 'https://covers.openlibrary.org/b/isbn/9780399587191-M.jpg'],
    ['In the Woods', 'Tana French', 'Mystery', '9780143113492', 4.3, 269, 4, 'A detective investigates a case linked to his past.', 'https://covers.openlibrary.org/b/isbn/9780143113492-M.jpg'],
    ['The Woman in the Window', 'A.J. Finn', 'Thriller', '9780062678416', 4.2, 289, 5, 'An agoraphobic woman witnesses a crime.', 'https://covers.openlibrary.org/b/isbn/9780062678416-M.jpg'],

    // Non-Fiction & Self-Help
    ['Sapiens', 'Yuval Noah Harari', 'Non-Fiction', '9780062316097', 4.7, 499, 4, 'A brief history of humankind.', 'https://covers.openlibrary.org/b/isbn/9780062316097-M.jpg'],
    ['Atomic Habits', 'James Clear', 'Self-Help', '9780735211292', 4.8, 349, 8, 'Tiny changes lead to remarkable results.', 'https://covers.openlibrary.org/b/isbn/9780735211292-M.jpg'],
    ['Thinking, Fast and Slow', 'Daniel Kahneman', 'Psychology', '9780374533557', 4.6, 399, 3, 'How we think and make decisions.', 'https://covers.openlibrary.org/b/isbn/9780374533557-M.jpg'],
    ['The Power of Habit', 'Charles Duhigg', 'Self-Help', '9780812981605', 4.5, 299, 6, 'Why we do what we do in life and business.', 'https://covers.openlibrary.org/b/isbn/9780812981605-M.jpg'],
    ['Educated', 'Tara Westover', 'Memoir', '9780399590504', 4.7, 349, 5, 'A memoir of growing up in a survivalist family.', 'https://covers.openlibrary.org/b/isbn/9780399590504-M.jpg'],
    ['Becoming', 'Michelle Obama', 'Memoir', '9781524763138', 4.8, 449, 6, 'The former First Lady shares her journey.', 'https://covers.openlibrary.org/b/isbn/9781524763138-M.jpg'],
    ['The Subtle Art of Not Giving a F*ck', 'Mark Manson', 'Self-Help', '9780062457714', 4.4, 279, 7, 'A counterintuitive approach to living a good life.', 'https://covers.openlibrary.org/b/isbn/9780062457714-M.jpg'],
    ['Rich Dad Poor Dad', 'Robert Kiyosaki', 'Finance', '9781612680194', 4.5, 299, 8, 'What the rich teach their kids about money.', 'https://covers.openlibrary.org/b/isbn/9781612680194-M.jpg'],
    ['How to Win Friends and Influence People', 'Dale Carnegie', 'Self-Help', '9780671027032', 4.6, 249, 6, 'Timeless advice on relationships.', 'https://covers.openlibrary.org/b/isbn/9780671027032-M.jpg'],
    ['The 7 Habits of Highly Effective People', 'Stephen Covey', 'Self-Help', '9781982137274', 4.5, 329, 5, 'Powerful lessons in personal change.', 'https://covers.openlibrary.org/b/isbn/9781982137274-M.jpg'],

    // Programming & Technology
    ['Clean Code', 'Robert C. Martin', 'Programming', '9780132350884', 4.7, 549, 4, 'A handbook of agile software craftsmanship.', 'https://covers.openlibrary.org/b/isbn/9780132350884-M.jpg'],
    ['The Pragmatic Programmer', 'David Thomas', 'Programming', '9780135957059', 4.8, 599, 3, 'Your journey to mastery in programming.', 'https://covers.openlibrary.org/b/isbn/9780135957059-M.jpg'],
    ['Python Crash Course', 'Eric Matthes', 'Programming', '9781593279288', 4.6, 449, 6, 'A hands-on, project-based introduction to Python.', 'https://covers.openlibrary.org/b/isbn/9781593279288-M.jpg'],
    ['JavaScript: The Good Parts', 'Douglas Crockford', 'Programming', '9780596517748', 4.4, 349, 5, 'Unearthing the excellence in JavaScript.', 'https://covers.openlibrary.org/b/isbn/9780596517748-M.jpg'],
    ['Introduction to Algorithms', 'Thomas Cormen', 'Programming', '9780262033848', 4.5, 799, 3, 'The bible of algorithms.', 'https://covers.openlibrary.org/b/isbn/9780262033848-M.jpg'],
    ['Head First Design Patterns', 'Eric Freeman', 'Programming', '9780596007126', 4.6, 499, 4, 'Learn design patterns the fun way.', 'https://covers.openlibrary.org/b/isbn/9780596007126-M.jpg'],
    ['Cracking the Coding Interview', 'Gayle McDowell', 'Programming', '9780984782857', 4.7, 549, 5, '189 programming questions and solutions.', 'https://covers.openlibrary.org/b/isbn/9780984782857-M.jpg'],
    ['You Don\'t Know JS', 'Kyle Simpson', 'Programming', '9781491950296', 4.5, 299, 6, 'Deep dive into JavaScript.', 'https://covers.openlibrary.org/b/isbn/9781491950296-M.jpg'],
    ['The Art of Computer Programming', 'Donald Knuth', 'Programming', '9780201896831', 4.8, 999, 2, 'The definitive computer science series.', 'https://covers.openlibrary.org/b/isbn/9780201896831-M.jpg'],
    ['Eloquent JavaScript', 'Marijn Haverbeke', 'Programming', '9781593279509', 4.5, 399, 5, 'A modern introduction to programming.', 'https://covers.openlibrary.org/b/isbn/9781593279509-M.jpg'],
];

try {
    $conn = getConnection();

    $insertCount = 0;
    $skipCount = 0;

    foreach ($books as $book) {
        // Check if book already exists (by ISBN or title)
        $check = $conn->prepare("SELECT id FROM books WHERE isbn = ? OR title = ?");
        $check->bind_param("ss", $book[3], $book[0]);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $skipCount++;
            continue;
        }
        $check->close();

        // Insert book
        $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, rating, price, stock, description, cover_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param(
            "ssssdidss",
            $book[0], // title
            $book[1], // author
            $book[2], // category
            $book[3], // isbn
            $book[4], // rating
            $book[5], // price
            $book[6], // stock
            $book[7], // description
            $book[8]  // cover_url
        );

        if ($stmt->execute()) {
            $insertCount++;
        }
        $stmt->close();
    }

    $conn->close();

    echo "<h1>✅ Books Added Successfully!</h1>";
    echo "<p>📚 Inserted: <strong>$insertCount</strong> books</p>";
    echo "<p>⏭️ Skipped (already exist): <strong>$skipCount</strong> books</p>";
    echo "<p><a href='get_books.php'>View All Books</a></p>";

} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>