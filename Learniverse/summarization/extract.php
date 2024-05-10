<?php

session_start();
putenv('PYTHONPATH=/home/master/.local/lib/python3.9/site-packages:' . getenv('PYTHONPATH'));
putenv('PATH=/home/master/.local/bin:' . getenv('PATH'));
require_once __DIR__ . '../../vendor/autoload.php';


if (isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    // Ensure that the 'images' directory exists before moving the file
    $upload_directory = "../summarization/images/";
    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true); // Create directory if it doesn't exist
    }

    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_type === 'pdf') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/extracter.py";
            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);
            // exec('pip3 install PyPDF2', $output, $returnCode);
            // if ($returnCode !== 0) {
            //     echo "Failed to install PyPDF2: " . implode(" ", $output);
            //     exit;
            // }
            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
                echo $extracted_text;
            } else {
                // Output an error message

                echo "Error executing Python script0: $return_code" . implode(" ", $output);
            }
        } else {
            // File upload failed
            echo "Failed to move uploaded file.";
        }
    } elseif ($file_type === 'txt') {
        // Check if a file with the same name already exists
        $existing_file_path = $upload_directory . $file_name;
        if (file_exists($existing_file_path)) {
            // Calculate checksums of both the uploaded file and the existing file
            $uploaded_file_checksum = md5_file($file_tmp);
            $existing_file_checksum = md5_file($existing_file_path);

            // Compare checksums to determine if the files have the same content
            if ($uploaded_file_checksum === $existing_file_checksum) {
                // File already exists and has the same content
                $myfile = fopen($existing_file_path, "r") or die("Unable to open file!");
                echo fread($myfile, filesize($existing_file_path));
                fclose($myfile);
                exit; // Exit the script after sending the content
            }
        }

        // If the file doesn't have the same content or a file with the same name doesn't exist,
        // find a unique file name
        $unique_file_name = $file_name;
        $counter = 1;
        while (file_exists($upload_directory . $unique_file_name)) {
            // Append a counter to the file name until a unique name is found
            $unique_file_name = pathinfo($file_name, PATHINFO_FILENAME) . " ($counter)." . pathinfo($file_name, PATHINFO_EXTENSION);
            $counter++;
        }

        // Move the uploaded file to the 'images' directory with the unique file name
        if (move_uploaded_file($file_tmp, $upload_directory . $unique_file_name)) {
            // Open and read the text file
            $file_path = $upload_directory . $unique_file_name;
            $myfile = fopen($file_path, "r") or die("Unable to open file!");
            echo fread($myfile, filesize($file_path));
            fclose($myfile);
        } else {
            // Log error if file upload failed
            echo "Failed to move uploaded file.";
        }
    } elseif ($file_type === 'docx') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/docReader.py";

            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);

            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
                echo $extracted_text;
            } else {
                // Output an error message
                echo "Error executing Python script1: $return_code";
            }
        } else {
            // File upload failed
            echo "Failed to move uploaded file.";
        }
    } elseif ($file_type === 'pptx') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/pptxReader.py";

            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);

            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
                echo $extracted_text;
            } else {
                // Output an error message
                echo "Error executing Python script2: $return_code";
            }
        } else {
            // File upload failed
            echo "Failed to move uploaded file.";
        }
    } else {
        // Unsupported file type
        echo "Unsupported file type.";
    }
}


// Include MongoDB PHP library
require_once __DIR__ . '../../vendor/autoload.php';

// Connect to MongoDB
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select database and collections
$database = $client->selectDatabase('Learniverse');
$summaryCollection = $database->selectCollection('summaries');

// Function to insert a new summary for a user, replacing the oldest one if necessary
function insertSummary($userId, $question, $answer, $summaryCollection)
{
    // Find the user's document
    $userDoc = $summaryCollection->findOne(['userId' => $userId]);

    // Initialize summaries array
    $summariesArray = [];
    $time = time();
    // If the user document exists and has summaries, convert them to a PHP array
    if ($userDoc && isset($userDoc['summaries'])) {
        $summariesArray = iterator_to_array($userDoc['summaries']);
    }

    // If the user already has three summaries, remove the oldest one
    if (count($summariesArray) >= 3) {
        // Remove the oldest summary (first item in the array)
        array_shift($summariesArray);
    }

    // Append the new summary to the summaries array
    $newSummary = [
        'question' => $question,
        'answer' => $answer,
        'data_created' => $time
    ];
    $summariesArray[] = $newSummary;

    // Upsert the user's document with the updated summaries array
    $summaryCollection->updateOne(
        ['userId' => $userId],
        ['$set' => ['summaries' => $summariesArray]],
        ['upsert' => true]
    );

    echo $answer;
}




