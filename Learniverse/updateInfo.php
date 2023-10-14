<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

if(isset($_POST['edit'])){

    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $hidden_id = $_POST['hidden_id'];
}

    $document = array('$set' => array(

        "firstname" => $firstname,
        "lastname" => $lastname,
    ));

    $update = $Usercollection -> updateOne(['_id' => new \MongoDB\BSON\ObjectID($hidden_id)], $document);

    if($update){
        header('Location: profile.php');
    }
    else{
        echo "Failed to update the profile.";
    }
?>