<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);

$foldersObject = $result_json[0]['folders'];

$name = $_POST['name'];
$foldersObject[$name] = new stdClass();

$bulk = new MongoDB\Driver\BulkWrite;

$bulk->update(
    ['email' => $_SESSION['email']],
    ['$set' => ['folders' => $foldersObject]],
    ['multi' => false, 'upsert' => false]
);

$result = $manager->executeBulkWrite('Learniverse.users', $bulk);
?>
