<?php
require "session.php";
//establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
//create a bulk writer to update data in the db
$bulkWrite = new MongoDB\Driver\BulkWrite;

//add new plan
if (isset($_POST['new-plan-name'])) {
    $planID = uniqid();
    $name = $_POST['new-plan-name'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $uploadedMats;
    $fullText="";
    function callPythonScript($filePath) {
        $command = escapeshellcmd("python3 python/extracter.py '" . $filePath . "'");
        $output = shell_exec($command);
        return $output;
    }
    if (isset($_POST['myFilesMats'])) {
        require_once __DIR__ . '/vendor/autoload.php';
        $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
        $database = $client->selectDatabase('Learniverse');
        $usersCollection = $database->selectCollection('users');

        // Get the email from the session
        $email = $_SESSION['email'];

        // Query the database for the user
        $userDocument = $usersCollection->findOne(['email' => $email]);

        // If user found, retrieve the _id
        $user_id = null;
        if ($userDocument) {
            $user_id = $userDocument->_id;
        }
        $path = 'user_files'.$DIRECTORY_SEPARATOR. $user_id . $DIRECTORY_SEPARATOR;
        $uploadedMats = json_decode($_POST['myFilesMats']);
        foreach ($uploadedMats as $f)
        $fullText = $fullText ."\n". callPythonScript($path.$f);
    }

    // Check if local files were uploaded
    if (isset($_FILES['localMats'])) {
        $fileCount = count($_FILES['localMats']['name']);
        echo "<br>";

        // Process each uploaded file
        for ($i = 0; $i < $fileCount; $i++) {
            $file = $_FILES['localMats']['tmp_name'][$i];
            $fileName = $_FILES['localMats']['name'][$i];
            $fileSize = $_FILES['localMats']['size'][$i];
            $fileError = $_FILES['localMats']['error'][$i];

            // Handle the file as needed
            if ($fileError === UPLOAD_ERR_OK) {
            // File was uploaded successfully
            $tmpFilePath = $_FILES['localMats']['tmp_name'][$i];
            // Move uploaded file to a permanent directory (optional)
            // Ensure your server has write permissions to the target directory
            $targetFilePath = "studyplanTemp". $DIRECTORY_SEPARATOR . basename($_FILES['localMats']['name'][$i]);
            if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
                // Call the Python script with the permanent file path
                $fullText = $fullText ."\n". callPythonScript($targetFilePath);
                
                // Delete the uploaded file after processing
                if (file_exists($targetFilePath)) {
                    unlink($targetFilePath);
                    echo "Processed and deleted file: $fileName<br>";
                } else {
                    echo "Failed to delete processed file: $fileName<br>";
                }
            } else {
                echo "Failed to move uploaded file for processing.<br>";
            }
            }
            echo "<br>";
        }
    }


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


    $bulk = new MongoDB\Driver\BulkWrite;
    //Define new calendar for the study plan
    $newCalendar = [
        'user_id' => $planID,
        'counter' => 0,
        'List' => [[]]
    ];

    // Add the insert operation to the BulkWrite instance
    $bulk->insert($newCalendar);

    //Execute BulkWriter
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    $result = $manager->executeBulkWrite("Learniverse.calendar", $bulk, $writeConcern);
    // end calendar creation

    // retrieve calendar id to generate distinctive ids for events in the loop
    $query = new MongoDB\Driver\Query(array('user_id' => $planID));
    $cursor = $manager->executeQuery('Learniverse.calendar', $query);
    $result_array = $cursor->toArray();
    $result_json = json_decode(json_encode($result_array), true);
    $id = $result_json[0]['counter'];

    // Python request

    $tempFilePath = tempnam(sys_get_temp_dir(), 'txt');
    if ($tempFilePath === false) {
        // Failed to create a temporary file
        echo "Failed to create temporary file.";
    } else {
        // Write the contents to the temporary file
        if (file_put_contents($tempFilePath, $fullText) === false) {
            // Failed to write to the temporary file
            echo "Failed to write to temporary file.";
        } else {
            // Construct the command to call the Python script with the file path as an argument
            $previousEventsJson = escapeshellarg(json_encode($previousEvents));
            $generate_command = "python3 python/StudyPlanner.py '" . escapeshellarg($tempFilePath) . "' '" . escapeshellarg($start) . "' '" . escapeshellarg($end) . "' " . $previousEventsJson;
            $generate_file = "python3 python/savePDF.py '" . escapeshellarg($tempFilePath);
            // Execute the command and capture output
            exec($generate_command, $plan, $resultsCode);
            if (isset($plan[0]) && trim($plan[0]) !== '') {
                $temp_file_path = trim($plan[0]);
                $plan_data = json_decode(file_get_contents($temp_file_path), true);
                $jsonData = $plan_data['study_plan'];
            } else {
                echo "Python script did not return any output.";
            }
        }
        unlink($tempFilePath);
    }


    //////DON'T COPY THIS SECTION FOR REGENERATING//////
    // Define file name and path
    $fileName = "". uniqid().".txt";
    $filePath = "studyplan/" . $fileName;
    // Write content to file
    file_put_contents($filePath, $fullText);

    // New file name with .pdf extension
    $newFileName = "".$planID.".pdf";
    $newFilePath = "studyplan/" . $newFileName;

    // Rename the file
    rename($filePath, $newFilePath);

        

 

    $data = $jsonData;
    $studyObjects = [];
    $studyPlanEvent = new MongoDB\Driver\BulkWrite;

    foreach ($data as $study) {
        $studyObject = new stdClass();
        $studyObject->startDate = $study['date'];
        $studyObject->endDate = date('Y-m-d', strtotime($study['date'] . ' + 1 day'));
        $studyObject->title = $study['title'];
        $studyObject->description = $study['description'];

        $studyObjects[] = $studyObject;
    }

    // Printing the study objects
    foreach ($studyObjects as $studyObject) {
        echo 'Start Date: ' . $studyObject->startDate . '<br>';
        echo 'End Date: ' . $studyObject->endDate . '<br>';
        echo 'Title: ' . $studyObject->title . '<br>';
        echo 'Task Descriptions: ' . $studyObject->description . '<br>';

        echo '<br>';
        $title = $studyObject->title;
        $description =  $studyObject->description;
        $uid = $planID;
        $start = $studyObject->startDate;
        $end =  $studyObject->endDate;


        $incrementedID = intval($id) + 1;

        $color = "mediumseagreen"; //default study plan color
        $event = ['id' => $id, 'title' => "STUDY: " . $title, 'description' => "Plan: $name. $description", 'reminder' => false, 'start' => $start, 'end' => $end, 'color' => $color, 'planID' => $planID];
        $id++;
        //
        $studyPlanEvent->update(
            [
                'user_id' => $planID,
            ],
            ['$push' => ['List' => $event]]
        );
        $studyPlanEvent->update(
            [
                'user_id' => $planID,
            ],
            ['$set' => ['counter' => $incrementedID]]
        );

        echo '<br>';
    }

    // define the plan
    $plan = [
        "planID" => $planID,
        "user_id" => $_SESSION['email'],
        "name" => $name,
        "creation_date" => date("Y-m-d"),
        "start" => $start,
        "end" => $end,
        "study_plan" => $studyObjects,
        "saved" => false,
        "color" => 'skyblue'

    ];
    $bulkWrite->insert($plan);
    $result = $manager->executeBulkWrite("Learniverse.studyPlan", $bulkWrite);
    if ($result->getInsertedCount() > 0) { //plan created successfully
        //execute the study plan events insertion command
        $result = $manager->executeBulkWrite('Learniverse.calendar', $studyPlanEvent);
        echo "success";
    } else
        echo "fail";
} elseif (isset($_POST['deletePlan'])) { //delete a plan
    $bulkWrite = new MongoDB\Driver\BulkWrite;
    $planID = $_POST['planID'];
    // Define the filter to identify the document
    $filter = ['planID' => $planID];

    // Add the delete operation to the BulkWrite instance
    $bulkWrite->delete($filter);

    // Execute the BulkWrite operation
    $result = $manager->executeBulkWrite("Learniverse.studyPlan", $bulkWrite);
    if ($result->getDeletedCount() > 0) {
        //delete the plan's own calendar
        $bulk = new MongoDB\Driver\BulkWrite;
        $filter = ['user_id' => $planID];
        $bulk->delete($filter);

        $result = $manager->executeBulkWrite("Learniverse.calendar", $bulk);

        //delete the plan's events from the user's calendar

        // Define the filter to find the document with the matching planID
        $filter = ['user_id' => $_SESSION['email'], 'List.planID' => $planID];

        // Define the update operation to remove the events with the matching planID
        $update = ['$pull' => ['List' => ['planID' => $planID]]];

        // Create an instance of MongoDB\Driver\BulkWrite and add the update operation
        $bul = new MongoDB\Driver\BulkWrite();
        $bul->update($filter, $update);
        $result = $manager->executeBulkWrite("Learniverse.calendar", $bul);

        echo 1;
    } else echo 0;
    exit;
} elseif (isset($_POST['savePlanCalendar'])) //save a plan calendar
{
    $planID = $_POST['planID'];
    //check if the plan is already saved
    $query = new MongoDB\Driver\Query(['planID' => $planID]);
    $result = $manager->executeQuery("Learniverse.studyPlan", $query);
    $p = $result->toArray()[0];
    if ($p->saved===true) {
        echo "saved";
        exit;
    }

    // retrieve plan calendar events to copy them
    // Define the query filter
    $filter = ['user_id' => $planID];
    // Define the query options
    $options = [
        'projection' => ['List' => 1], // Retrieve only the List field
    ];
    // Create a query object
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('Learniverse.calendar', $query);
    // Create an array to store the events
    $planEvents = [];
    // Get the first matching document from the result set
    $document = current($cursor->toArray());
    if ($document) {
        // Retrieve the List array from the document
        $list = $document->List;

        // Iterate over the List array starting from the second item
        for ($i = 1; $i < count($list); $i++) {
            // Retrieve the event object
            $event = $list[$i];

            // Add the event to the planEvents array
            $planEvents[] = $event;
        }
    }
    //get the user's calnedar to insert the plans events
    $query = new MongoDB\Driver\Query(array('user_id' => $_SESSION['email']));
    $cursor = $manager->executeQuery('Learniverse.calendar', $query);
    $result_array = $cursor->toArray();
    $result_json = json_decode(json_encode($result_array), true);
    $id = $result_json[0]['counter'];

    $bulk = new MongoDB\Driver\BulkWrite;
    foreach ($planEvents as $event) {
        // Check if $event has a planID property
        if (!isset($event->planID)) {
            // $event does not have a planID property, so add it
            $event->title = "STUDY: " . $event->title;
            $event->planID = $planID;
            $event->color = "mediumseagreen";
        }
        $event->id = $id;
        $event->reminder = true;
        $bulk->update(
            [
                'user_id' => $_SESSION['email'],
            ],
            ['$push' => ['List' => $event]]
        );
        $id++;
    }

    $bulk->update(
        [
            'user_id' => $_SESSION['email'],
        ],
        ['$set' => ['counter' => $id]]
    );

    $result = $manager->executeBulkWrite("Learniverse.calendar", $bulk);
    if ($result->getModifiedCount() > 0) {
        $bulk =  new MongoDB\Driver\BulkWrite;
        $bulk->update(
            [
                'planID' => $planID,
            ],
            ['$set' => ['saved' => true]]
        );
        $result = $manager->executeBulkWrite("Learniverse.studyPlan", $bulk);

        echo 1;
    } else echo 0;
} elseif (isset($_POST['regeneratePlan'])) //regenrate a plan
{

}

header("Location:studyplan.php");
