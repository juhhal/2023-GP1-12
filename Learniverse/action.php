<?php

session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$query = new MongoDB\Driver\Query(array('email' => $_POST['email']));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('test.users', $query);

// Convert cursor to Array and print result
$emailCount = count($cursor->toArray());

if($emailCount == 0) {
    $data = [ 'message' => false ];
    echo json_encode($data);
}
else {
    $query = new MongoDB\Driver\Query(array('password' => $_POST['password']));
    
    $cursor = $manager->executeQuery('Learniverse.users', $query);
    
    $passwordCount = count($cursor->toArray());

    if($passwordCount == 0) {
        $data = [ 'message' => false ];
        echo json_encode($data);
    }
    else {
        $data = [ 'message' => true ];
        echo json_encode($data);
        $_SESSION['email'] = $_POST['email'];
        header('Location: Workspace.html');
        exit();
    }
}