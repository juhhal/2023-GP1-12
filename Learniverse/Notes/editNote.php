<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$folderName = $_POST['folderName'];
$noteId = $_POST['noteId']; 
$content = $_POST['content'];
$title = $_POST['title'];

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['name' => $folderName, 'notes.id' => $noteId],
    ['$set' => [
        'notes.$.title' => $title,
        'notes.$.content' => $content,
        'notes.$.date' => date("Y-m-d H:i:s"),
    ]],
    ['multi' => false, 'upsert' => false]
);

$manager->executeBulkWrite('Learniverse.doc', $bulk);

// Return a success message or any other response
echo json_encode(['message' => 'Note updated successfully']);
?>
