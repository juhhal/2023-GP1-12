<?php
require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;

//MongoDB connection details

$database = 'Learniverse';
$collection = 'sharedSpace';

//MongoDB connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$spaceID = $_POST['spaceID'];
$message = $_POST['message'];
$date = date('Y-m-d \a\t\ H:i');

//Username for the user
$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$data = $cursor->toArray();
$username = $data[0]->username;

// Create message object
$chat = [
    "message" => $message,
    "date" => $date,
    "writtenBy" => $username
];

$bulkWrite = new BulkWrite();

// Update the document
$bulkWrite->update(
    ['spaceID' => $spaceID],
    ['$push' => ['feed' => $chat]]
);

// Insert the message
$result = $manager->executeBulkWrite("$database.$collection", $bulkWrite);

// Check if the update was successful
if ($result->getModifiedCount() > 0) {
    echo json_encode(['message' => $chat]);
} else {
    echo json_encode(['message' => "faild"]);
}
