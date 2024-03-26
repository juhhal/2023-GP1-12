<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
 
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('sharedSpace');
$response;

// Function to delete the file from the database
function deleteFileFromDatabase($fileId, &$files, $usersCollection, $spaceID)
{
    foreach ($files as $key => $file) {
        if ($file->fileID === $fileId) {
            $deleteResult = $usersCollection->updateOne(
                ['spaceID' => $spaceID],
                ['$pull' => ['files' => ['fileID' => $fileId]]]
            );

            return $deleteResult->getModifiedCount() === 1;
        }
    }
    return false;
}

// Function to delete the file from the folder
function deleteFileFromFolder($filePath)
{
    $path = $filePath;
    return unlink($path);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the fileId, filePath, and spaceID from the POST data
    $fileId = $_POST['fileId'];
    $filePath = $_POST['filePath'];
    $spaceID = $_POST['spaceID'];

    $userDocument = $usersCollection->findOne(['spaceID' => $spaceID]);
    $files = $userDocument->files;
    // Delete the file from the database and folder
    if (deleteFileFromDatabase($fileId, $files, $usersCollection, $spaceID) && deleteFileFromFolder($filePath)) {
        // File deletion successful
        $response = array(
            'status' => 'success',
            'message' => 'File deleted successfully.'
        );
    } else {
        // File deletion failed
        $response = array(
            'status' => 'error',
            'message' => 'Failed to delete file.'
        );
    }

    // Send the response as JSON
    echo json_encode($response);
}
