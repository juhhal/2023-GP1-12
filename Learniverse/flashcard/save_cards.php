<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('users');

$email = $_SESSION['email'];

$userDocument = $usersCollection->findOne(['email' => $email]);

$user_id = null;
if ($userDocument) {
    $user_id = $userDocument->_id;
}

// Directory path with user ID
$userDirectory = "user_files" . DIRECTORY_SEPARATOR . "{$user_id}" . DIRECTORY_SEPARATOR . "Flashcards";

// Check if the directory exists, if not create it
if (!is_dir($userDirectory)) {
    // true flag for recursive creation, 0777 for widest possible access permissions
    // Adjust permissions as needed for your security requirements
    if (!mkdir($userDirectory, 0777, true)) {
        die("Failed to create directories...");
    }
}

if (isset($_FILES['pdf'])) {
    $originalFileName = basename($_FILES['pdf']['name']);
    $targetFilePath = $userDirectory . DIRECTORY_SEPARATOR . $originalFileName;
    
    // Check if file already exists
    $counter = 0;
    $file_parts = pathinfo($originalFileName);
    while (file_exists($targetFilePath)) {
        $counter++;
        $newFileName = $file_parts['filename'] . " ($counter)." . $file_parts['extension'];
        $targetFilePath = $userDirectory . DIRECTORY_SEPARATOR . $newFileName;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetFilePath)) {
        echo "The file " . htmlspecialchars($originalFileName) . " has been uploaded.";
    } else {
        echo "There was an error uploading your file.";
    }
} else {
    echo "No file was uploaded.";
}
?>
