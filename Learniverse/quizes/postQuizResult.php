<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$user_id = $_SESSION['email']; 
$quizId = $_POST['quizId']; 
$newResult = $_POST['result'];

header('Content-Type: application/json');

if (!$user_id) {
    echo json_encode(['error' => 'User ID not found']);
    exit;
}

if (!$quizId || !$newResult) {
    echo json_encode(['error' => 'Missing quiz ID or result']);
    exit;
}

// Creating a filter to find the user and the specific quiz by ID within the user's quizzes.
$filter = [
    'userId' => $user_id,
    'quizzes.id' => $quizId // Make sure your path here matches your data structure.
];

// Defining the update operation to change the result of the specific quiz.
$update = [
    '$set' => [
        'quizzes.$.result' => $newResult, // Updates the result of the matched quiz.
    ]
];

$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update($filter, $update);

try {
    $result = $manager->executeBulkWrite('Learniverse.Quizzes', $bulk);
    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Quiz result updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Quiz result not updated. It might be the same as before or the quiz does not exist.']);
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
