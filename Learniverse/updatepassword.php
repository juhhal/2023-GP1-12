<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// $collection = (new MongoDB\Driver\Client)->test->users;
$query = new MongoDB\Driver\Query(array('email' => $_POST['email']));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$userCursor = $manager->executeQuery('Learniverse.users', $query);
$adminCursor = $manager->executeQuery('Learniverse.admins', $query);

// Convert cursor to Array and print result
$UseremailCount = count($userCursor->toArray());
$AdminemailCount = count($adminCursor->toArray());

if ($UseremailCount == 0) {
    if ($AdminemailCount == 0) {

        $data = ['message' => true];
        echo json_encode($data);
    } else {
        $bulk = new MongoDB\Driver\BulkWrite;

        $bulk->update(
            ['email' => $_POST['email']],
            ['$set' => ['password' => sha1($_POST['password'])]],
            ['multi' => false, 'upsert' => false]
        );


        $result = $manager->executeBulkWrite('Learniverse.admins', $bulk);

        $data = ['message' => false];
        echo json_encode($data);
    }
} else {
    $bulk = new MongoDB\Driver\BulkWrite;

    $bulk->update(
        ['email' => $_POST['email']],
        ['$set' => ['password' => sha1($_POST['password'])]],
        ['multi' => false, 'upsert' => false]
    );


    $result = $manager->executeBulkWrite('Learniverse.users', $bulk);

    $data = ['message' => false];
    echo json_encode($data);
}
