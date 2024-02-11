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
    $randomColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['pendingMembers' => $member]];
    $insertOperation = ['$push' => ['members' => ['email' => $member, 'color' => $randomColor]]];
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
    } else {
        echo "acceptance mail NOT sent";
    }
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['spaceID' => $spaceid],
            ['$push' => ['logUpdates' => [
                "type" => "join",
                "memberName" => $member,
                "date" => $string
            ]]]
        );
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        $filter = ['email' => $member];
        $query = new MongoDB\Driver\Query($filter);
        $result = $manager->executeQuery("Learniverse.users", $query);
        $user = $result->toArray()[0];
        $response = [
            'name' => $user->firstname . ' ' . $user->lastname,
            'color' => $randomColor
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo "failed accepting member";
    }
} else if ($operation === "reject") {
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['pendingMembers' => $member]];
    // Add the update operations to the bulk write operation
    $bulkWrite->update($filter, $updateOperation);
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
} else if ($operation === "kick") {
    // Construct the update operation using $pull operator
    $updateOperation = ['$pull' => ['members' => ['email' => $member]]];
    $bulkWrite->update($filter, $updateOperation);
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);

    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['spaceID' => $spaceid],
            ['$push' => ['logUpdates' => [
                "type" => "leave",
                "memberName" => $member,
                "date" => $string
            ]]]
        );
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
    } else {
        echo "failed to process member leave";
    }
} else if ($operation === "deleteSpace") {
    header("Location:sharedspace.php");
    // Add the update operations to the bulk write operation
    $bulkWrite->delete($filter, ['limit' => 0]);
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
}
// execute
