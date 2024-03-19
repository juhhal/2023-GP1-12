<?php

// Including the MongoDB PHP Library
require_once __DIR__ . '/vendor/autoload.php';
require "session.php";

// Importing necessary classes from the MongoDB library
use MongoDB\Client;
use MongoDB\Driver\ServerApi;
use MongoDB\Driver\BulkWrite;
// MongoDB connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$bulk = new MongoDB\Driver\BulkWrite();

// MongoDB Atlas connection URI
$uri = "mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/";

// Creating a ServerApi instance
$apiVersion = new ServerApi(ServerApi::V1);

// Creating a MongoDB client with connection options and ServerApi configuration
$client = new MongoDB\Client($uri, [], ['serverApi' => $apiVersion]);

try {
    // Checking if the MongoDB server is reachable
    $client->selectDatabase('admin')->command(['ping' => 1]);
} catch (Exception $e) {
    // Printing an error message if the server is not reachable
    printf($e->getMessage());
}

// Function to retrieve user information based on email
function getUser($email)
{
    global $client;
    try {
        // Selecting the 'users' collection from the 'Learniverse' database
        $collection = $client->selectCollection('Learniverse', 'users');

        // Building the query to find a user by email
        $query = ['email' => $email];

        // Executing the query and retrieving the user document
        $user = $collection->findOne($query);

        if ($user) {
            // Converting the MongoDB cursor to an associative array
            $userArray = iterator_to_array($user);
            return $userArray;
        } else {
            return null;
        }
    } catch (Exception $e) {
        // Printing an error message if an exception occurs
        printf($e->getMessage());
        return null;
    }
}
$user = getUser($_SESSION['email']);
// Function to insert a GPA document into the 'gpa' collection
function insertGPA($userId, $gpa, $type, $year, $semesters)
{
    global $client;
    try {
        // Selecting the 'gpa' collection from the 'Learniverse' database
        $collection = $client->selectCollection('Learniverse', 'gpa');

        // Creating a document to insert into the collection
        $document = [
            'user_id' => $userId,
            'gpa' => $gpa,
            'type' => $type,
            'year' => $year,
            'semesters' => $semesters
        ];

        // Inserting the document into the collection
        $insertResult = $collection->insertOne($document);

        if ($insertResult->getInsertedCount() === 1) {
            // Returning the inserted document's ID on success
            return $insertResult->getInsertedId();
        } else {
            // Returning -1 on failure
            return -1;
        }
    } catch (Exception $e) {
        // Printing an error message if an exception occurs
        printf($e->getMessage());
        return -1;
    }
}

// Function to retrieve all GPA documents for a user
function getAllGPA($userId)
{
    global $client;
    try {
        // Selecting the 'gpa' collection from the 'Learniverse' database
        $collection = $client->selectCollection('Learniverse', 'gpa');

        // Sorting the documents by date in descending order
        $sort = ['year' => -1];
        $filter = [
            'user_id' => $userId,
        ];

        $cursor = $collection->find($filter, ['sort' => $sort]);

        // Converting the MongoDB cursor to an associative array
        $allGPA = iterator_to_array($cursor);

        // Converting the array to a JSON-formatted string with pretty printing
        $jsonResult = json_encode($allGPA, JSON_PRETTY_PRINT);

        return $jsonResult;
    } catch (Exception $e) {
        // Printing an error message if an exception occurs
        printf($e->getMessage());
        return null;
    }
    exit();
}

// Function to update an existing GPA document
function updateGPA($id, $gpa, $hours)
{
    global $client;
    $collection = $client->selectCollection('Learniverse', 'gpa');

    // Finding the existing document by ID
    $existingDocument = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($id)]);

    if ($existingDocument) {
        // Extracting values from the existing document
        $oldGPA = $existingDocument['gpa'];
        $oldHours = $existingDocument['hours'];

        // Calculating new GPA based on the weighted average
        $totalhours = $oldHours + $hours;

        // Updating the existing document with the new GPA and hours
        $result = $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($id)],
            [
                '$set' => [
                    'gpa' => $gpa,
                    'hours' => $totalhours
                ]
            ]
        );

        if ($result->getModifiedCount() > 0) {
            // Returning true if the document is successfully updated
            echo "k";
            return true;
        } else {
            // Returning false if the document is not modified
            echo "f";
            return false;
        }
    } else {
        // Returning false if the document is not found
        echo "j";
        return false;
    }
}

// Checking if a session is not already started and starting one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieving user information if an email is stored in the session
$user = null;
if (isset($_SESSION["email"])) {
    $user = getUser($_SESSION["email"]);
}

// Redirecting to the login page if the user is not authenticated
if ($user == null) {
    header("Location: index.php");
    exit;
}

// Handling POST requests to retrieve all GPA records for a user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["getall"])) {
    // Retrieving all GPA records for the user
    $gpa = getAllGPA($user["_id"]);

    // Returning the JSON-formatted result
    echo $gpa;
}

