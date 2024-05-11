<?php

require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\BulkWriteException;

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

try {
    $bulkWrite = new BulkWrite();
    $complaintID = $_POST['complaintID'];

    // Add the delete operation for the specified document
    $bulkWrite->delete(['complaintID' => $complaintID]);

    // Execute the bulk write operation
    $result = $manager->executeBulkWrite("Learniverse.complaint", $bulkWrite);

    if ($result->getDeletedCount() > 0) {
        $response = array(
            'status' => 'success'
        );
    } else {
        $response = array(
            'status' => 'failed'
        );
    }
} catch (BulkWriteException $e) {
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
}

// Convert the response array to JSON and send it as the response
header('Content-Type: application/json');
echo json_encode($response);