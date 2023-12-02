<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$folderName = $_POST['folderName']??'Notes';
$content = $_POST['content'];
$title = $_POST['title'];
$date = date("Y-m-d H:i:s");

$bulk = new MongoDB\Driver\BulkWrite;

$noteId = uniqid();

$bulk->update(
    ['name' => $folderName],
    ['$push' => ['notes' => [
        'title' => $title,
        'content' => $content,
        'date' => $date,
        'id' => $noteId
    ]]],
    ['multi' => false, 'upsert' => false]
);

$manager->executeBulkWrite('Learniverse.doc', $bulk);

echo json_encode(['noteId' => $noteId]);
?>
