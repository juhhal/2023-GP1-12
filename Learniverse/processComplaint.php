<?php
session_start();
if (isset($_POST['CS_complaint'])) {
    //establish connection
    $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
    //create a bulk writer to update data in the db
    $bulkWrite = new MongoDB\Driver\BulkWrite;

    $complaintText = $_POST['CS_complaint'];
    $complaint = [
        "complaintID" => uniqid(),
        "user" => $_SESSION['email'],
        "complaint" => $complaintText,
        "date" => (new DateTime())->format('Y-m-d H:i:s'), // Convert date to string
        "status" => "unresolved"
    ];
    $bulkWrite->insert($complaint);
    $result = $manager->executeBulkWrite("Learniverse.complaint", $bulkWrite);
    if ($result->getInsertedCount() > 0) {
        echo 1;
    } else {
        echo 0;
    }
}
?>