<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$folderName = $_POST['folderName'] ?? 'Notes';
$content = $_POST['content'];
$title = $_POST['title'];
$date = date("Y-m-d H:i:s");

$user_email = $_SESSION['email'];

if ($user_email) {
    $bulk = new MongoDB\Driver\BulkWrite;

    $noteId = uniqid();

    $filter = [
        'name' => $folderName,
        'user_email' => $user_email, 
    ];

    $update = [
        '$push' => [
            'notes' => [
                'title' => $title,
                'content' => $content,
                'date' => $date,
                'id' => $noteId
            ]
        ]
    ];

    $options = ['multi' => false, 'upsert' => false];

    $bulk->update($filter, $update, $options);

    $manager->executeBulkWrite('Learniverse.doc', $bulk);

    echo json_encode(['noteId' => $noteId]);
} else {
    echo json_encode(['error' => 'User email not found in session']);
}
?>