<?php

if (!isset($_SESSION)) {
  session_start();
}
// ini_set('display_errors', '0'); // Turn off error displaying
// error_reporting(E_ERROR | E_PARSE); // Report only errors, not warnings or notices

// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;
$FileCollection = $database->subjectReview;


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

function getuserFile($id, $FileCollection)
{
  try {
    // Perform login validation
    $files = $FileCollection->find(['userId' => $id]);
    if ($files) {
      // Converting the MongoDB cursor to an associative array
      $filesArray = iterator_to_array($files);
      return $filesArray;
    } else {
      return null;
    }
  } catch (Exception $e) {
    // Printing an error message if an exception occurs
    printf($e->getMessage());
    return null;
  }
}

$user = null;
$googleID = null;

if (isset($_SESSION["email"])) {
  // Fetch the files directly using the user ID
  $filesList = getFilesByUserId($_SESSION["email"], $FileCollection);
}

function getFilesByUserId($userId, $FileCollection) {
  try {
      $users = $FileCollection->findOne(['userId' => $_SESSION["email"]]);
      if ($users) {
          return isset($users['subjects']) ? $users['subjects'] : [];
      } else {
          return [];
      }
  } catch (Exception $e) {
      printf($e->getMessage());
      return [];
  }
}

?>

<!DOCTYPE html>

<head>
  <meta charset="UTF-8">
  <title>Flashcards </title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="stylesheet" href="theFiles.css">
  <link rel="stylesheet" href="header-footer.css">

  <!-- PROFILE STYLESHEET -->
  <link rel="stylesheet" href="profile.css">

  <!-- Custom stylesheet -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- Sweetalert2 -->
  <!-- <script src="js/sweetalert2.all.min.js"></script> -->
  <!-- GPA STYLESHEET -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<?php
if (isset($_GET['file'])) {
  $path = $_GET['file'];
  $file_name = basename($path);
  $safe_path = escapeshellarg($path);
  $command = escapeshellcmd("python3 python/extracter.py '" . $path . "'");
  $outputString = shell_exec($command);  // After your exec() call

  require_once __DIR__ . '/vendor/autoload.php';

  // Connect to MongoDB
  $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
  
  
  // Select database and collections
  $database = $client->selectDatabase('Learniverse');
  $flashcardCollection = $database->selectCollection('subjectReview');
  
  // Function to insert a new summary for a user, replacing the oldest one if necessary
  function insertFlashcard($userId, $subjectName, $flashcardData, $flashcardCollection) {
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
  
      echo "<script type='text/javascript'>
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {

          retrieve($time, '$subjectName');
        }, 3000);
      });
    </script>";    }
  
  
  // Process the 'extracted_text' as needed
  $tempFilePath = tempnam(sys_get_temp_dir(), 'txt');
  if ($tempFilePath === false) {
      // Failed to create a temporary file
      echo "Failed to create temporary file.";
  } else {
      // Write the contents to the temporary file
      if (file_put_contents($tempFilePath, $outputString) === false) {
          // Failed to write to the temporary file
          echo "Failed to write to temporary file.";
      } else {
          // Construct the command to call the Python script with the file path as an argument
          $flashcard_command = "python3 python/flashcards.py '" . $tempFilePath . "'";  // Redirect stderr to stdout
          // Execute the command and capture output
          exec($flashcard_command, $content, $resultsCode);
          // Check if the command executed successfully
          if ($resultsCode === 0) {
              // Read the contents of the temporary file
              $temp_file_path = trim($content[0]);
              $flashcard_data = json_decode(file_get_contents($temp_file_path), true);
              // Call the insertSummary function to store the summary in the database
              $result = insertFlashcard($_SESSION['email'], $file_name, $flashcard_data, $flashcardCollection);
              echo json_encode($result);
             } else {
              // Output an error message
              echo "Error executing Python script.";
          }
          // Clean up: Delete the temporary file
          unlink($tempFilePath);
      }}
    }
?>
<style>

#tools_div {
  background-color: #bf97d8;
  width: 12%;
  padding: 2% 0.5% 0;
  float: left;
  margin-left: -13.9%;
  transition: 0.2s;
  box-shadow: 0px 1px 2px 1px rgba(58, 60, 67, 0.15);
  position: fixed;
  z-index: 100;
  height: 99vh;
}

.tool_list {
  display: inline-block;
  padding-inline-start: 0;
}

