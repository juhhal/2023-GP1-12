<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);

$folders = $result_json[0]['folders'];

$folder = $_POST['folder'];
$noteId = $_POST['noteid'];

if (isset($folders[$folder])) {
    $notes = $folders[$folder]['notes'];
    $noteIndex = array_search($noteId, array_column($notes, 'id'));

    if ($noteIndex !== false) {
        unset($folders[$folder]['notes'][$noteIndex]);

        $folders[$folder]['notes'] = array_values($folders[$folder]['notes']);

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            ['email' => $_SESSION['email']],
            ['$set' => ['folders' => $folders]],
            ['multi' => false, 'upsert' => false]
        );

        $result = $manager->executeBulkWrite('Learniverse.users', $bulk);

        if ($result->getModifiedCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete note']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Note not found in folder']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Folder not found']);
}
?>
