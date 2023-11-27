<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);

$folders = $result_json[0]['folders'];

$folder = $_POST['folder'];
$noteId = $_POST['noteId']; 

if (isset($folders[$folder])) {
    $notes = $folders[$folder]['notes'];
    $noteIndex = array_search($noteId, array_column($notes, 'id'));

    if ($noteIndex !== false) {
        $newTitle = $_POST['title'];
        $newContent = $_POST['content'];

        $folders[$folder]['notes'][$noteIndex]['title'] = $newTitle;
        $folders[$folder]['notes'][$noteIndex]['content'] = $newContent;

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['email' => $_SESSION['email']],
            ['$set' => ['folders' => $folders]],
            ['multi' => false, 'upsert' => false]
        );

        $result = $manager->executeBulkWrite('Learniverse.users', $bulk);

        if ($result->getModifiedCount() > 0) {
          // send noteId
          $response = array(
              'success' => true,
              'message' => 'Note edited successfully',
              'noteId' => $noteId
          );
            echo json_encode($response);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to edit note']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Note not found in folder']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Folder not found']);
}
?>
