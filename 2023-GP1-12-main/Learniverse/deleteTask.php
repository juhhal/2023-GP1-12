<?php

session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to delete data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

// Define the filter to identify the document
$filter = [
    'user_id' => $_SESSION['email'],
    'todo_list.list_name' => 'My To-Do List',
    'todo_list.tasks.task_name' => htmlspecialchars($_POST['taskName'], ENT_QUOTES, 'UTF-8')
];

// Remove the task using the filter
$bulkWrite->update(
    $filter,
    ['$pull' => ['todo_list.$.tasks' => ['task_name' => htmlspecialchars($_POST['taskName'], ENT_QUOTES, 'UTF-8')]]],
    ['multi' => false]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);

//redirect to the page with the response message
if ($result->getModifiedCount() > 0 || $result->getDeletedCount() > 0) {
    echo("Task deleted successfully.");
} else {
    echo("Task not found.");
}
