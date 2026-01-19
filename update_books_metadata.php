<?php
/**
 * Update Books with Publisher, Published Date, and Pages
 * Run once: http://localhost/digitallibrary_API/update_books_metadata.php
 */

require_once 'db.php';

// Book metadata: [title => [publisher, published_date, pages]]
$bookMetadata = [
    // Fiction Classics
    'To Kill a Mockingbird' => ['Harper Perennial', '1960-07-11', 336],
    'Pride and Prejudice' => ['Penguin Classics', '1813-01-28', 432],
    '1984' => ['Signet Classic', '1949-06-08', 328],
    'The Great Gatsby' => ['Scribner', '1925-04-10', 180],
    'Jane Eyre' => ['Penguin Classics', '1847-10-16', 532],
    'Wuthering Heights' => ['Penguin Classics', '1847-12-01', 416],
    'The Catcher in the Rye' => ['Little Brown', '1951-07-16', 277],
    'Lord of the Flies' => ['Penguin Books', '1954-09-17', 224],
    'Animal Farm' => ['Signet Classics', '1945-08-17', 112],
    'Brave New World' => ['Harper Perennial', '1932-01-01', 288],

    // Fantasy & Sci-Fi
    'Harry Potter and the Sorcerer\'s Stone' => ['Scholastic', '1997-06-26', 309],
    'The Hobbit' => ['Houghton Mifflin', '1937-09-21', 310],
    'The Lord of the Rings' => ['Houghton Mifflin', '1954-07-29', 1178],
    'Dune' => ['Ace Books', '1965-08-01', 688],
    'The Hunger Games' => ['Scholastic Press', '2008-09-14', 374],
    'Ender\'s Game' => ['Tor Books', '1985-01-15', 324],
    'The Chronicles of Narnia' => ['HarperCollins', '1950-10-16', 767],
    'A Game of Thrones' => ['Bantam Books', '1996-08-01', 694],
    'The Martian' => ['Crown Publishing', '2014-02-11', 369],
    'Ready Player One' => ['Crown Publishers', '2011-08-16', 374],

    // Mystery & Thriller
    'The Girl with the Dragon Tattoo' => ['Norstedts Förlag', '2005-08-01', 465],
    'Gone Girl' => ['Crown Publishing', '2012-06-05', 415],
    'The Da Vinci Code' => ['Doubleday', '2003-03-18', 489],
    'Sherlock Holmes' => ['Penguin Classics', '1892-10-14', 512],
    'Murder on the Orient Express' => ['Collins Crime Club', '1934-01-01', 256],
    'The Silent Patient' => ['Celadon Books', '2019-02-05', 336],
    'The Girl on the Train' => ['Riverhead Books', '2015-01-13', 336],
    'Big Little Lies' => ['Penguin Books', '2014-07-29', 460],
    'In the Woods' => ['Viking Press', '2007-05-17', 429],
    'The Woman in the Window' => ['William Morrow', '2018-01-02', 448],

    // Non-Fiction & Self-Help
    'Sapiens' => ['Harper', '2011-01-01', 443],
    'Atomic Habits' => ['Avery', '2018-10-16', 320],
    'Thinking, Fast and Slow' => ['Farrar Straus Giroux', '2011-10-25', 499],
    'The Power of Habit' => ['Random House', '2012-02-28', 371],
    'Educated' => ['Random House', '2018-02-20', 334],
    'Becoming' => ['Crown Publishing', '2018-11-13', 448],
    'The Subtle Art of Not Giving a F*ck' => ['Harper', '2016-09-13', 224],
    'Rich Dad Poor Dad' => ['Plata Publishing', '1997-04-01', 336],
    'How to Win Friends and Influence People' => ['Simon & Schuster', '1936-10-01', 288],
    'The 7 Habits of Highly Effective People' => ['Free Press', '1989-08-15', 381],

    // Programming
    'Clean Code' => ['Prentice Hall', '2008-08-01', 464],
    'The Pragmatic Programmer' => ['Addison-Wesley', '2019-09-13', 352],
    'Python Crash Course' => ['No Starch Press', '2019-05-03', 544],
    'JavaScript: The Good Parts' => ['O\'Reilly Media', '2008-05-01', 176],
    'Introduction to Algorithms' => ['MIT Press', '2009-07-31', 1312],
    'Head First Design Patterns' => ['O\'Reilly Media', '2004-10-01', 694],
    'Cracking the Coding Interview' => ['CareerCup', '2015-07-01', 687],
    'You Don\'t Know JS' => ['O\'Reilly Media', '2015-12-27', 278],
    'The Art of Computer Programming' => ['Addison-Wesley', '1968-01-01', 672],
    'Eloquent JavaScript' => ['No Starch Press', '2018-12-04', 472],

    // Romance & Contemporary
    'The Notebook' => ['Warner Books', '1996-10-01', 214],
    'Me Before You' => ['Penguin Books', '2012-01-05', 369],
    'The Alchemist' => ['HarperOne', '1988-01-01', 208],
    'The Kite Runner' => ['Riverhead Books', '2003-05-29', 371],
    'A Thousand Splendid Suns' => ['Riverhead Books', '2007-05-22', 372],
    'The Book Thief' => ['Knopf', '2005-03-14', 552],
    'Life of Pi' => ['Harcourt', '2001-09-11', 319],
    'The Lovely Bones' => ['Little Brown', '2002-07-03', 328],
    'Water for Elephants' => ['Algonquin Books', '2006-05-26', 335],
    'The Help' => ['Putnam Adult', '2009-02-10', 451],

    // Horror
    'It' => ['Viking', '1986-09-15', 1138],
    'The Shining' => ['Doubleday', '1977-01-28', 447],
    'Pet Sematary' => ['Doubleday', '1983-11-14', 374],
    'Dracula' => ['Archibald Constable', '1897-05-26', 418],
    'Frankenstein' => ['Lackington Hughes', '1818-01-01', 280],
    'The Exorcist' => ['Harper & Row', '1971-01-01', 340],
    'Bird Box' => ['Ecco Press', '2014-03-27', 262],
    'World War Z' => ['Crown Publishers', '2006-09-12', 342],
    'House of Leaves' => ['Pantheon Books', '2000-03-07', 709],
    'The Haunting of Hill House' => ['Viking Press', '1959-10-16', 246],

    // Young Adult
    'Divergent' => ['Katherine Tegen Books', '2011-04-25', 487],
    'The Maze Runner' => ['Delacorte Press', '2009-10-06', 375],
    'Twilight' => ['Little Brown', '2005-10-05', 498],
    'The Fault in Our Stars' => ['Dutton Books', '2012-01-10', 313],
    'Percy Jackson: The Lightning Thief' => ['Disney Hyperion', '2005-06-28', 377],
    'The Giver' => ['Houghton Mifflin', '1993-04-26', 240],
    'Eragon' => ['Knopf', '2003-08-26', 509],
    'The Mortal Instruments: City of Bones' => ['Margaret K. McElderry', '2007-03-27', 485],
    'Miss Peregrine\'s Home for Peculiar Children' => ['Quirk Books', '2011-06-07', 352],
    'Throne of Glass' => ['Bloomsbury', '2012-08-07', 404],

    // Biography
    'Steve Jobs' => ['Simon & Schuster', '2011-10-24', 656],
    'Einstein: His Life and Universe' => ['Simon & Schuster', '2007-04-10', 675],
    'The Diary of a Young Girl' => ['Contact Publishing', '1947-06-25', 283],
    'Long Walk to Freedom' => ['Little Brown', '1994-12-01', 656],
    'I Know Why the Caged Bird Sings' => ['Random House', '1969-01-01', 289],
    'The Immortal Life of Henrietta Lacks' => ['Crown Publishing', '2010-02-02', 381],
    'Born a Crime' => ['Spiegel & Grau', '2016-11-15', 304],
    'When Breath Becomes Air' => ['Random House', '2016-01-12', 228],
    'Wild' => ['Knopf', '2012-03-20', 315],
    'The Glass Castle' => ['Scribner', '2005-03-01', 288],

    // Science
    'A Brief History of Time' => ['Bantam Dell', '1988-04-01', 212],
    'Cosmos' => ['Random House', '1980-01-01', 365],
    'The Selfish Gene' => ['Oxford University Press', '1976-01-01', 360],
    'Guns, Germs, and Steel' => ['W. W. Norton', '1997-03-01', 528],
    'The Origin of Species' => ['John Murray', '1859-11-24', 502],
    'Freakonomics' => ['William Morrow', '2005-04-12', 315],
    'Outliers' => ['Little Brown', '2008-11-18', 309],
    'Blink' => ['Little Brown', '2005-01-11', 277],
    'The Tipping Point' => ['Little Brown', '2000-03-01', 301],
    'Quiet' => ['Crown Publishing', '2012-01-24', 333],
];