.tool_item {
  font-family: "Gill Sans", "Gill Sans MT", "Calibri", "Trebuchet MS",
    "sans-serif";
  font-size: 1.1rem !important;
  list-style-type: none;
  padding: 9%;
  margin-bottom: 5%;
  white-space: nowrap;
  text-indent: 1rem;
  font-size: 0.9rem;
  text-align: left;
  color: #000e23;
  width: 124%;
  transition: color 0.3s, background-color 0.3s;
  border-radius: 5%;
}

.tool_item:hover {
  cursor: pointer;
  transition-duration: 0.3s;
  color: #faf7ff !important;
  background-color: rgba(212, 179, 233, 0.933);
}

.tool_item:hover a {
  cursor: pointer;
  transition-duration: 0.3s;
  color: #faf7ff !important;
  background-color: rgba(212, 179, 233, 0.933);
}

.tool_item a {
  text-decoration: none;
  color: #000e23;
}

.tool_item img {
  width: 15%;
  float: left;
  margin-left: 2%;
}

#file-upload-button{
  background-color: #bf97d8;
    color: white;
    border: 1px solid #bf97d8;
}

  .btn-cl {
    background-color: #bf97d8;
    color: white;
    border: 1px solid #bf97d8;
  }

  /* style="border: 1px solid #d1b4e3;" */
  .btn-cl:hover {
    background-color: #bf97d8;
    color: white;
    border: 1px solid #bf97d8;
  }

  .btn-cl:focus {
    background-color: #bf97d8;
    color: white;
    border: 1px solid #bf97d8;
  }

  .table-primary tr{
    border: 1px solid #d1b4e3 !important;
    color: white;
    background-color: #d1b4e3 !important;

  }

  .fa-eye {
    font-size: 8px;
    color: white;
    background-color: #ec947e;
  }


  .file-eye:hover {
    background-color: #cc7c68;
    color: white;
  }

  .file-eye:focus {
    background-color: #cc7c68;
    color: white;
  }


  .file-edit {
    font-size: 8px;
    color: white;
    background-color: #ec947e;
  }

  .file-edit:hover {
    background-color: #cc7c68;
    color: white;
  }

  .file-edit:focus {
    background-color: #cc7c68;
    color: white;
  }

  .file-delete {
    font-size: 8px;
    color: white;
    background-color: #ec947e;
  }

  .file-delete:hover {
    background-color: #cc7c68;
    color: white;

  }

  .file-delete:focus {
    background-color: #cc7c68;
    color: white;
  }

  /* Style for the loading overlay */
  .overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none; /* Hide by default */
  }
  .spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }


  /* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
  .flip-card {
    background-color: transparent;
    /* width: 300px; */
    height: 300px;
    border: 1px solid #f1f1f1;
    perspective: 1000px;
    /* Remove this if you don't want the 3D effect */
  }

  /* This container is needed to position the front and back side */
  .flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s;
    transform-style: preserve-3d;
  }

  /* Do an horizontal flip when you move the mouse over the flip box container */
  .flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
  }

  /* Position the front and back side */
  .flip-card-front,
  .flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    /* Safari */
    backface-visibility: hidden;
  }

  /* Style the front side (fallback if image is missing) */
  .flip-card-front {
    background-color: #Faf7ff;
    color: black;
  }

  /* Style the back side */
  .flip-card-back {
    background-color: #Faf7ff;
    color: black;
    transform: rotateY(180deg);
  }

  /* file modal */
  /* Modal Background */
  #fileModal {
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Modal Container */
  .modal-container {
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    /* Adjust based on your preference */
    max-height: 80%;
    overflow-y: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    /* Center-align the items vertically */
  }

  /* Directory and File Items */
  .dirItem,
  .fileItem {
    padding: 10px;
    margin: 5px 0;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    cursor: pointer;
    width: 100%;
    /* Make items full width of their container */
    box-sizing: border-box;
    /* Include padding and border in the width */
  }

  /* Optionally, if you want the items to be only as wide as their content,
    you can remove the width property and add the following: */
  .dirItem,
  .fileItem {
    display: inline-block;
    /* Allows the item to be as wide as its content */
    white-space: nowrap;
    /* Prevents the text from wrapping */
  }


  /* Close Button */
  #fileModal button {
    padding: 10px 20px;
    background-color: #bf97d8;
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 20px;
    border-radius: 5px;
    align-self: flex-end;
    /* Align the button to the right */
  }

  #fileModal button:hover {
    background-color: #946aae;
  }

  #directoryButtons {
    margin-bottom: 20px;
  }

  .dirButton {
    padding: 7px 14px;
    margin-right: 10px;
    margin-top: 1%;
    /* Space between buttons */
    background-color: #bf97d8;
    /* Bootstrap primary color */
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    /* Rounded corners */
    display: inline-block;
    /* Display buttons inline */
  }

  .dirButton:hover {
    background-color: #946aae;
    /* Darker blue on hover */
  }

  /* Style the file list items similarly to before */
  #fileList .fileItem {
    padding: 10px;
    margin: 5px 0;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    cursor: pointer;
    width: 100%;
    box-sizing: border-box;
  }
  #selectFileButton{
    margin-top: 2%;
    background-color: #bf97d8;
  }

  #selectFileButton:hover, .btn-cl:hover {
    background-color: #946aae;
    /* Darker blue on hover */
  }
  .selected{
    background-color: #e2e2e2;
  }
  div:where(.swal2-container) .swal2-input {
    height: 2.625em;
    padding: 0 0.75em;
    margin-left: 0.9em !important;
    width: 93% !important;
}
div:where(.swal2-container){
  margin-bottom: 30%;
}
body, html {
    height: 100%; /* Ensure full height */
    margin: 0; /* Reset default margin */
}

