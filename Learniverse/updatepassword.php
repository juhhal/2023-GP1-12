<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// $collection = (new MongoDB\Driver\Client)->test->users;
$query = new MongoDB\Driver\Query(array('email' => $_POST['email']));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('Learniverse.users', $query);

// Convert cursor to Array and print result
$emailCount = count($cursor->toArray());

if($emailCount == 0) {
    
    $data = [ 'message' => true ];
    echo json_encode($data);

}else {
    $bulk = new MongoDB\Driver\BulkWrite;
    
    $bulk->update(
        ['email' => $_POST['email']],
        ['$set' => ['password' => sha1($_POST['password'])]],
        ['multi' => false, 'upsert' => false]
    );


    $result = $manager->executeBulkWrite('Learniverse.users', $bulk);
    
    if(isset($_SESSION['email'])){
        header("Location: index.html");
    }
    $data = [ 'message' => false ];
    echo json_encode($data);
}
