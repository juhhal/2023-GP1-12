<?php
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
// access the space id, member, and operation (Accept/Reject) from ajax request
$member = $_POST['member'];
$spaceid = $_POST['spaceid'];
$spacename = $_POST['spacename'];
$operation = $_POST['operation'];
$filter = ["spaceID" => $spaceid];

//find the space to get its info
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// Select the database and collection
$database = $connection->Learniverse;
$spaceCollection = $database->sharedSpace;
$userCollection = $database->users;
$space = $spaceCollection->findOne(['spaceID' => $spaceid]);
$adminEmail = $space->admin;
$admin = $userCollection->findOne(['email' => $adminEmail]);

if ($operation === "accept") {
    //generate a color for the member
    // $randomColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT); //['email' => $member, 'color' => $randomColor]
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['pendingMembers' => $member]];
    $insertOperation = ['$push' => ['members' => $member]];
    // Add the update operations to the bulk write operation
    $bulkWrite->update($filter, $updateOperation);
    $bulkWrite->update($filter, $insertOperation);

    //send alert email of acceptance to member
    $query = new MongoDB\Driver\Query(['email' => $member]);
    $cursor = $manager->executeQuery("Learniverse.users", $query);
    $data = $cursor->toArray();

    $firstname = $data[0]->firstname;
    $lastname = $data[0]->lastname;
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
    $mail->addAddress($member);
    $mail->Subject = 'You have been added to a new Space!';
    $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been added to $spacename by $admin->firstname $admin->lastname! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

    // Send the email
    if ($mail->send()) {
        echo "acceptance mail sent";
    } else {
        echo "acceptance mail NOT sent";
    }
} else if ($operation === "reject") {
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['pendingMembers' => $member]];
    // Add the update operations to the bulk write operation
    $bulkWrite->update($filter, $updateOperation);
} else if ($operation === "kick") {
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['members' => $member]];
    $bulkWrite->update($filter, $updateOperation);
} 
// execute
$result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
