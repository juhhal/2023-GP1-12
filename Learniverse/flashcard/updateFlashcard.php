<?php
session_start();

require_once __DIR__ . '../../vendor/autoload.php';

if (!isset($_SESSION['email'], $_POST['subjectName'], $_POST['cardNumber'], $_POST['content'], $_POST['answer'])) {
    echo json_encode(["error" => true, "message" => "Required information is missing."]);
    exit;
}
header('Content-Type: application/json');

$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$database = $client->selectDatabase('Learniverse');
$flashcardCollection = $database->selectCollection('subjectReview');

$userId = $_SESSION['email'];
$subjectName = $_POST['subjectName'];
$cardNumber = $_POST['cardNumber'];
$newContent = $_POST['content'];
$newAnswer = $_POST['answer'];

try {
    $result = $flashcardCollection->updateOne(
        [
            'userId' => $userId,
            'subjects.subjectName' => $subjectName,
        ],
        [
            '$set' => [
                'subjects.$[subj].flashcards.$[card].content' => $newContent,
                'subjects.$[subj].flashcards.$[card].answer' => $newAnswer,
            ],
        ],
        [
            'arrayFilters' => [
                ['subj.subjectName' => $subjectName],
                ['card.cardNumber' => $cardNumber],
            ],
        ]
    );

    if ($result->getModifiedCount() == 0) {
        echo json_encode(["error" => true, "message" => "Flashcard not found or data unchanged."]);
        return;
    }

    $updatedDocument = $flashcardCollection->findOne(['userId' => $userId, 'subjects.subjectName' => $subjectName]);

    echo json_encode(["success" => true, "message" => "Flashcard updated successfully.", "data" => $updatedDocument]);

} catch (Exception $e) {
    echo json_encode(["error" => true, "message" => "An error occurred: " . $e->getMessage()]);
}
?>