// Process the request if 'ogValue' is set in the POST data
if (isset($_POST['ogValue'])) {
    $ogValue = $_POST['ogValue'];

    $text = $ogValue;
    $client = OpenAI::client($ApiKey);
    $result = $client->chat()->create([
        'model' => 'gpt-3.5-turbo-0125', // Choose the desired OpenAI model
        'response_format' => ['type' => 'json_object'],
        'messages' => [
            ['role' => 'system', 'content' => 'You are a summarizer designed to output JSON with all the summary in one paragraph written inside this key (summary) and never return an empty response.'],
            ["role" => "user", "content" => $text]
        ],
    ]);

    $summary = json_decode($result->choices[0]->message->content);
    $summary_data = $summary->summary;
    // Call the insertSummary function to store the summary in the database
    insertSummary($_SESSION['email'], $ogValue, $summary_data, $summaryCollection);
}









if (isset($_FILES['fileUpload'])) {
    // Access the file properties correctly
    $file_name = $_FILES['fileUpload']['name'];
    $file_tmp = $_FILES['fileUpload']['tmp_name'];
    $extracted_text = '';

    // Ensure that the 'images' directory exists before moving the file
    $upload_directory = "flashcards/";
    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true); // Create directory if it doesn't exist
    }

    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if ($file_type === 'pdf') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/extracter.py";

            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);

            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
            } else {
                // Output an error message
                echo "Error executing Python script4: $return_code";
            }
        }
    } elseif ($file_type === 'txt') {
        // Check if a file with the same name already exists
        $existing_file_path = $upload_directory . $file_name;
        if (file_exists($existing_file_path)) {
            // Calculate checksums of both the uploaded file and the existing file
            $uploaded_file_checksum = md5_file($file_tmp);
            $existing_file_checksum = md5_file($existing_file_path);

            // Compare checksums to determine if the files have the same content
            if ($uploaded_file_checksum === $existing_file_checksum) {
                // File already exists and has the same content
                $myfile = fopen($existing_file_path, "r") or die("Unable to open file!");
                $extracted_text = fread($myfile, filesize($existing_file_path));
                fclose($myfile);
                exit; // Exit the script after sending the content
            }
        }

        // If the file doesn't have the same content or a file with the same name doesn't exist,
        // find a unique file name
        $unique_file_name = $file_name;
        $counter = 1;
        while (file_exists($upload_directory . $unique_file_name)) {
            // Append a counter to the file name until a unique name is found
            $unique_file_name = pathinfo($file_name, PATHINFO_FILENAME) . " ($counter)." . pathinfo($file_name, PATHINFO_EXTENSION);
            $counter++;
        }

        // Move the uploaded file to the 'images' directory with the unique file name
        if (move_uploaded_file($file_tmp, $upload_directory . $unique_file_name)) {
            // Open and read the text file
            $file_path = $upload_directory . $unique_file_name;
            $myfile = fopen($file_path, "r") or die("Unable to open file!");
            $extracted_text = fread($myfile, filesize($file_path));
            fclose($myfile);
        } else {
            // Log error if file upload failed
            echo "Failed to move uploaded file.";
        }
    } elseif ($file_type === 'docx') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/docReader.py";

            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);

            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
            } else {
                // Output an error message
                echo "Error executing Python script5: $return_code";
            }
        } else {
            // File upload failed
            echo "Failed to move uploaded file.";
        }
    } elseif ($file_type === 'pptx') {
        // Move the uploaded file to the 'images' directory
        if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
            // Assuming the path to the Python script is correct
            $python_script_path = "../python/pptxReader.py";

            // Construct the command to execute the Python script
            $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
            $output = array();
            $return_code = null;
            // Execute the command and capture output
            exec($command, $output, $return_code);

            // Check if the command executed successfully
            if ($return_code === 0) {
                // Output the extracted text
                $extracted_text = implode(" ", $output);
            } else {
                // Output an error message
                echo "Error executing Python script6: $return_code";
            }
        } else {
            // File upload failed
            echo "Failed to move uploaded file.";
        }
    } else {
        // Unsupported file type
        echo "Unsupported file type.";
    }




    // Include MongoDB PHP library
    require_once __DIR__ . '../../vendor/autoload.php';

    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");


    // Select database and collections
    $database = $client->selectDatabase('Learniverse');
    $flashcardCollection = $database->selectCollection('subjectReview');

    // Function to insert a new summary for a user, replacing the oldest one if necessary
    function insertFlashcard($userId, $subjectName, $flashcardData, $flashcardCollection)
    {
        // Find the user's document
        $userDoc = $flashcardCollection->findOne(['userId' => $userId,]);

        // Initialize user data array
        $userData = [
            'userId' => $userId,
            'subjects' => []
        ];

        // If the user document exists, convert it to a PHP array
        if ($userDoc) {
            $userData = (array) $userDoc;
        }

        $time = time();
        // If the subject doesn't exist, create a new entry for it
        $userData['subjects'][] = [
            'subjectName' => $subjectName,
            'data_created' => $time,
            'flashcards' => []
        ];
        $subjectIndex = count($userData['subjects']) - 1;

        foreach ($flashcardData as $cardIndex => $card) {

            // Extract the card number from the key (e.g., 'card1' => '1')
            $cardNumber = substr($cardIndex, 4);

            // Extract the flashcard content and answer from the nested array
            $content = $card['content'];
            $answer = $card['answer'];

            // Create the new flashcard array
            $newFlashcard = [
                'cardNumber' => $cardNumber,
                'content' => $content,
                'answer' => $answer,
            ];

            // Add the new flashcard to the user data
            $userData['subjects'][$subjectIndex]['flashcards'][] = $newFlashcard;
        }







        // Upsert the user's document with the updated user data
        $flashcardCollection->replaceOne(
            ['userId' => $userId],
            $userData,
            ['upsert' => true]
        );

        echo json_encode(array("success" => $time,));
    }


    // Process the 'extracted_text' as needed
    $tempFilePath = tempnam(sys_get_temp_dir(), 'txt');
    if ($tempFilePath === false) {
        // Failed to create a temporary file
        echo "Failed to create temporary file.";
    } else {
        // Write the contents to the temporary file
        if (file_put_contents($tempFilePath, $extracted_text) === false) {
            // Failed to write to the temporary file
            echo "Failed to write to temporary file.";
        } else {
            $text = $extracted_text;
            $client = OpenAI::client($ApiKey);
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125', // Choose the desired OpenAI model
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => "You are a 10 Flashcards summarizer designed to output JSON and never return empty response, write in this format (flashcards) that includes flashcard number (for example: card1), description of important concepts or definitions (content)[Do not include the answer in the content], (answer: name of the concept or term)[Do not include the answer in the content]."],
                    ["role" => "user", "content" => $text]
                ],
            ]);
            if ($result) {
                // Read the contents of the temporary file
                $flashcard_data = json_decode($result->choices[0]->message->content, true);
                // Call the insertSummary function to store the summary in the database
                insertFlashcard($_SESSION['email'], $file_name, $flashcard_data, $flashcardCollection);
            } else {
                // Output an error message
                echo "Error executing Python script7:";
            }
            // Clean up: Delete the temporary file
            unlink($tempFilePath);
        }
    }
}


