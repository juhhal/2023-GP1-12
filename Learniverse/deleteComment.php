<?php

session_start();

use MongoDB\BSON\ObjectID;

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to delete data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
$bulk = new MongoDB\Driver\BulkWrite;

// Define the filter to identify the document
$filter = [
    '_id' => new MongoDB\BSON\ObjectId($_GET['commentID']),
];
$post_id = $_GET['postID'];

// Remove the task using the filter
$bulkWrite->delete(
    $filter
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.comments', $bulkWrite);
$bulk->update(['_id' => new ObjectID($post_id)], ['$inc' => ['comments' => -1]]);

//redirect to the page with the response message
if ($result->getDeletedCount() > 0) {
    $dec = $manager->executeBulkWrite('Learniverse.community', $bulk);
    header("Location: viewPost.php?postID=" . $post_id);
} else {
    echo ("Comment not found.");
}