#footer {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    bottom: 0;
    width: 100%;
    background-color: #f0f0f0; /* Example background color */
    padding: 10px; /* Example padding */
    z-index: 1;
}

/* Adjust main content styling */
main {
    min-height: calc(100vh - 120px); /* Adjust based on header and footer size */
    /* other styles */
}

/* Adjust the position of Bootstrap tooltips */
.tooltip {
    position: fixed !important; /* Override Bootstrap's default positioning */
    z-index: 9999; /* Ensure the tooltip appears above other content */
}

</style>


<body>
<header class="head" style="font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS',
      'sans-serif' !important; ">
    <div class="header-container">
      <div class="flex-parent" style="color: black;">
        <div class="header_logo">
          <img src="LOGO.png" style="width: 20% !important; display: block; margin: 12% 12% 5% 12%;">
          <div class="proj_name" style="font-size: 1.75rem !important; font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS',
      'sans-serif' !important;">Learniverse</div>
        </div>
        <div class="header_nav">
          <nav id="navbar" class="nav__wrap collapse navbar-collapse">
            <ul class="nav__menu">
              <li>
                <a href="index.php">Home</a>
              </li>
              <li>
                <a href="community.php">Community</a>
              </li>
              <li class="active">
                <a href="Workspace.php">My Workspace</a>
              </li>
            </ul> <!-- end menu -->
          </nav>
        </div>
        <?php
        require 'jwt.php';
        require_once __DIR__ . '/vendor/autoload.php';

        // Create a MongoDB client
        $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

        // Select the database and collection
        $database = $connection->Learniverse;
        $Usercollection = $database->users;

        $data = array(
          "email" => $_SESSION['email']
        );

        $fetch = $Usercollection->findOne($data);
        //$googleID = $fetch->google_user_id;

        $headers = array(
          'alg' => 'HS256',
          'typ' => 'JWT'
        );
        $payload = array(
          'email' => $fetch['email'],
          'exp' => (time() + 36000)
        );

        $jwttoken = generate_jwt($headers, $payload);
        ?>
        <div class="dropdown">
          <button class="dropdown-button">
            <i class="fas fa-user" id='Puser-icon'> </i>
            <?php echo $fetch['firstname']; ?></button>
          <ul class="Pdropdown-menu">
            <li class='editName center'>
              <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
              <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
              <form id='rename-form' class='rename-form' method='POST' action='updateName.php?q=thefiles.php' onsubmit="return validateForm(event)">
                <input type='text' id='PRename' name='Rename' required value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                <span id='rename-error' style='color: red;'></span><br>
                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
              </form>
            </li>
            <li class='center'>Username: <?php echo $fetch['username']; ?></li>
            <li class='center'><?php echo $fetch['email']; ?></li>
            <hr>
            <li><a href='reset.php?q=pomodoro.php'><i class='far fa-edit'></i> Change password</a></li>
            <li><a href='#'><i class='far fa-question-circle'></i> Help</a></li>
            <hr>
            <li id="logout"><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div id="tools_div">
    <ul class="tool_list">
                <li class="tool_item">
                    <a href="workspace.php"> Calendar & To-Do
                    </a>
                </li>
                <li class="tool_item">
                    <a href="theFiles.php"> My Files</a>
                </li>
                <li class="tool_item">
                <a href="quizes/index.php"> Quizzes</a>
                </li>
                <li class="tool_item">
                <a href="flashcard.php"> Flashcards</a>
                </li>
                <li class="tool_item">
                <a href="summarization/summarization.php"> Summarization</a>
                </li>
                <li class="tool_item">
                <a href="studyplan.php"> Study Plan</a>
                </li>
                <li class="tool_item"><a href="Notes/notes.php">
                        Notes</a>
                </li>
                <li class="tool_item">
                    <a href="pomodoro.php">
                        Pomodoro</a>
                </li>
                <li class="tool_item"><a href="gpa.php">
                        GPA Calculator</a>
                </li>
                <li class="tool_item"><a href="sharedspace.php">
                        Shared spaces</a></li>
                <li class="tool_item">
                    Meeting Room
                </li>
                <li class="tool_item"><a href="community.php">
                        Community</a>
                </li>
            </ul>
    </div>


    <div class="workarea">
      <div class="workarea_item">
        <div class="row">
          <div class="col-md-6 text-left">
            <h2>Flash Card List</h2>
          </div>
          <div class="col-md-6 text-right">
            <button style="background-color: #fdae9b; border-radius: 10px; border: none;" type="button"
              class="btn btn-primary" data-toggle="modal" data-target="#uploadFileModel">
              New Flash Card
            </button>
          </div>
        </div>

        <div class="container1" style="margin-top: 24px;">
            <?php if (!empty($filesList)) { ?>
    <div class="container1" style="margin-top: 24px;">
        <table class="table">
            <thead class="table-primary">
                <tr>
                    <td>Recent Flashcards</td>
                    <td>Time Created</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filesList as $file) { ?>
                    <tr>
                        <?php foreach ($file as $key => $value) { ?>
                            <?php if ($key == '_id' || $key == 'answer') continue; 
                            if(is_string($value) ||$key == 'data_created'){// Skip _id and answer ?>
                            <td>
                                <?php 
                                    if ($key == 'subjectName') {
                                        // It's safe to use htmlspecialchars for strings
                                        echo htmlspecialchars(substr($value, 0, 50));
                                    } elseif ($key == 'data_created') {
                                        // Format the Unix timestamp to a readable date
                                        echo date('Y-m-d', $value);
                                        $date = $value;
                                    }
                                ?>
                              <?php } ?>
                            </td>
                        <?php } ?>
                        <td>
                            <a href="javascript:void(0)" id="updateFile">
                            <button onclick="retrieve('<?php echo $date; ?>','<?php echo $file['subjectName']; ?>')" class="file-edit btn" data-toggle="tooltip" title="Display">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </a>
                            <button onclick="savePDF(<?php echo $date;?>)" class="file-edit btn" data-toggle="tooltip" title="Save as PDF">
                                <i class="fas fa-download iconpdf" aria-hidden="true"></i>
                            </button>
                            <button onclick="toFiles(<?php echo $date;?>)" class="file-edit btn" data-toggle="tooltip" title="Save to Files">
                            <i class="fa fa-bookmark" aria-hidden="true"></i>
                            </button>



                            <a href="javascript:void(0)" >
                                <button onclick="deleteFlashcard(<?php echo $date;?>)" class="file-delete btn" data-toggle="tooltip" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <label> Don't have record yet!</label>
<?php } ?>


      </div>

    </div>

  </main>


  <!-- Modal -->
  <div class="modal fade" id="uploadFileModel" tabindex="-1" aria-labelledby="uploadFileModelLabel" aria-hidden="true"
    style="padding-top: 24px;">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadFileModelLabel">Flash Card</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div id="modal-body" class="modal-body">

          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button"
                role="tab" aria-controls="home" aria-selected="true">Upload File</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#file" type="button"
                role="tab" aria-controls="profile" aria-selected="false" >My Files</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                role="tab" aria-controls="profile" aria-selected="false">Manually</button>
            </li>
          </ul>

          <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

              <form enctype="multipart/form-data" method="post" action="" id="uploadForm"
                style="padding: 24px; margin-bottom: 48px;">

                <div class="form-group">
                  <label for="fileUpload">Select File (PDF)*</label>
                  <input type="file" class="form-control-file" id="fileUpload" name="fileUpload"
                  accept=".pdf" required>

                </div>
                <div class="form-group text-center">
                  <button type="submit" class="btn btn-cl">Submit</button>
                </div>

              </form>

            </div>

            <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="file-tab">
                        <div id="directoryButtons"></div> <!-- Placeholder for directory buttons -->
                        <div id="fileList"></div> <!-- Placeholder for file list -->

                    </div>

            <!--MANUAL--->
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

              <div style="padding: 24px;">


                  <div class="form-group">
                    <label class="h2" for="nameSubject">Name Subject</label>
                    <input type="text" class="form-control" id="nameSubject" name="nameSubject" required>
                  </div>

                  <div class="form-group">
                    <label class="h2" for="question">Title</label>
                    <textarea class="form-control" id="answer" name="answer" rows="3"
                      placeholder="Your text here"></textarea>
                  </div>

                  <div class="form-group">
                    <label class="h2" for="question">Card Content</label>
                    <textarea class="form-control" id="question" name="question" rows="3"
                      placeholder="Your text here"></textarea>
                  </div>



                  <div class="form-group text-center" style="margin-top: 16px;">
                    <button type="submit" class="btn btn-cl" name="submit" id="saveButton">Save</button>
                    <input type="button" class="btn btn-cl" value="Next Card" name="submitNext" id="submitNext">
                  </div>


              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>




  <footer id="footer" style="margin-top:7%;">
    <div id="copyright">Learniverse &copy; 2023</div>
  </footer>
  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>


  <script>

