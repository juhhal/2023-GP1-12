<?php
// Start the session
session_start();
// Include the MongoDB client library
require_once __DIR__ . '/vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $client->selectDatabase('Learniverse');
$summariesCollection = $database->selectCollection('Quizzes');

// Check if the 'date' POST variable is set
if (isset($_POST['date'])) {

    // Extract 'date_created' and 'userId' from POST data and session
    $dateCreated = (int)$_POST['date']; // Cast to int if it's sent as a string
    $userId = $_SESSION['email'];

    // Update criteria to match the document by 'userId'
    $updateCriteria = [
        'userId' => $userId
    ];

    // Update operation to remove the quiz with the specified 'date_created'
    $updateResult = $summariesCollection->updateOne(
        $updateCriteria,
        [
            '$pull' => [
                'quizzes' => [
                    'date_created' => $dateCreated
                ]
            ]
        ]
    );

    // Check if the update operation was successful
    if ($updateResult->isAcknowledged() && $updateResult->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Quiz deleted successfully']);
    } else {
        echo json_encode(['error' => 'Quiz deletion failed or quiz not found']);
    }
}



header('Content-Type: application/json');
session_start(); // Start the session if not already started

if (isset($_POST['datas'])) {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
    $database = $client->selectDatabase('Learniverse');
    $summariesCollection = $database->selectCollection('Quizzes');

    // Get the user ID from the session and date_created from POST data
    $userId = $_SESSION['email']; // Ensure the session has started and the email is set
    $dateCreated = intval($_POST['datas']);

    // Query the database for the summary
    $summaryDocument = $summariesCollection->findOne([
        'userId' => $userId
    ]);

    // Check if a document was found
    if ($summaryDocument) {
        // Convert BSONDocument to an associative array
        $summariesArray = $summaryDocument->getArrayCopy();

        // Search for the specific summary within the document
        foreach ($summariesArray['quizzes'] as $quiz) {
            if (isset($quiz['date_created']) && (int)$quiz['date_created'] === $dateCreated) {
                echo json_encode(['success'=> $quiz]);
                exit; // Terminate the script
            }
        }
        // If we complete the loop without finding the quiz, it wasn't found
        echo json_encode(['error' => 'Quiz not found for the provided date']);
    } else {
        // If no document was found for the user
        echo json_encode(['error' => 'No document found for the user']);
    }
} else {
    // If the 'datas' POST variable wasn't set
    echo json_encode(['error' => 'Data not provided']);
}
