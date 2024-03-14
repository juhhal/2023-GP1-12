<?php
require "session.php";
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

//add new plan
if (isset($_POST['new-plan-name'])) {
    $name = $_POST['new-plan-name'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    // $uploadedMats = ;
    // $localMats = $_FILES['localMats'];
    // $totalFiles = count($_FILES['localMats']['name']);
    // echo "count: $totalFiles";
    // // Loop through each uploaded file
    // for ($i = 0; $i < $totalFiles; $i++) {
    //     // Get the file name and temporary file path
    //     $fileName = $_FILES['localMats']['name'][$i];
    //     echo "FILE: " . $fileName;
    // }

    $filter = [
        'user_id' => $_SESSION['email'],
        'List.start' => [
            '$gte' => $start

        ],
        'List.end' => [
            '$lte' => $end
        ],
    ];

    $query = new MongoDB\Driver\Query($filter);

    // Execute the query
    $cursor = $manager->executeQuery("Learniverse.calendar", $query);

    // Fetch the matching events
    $previousEvents = [];
    foreach ($cursor as $document) {
        foreach ($document->List as $item) {
            if (!is_array($item)) {
                $eventStart = $item->start;
                $eventEnd = $item->end;

                if (substr($eventStart, 0, 10) >= $start && substr($eventEnd, 0, 10) <= $end) {
                    $previousEvents[] = $item;
                }
            }
        }
    }
    // print_r($previousEvents);
    $plan = [
        "planID" => uniqid(),
        "user_id" => $_SESSION['email'],
        "name" => $name,
        "creation_date" => date("Y-m-d"),
        "start" => $start,
        "end" => $end,
        // "study_plan" => $studyPlan,

        "color" => 'skyblue'

    ];

    $bulkWrite->insert($plan);
    $result = $manager->executeBulkWrite("Learniverse.studyPlan", $bulkWrite);
    if ($result->getInsertedCount() > 0)
        echo "success";
    else
        echo "fail";
} elseif (isset($_POST['deletePlan'])) {
    $bulkWrite = new MongoDB\Driver\BulkWrite;
    $planID = $_POST['planID'];
    // Define the filter to identify the document
    $filter = ['planID' => $planID];

    // Add the delete operation to the BulkWrite instance
    $bulkWrite->delete($filter);

    // Execute the BulkWrite operation
    $result = $manager->executeBulkWrite("Learniverse.studyPlan", $bulkWrite);
    if ($result->getDeletedCount() > 0) {
        echo 1;
    } else echo 0;
    exit;
}

header("Location:studyplan.php");
