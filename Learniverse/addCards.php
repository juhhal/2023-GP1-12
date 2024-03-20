<?php
session_start();
// Include MongoDB PHP library
require_once __DIR__ . '/vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");


// Select database and collections
$database = $client->selectDatabase('Learniverse');
$flashcardCollection = $database->selectCollection('subjectReview');


// Get the raw POST data
$data = file_get_contents("php://input");

// Decode the JSON data
$array = json_decode($data, true);

// Now, you can access your data
$name = $array['name'];
$flashcardData = $array['questions'];



// You might want to do further processing, like saving to a database

    // Find the user's document
    $userDoc = $flashcardCollection->findOne(['userId' => $_SESSION['email']]);

    // Initialize user data array
    $userData = [
        'userId' => $_SESSION['email'],
        'subjects' => []
    ];

    // If the user document exists, convert it to a PHP array
    if ($userDoc) {
        $userData = (array) $userDoc;
    }

    $time = time();
    // If the subject doesn't exist, create a new entry for it
        $userData['subjects'][] = [
            'subjectName' => $name,
            'data_created' => $time,
            'flashcards' => []
        ];
        $subjectIndex = count($userData['subjects']) - 1;
    $cardNumber = 1;
    foreach ($flashcardData as $cardIndex => $card) {

        // Extract the card number from the key (e.g., 'card1' => '1')

        // Extract the flashcard content and answer from the nested array
        $content = $card['question'];
        $answer = $card['answer'];
    
        // Create the new flashcard array
        $newFlashcard = [
            'cardNumber' => $cardNumber,
            'content' => $content,
            'answer' => $answer,
        ];
        $cardNumber += 1;

        // Add the new flashcard to the user data
        $userData['subjects'][$subjectIndex]['flashcards'][] = $newFlashcard;
    }
    

    // Upsert the user's document with the updated user data
    $flashcardCollection->replaceOne(
        ['userId' => $_SESSION['email']],
        $userData,
        ['upsert' => true]
    );

    echo json_encode(array("success" => $time,));

