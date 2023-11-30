<?php

require "session.php";
use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\BulkWriteException;

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to delete data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
$postID = $_GET['postID'];

// Create a bulk write operation
$bulkWrite = new BulkWrite();

// Add the delete operation for the specified document
$bulkWrite->delete(['_id' => new ObjectId($postID)]);

try {
    // Execute the bulk write operation
    $result = $manager->executeBulkWrite("Learniverse.community", $bulkWrite);
    
    if ($result->getDeletedCount() > 0) {
        echo "Document deleted successfully.";
        //delete any comments associated
        $filter = ['post_id' => $postID];
        $options = ['limit' => 0];

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->delete($filter, $options);

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $result = $manager->executeBulkWrite("Learniverse.comments", $bulk, $writeConcern);

    } else {
        echo "No document found with the specified ObjectId.";
    }
} catch (BulkWriteException $e) {
    echo "Error: " . $e->getMessage();
}

header("Location:community.php");
