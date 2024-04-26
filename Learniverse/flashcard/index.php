<?php

if (!isset($_SESSION)) {
  session_start();
  require "../customerSupport.php";
}
// ini_set('display_errors', '0'); // Turn off error displaying
// error_reporting(E_ERROR | E_PARSE); // Report only errors, not warnings or notices

// Require the MongoDB library
require_once __DIR__ . '../../vendor/autoload.php';

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

  <link rel="stylesheet" href="../theFiles.css">
  <link rel="stylesheet" href="../header-footer.css">

  <!-- PROFILE STYLESHEET -->
  <link rel="stylesheet" href="../profile.css">
  <!-- Custom stylesheet -->
  <link href="../css/style.css" rel="stylesheet" />
  <!-- Sweetalert2 -->
  <!-- <script src="js/sweetalert2.all.min.js"></script> -->
  <!-- GPA STYLESHEET -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
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
    </style>
<style>
  #updateFile{
    text-decoration: none !important;  
    color: transparent;
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

  .table-primary {
    border: 1px solid #d1b4e3;
    color: white;
    background-color: #d1b4e3;

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
    cursor: pointer;
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
</style>

<body>
  <header>
    <div class="header-container">
      <div class="flex-parent">
        <div class="header_logo">
          <img src="../LOGO.png">
          <div>Learniverse</div>
        </div>
        <div class="header_nav">
          <nav id="navbar" class="nav__wrap collapse navbar-collapse">
            <ul class="nav__menu"
              style="font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS', 'sans-serif' !important; ">
              <li>
                <a href="/index.php">Home</a>
              </li>
              <li>
                <a href="/community.php">Community</a>
              </li>
              <li class="active">
                <a href="/workspace.php">My Workspace</a>
              </li>
            </ul>
          </nav>
        </div>
        <div class="overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <?php
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;
        
                $data = array(
                  "email" => $_SESSION['email']
                );
        
                $fetch = $Usercollection->findOne($data);?> 
    </div>
    <div class="dropdown">
          <button class="dropdown-button">
            <i class="fas fa-user" id='Puser-icon'> </i>
            <?php echo $fetch['firstname']; ?></button>
          <ul class="Pdropdown-menu">
            <li class='editName center'>
              <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
              <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
              <form id='rename-form' class='rename-form' method='POST' action='../updateName.php?q=thefiles.php' onsubmit="return validateForm(event)">
                <input type='text' id='PRename' name='Rename' required value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                <span id='rename-error' style='color: red;'></span><br>
                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
              </form>
            </li>
            <li class='center'>Username: <?php echo $fetch['username']; ?></li>
            <li class='center'><?php echo $fetch['email']; ?></li>
            <hr>

            <?php //if ($googleID === null) {
            echo "<li><a href='../reset.php?q=thefiles.php'><i class='far fa-edit'></i> Change password</a></li>";
            ?>

<li onclick="customerSupport()"><a href='#'><i class='far fa-question-circle'></i> Customer Support</a></li>
            <hr>
            <li id="logout"><a href='../logout.php'><i class='fas fa-sign-out-alt'></i> Sign out </a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div id="tools_div">
    <ul class="tool_list">
        <li class="tool_item">
          <a href="/workspace.php"> Calendar & To-Do
          </a>
        </li>
        <li class="tool_item">
          <a href="/theFiles.php"> My Files</a>
        </li>
        <li class="tool_item">
        <a href="/quizes/"> Quiz
          </a>
        </li>
        <li class="tool_item">
        <a href="/flashcard"> Flashcard
          </a>
        </li>
        <li class="tool_item">
        <a href="/summarization/summarization.php"> Summarization
          </a>
        </li>
        <li class="tool_item"><a href="/studyplan.php">
        Study Planner</a>
                  </li>
        <li class="tool_item"><a href="/Notes/notes.php">
            Notes</a>
        </li>
        <li class="tool_item">
          <a href="/pomodoro.php">
            Pomodoro</a>
        </li>
        <li class="tool_item"><a href="/gpa.php">
            GPA Calculator</a>
        </li>
        <li class="tool_item"><a href="/sharedspace.php">
        Shared spaces</a>
                 </li>
        <li class="tool_item"><a href="/meetingroom.php">
          Meeting Room
        </li>
        <li class="tool_item"><a href="/community.php">
            Community</a>
        </li>
      </ul>
    </div>


    <div class="workarea" style="height: 83vh">
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
                                        echo htmlspecialchars(substr($value, 0, 50).'...');
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
                                <!-- <button onclick="startQuiz(<?php echo $date;?>)" class="file-edit btn">
                                   Start Quiz
                                </button> -->
                            </a>
                            <a href="javascript:void(0)" id="updateFile">
                            <button onclick="retrieve('<?php echo $date; ?>','<?php echo $file['subjectName']; ?>')" class="file-edit btn" style="cursor: pointer;">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </a> 
                            <a href="javascript:void(0)" >
                                <button onclick="deleteFlashcard(<?php echo $date;?>)" class="file-delete btn">
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

        <div class="modal-body">

          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button"
                role="tab" aria-controls="home" aria-selected="true">Uplod File</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button"
                role="tab" aria-controls="profile" aria-selected="false">Manualy</button>
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

            <!--MANUAL--->
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

              <div style="padding: 24px;">

                <form action="" method="post" id="manualForm">

                  <div class="form-group">
                    <label class="h2" for="nameSubject">Name Subject</label>
                    <input type="text" class="form-control" id="nameSubject" name="nameSubject" required>
                  </div>

                  <div class="form-group">
                    <label class="h2" for="question">Question</label>
                    <textarea class="form-control" id="question" name="question" rows="3"
                      placeholder="Your text here"></textarea>
                  </div>

                  <div class="form-group">
                    <label class="h2" for="question">Answer</label>
                    <textarea class="form-control" id="answer" name="answer" rows="3"
                      placeholder="Your text here"></textarea>
                  </div>

                  <div class="form-group text-center" style="margin-top: 16px;">
                    <button type="submit" class="btn btn-cl" name="submit">Save</button>
                    <input type="button" class="btn btn-cl" value="Save Card Content" name="submitNext" id="submitNext">
                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>




  <footer id="footer" style="margin-top: 7%;">

<div id="copyright">Learniverse &copy; 2024</div>
</footer>
  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>



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
      document.getElementById("sidebar-tongue").style.marginLeft = '13.5%';
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

    // No need to convert FormData to an array
    // Simply pass formData to the data property of the AJAX call
    $.ajax({
        url: 'summarization/extract.php',
        data: formData,
        method: 'POST',
        processData: false, // Important: don't process the files
        contentType: false, // Important: set this to false, don't set a content type
        success: function (response) {
          window.location.href = 'flashcard/displayFlashcards.php?data=' + encodeURIComponent(response) + '&subjectName=' + encodeURIComponent(fileInput.files[0].name);
            // Hide loading overlay on success
            $('#loadingOverlay').hide();
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

// Function to add subject review to the array
function addSubjectReview() {
  let subjectName = document.querySelector('#nameSubject').value.trim();
  let question = document.querySelector('#question').value.trim();
  let answer = document.querySelector('#answer').value.trim();

  if (subjectName !== "" && question !== "" && answer !== "") {
    // Check if the subject already exists in the array
    let existingSubjectIndex = subjectReviews.findIndex(review => review.subjectName === subjectName);

    if (existingSubjectIndex === -1) {
      // If the subject does not exist, create a new subject object
      let newSubject = {
        'subjectName': subjectName,
        'questions': [{ 'question': question, 'answer': answer }]
      };

      // Push the new subject object into the subjectReviews array
      subjectReviews.push(newSubject);
    } else {
      // If the subject already exists, push the new question to its questions array
      subjectReviews[existingSubjectIndex].questions.push({ 'question': question, 'answer': answer });
    }

    // Clear the input fields
    document.querySelector('#question').value = "";
    document.querySelector('#answer').value = "";

    // Print the updated subjectReviews array
    alert(JSON.stringify(subjectReviews));

    // Prevent form submission
    return false;
  } else {
    alert("Please fill in all input fields!");
  }
}

// Event listener for the "Next Card" button
document.querySelector('#submitNext').addEventListener('click', addSubjectReview);

// Event listener for the form submission
document.querySelector('#manualForm').addEventListener('submit', (e) => {
  e.preventDefault();

  $.ajax({
    url: 'subjectReview.php',
    data: { action: 'addSubjectReview', subjectReviews: subjectReviews },
    method: 'POST',
    dataType: 'json', // Set the expected response type
    success: function (res) {
      // alert(res.message);
      if (res.success) {
        location.reload();
      }
    },
    error: function (xhr, status, error) {
  // Assuming the server responds with a JSON object on error that includes an 'error' field
  var errorMsg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Unknown error';
  alert('Error: ' + errorMsg);
}
  });
});

function deleteFlashcard(date) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'cardsData.php', 
                method: 'POST',
                data: { date: date },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Your flashcard has been deleted.',
                        'success'
                    );
                    location.reload();
                },
                error: function(xhr, status, error) {
                  location.reload();

                }
            });
        }
    });
}



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

function startQuiz(datad) {
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
    window.location.href = 'flashcard/quiz.php?data=' + dataParam;

 
},

        error: function (xhr, status, error) {
            // Log the full response body to see what's causing the JSON parse error
            console.error(xhr.responseText);
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