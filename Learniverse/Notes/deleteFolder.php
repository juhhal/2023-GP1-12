<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Retrieve the user's document
$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);

$folders = $result_json[0]['folders'];

$folderId = $_POST['name'];
if (isset($folders[$folderId])) {
    unset($folders[$folderId]);

    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['email' => $_SESSION['email']],
        ['$set' => ['folders' => $folders]],
        ['multi' => false, 'upsert' => false]
    );

    $result = $manager->executeBulkWrite('Learniverse.users', $bulk);

    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Folder deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete folder']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Folder not found']);
}
?>
