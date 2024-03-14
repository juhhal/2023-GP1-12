<?php
session_start();

//get updated data received from POST
$new_name = htmlspecialchars($_POST['taskRename'], ENT_QUOTES, 'UTF-8');
$new_due = $_POST['newDue'];
$taskID = $_POST['taskID'];

//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

// Specify the filter criteria to match the document you want to update
$filter = [
    'user_id' => $_SESSION['email'],
    'todo_list.list_name' => 'My To-Do List',
    'todo_list.tasks.taskID' => $taskID
];

// Specify the update operations you want to perform
$update = [
    '$set' => [
        'todo_list.$[outer].tasks.$[inner].task_name' => $new_name,
        'todo_list.$[outer].tasks.$[inner].due' => $new_due
    ]
];

// Specify the array filters to identify the elements to update
$arrayFilters = [
    ['outer.list_name' => 'My To-Do List'],
    ['inner.taskID' => $taskID]
];

// Add the update operation to the bulk write
$bulkWrite->update(
    $filter,
    $update,
    ['multi' => false, 'arrayFilters' => $arrayFilters]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);

// Check the result
if ($result->getModifiedCount() > 0) {
    echo "Task updated successfully.";
    if ($new_due != "") {

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
            ['$set' =>
            [
                'List.$.title' => "TASK: " . $new_name,
                'List.$.start' => $new_due . ":00+03:00",
                'List.$.end' => $new_due . ":01+03:00"
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
    echo "No task matched the filter criteria.";
}

//redirect to the page
header("Location: workspace.php");
