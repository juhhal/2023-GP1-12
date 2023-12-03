<?php
require_once 'dbConfig.php';
require_once 'session.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Create a bulk writer and connect to collection
$bulk = new MongoDB\Driver\BulkWrite;    


// Initialize variables
$theme = '';
$pomodoroLength = '';
$shortLength = '';
$longLength = '';
$sound = '';
$vol = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // It's a good practice to validate and sanitize these inputs
    $theme = $_POST["theme"];
    $pomodoroLength = $_POST["pomodoroLength"];
    $shortLength = $_POST["shortLength"];
    $longLength = $_POST["longLength"];


    $setting = [
        'user_id' => $_SESSION['email'],
        'Theme' => $theme,
        'pomodoro timer' =>(int) $pomodoroLength,
        'short timer' =>(int) $shortLength, 
        'long timer' => (int)$longLength
    ];

    $bulk->update(
        ['user_id' => $_SESSION['email']],
        ['$set' => $setting],
        ['upsert' => true]
    );

    try {
        $result = $manager->executeBulkWrite('Learniverse.Pomodoro', $bulk);
    
        if ($result->isAcknowledged()) {
            // Check if any documents were actually modified
            $updateCount = $result->getModifiedCount();
    
            if ($updateCount > 0) {
                // If at least one document was updated
                echo json_encode(['message' => "success"]);
            } else {
                // If no documents were updated (perhaps the data was the same)
                echo json_encode(['message' => "no_change"]);
            }
        } else {
            // If the write operation wasn't acknowledged
            echo json_encode(['message' => "update_failed"]);
        }
    } catch (MongoDB\Driver\Exception\Exception $e) {
        // Catch any MongoDB driver exceptions
        $errorMessage = $e->getMessage();
        $errorCode = $e->getCode();


        // Log error message and code to error log
        error_log('MongoDB Exception: ' . $errorMessage . ' Code: ' . $errorCode);
    
        // Respond with a JSON error message
        echo json_encode(['message' => "error", 'error' => $errorMessage, 'code' => $errorCode]);
    }
    exit();
    
}

?>