try {
    $conn = getConnection();

    // First, check if columns exist, add if not
    $checkColumns = $conn->query("SHOW COLUMNS FROM books LIKE 'publisher'");
    if ($checkColumns->num_rows == 0) {
        $conn->query("ALTER TABLE books ADD COLUMN publisher VARCHAR(255) DEFAULT NULL");
    }

    $checkColumns = $conn->query("SHOW COLUMNS FROM books LIKE 'published_date'");
    if ($checkColumns->num_rows == 0) {
        $conn->query("ALTER TABLE books ADD COLUMN published_date DATE DEFAULT NULL");
    }

    $checkColumns = $conn->query("SHOW COLUMNS FROM books LIKE 'pages'");
    if ($checkColumns->num_rows == 0) {
        $conn->query("ALTER TABLE books ADD COLUMN pages INT DEFAULT 0");
    }

    $updatedCount = 0;
    $notFoundCount = 0;

    foreach ($bookMetadata as $title => $metadata) {
        $publisher = $metadata[0];
        $publishedDate = $metadata[1];
        $pages = $metadata[2];

        $stmt = $conn->prepare("UPDATE books SET publisher = ?, published_date = ?, pages = ? WHERE title = ?");
        $stmt->bind_param("ssis", $publisher, $publishedDate, $pages, $title);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $updatedCount++;
        } else {
            $notFoundCount++;
        }
        $stmt->close();
    }

    $conn->close();

    echo "<h1>✅ Books Metadata Updated!</h1>";
    echo "<p>📚 Updated: <strong>$updatedCount</strong> books with publisher, date, pages</p>";
    echo "<p>⏭️ Not found: <strong>$notFoundCount</strong> books</p>";
    echo "<p><a href='get_books.php'>View All Books</a></p>";

} catch (Exception $e) {
    echo "<h1>❌ Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>