<?php
require "session.php";

use MongoDB\Driver\BulkWrite;
use MongoDB\BSON\ObjectID;

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$reportedPost = $_POST['reportedPost'];
$description = $_POST['description'];
$submissionDate = $_POST['submissionDate'];
$reportedBy = $_POST['reportedBy'];


// Create a new BulkWrite instance
$bulkWrite = new BulkWrite();

// Create a new document to be inserted
$report = [
    "postID" => $reportedPost,
    "reportedBy" => $reportedBy,
    "reportDate" => $submissionDate,
    "description" => $description
];

// Insert the document into the BulkWrite instance
$bulkWrite->insert($report);

// Execute the BulkWrite operation
$result = $manager->executeBulkWrite("Learniverse.postReports", $bulkWrite);

// Check if the operation was successful
if ($result->getInsertedCount() > 0) {
    echo "the report has been inserted successfully.";
} else {
    echo "Failed to insert the report.";
}

header("Location:viewPost.php?postID=$reportedPost");
