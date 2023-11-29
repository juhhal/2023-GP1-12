<?php
session_start();

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Define the search term
$searchTerm = $_GET['searchTerm']; // Replace with your search term

// Initialize the filter
$filter = [];

// Check if the search term is based on user
if (strpos($searchTerm, "user:") === 0) {
    $searchValue = substr($searchTerm, strlen("user:"));
    // Construct the query to retrieve email from users collection

    // Define the query filter to retreive the users email using their username
    $filter = ['username' => $searchValue];

    // Define the projection to retrieve only the email field
    $projection = ['email' => 1];

    // Construct the query
    $query = new MongoDB\Driver\Query($filter, ['projection' => $projection]);

    // Execute the query
    $cursor = $manager->executeQuery("Learniverse.users", $query);

    // Retrieve the email value
    $email = null;
    foreach ($cursor as $document) {
        $email = $document->email;
        break; // we only want to retrieve the email from the first matching document
    }

    $filter = ['author' => $email];
} elseif (strpos($searchTerm, "likes:") === 0) { // Check if the search term is based on likes
    $minLikes = (int)substr($searchTerm, strlen('likes:'));
    $filter = ['likes' => ['$gte' => $minLikes]];
} elseif (strpos($searchTerm, "dislikes:") === 0) { // Check if the search term is based on dislikes
    $minDislikes = (int)substr($searchTerm, strlen('dislikes:'));
    $filter = ['dislikes' => ['$gte' => $minDislikes]];
} elseif (strpos($searchTerm, '[') === 0 && strpos($searchTerm, ']') === strlen($searchTerm) - 1) { // Check if the search term is based on tags
    // Search based on tags
    $tags = trim($searchTerm, '[]');
    $filter = ['tags' => $tags];
} else {
    // Search based on anything (title, author, tag)
    // Create a regular expression pattern for case-insensitive search
    $pattern = new \MongoDB\BSON\Regex($searchTerm, 'i');
    // Define the search criteria
    $filter = [
        '$or' => [
            ['title' => $pattern],
            ['author' => $pattern],
            ['tags' => $pattern]
        ]
    ];
}

// echo "<script>alert('".print_r($filter)."')</script>";

// Construct the query
$query = new MongoDB\Driver\Query($filter);

// Execute the query
$documents = $manager->executeQuery("Learniverse.community", $query);

$result = [];
// Output the results
foreach ($documents as $document) {
    $result[] = $document;
    // echo "Title: " . $document->title . "<br>";
    // echo "Tags: " . implode(', ', $document->tags) . "<br>";
    // echo "<br>";
}

$_SESSION['filteredSearch'] = json_encode($result);
// echo $_SESSION['filteredSearch'];
header("Location: community.php");
