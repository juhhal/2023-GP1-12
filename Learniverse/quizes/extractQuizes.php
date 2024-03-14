<?php

$quizType = isset($_POST['quizType']) ? $_POST['quizType'] : 'questionAnswers';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileUpload'])) {
    $error = '';
    $success = '';

    $pythonScript = '../python/quizzes.py'; 
    
    $fileTmpPath = $_FILES['fileUpload']['tmp_name'];
    $fileName = $_FILES['fileUpload']['name'];
    $fileSize = $_FILES['fileUpload']['size'];
    $fileType = $_FILES['fileUpload']['type'];
    $error = $_FILES['fileUpload']['error'];

    if ($error > 0) {
        $response = ['error' => true, 'message' => 'Error uploading the file!'];
    } else {
        $tempDir = sys_get_temp_dir();
        $tempFilePath = $tempDir . '/' . $fileName;

        if (move_uploaded_file($fileTmpPath, $tempFilePath)) {
            try {
              $cmd = escapeshellcmd("python3 $pythonScript " . escapeshellarg($tempFilePath) . " " . escapeshellarg($quizType));
              $output = shell_exec($cmd);

                if (!empty($output)) {
                    $resultFilePath = trim($output);
                    $quizData = file_get_contents($resultFilePath);

                    unlink($tempFilePath); 
                    unlink($resultFilePath); 

                    $response = ['success' => true, 'data' => json_decode($quizData, true)];
                } else {
                    $response = ['error' => true, 'message' => 'Failed to generate quizzes.'];
                }
            } catch (Exception $e) {
                $response = ['error' => true, 'message' => 'An error occurred during script execution.'];
            }
        } else {
            $response = ['error' => true, 'message' => 'Failed to move uploaded file.'];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = ['error' => true, 'message' => 'Invalid request'];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
