<?php
require "session.php";

// Connect to MongoDB
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// New FILE
if (isset($_FILES['newFile'])) {
    $file = $_FILES['newFile'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];

        // Move the uploaded file to the 'FILES' directory with the original file name
        $upload_directory = "FILES/";
        $file_path = $upload_directory . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the files attribute in the sharedSpace collection
            $collectionName = "Learniverse.sharedSpace";

            // Find the document to update based on the spaceID
            $filter = ['spaceID' => $_POST["spaceID"]];

            $fileID = uniqid();
            // Set the new value for the files attribute
            $update = [
                '$push' => [
                    'files' => [
                        'fileID' => $fileID,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                        'owner' => $_SESSION['email']
                    ]
                ]
            ];

            // Perform the update operation
            $bulk = new MongoDB\Driver\BulkWrite;
            $bulk->update($filter, $update);
            $result = $manager->executeBulkWrite($collectionName, $bulk);

            if ($result->getModifiedCount() > 0) {
                $response = [
                    'message' => 'success',
                    'file' => [
                        'fileID' => $fileID,
                        'file_name' => $file_name,
                        'file_path' => $file_path
                    ]
                ];
            } else {
                $response = [
                    'message' => 'failure'
                ];
            }

            echo json_encode($response);
        } else {
            // Log error if file upload failed
            echo "Failed to move uploaded file.";
        }
    } else {
        // Log error if upload failed
        echo "File upload failed.";
    }
}
// Existing FILE
else if (isset($_POST['existingFile']) && isset($_POST['spaceID']) && isset($_POST['fileName'])) {
    $collectionName = "Learniverse.sharedSpace";

    // Find the document to update based on the spaceID
    $filter = ['spaceID' => $_POST["spaceID"]];

    // Search for a file in the 'FILES' directory
    $file_name = $_POST["fileName"];
    $file_path = "FILES/" . $_POST['existingFile'] . ".pdf";

    if (file_exists($file_path)) {
        $fileID = uniqid();

        // Set the new value for the files attribute
        $update = [
            '$push' => [
                'files' => [
                    'fileID' => $fileID,
                    'file_name' => $file_name,
                    'file_path' => $file_path,
                    'owner' => $_SESSION['email']
                ]
            ]
        ];

        // Perform the update operation
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, $update);
        $result = $manager->executeBulkWrite($collectionName, $bulk);

        if ($result->getModifiedCount() > 0) {
            $response = [
                'message' => 'success',
                'file' => [
                    'fileID' => $fileID,
                    'file_name' => $file_name,
                    'file_path' => $file_path
                ]
            ];
        } else {
            $response = [
                'message' => 'failure'
            ];
        }

        echo json_encode($response);
    } else {
        // File does not exist in the 'FILES' directory
        echo "File not found.";
    }
} else {
    // Handle the case when no file or parameters are provided
    echo "Invalid request.";
}
?>