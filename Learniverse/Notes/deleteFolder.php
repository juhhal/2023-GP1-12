<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$folderName = $_POST['name'];
$userEmail = $_SESSION['email'];

$bulk = new MongoDB\Driver\BulkWrite;

// Delete the folder from the Learniverse.doc collection
$bulk->delete(['name' => $folderName, 'user_email' => $userEmail]);

$manager->executeBulkWrite('Learniverse.doc', $bulk);

// Remove the folder from the user's folders in the Learniverse.users collection
$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['email' => $userEmail],
    ['$unset' => ["folders.$folderName" => ""]],
    ['multi' => false, 'upsert' => false]
);

$manager->executeBulkWrite('Learniverse.users', $bulk);
?>