// Activate Bootstrap tooltips
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
let loadingOverlay = document.getElementById("loadingOverlay");
function showLoading() {
  loadingOverlay.style.display = 'block';
}

// Function to hide loading overlay
function hideLoading() {
  loadingOverlay.style.display = 'none';
}
function selectFile(fileName) {
  // Handle file selection
  console.log(fileName);
  showLoading(); // Show the loading overlay

  // Create FormData object to send the file name to the server
  const formData = new FormData();
  formData.append('flashcardFile', fileName);

  // Send the file name to the server using AJAX
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'summarization/extract.php', true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) { // Check if request is complete
      hideLoading(); // Hide the loading overlay once the request is complete
      if (xhr.status === 200) {
        // Parse the response JSON
        const response = JSON.parse(xhr.responseText);
        console.log(response);

        // Check if the request was successful
        if (response.success) {
          // Access the time and subjectName properties
          const time = response.time;
          const subjectName = response.subjectName;
          console.log(time, subjectName);
          // Call the retrieve function with time and subjectName parameters
          retrieve(time, subjectName);
        } else {
          console.error("Request was not successful");
        }
      } else {
        // Handle HTTP error (status code other than 200)
        console.error("Error response", xhr.statusText);
      }
    }
  };
  xhr.send(formData);
}

