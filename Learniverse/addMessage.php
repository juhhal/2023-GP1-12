<?php
require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;

// MongoDB connection details
$database = 'Learniverse';
$collection = 'sharedSpace';

// MongoDB connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$spaceID = $_POST['spaceID'];
$message = htmlspecialchars($_POST['message']);
$date = $_POST["date"];

// Username for the user
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

    // After saving the message to the database
    // Emit the new message to connected clients
    $messageData = [
        'writtenBy' => $username,
        'message' => $message,
        'date' => $date
    ];

    $clientMessage = json_encode($messageData);

    // Include the WebSocket server file only if the message is saved successfully
    if ($result->getModifiedCount() > 0) {
        require 'websocket-server.php';
    }
} else {
    echo json_encode(['message' => "failed"]);
}