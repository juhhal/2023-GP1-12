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
$dirs = array_filter(glob($baseDirectory . '*'), 'is_dir');

// Prioritize certain directories
$priorityDirs = array('Uploaded Files', 'Shared Spaces');

usort($dirs, function($a, $b) use ($priorityDirs) {
    $aName = basename($a);
    $bName = basename($b);
    $aPriority = array_search($aName, $priorityDirs);
    $bPriority = array_search($bName, $priorityDirs);

    if ($aPriority !== false && $bPriority !== false) {
        return $aPriority - $bPriority;
    } elseif ($aPriority !== false) {
        return -1;
    } elseif ($bPriority !== false) {
        return 1;
    } else {
        return strcasecmp($aName, $bName);
    }
});

echo "<h2>Folders</h2>";
foreach ($dirs as $dir) {
    $dirName = basename($dir);
    echo "<button class='dirButton' onclick='loadFiles(\"" . htmlspecialchars($dirName) . "\")'>" . htmlspecialchars($dirName) . "</button>";
}

?>
