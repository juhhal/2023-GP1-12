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

// Handling POST requests to update or insert GPA records
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["xxxx"])) {

    // // Retrieve the data
    // $gpaName = $_POST['gpaName'];
    // $year = $_POST['year'];
    // $gpaType = $_POST['gpaType'];
    // $semesterCount = $_POST['semesterCount'];
    // echo $gpaType;
    // // Access the semester data
    // for ($i = 1; $i <= $semesterCount; $i++) {
    //     $subjectNames = $_POST['subjectName' . $i];
    //     $marks = $_POST['marks' . $i];
    //     $hours = $_POST['hours' . $i];
    //     $grades = $_POST['grade' . $i];
    // }



    // $gpa = $_POST["gpa"]; //?
    // $type = $_POST["type"];
    // $hours = $_POST["hours"];
    // $year = $_POST["year"];
    // $id = $_POST["id"];

    // if ($id != -1) {
    //     // Updating the existing GPA record
    //     updateGPA($id, $gpa, $hours);
    // } else {
    //     // Inserting a new GPA record
    //     insertGPA($user["_id"], $gpa, $type, $hours, $year);
    // }

    // // Returning true as a response
    // echo true;
}
// Handling POST requests to retrieve all GPA records for a user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["getall"])) {
    // Retrieving all GPA records for the user
    $gpa = getAllGPA($user["_id"]);

    // Returning the JSON-formatted result
    echo $gpa;
}

//ADD NEW GPA
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['gpaName'])) {
    $gpa = $_POST['gpa'];
    $type = $_POST['gpaType'];
    $name = $_POST['gpaName'];
    $year = $_POST['gpaYear'];
    end($_POST);
    prev($_POST);
    $lastInputKey = key($_POST);
    preg_match_all('/Sem(\d+)Sub(\d+)/', $lastInputKey, $matches);
    $numSem = $matches[1][0];
    $numSub = $matches[2][0];
    // $logMessage = $numSem;
    // $jsCode = sprintf('<script>console.log("%s");</script>', $logMessage);
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

    if ($result->isAcknowledged()) {
        $logMessage = "success.";
        $jsCode = sprintf('<script>console.log("%s");</script>', $logMessage);
        echo $jsCode;
        echo json_encode('success');
    } else {
        echo json_encode('fail');
        $logMessage = "fail";
        $jsCode = sprintf('<script>console.log("%s");</script>', $logMessage);
        echo $jsCode;
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