//ADD NEW GPA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['addNewGpa']) && $_POST['addNewGpa']) {
    echo("<script>alert(PHP: Add);</script>");
    $gpa = $_POST['gpa'];
    $type = $_POST['gpaType'];
    $name = $_POST['gpaName'];
    $year = $_POST['gpaYear'];
    end($_POST);
    prev($_POST);
    prev($_POST);
    $lastInputKey = key($_POST);
    preg_match_all('/Sem(\d+)Sub(\d+)/', $lastInputKey, $matches);
    $numSem = $matches[1][0];
    $numSub = $matches[2][0];
    $semesters = [];
    $hours = 0;

    for ($i = 1; $i <= $numSem; $i++) {
        if (isset($_POST["subjectNameSem$i" . "Sub1"])) {
            $semester = []; // Create an empty array to store subjects for the current semester

            for ($j = 1; $j <= count($_POST); $j++) {
                if (isset($_POST["subjectNameSem$i" . "Sub$j"])) {
                    $subject = [
                        'name' => $_POST["subjectNameSem$i" . "Sub$j"],
                        'marks' => $_POST["marksSem$i" . "Sub$j"],
                        'hours' => $_POST["hoursSem$i" . "Sub$j"],
                        'grade' => $_POST["gradeSem$i" . "Sub$j"],
                        'points' => $_POST["pointsSem$i" . "Sub$j"]
                    ];
                    if ($type != 100)
                        $hours += $_POST["hoursSem$i" . "Sub$j"];

                    $semester[] = $subject; // Add subject to the current semester's subjects array
                }
            }
            $semesters[] = $semester; // Assign the semester object to the current semester
        }
    }
    // Insert the document into the collection
    $newgpa = [
        'user_id' => $user['_id'],
        'name' => $name,
        'gpa' => $gpa,
        'type' => $type,
        'hours' => $hours,
        'year' => $year,
        'semesters' => $semesters
    ];
    $bulk->insert($newgpa);
    $result = $manager->executeBulkWrite("Learniverse.gpa", $bulk);

    if ($result->getInsertedCount() > 0) {
        echo json_encode('success');
    } else {
        echo json_encode('fail');
    }
    getAllGPA($user['_id']);
}


//DELETE GPA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteGPA'])) {
    $id = $_POST['id'];
    $filter = [
        '_id' => new MongoDB\BSON\ObjectId($id)
    ];

    global $client;
    $collection = $client->selectCollection('Learniverse', 'gpa');
    // Delete the document
    $deleteResult = $collection->deleteOne($filter);

    // Check if the deletion was successful
    if ($deleteResult->getDeletedCount() > 0) {
        echo 'Document deleted successfully';
    } else {
        echo 'Document not found or deletion failed';
    }
}

//update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['updateGpa']) && $_POST['updateGpa']) {
    $gpaID = $_POST['id'];
    $gpaNew = $_POST['gpa'];
    $gpaName = $_POST['gpaName'];
    $gpaYear = $_POST['gpaYear'];        
    $type = $_POST['gpaType'];
  
 end($_POST);            
    prev($_POST);    
    prev($_POST);    
    prev($_POST);    
    prev($_POST);    
    prev($_POST);    
    $lastInputKey = key($_POST);
    echo("('PHP: lastInputKey " . $lastInputKey . "')");    
    preg_match_all('/Sem(\d+)Sub(\d+)/', $lastInputKey, $matches);
    
    $numSem = $matches[1][0];
    $numSub = $matches[2][0];
   $semesters = [];
    $hours = 0;
    echo("('PHP:numSem " . $numSem . "')");    
    echo("('PHP:numSub " . $numSub . "')");    
    for ($i = 1; $i <= $numSem; $i++) {
        if (isset($_POST["subjectNameSem$i" . "Sub1"])) {
            $semester = []; // Create an empty array to store subjects for the current semester

            for ($j = 1; $j <= count($_POST); $j++) {
                if (isset($_POST["subjectNameSem$i" . "Sub$j"])) {
                    $subject = [
                        'name' => $_POST["subjectNameSem$i" . "Sub$j"],
                        'marks' => $_POST["marksSem$i" . "Sub$j"],
                        'hours' => isset($_POST["hoursSem$i" . "Sub$j"])? $_POST["hoursSem$i" . "Sub$j"]  : 0 ,
                        'grade' => $_POST["gradeSem$i" . "Sub$j"],
                        'points' => isset($_POST["pointsSem$i" . "Sub$j"]) ? $_POST["pointsSem$i" . "Sub$j"]  : 0
                    ];
                    if ($type != 100)
                        $hours += $_POST["hoursSem$i" . "Sub$j"];

                    $semester[] = $subject; // Add subject to the current semester's subjects array
                }
            }
            $semesters[] = $semester; // Assign the semester object to the current semester
        }
    }

    // Define the collection where the document resides
    $collection = $client->Learniverse->gpa;

    // Define the query criteria
    $filter = [
        "_id" => new MongoDB\BSON\ObjectId($gpaID)
    ];
    // Define the update operation
    $update = ['$set' => ['semesters' => $semesters, 'gpa' => $gpaNew, 'name' => $gpaName, 'year' => $gpaYear, 'hours' => $hours]];

    // Find the document and update it
    $result = $collection->findOneAndUpdate($filter, $update);

    // Check if the update was successful
    if ($result) {
        echo "Document updated successfully.";
    } else {
        echo "Failed to update the document.";
    }
    getAllGPA($user['_id']);
}