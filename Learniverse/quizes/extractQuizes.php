<?php
require_once __DIR__ . '../../vendor/autoload.php';
putenv('PYTHONPATH=/home/master/.local/lib/python3.9/site-packages:' . getenv('PYTHONPATH'));

$quizType = isset($_POST['quizType']) ? $_POST['quizType'] : 'questionAnswers';
$response;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pythonScript = '../python/quizzes.py';
    $tempFilePath = '';
    $cleanupRequired = false;

    if (isset($_FILES['fileUpload'])) {
        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileName = $_FILES['fileUpload']['name'];
        $error = $_FILES['fileUpload']['error'];

        if ($error > 0) {
            $response = ['msg' => 'eror', 'message' => 'Error uploading the file!'];
            respondAndExit($response);
        } else {
            $tempDir = sys_get_temp_dir();
            $tempFilePath = $tempDir . '/' . $fileName;
            $cleanupRequired = true;

            if (!move_uploaded_file($fileTmpPath, $tempFilePath)) {
                $response = ['msg' => 'roro', 'message' => 'Failed to move uploaded file.'];
                respondAndExit($response);
            }
        }
    } elseif (isset($_POST['filePath'])) {
        $filePath = $_POST['filePath'];
        if (file_exists($filePath)) {
            $tempFilePath = $filePath;
        } else {
            $response = ['msg' => 'brr', 'message' => 'File path does not exist.'];
            respondAndExit($response);
        }
    } else {
        $response = ['msg' => 'okrr', 'message' => 'No file upload or file path provided.'];
        respondAndExit($response);
    }

    try {
        // $cmd = escapeshellcmd("python3 $pythonScript " . escapeshellarg($tempFilePath) . " " . escapeshellarg($quizType));
        // $output = shell_exec($cmd);
        $text;
        $python_script_path = "../python/extracter.py";
        // Construct the command to execute the Python script
        $command = "python3 " . escapeshellarg($python_script_path) . " " . escapeshellarg($tempFilePath);
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
            $text = $extracted_text;
        } else {
            // Output an error message

            echo "Error executing Python script0: $return_code" . implode(" ", $output);
        }
        $system_prompt;
        if ($quizType == "questionAnswers")
            $system_prompt = "You are a multiple choice quiz generator designed to output JSON and never return an empty response, write in this format array of questions (for example: questions) with a length of 10,  and includes the following array: question name (question), 3 multiple choices array (answers), correct choice answer (correctAnswer), and a score for the quality of the question from 1 to 10 (score).";
        elseif ($quizType == "trueFalse")
            $system_prompt = "Generate a JSON-formatted true or false quiz with the following specifications in one string: The output should be an object containing a key 'questions' with its value being an array of 10 objects. Each object must include: 'question' (string), 'correctAnswer' (a string of either 'true' or 'false' only), 'answers' (an array with two elements: ['true', 'false'] only), and 'score' (an integer from 1 to 10 indicating the question's quality). Ensure questions vary in difficulty and are non-repetitive, with no empty responses.";
        elseif ($quizType == "mixed")
            $system_prompt = "Generate a JSON-formatted mixed quiz (must include both multiple choice and true/false questions) with the following specifications in a single string:
            The output should be an object containing a key 'questions' with its value being an array of 10 objects. 
            Each object must include: 'question' (a string), 'answers' (an array, which will contain either 3 multiple choice answers or ['true', 'false'] for true/false questions), 'correctAnswer' (the correct choice answer as a string, which will be one of the items from 'answers'), and 'score' (an integer from 1 to 10 indicating the question's quality).
            Ensure questions vary in difficulty, cover a range of topics, and are non-repetitive, with no empty responses.";

        $client = OpenAI::client($ApiKey);
        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo-0125', // Choose the desired OpenAI model
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ["role" => "user", "content" => $text]
            ],
        ]);
        $output = $result->choices[0]->message->content;
        if ($output) {
            // $resultFilePath = trim($output);
            // $quizData = file_get_contents($resultFilePath);

            // if ($cleanupRequired) {
            //     unlink($tempFilePath);
            // }
            // unlink($resultFilePath);

            $response = ['msg' => "success", 'data' => json_decode($output, true)];
        } else {
            $response = ['msg' => 'error', 'error' => 'Failed to generate quizzes.'];
        }
    } catch (Exception $e) {
        $response = ['msg' => 'exception', 'message' => 'An error occurred during script execution.'];
    }

    respondAndExit($response);
} else {
    $response = ['msg' => 'invalid', 'message' => 'Invalid request'];
    respondAndExit($response);
}

function respondAndExit($response)
{
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
