<?php

session_start();

//construct the task JSON as received from POST
$taskID = uniqid();
$task =
    [
        "taskID" => $taskID,
        "task_name" => htmlspecialchars($_POST['taskDesc'], ENT_QUOTES, 'UTF-8'),
        "due" => $_POST['taskDue'],
        "checked" => false
    ];

//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
//the update method takes the filters first (here the user email is used as id to identify which document
//in the todo list collection. then it takes the command (SET for updating values, PUSH to insert data to a certain document) 
//and where it should store the pushed data)
$bulkWrite->update(
    [
        'user_id' => $_SESSION['email'],
        'todo_list.list_name' => 'My To-Do List'
    ],
    ['$push' => ['todo_list.$.tasks' => $task]]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);
if ($_POST['taskDue'] != "") { //add the timed task to the calendar
    $task = $_POST['taskDesc'];
    $due = $_POST['taskDue'];
    $uid = $_SESSION['email'];
    $start = $due . ":00+03:00";
    $end = $due . ":01+03:00";

    $query = new MongoDB\Driver\Query(array('user_id' => $_SESSION['email']));
    $cursor = $manager->executeQuery('Learniverse.calendar', $query);
    $result_array = $cursor->toArray();
    $result_json = json_decode(json_encode($result_array), true);
    $id = $result_json[0]['counter'];
    $incrementedID = intval($id) + 1;


    $event = ['id' => $id, 'title' => "TASK: " . $task, 'description' => '', 'reminder' => true, 'start' => $start, 'end' => $end, 'color' => '#fdae9b', 'taskID' => $taskID];
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
        echo json_encode($output);
    } else {
        header("Location:workspace.php");
        echo json_encode(['error' => 'Event Add request failed!']);
    }
    exit();
}
//redirect to the page
header("Location: workspace.php");
