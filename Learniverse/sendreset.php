<?php
require 'jwt.php';

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
    $headers = array('alg'=>'HS256','typ'=>'JWT');
    $payload = array('email'=>$_POST['email'], 'exp'=>(time() + 36000));
    
    $jwttoken = generate_jwt($headers, $payload);
    
    mail($_POST['email'],'SEND LINK','https://check.conceptdesigngroup.in/passwordchange.php?token='.$jwttoken);

    $data = [ 'message' => false ];
    echo json_encode($data);
}
