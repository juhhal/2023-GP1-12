<?php
// Include database configuration file 
require_once 'dbConfig.php';
require_once 'session.php';
// Retrieve JSON from POST body
$jsonStr = file_get_contents('php://input');
$jsonObj = json_decode($jsonStr);
$uid = 0;

if (property_exists($jsonObj, 'spaceID')) {
    $uid = $jsonObj->spaceID;
} else {
    $uid = $_SESSION['email'];
}

if ($jsonObj->request_type == 'addEvent') {
    $start = $jsonObj->start;
    $end = $jsonObj->end;

    $event_data = $jsonObj->event_data;
    $eventTitle = !empty($event_data[0]) ? $event_data[0] : '';
    $eventDesc = !empty($event_data[1]) ? $event_data[1] : '';
    $eventRem = isset($event_data[2]) ? ($event_data[2] ? true : false) : false;

    ///////////////////////////////////
    if (!empty($eventTitle)) {

        $bulk = new MongoDB\Driver\BulkWrite;

        // $document1 = ['id'=>$_SESSION['email'], 'List'=> [['Event name'=>$title, 'Event start'=>$start, 'Event End'=>$end]]];
        // $_id1 = $bulk->insert($document1);

        // $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);}
        $query = new MongoDB\Driver\Query(array('user_id' => $uid));
        $cursor = $manager->executeQuery('Learniverse.calendar', $query);
        $result_array = $cursor->toArray();
        $result_json = json_decode(json_encode($result_array), true);
        $id = $result_json[0]['counter'];
        $incrementedID = intval($id) + 1;

        $event = ['id' => $id, 'title' => $eventTitle, 'description' => $eventDesc, 'reminder' => $eventRem, 'start' => $start, 'end' => $end];
        $id++;
        $bulk->update(
            [
                'user_id' => $uid,
            ],
            ['$push' => ['List' => $event]]
        );
        $bulk->update(
            [
                'user_id' => $uid,
            ],
            ['$set' => ['counter' => $incrementedID]]
        );
        //execute the update command
        $result = $manager->executeBulkWrite('Learniverse.calendar', $bulk);

        if ($result->isAcknowledged()) {
            $output = [
                'status' => 1
            ];
            echo json_encode($output);
        } else {
            echo json_encode(['error' => 'Event Add request failed!']);
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////

} elseif ($jsonObj->request_type == 'editEvent') {
    $start = $jsonObj->start;
    $end = $jsonObj->end;
    $id1 = $jsonObj->event_id;
    $event_id = intval($id1);
    $event_data = $jsonObj->event_data;
    $eventTitle = !empty($event_data[0]) ? $event_data[0] : '';
    $eventDesc = !empty($event_data[1]) ? $event_data[1] : '';
    $eventRem = isset($event_data[2]) ? ($event_data[2] ? true : false) : false;

    $bulk = new MongoDB\Driver\BulkWrite;

    if (!empty($eventTitle)) {
        // Update event data into the database 
        $event = ['id' => $event_id, 'title' => $eventTitle, 'description' => $eventDesc,  'reminder' => $eventRem, 'start' => $start, 'end' => $end];
        $bulk->update(
            [
                'user_id' => $uid,
                'List.id' => $event_id
            ],
            ['$set' => ['List.$' => $event]],
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

    //////////////////////////////////////////////////////////////////////////////////////

} elseif ($jsonObj->request_type == 'deleteEvent') {
    $id1 = $jsonObj->event_id;
    $id = intval($id1);
    $bulkWrite = new MongoDB\Driver\BulkWrite;

    // Define the filter to identify the document
    $filter = [
        'user_id' => $uid,
    ];

    // Remove the task using the filter
    $bulkWrite->update(
        $filter,
        ['$pull' => ['List' => ['id' => $id]]],
        ['multi' => false]
    );

    //execute the update command
    $result = $manager->executeBulkWrite('Learniverse.calendar', $bulkWrite);
    if ($result->isAcknowledged()) {
        $output = [
            'status' => 1,
        ];
        echo json_encode($output);
    } else {
        echo json_encode(['error' => 'Event Delete request failed!']);
    }
} 
//////////
