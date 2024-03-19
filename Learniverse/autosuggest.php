<?php

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Get the search term from the query parameter
$searchTerm = $_GET['searchTerm'];
$filter = "";
$type = "";
$tags = "";
$searchTerm = trim($searchTerm);
if ($searchTerm == "") {
    exit;
}
// Check if the search term starts with '['
if (strpos($searchTerm, '[') === 0) {
    // Autosuggest based on tags
    $type = "tags";
    $tags = trim($searchTerm, '[]');
    $filter = ['tags' => new MongoDB\BSON\Regex($tags, 'i')];
    $projection = ['tags' => 1];
} elseif (strpos($searchTerm, 'user:') === 0) {
    // Autosuggest based on authors
    $type = "author";
    $author = substr($searchTerm, strlen('user:'));
    $filter = ['author' => new MongoDB\BSON\Regex($author, 'i')];
    $projection = ['author' => 1];
} else {
    // Autosuggest based on titles
    $type = "title";
    $filter = ['title' => new MongoDB\BSON\Regex($searchTerm, 'i')];
    $projection = ['title' => 1];
}

// Construct the query
$query = new MongoDB\Driver\Query($filter, ['projection' => $projection]);

// Execute the query
$cursor = $manager->executeQuery("Learniverse.community", $query);

// Retrieve the values and store them in an array
$values = [];
if ($type == "tags") {
    // Retrieve the unique values and store them in an array
    $values = [];
    $seen = []; // Keep track of seen tags
    foreach ($cursor as $document) {
        $tag = $document->tags;
        // if(!in_array($tag, $tags))
        if (!in_array($tag, $seen)) {
            $values[] = $tag;
            $seen[] = $tag;
        }
    }
} else foreach ($cursor as $document) {
    $values[] = $document->$type;
}

// Return the values as JSON response
echo json_encode($values);
