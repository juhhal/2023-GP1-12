<?php
    // Connect to MongoDB
    session_start();
    require_once __DIR__ . '../../vendor/autoload.php';
    
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

    // Select the database and collection
    $database = $client->selectDatabase('Learniverse');
    $summariesCollection = $database->selectCollection('summaries');
    header('Content-Type: application/json'); // Set correct content type for JSON response

    if (isset($_POST['date'])) {
               // Extract 'date_created' and 'userId' from POST data and session
               $dateCreated = (int)$_POST['date']; // Cast to int if it's sent as a string
               $userId = $_SESSION['email'];
           
               // Update criteria to match the document by 'userId'
               $updateCriteria = [
                   'userId' => $userId
               ];
           
               // Update operation to remove the summary with the specified 'date_created'
               $updateResult = $summariesCollection->updateOne(
                   $updateCriteria,
                   [
                       '$pull' => [
                           'summaries' => [
                               'data_created' => $dateCreated
                           ]
                       ]
                           ],         ['multi' => false]
       
             
               );
    
        // Check if the update operation was successful
        if ($updateResult->getModifiedCount() > 0) {
            http_response_code(200); // OK
            echo json_encode(['success' => true, 'message' => 'Summary deleted successfully']);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['error' => true, 'message' => 'Summary not found or deletion failed']);
        }
    } 
    




header('Content-Type: application/json');

if (isset($_POST['datas'])) {

    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
    $database = $client->selectDatabase('Learniverse');
    $summariesCollection = $database->selectCollection('summaries');

    // Get the user ID from the session and date_created from POST data
    $userId = 'jojoalomar4@yahoo.com'; // Ensure the session has started and the email is set
    $dateCreated = intval($_POST['datas']);

    // Query the database for the summary
    $summaryDocument = $summariesCollection->findOne([
        'userId' => $userId,
        'summaries.data_created' => $dateCreated
    ]);

    // Check if a document was found
    if ($summaryDocument) {
        // Convert BSONDocument to an associative array
        $summariesArray = $summaryDocument->getArrayCopy();

        // Search for the specific summary within the document
        $found = false; // flag to check if we found the summary
        foreach ($summariesArray['summaries'] as $sum) {
            if ((int)$sum['data_created'] === $dateCreated) {
                $found = true;
                echo json_encode($sum);
                exit; // Terminate the script
            }
        }

        if (!$found) {
            echo json_encode(['error' => 'Summary not found']);
        }
    } else {
        // If no document was found for the user
        echo json_encode(['error' => 'No document found for the user']);
    }
} else {
    // If the 'datas' POST variable wasn't set
    echo json_encode(['error' => 'Data not provided']);
}

    