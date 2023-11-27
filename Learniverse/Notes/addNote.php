<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$userData = json_decode(json_encode($result_array[0]), true);

$foldersObject = $userData['folders']; 

$title = $_POST['title'];
$content = $_POST['content'];
$folder = empty($_POST['folder']) ? 'Notes' : $_POST['folder'];
$userEmail = $_SESSION['email'];
$date = date('Y-m-d H:i:s');

if (!isset($foldersObject[$folder])) {
    $foldersObject[$folder] = ['notes' => []];
}

$newNote = [
    'title' => $title,
    'content' => $content,
    'date' => $date,
    'folder' => $folder,
    'userEmail' => $userEmail,
    'id' => uniqid()
];

$foldersObject[$folder]['notes'][] = $newNote;

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['email' => $_SESSION['email']],
    ['$addToSet' => ['folders.' . $folder . '.notes' => $newNote]],
    ['multi' => false, 'upsert' => false]
);

$result = $manager->executeBulkWrite('Learniverse.users', $bulk);



if ($result->getModifiedCount() > 0) {
    $response = array(
        'success' => true,
        'message' => 'Note added successfully',
        'noteId' => $newNote['id']
    );
    echo json_encode($response);
    exit();
    
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add note']);
}

?>
