<?php

require 'session.php';
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
if (isset($_POST['spaceName']) && $_POST['spaceName'] != "") {
    // save space name received from POST
    $spaceName = $_POST['spaceName'];
    $spaceID  = uniqid();
    // Create space object
    $space = [
        'spaceID' => $spaceID,
        'name' => $spaceName,
        'admin' => $_SESSION['email'],
        'members' => [],
        'pendingMembers' => [],
        'tasks' => [],
        'files' => [],
        'feed' => [],
        'logUpdates'=>[]
    ];
    $bulkWrite->insert($space);

    // Insert the document into the collection
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    $spaceCalendar =   [
        "user_id" => $spaceID,
        "counter" => 0,
        "List" => [
            []
        ]
    ];
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($spaceCalendar);
    $result = $manager->executeBulkWrite("Learniverse.calendar", $bulk);


} elseif (isset($_POST['spaceID']) && $_POST['spaceID'] != "") {

    // Define the query filter
    $filter = ['spaceID' => $_POST['spaceID']];

    // Create a query object
    $query = new MongoDB\Driver\Query($filter);

    // Execute the query
    $cursor = $manager->executeQuery('Learniverse.sharedSpace', $query);
    // Get the first document from the result
    $space = $cursor->toArray()[0];

    // Convert the document to an object
    $spaceObject = (object) $space;

    if ($spaceObject->admin === $_SESSION['email'])
        echo "youre this space admin";
    elseif (in_array($_SESSION['email'], $spaceObject->members))
        echo "youre already member of this space";
    elseif (in_array($_SESSION['email'], $spaceObject->pendingMembers))
        echo "You already requested to join this space. please wait admin approval";
    else {
        // Create an update query with the $push operator
        $updateQuery = ['$push' => ['pendingMembers' => $_SESSION['email']]];

        // Create an update command
        $bulkWrite->update($filter, $updateQuery);

        // Insert the document into the collection
        $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
        header("Location:sharedSpace.php");
    }
}

header("Location:sharedSpace.php");
