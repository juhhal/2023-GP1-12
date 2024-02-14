<?php

// Include MongoDB PHP library
require 'vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017");

// Select database and collections
$database = $client->selectDatabase('your_database_name');
$userCollection = $database->selectCollection('users');
$summaryCollection = $database->selectCollection('summaries');

// Function to insert a new summary for a user, replacing the oldest one if necessary
function insertSummary($userId, $subjectName, $question, $answer, $summaryCollection) {
    // Find the number of summaries for the given user
    $count = $summaryCollection->count(['userId' => $userId]);

    // If there are already 3 summaries for the user, find the oldest one and replace it
    if ($count >= 3) {
        $oldestSummary = $summaryCollection->findOne(['userId' => $userId], ['sort' => ['data_created' => 1]]);
        $oldestSummaryId = $oldestSummary['_id'];

        // Update the oldest summary with the new data
        $summaryCollection->updateOne(
            ['_id' => $oldestSummaryId],
            ['$set' => [
                'subjectName' => $subjectName,
                'question' => $question,
                'answer' => $answer,
                'data_created' => time()
            ]]
        );

        echo "Summary replaced successfully.<br>";
    } else {
        // Insert a new summary
        $summaryCollection->insertOne([
            'userId' => $userId,
            'subjectName' => $subjectName,
            'question' => $question,
            'answer' => $answer,
            'data_created' => time()
        ]);

        echo "Summary added successfully.<br>";
    }
}

// Usage example
$userId = "user_id_here"; // Replace with actual user ID
$subjectName = "Subject Name";
$question = "Question";
$answer = "Answer";

// Insert the new summary
insertSummary($userId, $subjectName, $question, $answer, $summaryCollection);

?>