if (isset($_POST['fileNames'])) {
    $fileNames = $_POST['fileNames'];
    require_once __DIR__ . '../../vendor/autoload.php';
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

    $baseDirectory = "../user_files" . $DIRECTORY_SEPARATOR . "{$user_id}" . $DIRECTORY_SEPARATOR;
    $upload_directory = "images/"; // Assuming the files are stored here

    // Construct the full path to the file
    $filePath = $baseDirectory . $fileNames;

    // Check if the file exists
    if (!file_exists($filePath)) {
        echo "File does not exist: $filePath";
        exit(); // Skip to the next file
    }

    // Determine the file's type
    $file_type = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Define the command based on the file type
    $python_script_path = "";
    switch ($file_type) {
        case 'pdf':
            $python_script_path = "../python/extracter.py";
            break;
        case 'docx':
            $python_script_path = "../python/docReader.py";
            break;
        case 'pptx':
            $python_script_path = "../python/pptxReader.py";
            break;
        case 'txt':
            // Directly output the content for txt files
            $myfile = fopen($filePath, "r") or die("Unable to open file!");
            echo fread($myfile, filesize($filePath));
            fclose($myfile);
    }

    if (!empty($python_script_path)) {
        // Construct the command to execute the Python script
        $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($filePath);
        $output = array();
        $return_code = null;
        // Execute the command and capture output
        exec($command, $output, $return_code);

        // Check if the command executed successfully
        if ($return_code === 0) {
            // Output the extracted text
            $extracted_text = implode(" ", $output);
            echo $extracted_text;
        } else {
            // Output an error message
            echo "Error executing Python script for file0: $fileNames";
        }
    } else {
        echo "Unsupported file type for file: $fileName";
    }
}

