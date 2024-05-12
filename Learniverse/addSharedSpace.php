<?php

require 'session.php';
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;
if (isset($_POST['spaceName']) && $_POST['spaceName'] != "") {
    // save space name received from POST
    $spaceName = $_POST['spaceName'];
    $spaceID  = uniqid();
    $spaceColor = $_POST['color'];

    //create meeting room for the shared space
    $roomUrl;
    $hostURL;
    $api_key = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmFwcGVhci5pbiIsImF1ZCI6Imh0dHBzOi8vYXBpLmFwcGVhci5pbi92MSIsImV4cCI6OTAwNzE5OTI1NDc0MDk5MSwiaWF0IjoxNzEwODc0OTI3LCJvcmdhbml6YXRpb25JZCI6MjE5NDk2LCJqdGkiOiJkMDdjNDkwZS00MjJlLTQwMzctYWViOS00ODM2NTc1ZDQxZTMifQ.QnhnMtHYeDa__GtBYDNBAwJ31_dJ0SMhFigPwUKCrTg";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt(
        $ch,
        CURLOPT_POSTFIELDS,
        '{
    "endDate": "2099-02-18T14:23:00+03:00",
    "roomMode":"group",
    "fields": ["hostRoomUrl"]}'
    );

    $headers = [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    if ($httpcode !== 201) {
        $error = [
            "message" => "error",
            "error" => [
                "code" => $httpcode
            ]
        ];
        echo json_encode($error);
    } else {
        $dataURL = json_decode($response);
        $roomUrl = $dataURL->{'roomUrl'};
        $hostURL = $dataURL->{'hostRoomUrl'};
        $meetingID = $dataURL->{'meetingId'};
    }
    // Create space object
    $space = [
        'spaceID' => $spaceID,
        'name' => $spaceName,
        'admin' => $_SESSION['email'],
        'color' => $spaceColor,
        'members' => [],
        'pendingMembers' => [],
        'tasks' => [],
        'files' => [],
        'feed' => [],
        'hostUrl' => $hostURL,
        'roomUrl' => $roomUrl,
        'meetingID' => $meetingID,
        'logUpdates' => []
    ];
    $bulkWrite->insert($space);

    // Insert the document into the collection
    $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
    $spaceCalendar =   [
        "user_id" => $spaceID,
        "counter" => 0,
        "List" => [
            []
        ]
    ];
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($spaceCalendar);
    $result2 = $manager->executeBulkWrite("Learniverse.calendar", $bulk);
    echo json_encode(['msg' => 'Creating..', 'createdSpace' => "$spaceID"]);
    exit();
} elseif (isset($_POST['spaceID']) && $_POST['spaceID'] != "") {

    // Define the query filter
    $filter = ['spaceID' => $_POST['spaceID']];

    // Create a query object
    $query = new MongoDB\Driver\Query($filter);

    // Execute the query
    $cursor = $manager->executeQuery('Learniverse.sharedSpace', $query);
    $result = $cursor->toArray();
    if (empty($result)) {
        echo "No such Space found.";
        exit();
    }
    // Get the first document from the result
    $space = $result[0];
    // Convert the document to an object
    $spaceObject = (object) $space;

    $found = false;
    foreach ($spaceObject->members as $member) {
        if ($member->email === $_SESSION['email']) {
            $found = true;
            break;
        }
    }

    if ($spaceObject->admin === $_SESSION['email'])
        echo "youre this space admin";
    elseif ($found) {
        echo "youre already a member of this space";
    } elseif (in_array($_SESSION['email'], $spaceObject->pendingMembers))
        echo "You already requested to join this space. please wait admin approval";
    else {
        // Create an update query with the $push operator
        $updateQuery = ['$push' => ['pendingMembers' => $_SESSION['email']]];

        // Create an update command
        $bulkWrite->update($filter, $updateQuery);

        // Insert the document into the collection
        $result = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulkWrite);
        echo "Requesting..";
    }
}
