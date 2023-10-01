<?php

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// $collection = (new MongoDB\Driver\Client)->test->users;
$query = new MongoDB\Driver\Query(array('email' => $_POST['email']));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('test.users', $query);

// Convert cursor to Array and print result
$emailCount = count($cursor->toArray());

if($emailCount == 0) {
    $bulk = new MongoDB\Driver\BulkWrite;

    $document1 = ['username' => $_POST['username'],'firstname' => $_POST['firstname'], 'lastname' => $_POST['lastname'], 'email' => $_POST['email'], 'password' => $_POST['password']];
    
    $_id1 = $bulk->insert($document1);
    
    $result = $manager->executeBulkWrite('test.users', $bulk);
    
    $data = [ 'message' => true ];
    echo json_encode($data);

}else {
    $data = [ 'message' => false ];
    echo json_encode($data);
}
