<?php

require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\BulkWriteException;

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Create a bulk write operation
$bulkWrite = new BulkWrite();

//get the post id from the ajax request
$postID = $_POST['postID'];
$action = $_POST['action'];

// Retrieve the user's ID from the session
$userId = $_SESSION['email'];


// Create a Query instance to retrieve the MongoDB document
$query = new MongoDB\Driver\Query(['_id' => new ObjectID($postID)]);

// Execute the query and retrieve the MongoDB document
$document = $manager->executeQuery('Learniverse.community', $query)->toArray()[0];

$msg = "";
if ($userId === $document->author) {
    $msg = "AUTHOR";
} elseif ($action === 'likes') {
    if (in_array($userId, $document->likedBy)) {
        // User has already liked the post, cancel the like
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$pull' => ['likedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['likes' => -1]]);
        $msg = "UN-LIKED";
    } elseif (in_array($userId, $document->dislikedBy)) {
        // User has disliked the post, cancel the dislike and add the like
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$pull' => ['dislikedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$addToSet' => ['likedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['likes' => 1, 'dislikes' => -1]]);
        $msg = "DISLIKED -> LIKED";
    } else {
        // User has not liked or disliked the post, add the like
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$addToSet' => ['likedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['likes' => 1]]);
        $msg = "LIKED";
    }
} elseif ($action === 'dislikes') {
    if (in_array($userId, $document->dislikedBy)) {
        // User has already disliked the post, cancel the dislike
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$pull' => ['dislikedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['dislikes' => -1]]);
        $msg = "UN-DISLIKED";
    } elseif (in_array($userId, $document->likedBy)) {
        // User has liked the post, cancel the like and add the dislike
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$pull' => ['likedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$addToSet' => ['dislikedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['dislikes' => 1, 'likes' => -1]]);
        $msg = "LIKED -> DISLIKED";
    } else {
        // User has not liked or disliked the post, add the dislike
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$addToSet' => ['dislikedBy' => $userId]]);
        $bulkWrite->update(['_id' => new ObjectID($postID)], ['$inc' => ['dislikes' => 1]]);
        $msg = "DISLIKED";
    }
}

// Execute the bulk write operation
try {
    if ($msg != "AUTHOR")
        $manager->executeBulkWrite('Learniverse.community', $bulkWrite);
    echo $msg;
} catch (Exception $e) {
    echo "Error executing bulk write operation: " . $e->getMessage();
}


// // Increment the "likes" field by 1
// $bulkWrite->update(
//     ['_id' => new ObjectId($postID)],
//     ['$inc' => [$action => 1]]
// );

// try {
//     // Execute the bulk write operation
//     $result = $manager->executeBulkWrite("Learniverse.community", $bulkWrite);

//     if ($result->getModifiedCount() > 0) {
//         echo "$action incremented successfully.";
//     } else {
//         echo "No document found with the specified ObjectId.";
//     }
// } catch (BulkWriteException $e) {
//     echo "Error: " . $e->getMessage();
// }