$('#uploadFileModel').on('shown.bs.modal', function (e) {
        loadDirectories();
        loadFiles('Uploaded Files');
    });

    function loadDirectories() {
        fetch('summarization/listDirectories.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('directoryButtons').innerHTML = html;
            })
            .catch(err => {
                console.error('Failed to load directories', err);
            });
    }

    function loadFiles(directoryName) {
        fetch(`summarization/listFiles.php?directory=${directoryName}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('fileList').innerHTML = html;
            })
            .catch(err => {
                console.error('Failed to load files', err);
            });
    }
    // JavaScript function to handle file selection
function handleFileSelection() {
    // Get the selected file name
    var selectedFileName = document.querySelector('.fileItem.selected').textContent;

    // Do something with the selected file name
    console.log('Selected file:', selectedFileName);
}

// Event listener to handle file item selection
document.addEventListener('click', function(event) {
    // Check if clicked element is a file item
    if (event.target.classList.contains('fileItem')) {
        // Remove the 'selected' class from all file items
        var fileItems = document.querySelectorAll('.fileItem');
        fileItems.forEach(function(item) {
            item.classList.remove('selected');
        });

        // Add the 'selected' class to the clicked file item
        event.target.classList.add('selected');
    }
});

  </script>





  <!-- SHOUQ SECTION: -->
<script>

    $(document).ready(function () {
      $("#rename-form").hide();
      if ($("#rename-form").css("display") === "none" || $("#rename-form").is(":hidden"))
        cancelRename();

      document.querySelector(".Pdropdown-menu").addEventListener("mouseleave", function () {
        cancelRename();
      });


      var isSidebarOpen = false;
      var isButtonClicked = false;
      var sidebarTongue = document.querySelector('#sidebar-tongue');
      var sidebarDiv = document.querySelector('#tools_div');

      // Event listener for hover on the sidebar tongue button
      sidebarTongue.addEventListener('mouseenter', function (event) {
        if (!isSidebarOpen) {
          w3_open();
          isSidebarOpen = true;

        } else if (!isButtonClicked) {
          w3_close();
          isSidebarOpen = false;
        }
      });

      sidebarTongue.addEventListener('click', function (event) {
        if (!isButtonClicked && isSidebarOpen) {
          isButtonClicked = true;
        } else {
          w3_close();
          isButtonClicked = false;
          isSidebarOpen = false;
        }
      });

      sidebarDiv.addEventListener('mouseleave', function (event) {
        if (!isButtonClicked && isSidebarOpen) {
          w3_close();
          isSidebarOpen = false;
        }
      });


      window.addEventListener('scroll', function () {
        var toolsDiv = document.querySelector('#tools_div');
        var toolList = document.querySelector('.tool_list');
        var footer = document.getElementsByTagName('footer')[0];
        var header = document.getElementsByTagName('header')[0];
        var toolsDivHeight = toolsDiv.offsetHeight;
        var headerHeight = header.offsetHeight;
        var windowHeight = window.innerHeight;
        var footerTop = footer.offsetTop;
        var footerOffset = footerTop - windowHeight;
        var toolListOffsetTop = toolList.offsetTop;

        if (window.pageYOffset > footerOffset) {
          toolsDiv.style.position = 'absolute';
          toolsDiv.style.top = Math.max(headerHeight, footerTop - toolsDivHeight) + 'px';
          toolsDiv.style.bottom = '0';
          toolList.style.transform = 'translateY()';
        } else {
          toolsDiv.style.position = 'fixed';
          toolsDiv.style.top = headerHeight + 'px';
          toolsDiv.style.bottom = '';
          toolList.style.transform = 'translateY(' + (headerHeight - toolListOffsetTop) + 'px)';
        }

        // Apply smooth transition
        toolsDiv.style.transition = 'transform 0.3s ease-out';
      });
    });
    // PROFILE DROPDOWN MENU
    function Rename() {
      $('#Pname').hide();
      $('#Puser-icon').hide();
      $("#rename-form").show();
      $("#PRename").focus();
      if ($("#rename-form").submitted) {
        cancelRename();
      }
    };

    function cancelRename() {
      $("#rename-form").get(0).reset();
      $("#rename-form").hide();
      $('#Pname').show();
      $('#Puser-icon').show();
    };

    function validateForm(event) {
      event.preventDefault(); // Prevent the form from submitting by default

      var input = document.getElementById('PRename');
      var value = input.value.trim(); // Trim whitespace from the input value

      var errorSpan = document.getElementById('rename-error');

      if (value === '') {
        errorSpan.textContent = 'Please enter a valid name.'; // Display the error message
        return false; // Cancel form submission
      }

      var nameParts = value.split(' ').filter(part => part !== ''); // Split on whitespace and remove empty parts

      if (nameParts.length < 2) {
        errorSpan.textContent = 'Please enter both first name and last name.'; // Display the error message
        return false; // Cancel form submission
      }

      // Check if both names start with a letter
      var isValid = nameParts.every(part => /^[A-Za-z]/.test(part));

      if (!isValid) {
        errorSpan.textContent = 'Names should start with a letter.'; // Display the error message
        return false; // Cancel form submission
      } else {
        errorSpan.textContent = ''; // Clear the error message if it's not needed
      }

      // If the validation passes, you can proceed with form submission
      document.getElementById('rename-form').submit();
    }

    function w3_open() {
      document.getElementsByClassName("workarea")[0].style.marginLeft = "auto";
      document.getElementById("tools_div").style.transition = '1s';
      document.getElementById("sidebar-tongue").style.transition = '1s';
      document.getElementsByClassName("workarea")[0].style.transition = '1s';
      document.getElementsByClassName("workarea")[0].style.marginLeft = "10%";
      document.getElementById("tools_div").style.marginLeft = '0';
      document.getElementById("sidebar-tongue").style.marginLeft = '12%';
      document.getElementById("sidebar-tongue").textContent = "<";
      document.getElementById("sidebar-tongue").style.boxShadow = "none";
    }

    function w3_close() {
      document.getElementById("sidebar-tongue").style.transition = '1s';
      document.getElementById("tools_div").style.transition = '1s';
      document.getElementsByClassName("workarea")[0].style.marginLeft = "0";
      document.getElementById("sidebar-tongue").textContent = ">";
      document.getElementById("tools_div").style.marginLeft = "-13.9%";
      document.getElementById("sidebar-tongue").style.marginLeft = '0';
    }


</script>

<script type="text/javascript">

function toFiles(datad){
  $.ajax({
        url: 'cardsData.php',
        method: 'POST',
        data: { datas: datad },
        success: function(response) {
    // Assuming response is already a JavaScript object, not JSON
    var responseData = response;
    console.log(response);
    // Access the fields of the response object
    var paragraph = "";

// Loop through each card in the success array
response.success.forEach(function(card) {
  paragraph += "Title: " + card.answer + " \nContent: " + card.content + "\n\n";
});
console.log(paragraph);

const { jsPDF } = window.jspdf;

const doc = new jsPDF();

Swal.fire({
    title: "Enter filename",
    input: "text",
    inputPlaceholder: "Filename (without extension)",
    showCancelButton: true,
    confirmButtonText: "Save",
    cancelButtonText: "Cancel",
    inputValidator: (value) => {
        if (!value) {
            return "Filename is required";
        }
    },
}).then((result) => {
    if (result.isConfirmed) {
        console.log(result.value);

        var maxWidth = 250; // Adjust this value as needed
var textLines = doc.splitTextToSize(paragraph, maxWidth);

var x = 20; // X position
var y = 20; // Initial Y position
var pageHeight = doc.internal.pageSize.height; // Get the height of the page

// Set the font size
var fontSize = 10; // Example font size in points
doc.setFontSize(fontSize);

// Set line height with a spacing factor of 1.15
var lineHeight = 7; // Adjusting line spacing to 1.15 times the font size

textLines.forEach(function(line) {
    if (y > pageHeight - 10) { // Check to see if the line is near the bottom of the page
        doc.addPage();
        y = 20; // Reset Y position for the new page
    }
    
    doc.text(line, x, y);
    y += lineHeight; // Move to the next line
});
        // Convert the PDF to a Blob
        const blob = doc.output('blob'); // Get blob directly

        // Create FormData object to send data to the server
        const formData = new FormData();
        const fileName = result.value + '.pdf'; // Trim any leading/trailing spaces from the filename
        formData.append('pdf', blob, fileName);

        // Send the PDF to the server using AJAX
        const xhr = new XMLHttpRequest();
        const path = "save_cards.php"; // Make sure this is the correct path to your PHP script
        xhr.open('POST', path, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText); // Log server response
            }
        };
        xhr.send(formData);
    }
});

},

        error: function (xhr, status, error) {
            // Log the full response body to see what's causing the JSON parse error
            console.error(xhr.responseText);
        }   
    });
}



function savePDF(datad){
  $.ajax({
        url: 'cardsData.php',
        method: 'POST',
        data: { datas: datad },
        success: function(response) {
    // Assuming response is already a JavaScript object, not JSON
    var responseData = response;
    console.log(response);
// Initialize an empty string to hold the paragraph
var paragraph = "";

// Loop through each card in the success array
response.success.forEach(function(card) {
  paragraph += "Title: " + card.answer + " \nContent: " + card.content + "\n\n";
});
console.log(paragraph);


const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  // Retrieve the text content of the element

  // Use SweetAlert for the filename prompt
  Swal.fire({
    title: "Enter filename",
    input: "text",
    inputPlaceholder: "Filename (without extension)",
    showCancelButton: true,
    confirmButtonText: "Save",
    cancelButtonText: "Cancel",
    inputValidator: (value) => {
      if (!value) {
        return "Filename is required";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      // Add the text content to the PDF
      var maxWidth = 250; // Adjust this value as needed
var textLines = doc.splitTextToSize(paragraph, maxWidth);

var x = 20; // X position
var y = 20; // Initial Y position
var pageHeight = doc.internal.pageSize.height; // Get the height of the page

// Set the font size
var fontSize = 10; // Example font size in points
doc.setFontSize(fontSize);

// Set line height with a spacing factor of 1.15
var lineHeight = 7; // Adjusting line spacing to 1.15 times the font size

textLines.forEach(function(line) {
    if (y > pageHeight - 10) { // Check to see if the line is near the bottom of the page
        doc.addPage();
        y = 20; // Reset Y position for the new page
    }
    
    doc.text(line, x, y);
    y += lineHeight; // Move to the next line
});   
      // Save the PDF with the chosen filename
      doc.save(result.value + ".pdf");
    }
  });
},

        error: function (xhr, status, error) {
            // Log the full response body to see what's causing the JSON parse error
            console.error(xhr.responseText);
        }   
    });
}
</script>
  <script>
    
    function confirmFileDelete(data) {
      var id = '';
      for (var key in data) {
        // alert(data[key]);
        id = data[key];
      }
      // alert (id);     
      // Show a confirmation dialog  
      if (confirm("Are you sure you want to delete this File?")) {
        // User confirmed, proceed with deletion
        deleteFile(id);
      }
    }




    function showModelById(data) {
      var id = '';
      for (var key in data) {
       id = data[key];
      }
  
      $.ajax({
        url: 'subjectReview.php',
        data: {
          'action': 'getSubjectReview',
          'id': id,
        },
        method: 'POST',
       success: function (res) {
         if (res != "Don't have recored!") {
            $("body").append(res);
            $('#viewModel').toggle();

            $('#viewModel').modal('show');

          } else {
            alert(res);
          }

        }
      });
    }



    $('#uploadFileModel').on('shown.bs.modal', function () {
      $('#fileUpload').trigger('focus')
    });

    $('button[data-toggle="tab"]').on('shown.bs.tab', function (event) {
      event.target // newly activated tab
      event.relatedTarget // previous active tab
    })

    document.querySelector('#uploadForm').addEventListener('submit', (e) => {
    e.preventDefault();
    // Show loading overlay
    $('#loadingOverlay').show();
    let fileInput = document.querySelector('#fileUpload');
    let formData = new FormData();
    formData.append('fileUpload', fileInput.files[0]);
      let gen_date = 0;
    // No need to convert FormData to an array
    // Simply pass formData to the data property of the AJAX call
    $.ajax({
        url: 'summarization/extract.php',
        data: formData,
        method: 'POST',
        processData: false, // Important: don't process the files
        contentType: false, // Important: set this to false, don't set a content type
        success: function (response) {
          let datatext = JSON.parse(response);

          console.log(datatext);
          $('#uploadFileModel').modal('hide'); 


          gen_date = datatext['success'];
          console.log('time: '+ gen_date);
          retrieve(gen_date, fileInput.files[0].name);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error: ", textStatus, errorThrown);
            // Hide loading overlay on error
            $('#loadingOverlay').hide();
        }
    });

});





// Define an array to store the subject reviews
let subjectReviews = [];
let name = " ";
let array ={};
// Function to add subject review to the array
function addSubjectReview() {
  let subjectName = document.querySelector('#nameSubject').value;
  let question = document.querySelector('#question').value.trim();
  let answer = document.querySelector('#answer').value.trim();

  if (subjectName !== "" && question !== "" && answer !== "") {
    // Check if the subject already exists in the array

      // If the subject does not exist, create a new subject object
      let newSubject = {
        'question': question,
        'answer': answer
      };
      name = subjectName;
      // Push the new subject object into the subjectReviews array
      subjectReviews.push(newSubject);
    
      array['name'] = name;
      array['subjectReviews'] = subjectReviews;
    // Clear the input fields
    document.querySelector('#question').value = "";
    document.querySelector('#answer').value = "";

    // Print the updated subjectReviews array

    // Prevent form submission
    return false;
  } else {
    alert("Please fill in all input fields!");
  }
}

// Event listener for the "Next Card" button
document.querySelector('#submitNext').addEventListener('click', addSubjectReview);



function retrieve(datad, subjectName) {
    // Make AJAX request to the PHP file
    console.log(datad);
    $.ajax({
        url: 'cardsData.php',
        method: 'POST',
        data: { datas: datad },
        success: function(response) {
    // Assuming response is already a JavaScript object, not JSON
    var responseData = response;
    console.log(response);
    // Access the fields of the response object
    var dataParam = encodeURIComponent(JSON.stringify(responseData));
    window.location.href = 'flashcard/displayFlashcards.php?data=' + dataParam + '&subjectName=' + subjectName;

 
},

        error: function (xhr, status, error) {
            // Log the full response body to see what's causing the JSON parse error
            console.error(xhr.responseText);
        }   
    });
}

document.querySelector('#saveButton').addEventListener('click', function() {
  addSubjectReview();
  
  let array = {
    'name': name,
    'questions': subjectReviews
  };
 let retdate = 0;
  // Send the data as JSON
  fetch('addCards.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(array) // Convert the JavaScript object to a JSON string
  })
  .then(response => response.text())
  .then(data => {
    // Handle the response data from the server
    console.log('up');
    let datatext = JSON.parse(data);
    console.log('down');

    console.log(datatext);
    $('#uploadFileModel').modal('hide'); 
    

   retdate = datatext['success'];
   retrieve(retdate, name);

  })
  .catch(error => {
    // Handle any errors
    console.error('Error:', error);
  });

});


function deleteFlashcard(date) {
    // Make AJAX request to the PHP file
    $.ajax({
        url: 'cardsData.php', // Replace with the path to your PHP file
        method: 'POST',
        data: { date: date }, // Send the date as data
        success: function(response) {
            // Handle the response from the PHP file
            location.reload(); // Reload the page to reflect the changes
            console.log('Delete request successful');
        },
        error: function(xhr, status, error) {
            // Handle errors           
             location.reload(); // Reload the page to reflect the changes

            console.error('Error sending delete request:', error);
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    var tables = document.querySelectorAll('.table');
    
    tables.forEach(function(table) {
        var tbody = table.querySelector('tbody');
        if (!tbody || tbody.getElementsByTagName('tr').length === 0) {
            table.style.display = 'none';
        }
    });
});

  </script>

  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>



</body>

</html>