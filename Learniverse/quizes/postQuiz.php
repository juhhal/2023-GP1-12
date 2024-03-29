<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$user_id = $_SESSION['email'];
$quizName = $_POST['name'] ?? ''; 
$quizBody = $_POST['body'] ?? ''; 
$quizDate = date("Y-m-d H:i:s");


if ($user_id) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $quizId = uniqid();
    $filter = ['userId' => $user_id];
    $update = [ 
        '$push' => [
            'quizzes' => [
                'name' => $quizName,
                'body' => $quizBody,
                'date' => $quizDate,
                'id' => $quizId
            ]
        ]
    ];
    $options = ['upsert' => true];
    $bulk->update($filter, $update, $options);
    $result = $manager->executeBulkWrite('Learniverse.Quizzes', $bulk);

    if ($result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0) {
        $response = ['success' => true, 'quizId' => $quizId];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add quiz']);
        exit; 
    }
} else {
    echo json_encode(['error' => 'User ID not found']);
    exit; 
}
?>
