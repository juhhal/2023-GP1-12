<?php

// Get the user ID
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';

// Specify the destination directory
$upload_directory = "user_files/{$user_id}/Uploaded Files/";

// Ensure the directory exists, create it if not
if (!file_exists($upload_directory)) {
    mkdir($upload_directory, 0755, true);
}

// Check if a file is uploaded
if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];

    // Calculate the hash of the uploaded file
    $file_hash = hash_file('sha256', $file_tmp);

    // Check if a file with the same hash already exists in the destination directory
    $counter = 0;
    $new_file_name = $file_name;
    while (file_exists($upload_directory . $new_file_name)) {
        $existing_file_hash = hash_file('sha256', $upload_directory . $new_file_name);
        if ($existing_file_hash === $file_hash) {
            // The same file already exists, so we don't need to add it again
            echo "File already exists with the same content.";
            exit; // Stop further processing
        }
        
        $counter++;
        $file_parts = pathinfo($file_name);
        $new_file_name = $file_parts['filename'] . " ($counter)." . $file_parts['extension'];
    }

    // Move the uploaded file to the destination directory
    if(move_uploaded_file($file_tmp, $upload_directory . $new_file_name)) {
        echo "File uploaded successfully.";
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file uploaded or an error occurred.";
}
?>
