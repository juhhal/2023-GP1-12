<?php
session_start();// Require the MongoDB library
// require_once __DIR__ . '/vendor/autoload.php';
require 'dbConfig.php'; // Fixed syntax error with the quote

if (isset($_POST['action']) && $_POST['action'] === "addSubjectReview") {
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        echo json_encode(['error' => 'Session email not set or empty']);
        exit;
    }

    if (
        isset($_POST["subjectReviews"]) &&  !empty($_POST["subjectReviews"])
    ) {
        try {
            $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
            $bulk = new MongoDB\Driver\BulkWrite;    
            $userId = $_SESSION['email']; // Initialize $userId with the session email
            
            // Debugging: Log the session email being used
            error_log("Session email: " . $userId);

            $query = new MongoDB\Driver\Query(array('userId' => $_SESSION['email']));
            $cursor = $manager->executeQuery('Learniverse.subjectReview', $query);
            $result_array = $cursor->toArray();

            if (empty($result_array)) {
                // Handle case where there is no such user
                echo json_encode(['success' => 'User not found in the database']);
                exit;
            }

            $result_json = json_decode(json_encode($result_array), true);
            $id = $result_json[0]['counter'] ?? 0; // Use null coalescing operator to provide a default
            $incrementedID = $id + 1;

            foreach ($_POST["subjectReviews"] as $subjectReview) { // Loop through each subject review
                $subjectName = trim($subjectReview["subjectName"]);
                $questionsAndAnswers = [];

                foreach ($subjectReview["questions"] as $questionAndAnswer) {
                    $question = trim($questionAndAnswer["question"]);
                    $answer = trim($questionAndAnswer["answer"]);
                    $questionsAndAnswers[] = ["question" => $question, "answer" => $answer];
                }

                $flashcard = [
                    'subjectName' => $subjectName,
                    'questionsAndAnswers' => $questionsAndAnswers,
                    'id' => $incrementedID,
                    'date_created' => time()
                ];

                $bulk->update(
                    ['userId' => $userId],
                    ['$push' => ['subjects' => $flashcard]],
                    ['upsert' => true]
                );
                
                $bulk->update(
                    ['userId' => $userId],
                    ['$set' => ['counter' => $incrementedID]],
                    ['upsert' => true]
                );
            }

            $result = $manager->executeBulkWrite('Learniverse.subjectReview', $bulk);
        
            if ($result->isAcknowledged()) {
                echo json_encode(['status' => 1]);
            } else {
                echo json_encode(['success' => 'Event Add request failed!']);
            }
          
        } catch (Exception $e) {
            echo json_encode(['success' => 'An exception occurred: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => 'Invalid action or session email']);
}

// // Create a MongoDB client
// $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// // Select the database and collection
// $database = $connection->Learniverse;
// $FileCollection = $database->userFile;
// $SubjectReviewCollection = $database->subjectReview;



// function getuserFile($userId, $name, $FileCollection)
// {
//     try {
//         // Perform login validation
//         $files = $FileCollection->findOne(['userId' => $userId, 'fileName' => $name]);
//         if ($files) {
//             // Converting the MongoDB cursor to an associative array
//             $filesArray = iterator_to_array($files);
//             return $filesArray;
//         } else {
//             return null;
//         }
//     } catch (Exception $e) {
//         // Printing an error message if an exception occurs
//         printf($e->getMessage());
//         return null;
//     }
// }




// function getuserFileById($userId, $id, $FileCollection)
// {
//     try {


//         $files = $FileCollection->find([
//             'userId' => $userId,
//             '_id' => new MongoDB\BSON\ObjectID($id)
//         ]);

//         if ($files  ) {
//            // Converting the MongoDB cursor to an associative array
//             $filesArray = iterator_to_array($files);
//             return $filesArray;
//         } else {
//             return null;
//         }
//     } catch (Exception $e) {
//         // Printing an error message if an exception occurs
//         printf($e->getMessage());
//         return null;
//     }
// }

// if (isset($_POST['action']) && $_POST['action'] === "addSubjectReview") {
//     if (
//         isset($_POST["subjectReviews"]) && is_array($_POST["subjectReviews"]) && !empty($_POST["subjectReviews"])
//     ) {
//         try {
//             $bulk = new MongoDB\Driver\BulkWrite;    

//             $query = new MongoDB\Driver\Query(array('user_id' => $_SESSION['email']));
//             $cursor = $manager->executeQuery('Learniverse.subjectReview', $query);
//             $result_array = $cursor->toArray();
//             $result_json = json_decode(json_encode($result_array), true);
//             $id = $result_json[0]['counter'];
//             $incrementedID = intval($id)+1;

//                 $subjectName = trim($subjectReview["subjectName"]);
//                 $questionsAndAnswers = [];

//                 // Extract questions and answers from each subject review
//                 foreach ($subjectReview["questions"] as $questionAndAnswer) {
//                     $question = trim($questionAndAnswer["question"]);
//                     $answer = trim($questionAndAnswer["answer"]);
//                     $questionsAndAnswers[] = ["question" => $question, "answer" => $answer];
//                 }
//                 $flashcard = [
//                         'subjectName' => $subjectName,
//                         'questionsAndAnswers' => $questionsAndAnswers,
//                         'id' => $incrementedID
//                     ];
//                 // Insert new subject review with incremented counter
//                 $bulk->update(
//                     ['userId' => $_SESSION['email']],
//                     ['$push' => ['subjects' => $flashcard]],
//                     ['upsert' => true] // Create the user document if it doesn't exist
//                 );
//                 $bulk->update(
//                     [
//                         'user_id' => $_SESSION['email'],
//                 ],
//                     ['$set' => ['counter' => $incrementedID]],
//                     ['upsert' => true] // Create the user document if it doesn't exist

//                 );

//                 $result = $manager->executeBulkWrite('Learniverse.subjectReview', $bulk);
        
//                 if($result->isAcknowledged()){
//                 $output = [
//                     'status' => 1
//                 ];
//                 echo json_encode($output);}
//                 else{
//                     echo json_encode(['error' => 'Event Add request failed!']);
//                 }
//             }
//     }
// }











// if (isset($_POST['action']) && $_POST['action'] === "getSubjectReview") {

//     if (isset($_POST["id"]) && !empty($_POST["id"])) {
//         $subjectReviewId = trim($_POST["id"]);

//         $user = getUser($_SESSION["email"], $Usercollection);
        
//         try {

//             $userId = (string) $user['_id'];
//             $fileExiest = getuserFileById($userId, $subjectReviewId, $FileCollection);


        
//             $subjectReviewName = $fileExiest[0]['fileName'];

       
//             // Perform login validation
//             $subjectReview = $SubjectReviewCollection->find(
//                 [
//                     'userId' => $userId,
//                     'subjectName' => $subjectReviewName
//                 ]
//             );

//             if ($subjectReview) {
//              $subjectReviewArray = iterator_to_array($subjectReview);
   
//                 if ($subjectReviewArray && count($subjectReviewArray) > 0) {
             
//                     echo '<!-- Modal View -->
//                         <div class="modal fade" id="viewModel" tabindex="-1" aria-labelledby="viewModelLabel" aria-hidden="true"
//                             style="padding-top: 24px; ">
//                             <div class="modal-dialog">
//                             <div class="modal-content">
//                                 <div class="modal-header">
//                                 <h5 class="modal-title" id="viewModelLabel"> ' . $subjectReviewArray[0]["subjectName"] . '</h5>
//                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
//                                     <span aria-hidden="true">&times;</span>
//                                 </button>
//                                 </div>
//                                     <div class="modal-body">
//                                     <div class="flip-card">
//                                     <div class="flip-card-inner">
//                                     <div class="flip-card-front">
//                                     <h1>' . $subjectReviewArray[0]["question"] . '</h1>
//                                     </div>
//                                     <div class="flip-card-back">
//                                     <p>' . $subjectReviewArray[0]["answer"] . '</p>
//                                 </div>
//                                 </div>
//                                 </div>
//                                 </div>
//                                 <div class="modal-footer">
//                                 <div class="text-center">
//                                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Last</button>
//                                     <button type="button" class="btn btn-primary">Next</button>
//                                 </div>
//                                 </div>
//                                 </div>
//                             </div>
//                         </div> ';
//                 }else {
//                     // return "Don't have recored!";
//                     echo "Don't have recored!";
//                 }    

//             } else {
//                 // return "Don't have recored!";
//                 echo "Don't have recored!";
//             }


//         } catch (Exception $e) {
//             // Printing an error message if an exception occurs
//             printf($e->getMessage());
//             return null;
//         }
//     }
// }



