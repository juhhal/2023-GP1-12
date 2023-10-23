<?php
session_start();

// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

$username = "";
$firstname = "";
$lastname = "";
$email = "";
$password = "";
$googleID = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(htmlspecialchars($_POST["username"]));   //Sanitize the input
    $firstname = htmlspecialchars($_POST["firstname"]);   //Sanitize the input
    $lastname = htmlspecialchars($_POST["lastname"]);   //Sanitize the input
    $email = strtolower($_POST["email"]);
    $password = sha1(htmlspecialchars($_POST["password"]));   //Sanitize the input

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);   //Sanitize the input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = 'Invalid email address.';
    } else {

        $findemail = $Usercollection->findOne(['email' => $email]);
        $findusername = $Usercollection->findOne(['username' => $username]);

        if ($findemail) {

            echo json_encode(['status' => 'error', 'message' => 'Your are already registred! Try to login']);
            exit();
        
        } else if ($findusername) {

            echo json_encode(['status' => 'error', 'message' => 'That username is taken. Try another one.']);
            exit();
        
        } else {

            $newUser = ['google_user_id' => null, 'username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password, 'files_count' => 0, 'file_names' => ""];
            $result = $Usercollection->insertOne($newUser);

            if ($result) {

                $_SESSION['email'] = $email;
                // Successful register
                require 'initialize_tools.php';
                echo json_encode(['status' => 'success']);
                exit();

            } else {
                echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again later.']);
            }
        }
    }
}
?>