if (isset($_POST['flashcardFile'])) {
    $fileNames = $_POST['flashcardFile'];
    $file_name = basename($fileNames);
    require_once __DIR__ . '../../vendor/autoload.php';
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

    $baseDirectory = "../user_files" . $DIRECTORY_SEPARATOR . "{$user_id}" . $DIRECTORY_SEPARATOR;
    $upload_directory = "flashcards/"; // Assuming the files are stored here

    // Construct the full path to the file
    $filePath = $baseDirectory . $fileNames;

    // Check if the file exists
    if (!file_exists($filePath)) {
        echo "File does not exist: $filePath";
        exit(); // Skip to the next file
    }

    // Determine the file's type
    $file_type = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Define the command based on the file type
    $python_script_path = "";
    switch ($file_type) {
        case 'pdf':
            $python_script_path = "../python/extracter.py";
            break;
        case 'docx':
            $python_script_path = "../python/docReader.py";
            break;
        case 'pptx':
            $python_script_path = "../python/pptxReader.py";
            break;
        case 'txt':
            // Directly output the content for txt files
            $myfile = fopen($filePath, "r") or die("Unable to open file!");
            echo fread($myfile, filesize($filePath));
            fclose($myfile);
    }

    if (!empty($python_script_path)) {
        // Construct the command to execute the Python script
        $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($filePath);
        $output = array();
        $return_code = null;
        // Execute the command and capture output
        exec($command, $output, $return_code);

        // Check if the command executed successfully
        if ($return_code === 0) {
            // Output the extracted text
            $extracted_text = implode(" ", $output);
        } else {
            // Output an error message
            echo "Error executing Python script for file1: $fileNames";
        }
    } else {
        echo "Unsupported file type for file: $fileName";
    }



    // Include MongoDB PHP library
    require_once __DIR__ . '../../vendor/autoload.php';

    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");


    // Select database and collections
    $database = $client->selectDatabase('Learniverse');
    $flashcardCollection = $database->selectCollection('subjectReview');

    // Function to insert a new summary for a user, replacing the oldest one if necessary
    function insertFlashcard($userId, $subjectName, $flashcardData, $flashcardCollection)
    {
        // Find the user's document
        $userDoc = $flashcardCollection->findOne(['userId' => $userId,]);

        // Initialize user data array
        $userData = [
            'userId' => $userId,
            'subjects' => []
        ];

        // If the user document exists, convert it to a PHP array
        if ($userDoc) {
            $userData = (array) $userDoc;
        }

        $time = time();
        // If the subject doesn't exist, create a new entry for it
        $userData['subjects'][] = [
            'subjectName' => $subjectName,
            'data_created' => $time,
            'flashcards' => []
        ];
        $subjectIndex = count($userData['subjects']) - 1;

        foreach ($flashcardData as $cardIndex => $card) {

            // Extract the card number from the key (e.g., 'card1' => '1')
            $cardNumber = substr($cardIndex, 4);

            // Extract the flashcard content and answer from the nested array
            $content = $card['content'];
            $answer = $card['answer'];

            // Create the new flashcard array
            $newFlashcard = [
                'cardNumber' => $cardNumber,
                'content' => $content,
                'answer' => $answer,
            ];

            // Add the new flashcard to the user data
            $userData['subjects'][$subjectIndex]['flashcards'][] = $newFlashcard;
        }







        // Upsert the user's document with the updated user data
        $flashcardCollection->replaceOne(
            ['userId' => $userId],
            $userData,
            ['upsert' => true]
        );

        echo json_encode(array("success" => true, "time" => $time, "subjectName" => $subjectName));
    }


    // Process the 'extracted_text' as needed
    $tempFilePath = tempnam(sys_get_temp_dir(), 'txt');
    if ($tempFilePath === false) {
        // Failed to create a temporary file
        echo "Failed to create temporary file.";
    } else {
        // Write the contents to the temporary file
        if (file_put_contents($tempFilePath, $extracted_text) === false) {
            // Failed to write to the temporary file
            echo "Failed to write to temporary file.";
        } else {
            // Construct the command to call the Python script with the file path as an argument
            $text = $extracted_text;
            $client = OpenAI::client($ApiKey);
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo-0125', // Choose the desired OpenAI model
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => "You are a 10 Flashcards summarizer designed to output JSON and never return empty response, write in this format (flashcards) that includes flashcard number (for example: card1), description of important concepts or definitions (content)[Do not include the answer in the content], (answer: name of the concept or term)[Do not include the answer in the content]."],
                    ["role" => "user", "content" => $text]
                ],
            ]);
            if ($result) {
                // Read the contents of the temporary file
                $flashcard_data = json_decode($result->choices[0]->message->content, true);
                // Call the insertSummary function to store the summary in the database
                insertFlashcard($_SESSION['email'], $file_name, $flashcard_data, $flashcardCollection);
            } else {
                // Output an error message
                echo "Error executing Python script10: $resultsCode";
            }
            // Clean up: Delete the temporary file
            unlink($tempFilePath);
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['filing'])) {
    $file_name = $_FILES['filing']['name'];
    $file_tmp = $_FILES['filing']['tmp_name'];

    // Ensure that the 'images' directory exists before moving the file
    $upload_directory = "quizzes/";
    if (!file_exists($upload_directory)) {
        mkdir($upload_directory, 0777, true); // Create directory if it doesn't exist
    }

    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Move the uploaded file to the 'images' directory
    if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
        // Assuming the path to the Python script is correct
        $python_script_path = "../python/extracter.py";

        // Construct the command to execute the Python script
        $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($upload_directory . $file_name);
        $output = array();
        $return_code = null;
        // Execute the command and capture output
        exec($command, $output, $return_code);

        // Check if the command executed successfully
        if ($return_code === 0) {
            // Output the extracted text
            $extracted_text = implode(" ", $output);
            echo $extracted_text;
        } else {
            // Output an error message
            echo "Error executing Python script11: $return_code";
        }
    } else {
        // File upload failed
        echo "Failed to move uploaded file.";
    }
}

