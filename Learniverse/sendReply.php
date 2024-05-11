<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$complaintID = $_POST['complaintID'];
$message = $_POST['reply'];
$user = $_POST['user'];

$bulkWrite = new MongoDB\Driver\BulkWrite();
$bulkWrite->update(
    ['complaintID' => $complaintID],
    ['$set' => ['reply' => $message, 'replyDate' => (new DateTime())->format('Y-m-d H:i:s')]],
    ['multi' => false]
);

$manager = new MongoDB\Driver\Manager($connection);
$result = $manager->executeBulkWrite('Learniverse.complaint', $bulkWrite);

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

$data = array(
    "email" => $user
);

$fetch = $Usercollection->findOne($data);

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
$recipientName = $fetch['firstname'];
$recipientEmail = $fetch['email'];
$mail->addAddress($recipientEmail, $recipientName);

// Set the email content
$mail->Subject = 'Learniverse customer support';
$mail->Body = $message;
$response = array();

if ($mail->send()) {
    $response = array(
        'status' => 'success',
        'message' => 'Email sent successfully',
        'complaintID' => $complaintID
    );
} else {
    $response = array(
        'status' => 'fail',
        'message' => 'Failed to send email'
    );
}

header('Content-Type: application/json');
echo json_encode($response);