<?php

require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\Exception\BulkWriteException;

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

try {
    // Retrieve the complaint ID and status from the POST data
    $complaintID = $_POST['complaintID'];
    $status = $_POST['cstatus'];

    // Update the status of the complaint in the database
    $bulkWrite = new MongoDB\Driver\BulkWrite();
    $bulkWrite->update(
        ['complaintID' => $complaintID],
        ['$set' => ['status' => $status]],
        ['multi' => false]
    );

    $result = $manager->executeBulkWrite('Learniverse.complaint', $bulkWrite);

    if ($result->getModifiedCount() > 0) { // Check if any document was modified
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
