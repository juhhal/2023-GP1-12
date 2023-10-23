<?php

session_start();

//get updated data received from POST
$task_name = htmlspecialchars($_POST['task_name'], ENT_QUOTES, 'UTF-8');

if ($_POST['status']==='true') {
    $status = true;
} else {
    $status = false;
}
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

// Specify the filter criteria to match the document you want to update
$filter = [
    'user_id' => $_SESSION['email'],
    'todo_list.list_name' => 'My To-Do List',
    'todo_list.tasks.task_name' => $task_name
];

// Specify the update operations you want to perform
$update = [
    '$set' => [
        'todo_list.$[outer].tasks.$[inner].checked' => $status
    ]
];

// Specify the array filters to identify the elements to update
$arrayFilters = [
    ['outer.list_name' => 'My To-Do List'],
    ['inner.task_name' => $task_name]
];

// Add the update operation to the bulk write
$bulkWrite->update(
    $filter,
    $update,
    ['multi' => false, 'arrayFilters' => $arrayFilters]
);

//execute the update command
$result = $manager->executeBulkWrite('Learniverse.To-do-list', $bulkWrite);
//redirect to the page

// Check the result
if ($result->getModifiedCount() > 0) {
    echo "Data updated successfully.";
} else {
    echo "No documents matched the filter criteria.";
}

//redirect to the page
// header("Location: todo.php");
