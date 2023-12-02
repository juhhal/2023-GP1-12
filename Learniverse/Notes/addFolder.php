<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$name = $_POST['name'];
$userEmail = $_SESSION['email'];

// Validate if the folder already exists
$filter = ['name' => $name, 'user_email' => $userEmail];
$query = new MongoDB\Driver\Query($filter);
$cursor = $manager->executeQuery('Learniverse.doc', $query);

if (iterator_count($cursor) > 0) {
    // Folder with the same name already exists, return an error message
    echo json_encode(['error' => 'Folder with the same name already exists']);
    exit();
}

$newFolder = [
    'name' => $name,
    'notes' => [],
    'user_email' => $userEmail
];

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->insert($newFolder);

$manager->executeBulkWrite('Learniverse.doc', $bulk);

$foldersObject[$name] = new stdClass();

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['email' => $_SESSION['email']],
    ['$set' => ['folders' => $foldersObject]],
    ['multi' => false, 'upsert' => false]
);

$manager->executeBulkWrite('Learniverse.users', $bulk);

// Return a success message if the folder is added successfully
echo json_encode(['success' => 'Folder added successfully']);
?>
