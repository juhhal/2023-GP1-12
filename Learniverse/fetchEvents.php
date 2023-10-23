<?php     
// Include database configuration file 
require_once 'dbConfig.php';
require_once 'session.php';


$query = new MongoDB\Driver\Query(array('user_id' => $_SESSION['email']));
$cursor = $manager->executeQuery('Learniverse.calendar', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);
$events = $result_json[0]['List'];
echo json_encode($events);


// Render event data in JSON format
//echo json_encode($eventsArr);



