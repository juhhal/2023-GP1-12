<?php
// include("connection.php");
if (!isset($_SESSION)) {
    session_start();
}
// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;
$FileCollection = $database->userFile;



// Function to retrieve user information based on email
function getUser($email, $Usercollection)
{
    try {

        // Perform login validation
        $user = $Usercollection->findOne(['email' => $email]);

        if ($user) {
            // Converting the MongoDB cursor to an associative array
            $userArray = iterator_to_array($user);
            return $userArray;
        } else {
            return null;
        }
    } catch (Exception $e) {
        // Printing an error message if an exception occurs
        printf($e->getMessage());
        return null;
    }
}



if (isset($_POST)) {

    $user = getUser($_SESSION["email"], $Usercollection);

    if ($_FILES['fileUpload']['type'] == 'application/pdf') {

        $uploadTo = "/uploads/";
        $allowFileType = array('pdf', 'doc');
        $fileName = $_FILES['fileUpload']['name'];
        $tempPath = $_FILES["fileUpload"]["tmp_name"];

        $basename = basename($fileName);
        $originalPath = __DIR__ . $uploadTo . $basename;
        echo $originalPath;
        $fileType = pathinfo($originalPath, PATHINFO_EXTENSION);
        if (!empty($fileName)) {

            if (in_array($fileType, $allowFileType)) {
                // Upload file to server 
                if (move_uploaded_file($tempPath, $originalPath)) {
                   $id = (string) $user['_id'];

                    if (!empty($id)) {
                        $newFile = ["userId" => $id, "fileName" => $fileName, "data_created" => time()];
                        $result = $FileCollection->insertOne($newFile);

                        if ($result) {
                            echo "" . $result . "Upload File and Save record Successfully" . $fileName;
                        } else {
                            $response = 'Upload File failed. Please try again later.';
                        }
                    }

                } else {
                   echo 'File Not uploaded ! try again';

                }
            } else {
                echo $fileType . " file type not allowed";
            }
        } else {
            echo "Please Select a file";
        }
  }

}else{
    echo "Not post form";
}   