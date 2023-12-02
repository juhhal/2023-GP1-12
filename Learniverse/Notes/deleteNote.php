<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$folderName = $_POST['folderName'];
$noteId = $_POST['noteid']; 

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['name' => $folderName],
    ['$pull' => ['notes' => ['id' => $noteId]]], 
    ['multi' => false, 'upsert' => false]
);

$manager->executeBulkWrite('Learniverse.doc', $bulk);

?>
