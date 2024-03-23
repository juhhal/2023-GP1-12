<?php
session_start();
require_once __DIR__ . '../../vendor/autoload.php';
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

$baseDirectory = "../user_files" . DIRECTORY_SEPARATOR . "{$user_id}" . DIRECTORY_SEPARATOR;
$defaultDir = 'Uploaded Files'; // Set the default directory here

$selectedDir = isset($_GET['directory']) ? $_GET['directory'] : $defaultDir;

// Remove any parent directory traversal attempts
$selectedDir = str_replace('..', '', $selectedDir);

$fullPath = realpath($baseDirectory . $selectedDir);

// Ensure that the fullPath is still within the baseDirectory
if ($fullPath === false || strpos($fullPath, realpath($baseDirectory)) !== 0) {
    echo "Invalid directory.";
    exit;
}

$files = scandir($fullPath);
$fileExists = false;

echo "<h2>Files</h2>";

foreach ($files as $file) {
    // Skip hidden files (files starting with a dot)
    if ($file[0] === '.') {
        continue;
    }

    // Use htmlspecialchars to prevent XSS attacks
    $safeFile = htmlspecialchars($selectedDir.DIRECTORY_SEPARATOR.$file, ENT_QUOTES, 'UTF-8');
    echo "<div class='fileItem' onclick='selectFile(\"$safeFile\")'>$file</div>";
    $fileExists = true;
}

// Check if no files were found and display a message
if (!$fileExists) {
    echo "<p>No files found in the '$selectedDir' directory.</p>";
}



?>
