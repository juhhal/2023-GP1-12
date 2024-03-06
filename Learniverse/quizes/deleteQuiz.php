<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$user_id = $_SESSION['email'];
$quizId = $_POST['quizId'] ?? ''; 

if ($user_id && $quizId) {
    $bulk = new MongoDB\Driver\BulkWrite;

    $filter = ['userId' => $user_id];
    
    $update = [
        '$pull' => [
            'quizzes' => [
                'id' => $quizId
            ]
        ]
    ];
    
    $bulk->update($filter, $update);

    $result = $manager->executeBulkWrite('Learniverse.Quizzes', $bulk);

    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Quiz deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete quiz or quiz not found']);
    }
} else {
    echo json_encode(['error' => 'User ID or Quiz ID not found']);
}
?>
