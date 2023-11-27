<?php

session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to delete data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

// Define the filter to identify the document
$filter = [
    '_id' => new MongoDB\BSON\ObjectId($_GET['commentID']),
];
$postid = $_GET['postID'];

// Remove the task using the filter
$bulkWrite->delete(
    $filter );

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.comments', $bulkWrite);

//redirect to the page with the response message
if ($result->getDeletedCount() > 0) {
    header("viewPost.php?postID=" . $postid);
} else {
    echo("Comment not found.");
}
