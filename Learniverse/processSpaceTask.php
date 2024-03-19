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
//get space name
$query = new MongoDB\Driver\Query(['spaceID' => $spaceID]);
$cursor = $manager->executeQuery("Learniverse.sharedSpace", $query);
$space = $cursor->toArray()[0];
$spaceName = $space->name;

// Add task request
if ($operation === 'addTask') {
    $bulk = new MongoDB\Driver\BulkWrite();
    $taskName = $_POST['task_name'];
    $taskDesc = $_POST['description'];
    $taskDue = $_POST['due'];
    $taskAssignee = $_POST['assignee'];
    $taskID = uniqid();
    // Retrieve task data and create task object
    $task = [
        "taskID" => $taskID,
        "task_name" => htmlspecialchars($taskName, ENT_QUOTES, 'UTF-8'),
        "creator" => $_SESSION["email"],
        "description" => $taskDesc,
        "due" => $taskDue,
        "checked" => false,
        "assignee" => $taskAssignee,
        "lastEditedBy" => ""
    ];


    // Target the space of the task
    $bulkWrite->update(
        ['spaceID' => $spaceID],
        ['$push' => ['tasks' => $task]]
    );
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk->update(
            ['spaceID' => $spaceID],
            ['$push' => ['logUpdates' => [
                "type" => "create",
                "creator" => $_SESSION['email'],
                "date" => $string
            ]]]
        );
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
            $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task in $spaceName space, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

            // Send the email
            if ($mail->send()) {
                echo "acceptance mail sent";
                $dateTime = new DateTime();
                $string = $dateTime->format('Y-m-d H:i:s');
                $bulk->update(
                    ['spaceID' => $spaceID],
                    ['$push' => ['logUpdates' => [
                        "type" => "assign",
                        "assignor" => $_SESSION['email'],
                        "assignee" => $taskAssignee,
                        "date" => $string
                    ]]]
                );
            } else {
                echo "acceptance mail NOT sent";
            }
        }
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        echo $taskID;
        if ($taskDue != "") { //add the timed task to the calendar
            $task = $taskName;
            $due = $taskDue;
            $uid = $spaceID;
            $start = $due . ":00+03:00";
            $end = $due . ":01+03:00";

            $query = new MongoDB\Driver\Query(array('user_id' => $uid));
            $cursor = $manager->executeQuery('Learniverse.calendar', $query);
            $result_array = $cursor->toArray();
            $result_json = json_decode(json_encode($result_array), true);
            $id = $result_json[0]['counter'];
            $incrementedID = intval($id) + 1;

            $color = "#3788d8"; //default admin color
            if ($space->admin != $taskAssignee)
                foreach ($space->members as $member)
                    if ($member->email === $taskAssignee) $color = $member->color; //get member color

            $event = ['id' => $id, 'title' => "TASK: " . $task, 'description' => $taskDesc, 'reminder' => true, 'start' => $start, 'end' => $end, 'color' => $color, 'taskID' => $taskID];
            $id++;
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                [
                    'user_id' => $uid,
                ],
                ['$push' => ['List' => $event]]
            );
            $bulk->update(
                [
                    'user_id' => $uid,
                ],
                ['$set' => ['counter' => $incrementedID]]
            );
            //execute the update command
            $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);


            if ($result->isAcknowledged()) {
                $output = [
                    'status' => 1
                ];
                header("Location:workspace.php");
            } else {
                header("Location:workspace.php");
                echo json_encode(['error' => 'Event Add request failed!']);
            }
            exit();
        }
    } else {
        echo "Failed to add task";
    }
} elseif ($operation === "editTask") {
    $bulk = new MongoDB\Driver\BulkWrite();
    $taskID = $_POST['taskID'];
    $taskCheck = $_POST['taskCheck'];
    $taskName = $_POST['task_name'];
    $taskDesc = $_POST['description'];
    $taskDue = $_POST['due'];
    $taskAssignee = $_POST['assignee'];

    if ($taskCheck === "true")
        $taskCheck = true;
    else $taskCheck = false;

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


    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['spaceID' => $spaceID],
            ['$push' => ['logUpdates' => [
                "type" => "edit",
                "editor" => $_SESSION['email'],
                "date" => $string
            ]]]
        );
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
            $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task in '$spaceName' space, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

            // Send the email
            if ($mail->send()) {
                echo "acceptance mail sent";
                $dateTime = new DateTime();
                $string = $dateTime->format('Y-m-d H:i:s');
                $bulk->update(
                    ['spaceID' => $spaceID],
                    ['$push' => ['logUpdates' => [
                        "type" => "assign",
                        "assignor" => $_SESSION['email'],
                        "assignee" => $taskAssignee,
                        "date" => $string
                    ]]]
                );
            } else {
                echo "acceptance mail NOT sent";
            }
        }
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        if ($taskDue != "") {

            $filter = [
                'user_id' => $spaceID,
                'List' => [
                    '$elemMatch' => [
                        'taskID' => $taskID
                    ]
                ]
            ];
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                $filter,
                ['$set' =>
                [
                    'List.$.title' => "TASK: " . $taskName,
                    'List.$.start' => $taskDue . ":00+03:00",
                    'List.$.end' => $taskDue . ":01+03:00"
                ]],
                ['multi' => false]
            );
            //execute the update command
            $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);

            if ($result->isAcknowledged()) {
                $output = [
                    'status' => 1
                ];
                echo json_encode($output);
            } else {
                echo json_encode(['error' => 'Event Edit request failed!']);
            }
        }
    } else {
        echo "Failed to edit task";
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

    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk->update(
            ['spaceID' => $spaceID],
            ['$push' => ['logUpdates' => [
                "type" => "delete",
                "deletor" => $_SESSION['email'],
                "date" => $string
            ]]]
        );
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        //then delete the reminder of that task
        $filter = [
            'user_id' => $spaceID,
            'List' => [
                '$elemMatch' => [
                    'taskID' => $taskID
                ]
            ]
        ];
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
            $filter,
            ['$pull' => ['List' => ['taskID' => $taskID]]],
            ['multi' => false]
        );
        //execute the update command
        $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);

        if ($result->isAcknowledged()) {
            $output = [
                'status' => 1
            ];
            echo json_encode($output);
        } else {
            echo json_encode(['error' => 'Event Edit request failed!']);
        }
    } else {
        echo "Failed to delete task";
    }
} elseif ($operation === "updateAssignee") {
    $taskID = $_POST['taskID'];
    $taskAssignee = $_POST['assignee'];
    $bulkWrite->update(
        ['spaceID' => $spaceID, 'tasks.taskID' => $taskID],
        ['$set' => ['tasks.$.assignee' => $taskAssignee]]
    );

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
        $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task in $spaceName space, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

        // Send the email
        if ($mail->send()) {
            echo "acceptance mail sent";
            $bulk = new MongoDB\Driver\BulkWrite();
            $dateTime = new DateTime();
            $string = $dateTime->format('Y-m-d H:i:s');
            $bulk->update(
                ['spaceID' => $spaceID],
                ['$push' => ['logUpdates' => [
                    "type" => "assign",
                    "assignor" => $_SESSION['email'],
                    "assignee" => $taskAssignee,
                    "date" => $string
                ]]]
            );
            $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        } else {
            echo "acceptance mail NOT sent";
        }
    }
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
} elseif ($operation === 'checkTask') {
    $taskID = $_POST['taskID'];
    $checked = $_POST['checked'];

    // Define the filter to match the document containing the tasks array
    $filter = [
        // Add your filter conditions to match the document
        'spaceID' => $spaceID,
        "tasks.taskID" => $taskID
    ];
    if ($checked === "true")
        $checked = true;
    else $checked = false;
    // Define the pull operation to remove the specific task from the tasks array
    $updateOperation = [
        '$set' => [
            'tasks.$[element].checked' => $checked,

        ]
    ];
    $arrayFilters = [
        [
            'element.taskID' => $taskID
        ]
    ];

    $bulkWrite->update($filter, $updateOperation, ['multi' => false, 'arrayFilters' => $arrayFilters]);
    $checked = ($checked) ? "true" : "false";
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        echo "checked task: $checked";
    } else echo "error checking task: $checked";
} elseif ($operation === 'quickDue') {
    $bulkwrite = new MongoDB\Driver\BulkWrite();
    $taskID = $_POST['taskID'];
    $taskDue = $_POST['newDue'];
    $taskName = $_POST['taskName'];

    // Define the filter to match the document and the specific task within the tasks array
    $filter = [
        "spaceID" => $spaceID,
        "tasks.taskID" => $taskID
    ];
    // Define the update operation using the $set operator
    $updateOperation = [
        '$set' => [
            'tasks.$[element].due' => $taskDue,
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


    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    if ($result->getModifiedCount() > 0) {
        $dateTime = new DateTime();
        $string = $dateTime->format('Y-m-d H:i:s');
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['spaceID' => $spaceID],
            ['$push' => ['logUpdates' => [
                "type" => "edit",
                "editor" => $_SESSION['email'],
                "date" => $string
            ]]]
        );
        $taskAssignee = "";
        foreach ($space->tasks as $task) {
            if ($task->taskID === $taskID)
                $taskAssignee = $task->assignee;
        }
        $query = new MongoDB\Driver\Query([""]);
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
            $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nYou have been assigned a task in '$spaceName' space, come and check it out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

            // Send the email
            if ($mail->send()) {
                echo "acceptance mail sent";
            } else {
                echo "acceptance mail NOT sent";
            }
        }
        $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
        if ($taskDue != "") {

            $filter = [
                'user_id' => $spaceID,
                'List' => [
                    '$elemMatch' => [
                        'taskID' => $taskID
                    ]
                ]
            ];
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update(
                $filter,
                ['$set' =>
                [
                    'List.$.title' => "TASK: " . $taskName,
                    'List.$.start' => $taskDue . ":00+03:00",
                    'List.$.end' => $taskDue . ":01+03:00"
                ]],
                ['multi' => false]
            );
            //execute the update command
            $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);

            if ($result->isAcknowledged()) {
                $output = [
                    'status' => 1
                ];
                echo json_encode($output);
            } else {
                echo json_encode(['error' => 'Event Edit request failed!']);
            }
        }
    } else {
        echo "Failed to edit task";
    }
}
