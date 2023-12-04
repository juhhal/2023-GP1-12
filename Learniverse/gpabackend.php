<?php

// Including the MongoDB PHP Library
require_once __DIR__ . '/vendor/autoload.php';

// Importing necessary classes from the MongoDB library
use MongoDB\Client;
use MongoDB\Driver\ServerApi;

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

// Function to insert a GPA document into the 'gpa' collection
function insertGPA($userId, $gpa, $type, $hours, $year)
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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["gpa"])) {
    $gpa = $_POST["gpa"];
    $type = $_POST["type"];
    $hours = $_POST["hours"];
    $year = $_POST["year"];
    $id = $_POST["id"];

    if ($id != -1) {
        // Updating the existing GPA record
        updateGPA($id, $gpa, $hours);
    } else {
        // Inserting a new GPA record
        insertGPA($user["_id"], $gpa, $type, $hours, $year);
    }

    // Returning true as a response
    echo true;
}
// Handling POST requests to retrieve all GPA records for a user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["getall"])) {
    // Retrieving all GPA records for the user
    $gpa = getAllGPA($user["_id"]);

    // Returning the JSON-formatted result
    echo $gpa;
}
