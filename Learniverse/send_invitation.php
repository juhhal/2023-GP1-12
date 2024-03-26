<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/vendor/autoload.php';

// Retrieve the JSON data from the AJAX request
$jsonData = $_POST['selectedUsers'];
$meetingLink = $_POST['meetingLink'];
$enteredEmail = $_POST['enteredEmail'];

// Decode the JSON data into an associative array
$selectedUsers = json_decode($jsonData, true);

// Connect to MongoDB
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('users');
$owner = '';

// Query the database for the user
$userDocument = $usersCollection->findOne(['email' => $_SESSION['email']]);

if ($userDocument) {
    $owner = $userDocument['firstname'] . ' ' . $userDocument['lastname'];
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

// Set the email content for selected users
foreach ($selectedUsers as $user) {
    $recipientName = $user['name'];
    $recipientEmail = $user['email'];
    $mail->addAddress($recipientEmail);

    // Set the email content
    $mail->Subject = 'You are Invited to a Meeting';
    $mail->Body = "Dear $recipientName,\n\nA meeting session has started by $owner. Don't miss out!\n\nMeeting Link:\n$meetingLink\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

    if (!$mail->send()) {
        // Handle email sending failure
        echo 'Failed to send email to ' . $recipientEmail . ': ' . $mail->ErrorInfo;
    }

    // Clear recipients for the next iteration
    $mail->clearAddresses();
}

// Set the email content for entered email
if (!empty($enteredEmail)) {
    $mail->addAddress($enteredEmail);

    // Set the email content
    $mail->Subject = 'You are Invited to a Meeting';
    $mail->Body = "Dear " .$enteredEmail. ",\n\nA meeting session has started by $owner. Don't miss out!\n\nMeeting Link:\n$meetingLink\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

    if (!$mail->send()) {
        // Handle email sending failure
        echo 'Failed to send email to ' . $enteredEmail . ': ' . $mail->ErrorInfo;
    }
}

// Email sent successfully
echo 'Emails sent successfully';
?>