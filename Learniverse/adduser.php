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

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(htmlspecialchars($_POST["username"]));
    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $email = strtolower($_POST["email"]);
    $password = $_POST["password"];
    if (strlen($_POST["password"]) < 12) {
        echo json_encode(['message' => "Password must be at least 12 character."]);
    } else if (!preg_match('/^[A-Za-z]/', $firstname)) {
        echo json_encode(['message' => "The first character of the first name must be alphabetical characters."]);
    } else if (!preg_match('/^[A-Za-z]/', $lastname)) {
        echo json_encode(['message' => "The first character of the last name must be alphabetical characters."]);
    }
        else {
        $password = sha1(htmlspecialchars($_POST["password"]));
        // Sanitize and validate email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['message' => "Invalid email address."]);
        } else {
            // Perform register validation
            $findemail = $Usercollection->findOne(['email' => $email]);
            $findusername = $Usercollection->findOne(['username' => $username]);
            if ($findemail) {
                echo json_encode(['message' => "Your are already registred! Try to login"]);
            } else if ($findusername) {
                echo json_encode(['message' => "That username is taken. Try another one."]);
            } else {
                $newUser = ['username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password, 'files_count' => 0, 'file_names' => ""];
                $result = $Usercollection->insertOne($newUser);

                if ($result) {
                    $_SESSION['email'] = $email;
                    require 'initialize_tools.php';
                    // Successful register
                    header("Location: workspace.php");
                    exit();
                } else {
                    echo json_encode(['message' => "Registration failed. Please try again later."]);
                }
            }
        }
    }
}

?>