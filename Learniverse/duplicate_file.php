<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$spaceCollection = $database->selectCollection('sharedSpace');
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the fileId, filePath, and spaceID from the POST data
    $fileId = $_POST['fileID'];
    $fileName = $_POST['fileName'];

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

    // Specify the original PDF file path
    $originalFilePath = 'FILES/' . $fileId . '_' . $fileName;

    // Create the destination directory path with user ID
    $destinationDirectory = "user_files" . DIRECTORY_SEPARATOR . "{$user_id}" . DIRECTORY_SEPARATOR. "Shared Spaces" . DIRECTORY_SEPARATOR;
    $destinationFilePath = $destinationDirectory . $fileName;

    // Copy the file
    if (copy($originalFilePath, $destinationFilePath)) {
        // File duplication successful
        $response = array(
            'status' => 'success',
            'message' => 'File saved successfully.'
        );
    } else {
        // File duplication failed
        $response = array(
            'status' => 'error',
            'message' => 'Failed to save file.'
        );
    }

    // Send the response as JSON
    echo json_encode($response);
}
?>