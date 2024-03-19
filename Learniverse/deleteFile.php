<?php


session_start();
require 'jwt.php';
require_once __DIR__ . '/vendor/autoload.php';
// Check if the request is made through POST and if the action is 'deleteFile'
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('users');

// Get the email from the session
$email = $_SESSION['email'];

// Query the database for the user
$userDocument = $usersCollection->findOne(['email' => $email]);

// If user found, retrieve the _id
$user_id = null;
if ($userDocument) {
    $user_id = $userDocument->_id;
}

// Now $user_id contains the _id field for the user with the specified email

// Create directory path with user ID
$userDirectory = "user_files".$DIRECTORY_SEPARATOR."{$user_id}";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteFile') {
    // Check if filePath is provided
    if (isset($_POST['filePath'])) {
        $filePath = $_POST['filePath'];

        // Check if the file exists
        if (file_exists($filePath)) {
            // Attempt to delete the file
            if (unlink($filePath)) {
                // File deleted successfully
                echo json_encode(array('status' => 'success', 'message' => 'File deleted successfully.'));
                exit;
            } else {
                // Failed to delete file
                echo json_encode(array('status' => 'error', 'message' => 'Failed to delete file.'));
                exit;
            }
        } else {
            // File not found
            echo json_encode(array('status' => 'error', 'message' => 'File not found.'));
            exit;
        }
    } else {
        // filePath parameter is missing
        echo json_encode(array('status' => 'error', 'message' => 'File path is missing.'));
        exit;
    }
} else {
    // Invalid request
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request.'));
    exit;
}
?>

