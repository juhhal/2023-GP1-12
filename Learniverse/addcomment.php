<?php
require "session.php";

use MongoDB\BSON\ObjectID;

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
    $commentID = "";
    $bulk = new MongoDB\Driver\BulkWrite;

    $bulkwrite = new MongoDB\Driver\BulkWrite;

    //check if it is a comment update request
    if (isset($_POST['commentID'])) {
        $commentID = $_POST['commentID'];
        $bulk->update(['_id' => new ObjectID($commentID)], ['$set' => ['comment' => $commentdata, 'edited_at' => date('Y-m-d \a\t\ H:i')]]);
    } else { //it is a new comment, add it to comments collection
        $comment = [
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'comment' => $commentdata,
            'commented_at' => $commentDate,
            'post_id' => $post_id,
            'email' => $email,
            'edited_at' => ""
        ];
        $bulk->insert($comment);
        $bulkwrite->update(['_id' => new ObjectID($post_id)], ['$inc' => ['comments' => 1]]);
        $inc = $manager->executeBulkWrite("$databaseName.community", $bulkwrite);
    }

    $result = $manager->executeBulkWrite("$databaseName.$collectionName", $bulk);

    header("Location: viewPost.php?postID=" . $post_id);
}
