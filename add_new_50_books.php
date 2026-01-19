<?php
/**
 * Add 50 NEW Popular Books with Cover URLs
 * Run once: http://localhost/digitallibrary_API/add_new_50_books.php
 */

require_once 'db.php';

$books = [
    // Romance & Contemporary Fiction
    ['The Notebook', 'Nicholas Sparks', 'Romance', '9780446676090', 4.5, 249, 6, 'A timeless love story of devotion and enduring love.', 'https://covers.openlibrary.org/b/isbn/9780446676090-M.jpg'],
    ['Me Before You', 'Jojo Moyes', 'Romance', '9780143124542', 4.6, 299, 5, 'A heartbreaking tale of love and sacrifice.', 'https://covers.openlibrary.org/b/isbn/9780143124542-M.jpg'],
    ['The Alchemist', 'Paulo Coelho', 'Fiction', '9780062315007', 4.7, 279, 8, 'A magical story of following your dreams.', 'https://covers.openlibrary.org/b/isbn/9780062315007-M.jpg'],
    ['The Kite Runner', 'Khaled Hosseini', 'Fiction', '9781594631931', 4.6, 319, 5, 'A powerful story of friendship, betrayal, and redemption.', 'https://covers.openlibrary.org/b/isbn/9781594631931-M.jpg'],
    ['A Thousand Splendid Suns', 'Khaled Hosseini', 'Fiction', '9781594489501', 4.7, 329, 4, 'An epic tale of two Afghan women.', 'https://covers.openlibrary.org/b/isbn/9781594489501-M.jpg'],
    ['The Book Thief', 'Markus Zusak', 'Fiction', '9780375842207', 4.6, 289, 6, 'Death narrates the story of a girl in Nazi Germany.', 'https://covers.openlibrary.org/b/isbn/9780375842207-M.jpg'],
    ['Life of Pi', 'Yann Martel', 'Fiction', '9780156027328', 4.4, 269, 5, 'A boy survives 227 days on a lifeboat with a tiger.', 'https://covers.openlibrary.org/b/isbn/9780156027328-M.jpg'],
    ['The Lovely Bones', 'Alice Sebold', 'Fiction', '9780316044936', 4.3, 249, 5, 'A murdered girl watches her family from heaven.', 'https://covers.openlibrary.org/b/isbn/9780316044936-M.jpg'],
    ['Water for Elephants', 'Sara Gruen', 'Fiction', '9781565125605', 4.4, 279, 4, 'Love and spectacle during the Great Depression.', 'https://covers.openlibrary.org/b/isbn/9781565125605-M.jpg'],
    ['The Help', 'Kathryn Stockett', 'Fiction', '9780425232200', 4.5, 299, 6, 'Stories of black maids in 1960s Mississippi.', 'https://covers.openlibrary.org/b/isbn/9780425232200-M.jpg'],

    // Horror & Supernatural
    ['It', 'Stephen King', 'Horror', '9781501142970', 4.5, 449, 4, 'A shape-shifting clown terrorizes a small town.', 'https://covers.openlibrary.org/b/isbn/9781501142970-M.jpg'],
    ['The Shining', 'Stephen King', 'Horror', '9780307743657', 4.6, 349, 5, 'A haunted hotel drives a man to madness.', 'https://covers.openlibrary.org/b/isbn/9780307743657-M.jpg'],
    ['Pet Sematary', 'Stephen King', 'Horror', '9781501156700', 4.4, 299, 4, 'Sometimes dead is better.', 'https://covers.openlibrary.org/b/isbn/9781501156700-M.jpg'],
    ['Dracula', 'Bram Stoker', 'Horror', '9780486411095', 4.3, 199, 7, 'The original vampire tale.', 'https://covers.openlibrary.org/b/isbn/9780486411095-M.jpg'],
    ['Frankenstein', 'Mary Shelley', 'Horror', '9780486282114', 4.2, 179, 8, 'A scientist creates a monster.', 'https://covers.openlibrary.org/b/isbn/9780486282114-M.jpg'],
    ['The Exorcist', 'William Peter Blatty', 'Horror', '9780061007224', 4.3, 279, 4, 'A girl is possessed by a demon.', 'https://covers.openlibrary.org/b/isbn/9780061007224-M.jpg'],
    ['Bird Box', 'Josh Malerman', 'Horror', '9780062259653', 4.2, 269, 5, 'See no evil, or die.', 'https://covers.openlibrary.org/b/isbn/9780062259653-M.jpg'],
    ['World War Z', 'Max Brooks', 'Horror', '9780307346612', 4.4, 299, 5, 'An oral history of the zombie war.', 'https://covers.openlibrary.org/b/isbn/9780307346612-M.jpg'],
    ['House of Leaves', 'Mark Z. Danielewski', 'Horror', '9780375703768', 4.1, 399, 3, 'A house that is larger inside than outside.', 'https://covers.openlibrary.org/b/isbn/9780375703768-M.jpg'],
    ['The Haunting of Hill House', 'Shirley Jackson', 'Horror', '9780143039983', 4.3, 249, 4, 'The greatest haunted house story ever told.', 'https://covers.openlibrary.org/b/isbn/9780143039983-M.jpg'],

    // Young Adult & Dystopian
    ['Divergent', 'Veronica Roth', 'Young Adult', '9780062024039', 4.4, 329, 7, 'Society is divided into five factions.', 'https://covers.openlibrary.org/b/isbn/9780062024039-M.jpg'],
    ['The Maze Runner', 'James Dashner', 'Young Adult', '9780385737951', 4.3, 299, 6, 'Teens trapped in a deadly maze.', 'https://covers.openlibrary.org/b/isbn/9780385737951-M.jpg'],
    ['Twilight', 'Stephenie Meyer', 'Young Adult', '9780316015844', 4.2, 279, 8, 'A human falls in love with a vampire.', 'https://covers.openlibrary.org/b/isbn/9780316015844-M.jpg'],
    ['The Fault in Our Stars', 'John Green', 'Young Adult', '9780142424179', 4.5, 269, 6, 'Two teens with cancer fall in love.', 'https://covers.openlibrary.org/b/isbn/9780142424179-M.jpg'],
    ['Percy Jackson: The Lightning Thief', 'Rick Riordan', 'Young Adult', '9780786838653', 4.6, 289, 7, 'A boy discovers he is a Greek demigod.', 'https://covers.openlibrary.org/b/isbn/9780786838653-M.jpg'],
    ['The Giver', 'Lois Lowry', 'Young Adult', '9780544336261', 4.5, 229, 6, 'A society without pain or color.', 'https://covers.openlibrary.org/b/isbn/9780544336261-M.jpg'],
    ['Eragon', 'Christopher Paolini', 'Fantasy', '9780375826696', 4.3, 349, 5, 'A farm boy becomes a dragon rider.', 'https://covers.openlibrary.org/b/isbn/9780375826696-M.jpg'],
    ['The Mortal Instruments: City of Bones', 'Cassandra Clare', 'Fantasy', '9781481455923', 4.4, 319, 5, 'A girl discovers she is a Shadowhunter.', 'https://covers.openlibrary.org/b/isbn/9781481455923-M.jpg'],
    ['Miss Peregrine\'s Home for Peculiar Children', 'Ransom Riggs', 'Fantasy', '9781594746031', 4.3, 299, 5, 'Children with strange powers hide from monsters.', 'https://covers.openlibrary.org/b/isbn/9781594746031-M.jpg'],
    ['Throne of Glass', 'Sarah J. Maas', 'Fantasy', '9781619630345', 4.5, 329, 4, 'An assassin competes for her freedom.', 'https://covers.openlibrary.org/b/isbn/9781619630345-M.jpg'],

    // Biography & History
    ['Steve Jobs', 'Walter Isaacson', 'Biography', '9781451648539', 4.6, 499, 4, 'The exclusive biography of Apple\'s founder.', 'https://covers.openlibrary.org/b/isbn/9781451648539-M.jpg'],
    ['Einstein: His Life and Universe', 'Walter Isaacson', 'Biography', '9780743264747', 4.5, 449, 3, 'The life of the greatest scientist.', 'https://covers.openlibrary.org/b/isbn/9780743264747-M.jpg'],
    ['The Diary of a Young Girl', 'Anne Frank', 'Biography', '9780553296983', 4.7, 229, 6, 'A Jewish girl\'s diary during the Holocaust.', 'https://covers.openlibrary.org/b/isbn/9780553296983-M.jpg'],
    ['Long Walk to Freedom', 'Nelson Mandela', 'Biography', '9780316548182', 4.6, 399, 4, 'Nelson Mandela\'s autobiography.', 'https://covers.openlibrary.org/b/isbn/9780316548182-M.jpg'],
    ['I Know Why the Caged Bird Sings', 'Maya Angelou', 'Biography', '9780345514400', 4.5, 279, 5, 'Maya Angelou\'s powerful memoir.', 'https://covers.openlibrary.org/b/isbn/9780345514400-M.jpg'],
    ['The Immortal Life of Henrietta Lacks', 'Rebecca Skloot', 'Biography', '9781400052189', 4.4, 349, 4, 'The woman behind HeLa cells.', 'https://covers.openlibrary.org/b/isbn/9781400052189-M.jpg'],
    ['Born a Crime', 'Trevor Noah', 'Biography', '9780399588174', 4.7, 319, 5, 'Growing up in apartheid South Africa.', 'https://covers.openlibrary.org/b/isbn/9780399588174-M.jpg'],
    ['When Breath Becomes Air', 'Paul Kalanithi', 'Biography', '9780812988406', 4.8, 269, 5, 'A neurosurgeon faces terminal cancer.', 'https://covers.openlibrary.org/b/isbn/9780812988406-M.jpg'],
    ['Wild', 'Cheryl Strayed', 'Memoir', '9780307476074', 4.4, 289, 5, 'A woman hikes the Pacific Crest Trail.', 'https://covers.openlibrary.org/b/isbn/9780307476074-M.jpg'],
    ['The Glass Castle', 'Jeannette Walls', 'Memoir', '9780743247542', 4.5, 279, 5, 'A remarkable memoir of resilience.', 'https://covers.openlibrary.org/b/isbn/9780743247542-M.jpg'],

    // Science & Philosophy
    ['A Brief History of Time', 'Stephen Hawking', 'Science', '9780553380163', 4.5, 349, 4, 'The universe explained for everyone.', 'https://covers.openlibrary.org/b/isbn/9780553380163-M.jpg'],
    ['Cosmos', 'Carl Sagan', 'Science', '9780345539434', 4.7, 399, 4, 'A personal voyage through the universe.', 'https://covers.openlibrary.org/b/isbn/9780345539434-M.jpg'],
    ['The Selfish Gene', 'Richard Dawkins', 'Science', '9780198788607', 4.4, 329, 3, 'A revolutionary view of evolution.', 'https://covers.openlibrary.org/b/isbn/9780198788607-M.jpg'],
    ['Guns, Germs, and Steel', 'Jared Diamond', 'History', '9780393354324', 4.5, 429, 3, 'Why some civilizations conquered others.', 'https://covers.openlibrary.org/b/isbn/9780393354324-M.jpg'],
    ['The Origin of Species', 'Charles Darwin', 'Science', '9780451529060', 4.3, 299, 5, 'The foundation of evolutionary biology.', 'https://covers.openlibrary.org/b/isbn/9780451529060-M.jpg'],
    ['Freakonomics', 'Steven Levitt', 'Economics', '9780060731335', 4.3, 279, 6, 'The hidden side of everything.', 'https://covers.openlibrary.org/b/isbn/9780060731335-M.jpg'],
    ['Outliers', 'Malcolm Gladwell', 'Psychology', '9780316017930', 4.4, 299, 6, 'The story of success.', 'https://covers.openlibrary.org/b/isbn/9780316017930-M.jpg'],
    ['Blink', 'Malcolm Gladwell', 'Psychology', '9780316010665', 4.3, 269, 5, 'The power of thinking without thinking.', 'https://covers.openlibrary.org/b/isbn/9780316010665-M.jpg'],
    ['The Tipping Point', 'Malcolm Gladwell', 'Psychology', '9780316346627', 4.2, 259, 5, 'How little things make a big difference.', 'https://covers.openlibrary.org/b/isbn/9780316346627-M.jpg'],
    ['Quiet', 'Susan Cain', 'Psychology', '9780307352156', 4.5, 299, 5, 'The power of introverts.', 'https://covers.openlibrary.org/b/isbn/9780307352156-M.jpg'],
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

    echo "<h1>✅ NEW Books Added Successfully!</h1>";
    echo "<p>📚 Inserted: <strong>$insertCount</strong> new books</p>";
    echo "<p>⏭️ Skipped (already exist): <strong>$skipCount</strong> books</p>";
    echo "<p><a href='get_books.php'>View All Books</a></p>";

} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>