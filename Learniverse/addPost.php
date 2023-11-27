<?php
require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;
// MongoDB connection details
$database = 'Learniverse';
$collection = 'community';

// MongoDB connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Values from $_POST array
$postTitle = $_POST['postTitle'];
$postContent = $_POST['post_content'];
$postTagsString = $_POST['postTags'];
$postDateString = $_POST['postDate'];
$author = $_SESSION['email'];

// Split the tags string into an array and remove whitespace
$postTags = explode(',', $postTagsString);
$postTags = array_map('trim', $postTags);

$postDate = explode("T", $postDateString);

//check if ths is an update request by examining if postID is set
if (isset($_POST['postID'])) {
    // Create a bulk write instance for the update operation
    $bulkWrite = new BulkWrite();

    // Update the document
    $bulkWrite->update(
        ['_id' => new ObjectID($_POST['postID'])],
        ['$set' => ['title' => $postTitle, 'content' => $postContent, 'tags' => $postTags, 'edited' => true, 'dateEdited' => $postDate[0] . " at " . $postDate[1]]]
    );

    // Execute the bulk write operation
    $result = $manager->executeBulkWrite("$database.$collection", $bulkWrite);

    // Check if the update was successful
    if ($result->getModifiedCount() > 0) {
        echo 'Document updated successfully.';
    } else {
        echo 'No document updated.';
    }
    //
} else {
    // Create post object
    $post = [
        'title' => $postTitle,
        'content' => $postContent,
        'author' => $author,
        'tags' => $postTags,
        'posted_at' => $postDate[0] . " at " . $postDate[1],
        'likes' => 0,
        'dislikes' => 0,
        "likedBy" => [],
        "dislikedBy" => [],
        "comments" => 0,
        "edited" => false,
        "dateEdited" => ""
    ];

    // Prepare the MongoDB document
    $document = new MongoDB\Driver\BulkWrite();
    $document->insert($post);

    // Insert the document into the collection
    $manager->executeBulkWrite("$database.$collection", $document);
}
unset($_SESSION['filteredSearch']);
header("Location:community.php");
