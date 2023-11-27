<?php
session_start();

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$databaseName = 'Learniverse';
$collectionName = 'comments';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = new MongoDB\Driver\Query(['email' => $_SESSION['email']]);
    $cursor = $manager->executeQuery("$databaseName.users", $query);
    $data = $cursor->toArray();

    $firstname = $data[0]->firstname;
    $lastname = $data[0]->lastname;
    $username = $data[0]->username;
    $commentdata = $_POST['comment'];
    $post_id = $_POST['id_post'];
    $commentDate = date('Y-m-d \a\t\ H:i');
    $email = $_SESSION['email'];

    $bulk = new MongoDB\Driver\BulkWrite;

    $comment = [
        'username' => $username,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'comment' => $commentdata,
        'commented_at' => $commentDate,
        'post_id' => $post_id,
        'email' => $email,
    ];

    $bulk->insert($comment);

    $result = $manager->executeBulkWrite("$databaseName.$collectionName", $bulk);

    header("Location: viewPost.php?postID=" . $post_id);
}
?>