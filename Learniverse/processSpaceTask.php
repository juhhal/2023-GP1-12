<?php
require "session.php";
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Driver\Query;
use PHPMailer\PHPMailer\PHPMailer;
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite(); // Instantiate the BulkWrite object
$spaceID = $_POST['spaceID'];
$operation = $_POST['operation'];

$msg = "";
// Add task request
if ($operation === 'addTask') {

    $taskName = $_POST['task_name'];
    $taskDesc = $_POST['description'];
    $taskDue = $_POST['due'];
    $taskAssignee = $_POST['assignee'];

    // Retrieve task data and create task object
    $task = [
        "taskID" => uniqid(),
        "task_name" => htmlspecialchars($taskName, ENT_QUOTES, 'UTF-8'),
        "creator" => $_SESSION["email"],
        "description" => $taskDesc,
        "due" => $taskDue,
        "checked" => false,
        "assignee" => $taskAssignee,
        "lastEditedBy" => ""
    ];

    if ($taskAssignee != "unassigned") {
        //send alert email of assignment to member
        $query = new MongoDB\Driver\Query(['email' => $taskAssignee]);
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
        $mail->addAddress($taskAssignee);
        $mail->Subject = 'You have been assigned a Task!';
        $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

        // Send the email
        if ($mail->send()) {
            echo "acceptance mail sent";
        } else {
            echo "acceptance mail NOT sent";
        }
    }

    // Target the space of the task
    $bulkWrite->update(
        ['spaceID' => $spaceID],
        ['$push' => ['tasks' => $task]]
    );
    $msg = "Added Task Sucessfully";
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['spaceID' => $spaceID],
            ['$push' => ['logUpdates' => [
                "type" => "assign",
                "assignee" => $taskAssignee,
                "assignor" => $_SESSION['email'],
                "date" => $string
            ]]]
        );
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        echo $msg;
    } else {
        echo "Failed to add/edit task";
    }
} elseif ($operation === "editTask") {
    $taskID = $_POST['taskID'];
    $taskCheck = $_POST['taskCheck'];
    $taskName = $_POST['task_name'];
    $taskDesc = $_POST['description'];
    $taskDue = $_POST['due'];
    $taskAssignee = $_POST['assignee'];

    // Define the filter to match the document and the specific task within the tasks array
    $filter = [
        "spaceID" => $spaceID,
        "tasks.taskID" => $taskID
    ];
    // Define the update operation using the $set operator
    $updateOperation = [
        '$set' => [
            'tasks.$[element].task_name' => $taskName,
            'tasks.$[element].description' => $taskDesc,
            'tasks.$[element].due' => $taskDue,
            'tasks.$[element].checked' => $taskCheck,
            "tasks.$[element].assignee" => $taskAssignee,
            "tasks.$[element].lastEditedBy" => $_SESSION['email']
        ]
    ];

    // Define the array filters for the update operation
    $arrayFilters = [
        [
            'element.taskID' => $taskID
        ]
    ];


    // Add the update operation to the bulk write
    $bulkWrite->update($filter, $updateOperation, ['multi' => false, 'arrayFilters' => $arrayFilters]);

    if ($taskAssignee != "unassigned") {
        //send alert email of assignment to member
        $query = new MongoDB\Driver\Query(['email' => $taskAssignee]);
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
        $mail->addAddress($taskAssignee);
        $mail->Subject = 'You have been assigned a Task!';
        $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

        // Send the email
        if ($mail->send()) {
            echo "acceptance mail sent";
        } else {
            echo "acceptance mail NOT sent";
        }
    }

    $msg = "Edited Task Successfully";
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        echo $msg;
    } else {
        echo "Failed to add/edit task";
    }
} elseif ($operation === 'deleteTask') {
    $taskID = $_POST['taskID'];
    // Define the filter to match the document containing the tasks array
    $filter = [
        // Add your filter conditions to match the document
        'spaceID' => $spaceID
    ];

    // Define the pull operation to remove the specific task from the tasks array
    $pullOperation = [
        '$pull' => [
            'tasks' => [
                'taskID' => $taskID
            ]
        ]
    ];

    $bulkWrite->update($filter, $pullOperation);
    $msg = "Deleted Task Successfully";

    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        echo $msg;
    } else {
        echo "Failed to add/edit task";
    }
}
