<?php
require "session.php";
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

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
    $commentID = $_POST['commentID'];
    
    $bulkwrite = new MongoDB\Driver\BulkWrite;
    $bulk = new MongoDB\Driver\BulkWrite;

    //check if it is a comment update request
    if ($commentID != "") {
        $bulk->update(['_id' => new ObjectID($commentID)], ['$set' => ['comment' => $commentdata, 'edited_at' => date('Y-m-d \a\t\ H:i')]]);
        $update = $manager->executeBulkWrite("$databaseName.$collectionName", $bulk);
        header("Location: viewPost.php?postID=$post_id");
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
        $bulkwrite->insert($comment);
        $bulk->update(['_id' => new ObjectID($post_id)], ['$inc' => ['comments' => 1]]);
        $inc = $manager->executeBulkWrite("$databaseName.community", $bulk);
   

    $result = $manager->executeBulkWrite("$databaseName.$collectionName", $bulkwrite);

    if ($result->getWriteErrors() === []) {
        // Comment added successfully, send notification and redirect
        $sent = sendNotification($post_id);
        if ($sent) {
            header("Location: viewPost.php?postID=$post_id");
            exit;
        } else {
            echo 'Notification email could not be sent.';
        }
    }
}
}

function sendNotification($postID) {

    global $post_id;
    // Create a MongoDB client
    $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

    // Select the database and collection
    $database = $connection->Learniverse;
    $Usercollection = $database->community;

    $email = "";
    $title = "";
    $user = $Usercollection->findOne(['_id' => new ObjectID($postID)]);
    if ($user) {
        $email = $user['author'];
        $title = $user['title'];

        $name = $database->users->findOne(['email' => $email]);
        if ($name) {
            $firstname = $name['firstname'];
            $lastname = $name['lastname'];
        }

        $smtpUsername = 'Learniverse.website@gmail.com';
        $smtpPassword = 'hnrl utwf fxup rnyd';
        $smtpHost = 'smtp.gmail.com';
        $smtpPort = 587;
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer;

        // Enable SMTP debugging
        $mail->SMTPDebug = 0;

        // Set the SMTP settings
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->Port = $smtpPort;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;

        // Set the email content
        $mail->setFrom('Learniverse.website@gmail.com');
        $mail->addAddress($email);
        $mail->Subject = 'You received a new comment on your post!';
        $mail->Body = "Dear ". $firstname . " " . $lastname .",\n\nYou have received a new comment on your\" " . $title . "\"  post. Check it now!!.\n http://localhost:3000/viewPost.php?" . $post_id . "\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

        // Send the email
        if ($mail->send()) {
            return true;
        }
    }

    return false;
}
?>
