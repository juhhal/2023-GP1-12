<?php

$quizType = isset($_POST['quizType']) ? $_POST['quizType'] : 'questionAnswers';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pythonScript = '../python/quizzes.py';
    $tempFilePath = '';
    $cleanupRequired = false; 

    if (isset($_FILES['fileUpload'])) {
        $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
        $fileName = $_FILES['fileUpload']['name'];
        $error = $_FILES['fileUpload']['error'];

        if ($error > 0) {
            $response = ['error' => true, 'message' => 'Error uploading the file!'];
            respondAndExit($response);
        } else {
            $tempDir = sys_get_temp_dir();
            $tempFilePath = $tempDir . '/' . $fileName;
            $cleanupRequired = true; 

            if (!move_uploaded_file($fileTmpPath, $tempFilePath)) {
                $response = ['error' => true, 'message' => 'Failed to move uploaded file.'];
                respondAndExit($response);
            }
        }
    } elseif (isset($_POST['filePath'])) { 
        $filePath = $_POST['filePath'];
        if (file_exists($filePath)) {
            $tempFilePath = $filePath;
        } else {
            $response = ['error' => true, 'message' => 'File path does not exist.'];
            respondAndExit($response);
        }
    } else {
        $response = ['error' => true, 'message' => 'No file upload or file path provided.'];
        respondAndExit($response);
    }

    try {
        $cmd = escapeshellcmd("python3 $pythonScript " . escapeshellarg($tempFilePath) . " " . escapeshellarg($quizType));
        $output = shell_exec($cmd);

        if (!empty($output)) {
            $resultFilePath = trim($output);
            $quizData = file_get_contents($resultFilePath);

            if ($cleanupRequired) {
                unlink($tempFilePath);
            }
            unlink($resultFilePath); 

            $response = ['success' => true, 'data' => json_decode($quizData, true)];
        } else {
            $response = ['error' => true, 'message' => 'Failed to generate quizzes.'];
        }
    } catch (Exception $e) {
        $response = ['error' => true, 'message' => 'An error occurred during script execution.'];
    }

    respondAndExit($response);
} else {
    $response = ['error' => true, 'message' => 'Invalid request'];
    respondAndExit($response);
}

function respondAndExit($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
