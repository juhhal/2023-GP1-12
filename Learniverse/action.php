<?php
session_start();

// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;
$Admincollection = $database->admins;

// Initialize variables
$email = "";
$password = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower($_POST["email"]);
    $password = sha1(htmlspecialchars($_POST["password"]));

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['message' => "Invalid email address "]);
        exit();
    } else {
        // Perform login validation
        $admin = $Admincollection->findOne(['email' => $email, 'password' => $password]);
        $user = $Usercollection->findOne(['email' => $email, 'password' => $password]);

        if ($admin) {
            $_SESSION['email'] = $admin['email'];
            // Successful login
            echo json_encode([
                'message' => "success",
                'type' => 'admin'
            ]);
            exit();
        } elseif ($user) {
            $_SESSION['email'] = $user['email'];
            // Successful login
            echo json_encode([
                'message' => "success",
                'type' => 'user'
            ]);
            exit();
        } else {
            // Invalid email or password
            echo json_encode(['message' => "Incorrect email address or password"]);
            exit();
        }
    }
}