if (isset($_POST['fileNaming'])) {
    $fileNames = $_POST['fileNaming'];
    require_once __DIR__ . '../../vendor/autoload.php';
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

    $baseDirectory = "../user_files" . $DIRECTORY_SEPARATOR . "{$user_id}" . $DIRECTORY_SEPARATOR;
    $upload_directory = "quizzes/"; // Assuming the files are stored here

    // Construct the full path to the file
    $filePath = $baseDirectory . $fileNames;

    // Check if the file exists
    if (!file_exists($filePath)) {
        echo "File does not exist: $filePath";
        exit(); // Skip to the next file
    }

    // Determine the file's type
    $file_type = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Define the command based on the file type
    $python_script_path = "";
    switch ($file_type) {
        case 'pdf':
            $python_script_path = "../python/extracter.py";
            break;
        case 'docx':
            $python_script_path = "../python/docReader.py";
            break;
        case 'pptx':
            $python_script_path = "../python/pptxReader.py";
            break;
        case 'txt':
            // Directly output the content for txt files
            $myfile = fopen($filePath, "r") or die("Unable to open file!");
            echo fread($myfile, filesize($filePath));
            fclose($myfile);
    }

    if (!empty($python_script_path)) {
        // Construct the command to execute the Python script
        $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($filePath);
        $output = array();
        $return_code = null;
        // Execute the command and capture output
        exec($command, $output, $return_code);

        // Check if the command executed successfully
        if ($return_code === 0) {
            // Output the extracted text
            $extracted_text = implode(" ", $output);
            echo $extracted_text;
        } else {
            // Output an error message
            echo "Error executing Python script for file2: $fileNames";
        }
    } else {
        echo "Unsupported file type for file: $fileName";
    }
}
