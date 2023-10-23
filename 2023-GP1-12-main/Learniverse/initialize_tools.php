<?php

// TODO List initialization
$bulkWrite = new MongoDB\Driver\BulkWrite;
$bulk = new MongoDB\Driver\BulkWrite;

// Define the document to be inserted
$newTodo = [
    "user_id" =>$_SESSION['email'],
    "todo_list" => [
        [
            "list_name" => "My To-Do List",
            "tasks" => []
        ]
    ]
];

// Add the insert operation to the BulkWrite instance
$bulkWrite->insert($newTodo);

// Execute the BulkWrite operation
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite("Learniverse.To-do-list", $bulkWrite, $writeConcern);

// Check the result
if ($result->getInsertedCount() > 0) {
    echo "new to-do document inserted successfully.";
} else {
    echo "new to-do document not inserted.";
}

// DONE INITIALIZING TODO

// INITIALIZE CALENDAR EVENTS
//Define new calendar
$newCalendar = [
    'user_id' => $_SESSION['email'],
    'counter' => 0,
    'List' => [[]]
];

// Add the insert operation to the BulkWrite instance
$bulk->insert($newCalendar);

//Execute BulkWriter
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$result = $manager->executeBulkWrite("Learniverse.calendar", $bulk, $writeConcern);

// Check the result
if ($result->getInsertedCount() > 0) {
    echo "new Calendar inserted successfully.";
} else {
    echo "new Calendar not inserted.";
}