<?php
require "session.php";
// Establish connection
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$collectionName = "Learniverse.sharedSpace";

if (isset($_FILES['newFile'])) {
    $spaceID = $_POST['spaceID'];
    $fileID = uniqid();
    $owner = $_SESSION['email'];

    // Directory where the file will be uploaded
    $file_path = 'FILES/';

    // Get the file name and temporary file path
    $fileName = $_FILES['newFile']['name'];
    $tmpFilePath = $_FILES['newFile']['tmp_name'];

    // Generate the destination file path
    $destinationFilePath = $file_path . $fileID . '_' . $fileName;

    $filter = ['spaceID' => $spaceID];

    // Move the file to the destination directory
    if (move_uploaded_file($tmpFilePath, $destinationFilePath)) {
        $update = [
            '$push' => [
                'files' => [
                    'fileID' => $fileID,
                    'file_name' => $fileName,
                    'file_path' => $destinationFilePath,
                    'owner' => $owner
                ]
            ]
        ];

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($filter, $update);
        $result = $manager->executeBulkWrite($collectionName, $bulk);

        if ($result->getModifiedCount() > 0) {
            $dateTime = new DateTime();
            $string = $dateTime->format('Y-m-d H:i:s');

            $bulk = new MongoDB\Driver\BulkWrite();
            $bulk->update(
                ['spaceID' => $spaceID],
                ['$push' => ['logUpdates' => [
                    "type" => "upload",
                    "owner" => $_SESSION['email'],
                    "fileName" => $fileName,
                    "date" => $string
                ]]]
            );
            $log = $manager->executeBulkWrite($collectionName, $bulk);

            header("Location: viewSpace.php?space=" . $spaceID);
            exit;
        } else {
            $response = [
                'message' => 'failure'
            ];
            echo json_encode($response);
        }
    } else {
        // File upload failed
        $response = [
            'message' => 'Error uploading the file.'
        ];
        echo json_encode($response);
    }
} else {
    $fileID = uniqid();
    $owner = $_SESSION['email'];
    $filesMaterial = [];
    $uploadedMats;
    $fullText = "";
    $file_path = "FILES";
    $responses = [];

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
        $userDirectory = "user_files" . $DIRECTORY_SEPARATOR . "{$user_id}";
        $path = 'user_files' . $DIRECTORY_SEPARATOR . $user_id . $DIRECTORY_SEPARATOR;
        $uploadedMats = json_decode($_POST['myFilesMats']);
        $filesMaterial = $uploadedMats;

        $collectionName = "Learniverse.sharedSpace";
        // Find the document to update based on the spaceID
        $filter = ['spaceID' => $_POST["spaceID"]];
        foreach ($uploadedMats as $file) {
            $directory = dirname($file);
            $fileName = substr($file, strlen($directory) + 1);
            $file_path = $file_path . $DIRECTORY_SEPARATOR . $fileID . "_" . $fileName;

            // Specify the original PDF file path
            $originalFilePath = $userDirectory . $DIRECTORY_SEPARATOR . $file;

            // Specify the destination directory and the name for the new file
            $destinationFilePath = $file_path;

            // Copy the file
            if (copy($originalFilePath, $destinationFilePath)) {
                $update = [
                    '$push' => [
                        'files' => [
                            'fileID' => $fileID,
                            'file_name' => $fileName,
                            'file_path' => $file_path,
                            'owner' => $owner
                        ]
                    ]
                ];

                // Perform the update operation
                $bulk = new MongoDB\Driver\BulkWrite;
                $bulk->update($filter, $update);
                $result = $manager->executeBulkWrite($collectionName, $bulk);
                if ($result->getModifiedCount() > 0) {
                    $dateTime = new DateTime();
                    $string = $dateTime->format('Y-m-d H:i:s');
                    $bulk = new MongoDB\Driver\BulkWrite();
                    $bulk->update(
                        ['spaceID' => $_POST["spaceID"]],
                        ['$push' => ['logUpdates' => [
                            "type" => "upload",
                            "owner" => $_SESSION['email'],
                            "fileName" => $fileName,
                            "date" => $string
                        ]]]
                    );
                    $log = $manager->executeBulkWrite("Learniverse.sharedSpace", $bulk);
                    $response = [
                        'message' => 'success',
                        'file' => [
                            'fileID' => $fileID,
                            'file_name' => $fileName,
                            'file_path' => $file_path,
                            'owner' => $owner
                        ]
                    ];
                    $responses[] = $response;
                } else {
                    $responses = [
                        'message' => 'failure'
                    ];
                }
            } else {
                $responses = [
                    'message' => 'failure'
                ];
            }
        }
    }
}

header("Location: viewSpace.php?space=" . $_POST["spaceID"]);
