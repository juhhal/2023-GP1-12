<?php

session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to delete data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
$taskID = $_POST['taskID'];
// Define the filter to identify the document
$filter = [
    'user_id' => $_SESSION['email'],
    'todo_list.list_name' => 'My To-Do List',
    'todo_list.tasks.taskID' => $taskID
];

// Remove the task using the filter
$bulkWrite->update(
    $filter,
    ['$pull' => ['todo_list.$.tasks' => ['taskID' => $taskID]]],
    ['multi' => false]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);

//redirect to the page with the response message
if ($result->getModifiedCount() > 0 || $result->getDeletedCount() > 0) {
    echo("Task deleted successfully.");//then delete the reminder of that task
    $filter = [
        'user_id' => $_SESSION['email'],
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
    echo("Task not found.");
}
