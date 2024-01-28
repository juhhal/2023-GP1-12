<?php

require 'session.php';
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
if (isset($_POST['spaceName']) && $_POST['spaceName'] != "") {
    // save space name received from POST
    $spaceName = $_POST['spaceName'];

    // Create space object
    $space = [
        'spaceID' => uniqid(),
        'name' => $spaceName,
        'admin' => $_SESSION['email'],
        'members' => [],
        'projects' => [],
        'feed' => [],
    ];
    $bulkWrite->insert($space);
} elseif (isset($_POST['spaceID']) && $_POST['spaceID']!= "") {
    $filter = ['spaceID' => $_POST['spaceID']];

    // Create an update query with the $push operator
    $updateQuery = ['$push' => ['members' => $_SESSION['email']]];

    // Create an update command
    $bulkWrite->update($filter, $updateQuery);
}

// Insert the document into the collection
$result = $manager->executeBulkWrite("Learniverse.space", $bulkWrite);

header("Location:sharedSpace.php");
