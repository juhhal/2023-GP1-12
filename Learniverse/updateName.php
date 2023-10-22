<?php
session_start();

$newName = htmlspecialchars($_POST['Rename']);

// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

$firstname = '';
$lastname = '';
$spaceIndex = strpos($newName, ' ');

if ($spaceIndex !== false) {
    $firstname =  substr($newName, 0, $spaceIndex);
    $lastname = substr($newName, $spaceIndex + 1);
}
else {
    $firstname = $newName;
}

// Specify the filter criteria to match the document you want to update
$filter = [
    'email' => $_SESSION['email'],
];
// Specify the update operations you want to perform
$update = [
    '$set' => [
        'firstname' => $firstname,
        'lastname' => $lastname
    ]
];

//execute the update command
$result = $Usercollection->updateOne(
    $filter,
    $update
);

//check the result
if ($result->getModifiedCount() > 0) {
    echo "Data updated successfully.";
} else {
    echo "No documents matched the filter criteria.";
}

// Redirect to the page
// Redirect to the page
if ($_GET['q'] === "thefiles.php") {
    header("Location: thefiles.php?q=My Files");
} else if ($_GET['q'] === "workspace.php") {
    header("Location: workspace.php");
} else header("Location: index.php");

?>