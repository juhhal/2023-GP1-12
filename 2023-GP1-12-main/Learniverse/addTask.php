<?php

session_start();

//construct the task JSON as received from POST
$task =
    [
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
echo ("<script>alert('".$_SESSION['email'])."');</script>";
$bulkWrite->update(
    [
        'user_id' => $_SESSION['email'],
        'todo_list.list_name' => 'My To-Do List'
    ],
    ['$push' => ['todo_list.$.tasks' => $task]]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);
//redirect to the page
header("Location: workspace.php");
