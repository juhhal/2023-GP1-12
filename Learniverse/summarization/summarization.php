<!DOCTYPE html>
<?php
require_once __DIR__ . '../../vendor/autoload.php';
session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$userEmail = $_SESSION['email'];
$query = new MongoDB\Driver\Query(['user_email' => $userEmail]);
ini_set('display_errors', '0'); // Turn off error displaying
error_reporting(E_ERROR | E_PARSE); // Report only errors, not warnings or notices


?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" /> -->
  <link rel="stylesheet" href="../profile.css">
  <link rel="stylesheet" href="../header-footer.css">
  <link rel="stylesheet" href="../theFiles.css">
  <link rel="stylesheet" href="index.css">
  <script src="../jquery.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://fontawesome.com/icons/file-export?f=classic&s=solid"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
  <script type='text/javascript'>
    $(document).ready(function() {
      $("#rename-form").hide();
      if ($("#rename-form").css("display") === "none" || $("#rename-form").is(":hidden"))
        cancelRename();

      document.querySelector(".Pdropdown-menu").addEventListener("mouseleave", function() {
        cancelRename();
      });


      var isSidebarOpen = false;
      var isButtonClicked = false;
      var sidebarTongue = document.querySelector('#sidebar-tongue');
      var sidebarDiv = document.querySelector('#tools_div');

      // Event listener for hover on the sidebar tongue button
      sidebarTongue?.addEventListener('mouseenter', function(event) {
        if (!isSidebarOpen) {
          w3_open();
          isSidebarOpen = true;

        } else if (!isButtonClicked) {
          w3_close();
          isSidebarOpen = false;
        }
      });

      sidebarTongue?.addEventListener('click', function(event) {
        if (!isButtonClicked && isSidebarOpen) {
          isButtonClicked = true;
        } else {
          w3_close();
          isButtonClicked = false;
          isSidebarOpen = false;
        }
      });

      sidebarDiv.addEventListener('mouseleave', function(event) {
        if (!isButtonClicked && isSidebarOpen) {
          w3_close();
          isSidebarOpen = false;
        }
      });
// Assuming you have a file input element with id "file-input" and a button with id "upload-button"
let fileInput = document.getElementById("file-input");
            let messageContainer = document.getElementById("og");
            let loadingOverlay = document.getElementById("loadingOverlay");

            fileInput.addEventListener("change", function() {
                let formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('readpdf', 'true'); // Include the 'readpdf' parameter

                showLoading(); // Show loading overlay

                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'extract.php'); 
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        hideLoading(); // Hide loading overlay
                        if (xhr.status === 200) {
                            // Request was successful, handle response here
                            messageContainer.value = xhr.responseText;

                        } else {
                            // Request failed, handle error here
                            console.error('Error:', xhr.statusText);
                        }
                    }
                };
                xhr.send(formData);
            });

            $('#summarize').click(function() {
              var ogValue = $('#og').val(); // Get the value of the textarea

              // Check if ogValue is empty
              if (!ogValue.trim()) {
                  // If ogValue is empty, display a SweetAlert message
                  Swal.fire({
                      title: 'Error!',
                      text: 'The input field cannot be empty!',
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
              } else {
                  // If ogValue is not empty, proceed with the AJAX request
                  showLoading(); // Show loading overlay
                  console.log("Sending request to extract.php...");

                  $.ajax({
                      url: 'extract.php', // Change this to the file extractor page
                      type: 'POST',
                      data: { ogValue: ogValue }, // Send the 'ogValue' to PHP
                      success: function(response) {
                          hideLoading(); // Hide loading overlay
                          console.log("Received response from extract.php:", response);
                          $('#summary').html(response);
                      },
                      error: function(jqXHR, textStatus, errorThrown) {
                          hideLoading(); // Hide loading overlay
                          console.error("Error:", textStatus, errorThrown);
                          Swal.fire({
                              title: 'Request Failed',
                              text: 'There was an error processing your request.',
                              icon: 'error',
                              confirmButtonText: 'OK'
                          });
                      }
                  });
              }
          });


            function showLoading() {
                loadingOverlay.style.display = 'block';
            }

            // Function to hide loading overlay
            function hideLoading() {
                loadingOverlay.style.display = 'none';
            }
   

      window.addEventListener('scroll', function() {
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
      document.getElementById("tools_div").style.transition = '1s';
      document.getElementById("sidebar-tongue").style.transition = '1s';
      document.getElementById("tools_div").style.marginLeft = '0';
      document.getElementById("sidebar-tongue").style.marginLeft = '13.5%';
      document.getElementById("sidebar-tongue").textContent = "<";
      document.getElementById("sidebar-tongue").style.boxShadow = "none";
    }

    function w3_close() {
      document.getElementById("sidebar-tongue").style.transition = '1s';
      document.getElementById("tools_div").style.transition = '1s';
      document.getElementById("sidebar-tongue").textContent = ">";
      document.getElementById("tools_div").style.marginLeft = "-13.9%";
      document.getElementById("sidebar-tongue").style.marginLeft = '0';
    }
  </script>
</head>
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
            <ul class="nav__menu">
              <li>
                <a href="../index.php">Home</a>
              </li>
              <li>
                <a href="#">Community</a>
              </li>
              <li class="active">
                <a href="../workspace.php">My Workspace</a>
              </li>
            </ul> <!-- end menu -->
          </nav>
        </div>
        <div class="overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
        <?php
        require '../jwt.php';

        // Create a MongoDB client
        $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

        // Select the database and collection
        $database = $connection->Learniverse;
        $Usercollection = $database->users;

        $data = array(
          "email" => $_SESSION['email']
        );

        $fetch = $Usercollection->findOne($data);
        // $googleID = $fetch->google_user_id;

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

            <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
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
        <li class="tool_item">
          Meeting Room
        </li>
        <li class="tool_item"><a href="/community.php">
            Community</a>
        </li>
      </ul>
    </div>

    <div class="container">
      <div class="summarize-heading">
        <h1>Document Summarization</h1>
        <p>Summarize content to extract key points and insights.</p>
      </div>
      <div class="button-container">
        <div class="summary-wrapper">

        <input type="file" id="file-input" multiple />
        <label id="" for="file-input">
          <i class="fa-solid fa-arrow-up-from-bracket"></i>
          &nbsp; Choose Files To Upload
        </label>
        <label  id="summarize">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
          &nbsp; Summarize
        </label>
        </div>

        <div id="num-of-files"></div>
        <ul id="files-list"></ul>
      </div>
      <div class="summary-container">
      <div class="summary-wrapper">
        <div class="text-container">
            <span class="sum">Original Text</span>
            <div class="summarize-container"> 
            <textarea id="og" style="resize: none; overflow: auto;"></textarea>
            </div>
        </div>
        <div class="text-container">
            <span class="sum">Summarized Text</span>
            <div class="containers">
            <div class="summary-wrapper">

            <i class="fa-solid fa-file-export iconpdf" onclick="exportPDF()"></i>
            <i class="fas fa-copy iconp" onclick="Copysummary()"></i>
            </div>
          </div>
            <div id="cont" class="summarize-container">
            <textarea id="summary" style="resize: none; overflow: auto;"></textarea>

              </div>
        </div>
    </div>
</div>
<?php
    $database = $connection->Learniverse;
    $Usercollection = $database->users;
    $FileCollection = $database->summaries;

    
    $user = null;
    $googleID = null;
    
    if (isset($_SESSION["email"])) {
      // Fetch the files directly using the user ID
      $filesList = getFilesByUserId($_SESSION["email"], $FileCollection);
  }
  
  function getFilesByUserId($userId, $FileCollection) {
      try {
          $user = $FileCollection->findOne(['userId' => $_SESSION["email"]]);
          if ($user) {
              return isset($user['summaries']) ? $user['summaries'] : [];
          } else {
              return [];
          }
      } catch (Exception $e) {
          printf($e->getMessage());
          return [];
      }
  }
  
  

    ?>
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
  .table{
    width: 90%;
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
    cursor: pointer;
    font-size: 8px ;
    color: white;
    background-color: #ec947e;
    vertical-align: middle;
    border: 1px solid transparent ;
    padding: 0.375rem 0.75rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
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
    font-size: 8px ;
    color: white;
    background-color: #ec947e;
    vertical-align: middle;
    border: 1px solid transparent ;
    padding: 0.375rem 0.75rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
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
  .container1{
    background-color: rgb(250, 247, 255);
  }

  /* Base table styles */
.table {
  width: 90%;
  margin: 0 auto; /* Center table with automatic margins */
  border-collapse: collapse;
  border-radius: 10px;
  border-color: transparent;
}

/* Header styles with gradient background */
.table-primary th {
  background-color: #6c757d; /* Replace with the top gradient color */
  background-image: linear-gradient(#7c8c97, #6c757d); /* Replace with gradient colors */
  color: white;
  text-align: left;
  padding: 10px;
  border-bottom: 2px solid #51585e; /* Border color */

}

/* Cell styles */
.table td {
  padding: 10px;
  border-top: 1px solid #c7c7c785;
}

/* First cell in every row has a larger left padding for aesthetics */
.table td:first-child {
  padding-left: 20px;
}


.file-edit .file-delete  {
  font-size: 8px !important;
    color: white;
    background-color: #ec947e;
    vertical-align: middle;
    border: 1px solid transparent !important;
    padding: 0.375rem 0.75rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

/* Hover effect for buttons */




/* Responsive table adjustments */
@media screen and (max-width: 768px) {
  .table {
    width: 100%;
    margin: 10px; /* Adjust margins for small screens */
    box-shadow: none; /* Optional: remove shadow on small screens */
  }
  
  /* Adjust padding for small screens */
  .table td, .table th {
    padding: 8px;
  }
}

</style>





    </div>
    <div class="container1" style="margin-top: 24px;">
    <?php if (!empty($filesList)) { ?>
        <table class="table">
            <thead class="table-primary">
                <tr>
                    <!-- <td>_id</td> -->
                    <td>Recent Summaries</td>
                    <td>Time Created</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filesList as $file) { ?>
                    <tr>
                        <?php foreach ($file as $key => $value) { ?>
                            <?php if ($key == '_id' || $key == 'answer') continue; // Skip _id and summary ?>
                            <td>
                              <?php 
                                // Check if the key is 'question' and truncate the value to the first 20 characters
                                
                                if ($key == 'question') {
                                    echo htmlspecialchars(substr($value, 0, 50).'...');
                                } elseif ($key == 'data_created') {
                                    // Format the Unix timestamp to a readable date
                                    echo date('Y-m-d', $value);
                                    $date = $value;
                                } else {
                                    echo htmlspecialchars($value);
                                }
                                
                              ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php $id = $file['_id']; ?>
                            <a href="javascript:void(0)" id="updateFile" >
                                <button onclick="retrieve(<?php echo $date;?>)" class="file-edit btn">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </a>
                            <a href="javascript:void(0)" >
                                <button onclick="deleteSummary(<?php echo $date;?>)" class="file-delete btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

</main>
    <footer id="footer" style="margin-top: 7%;">

<div id="copyright">Learniverse &copy; 2024</div>
</footer>


  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<script>
// Include pdfjs-dist library


let fileInput = document.getElementById("file-input");
let fileList = document.getElementById("files-list");
let numOfFiles = document.getElementById("num-of-files");
let ogInput = document.getElementById("og"); // Assuming you have an input element with the ID 'og'

fileInput.addEventListener("change", () => {
    // Clear the file list and the number of files text
    fileList.innerHTML = "";
    numOfFiles.textContent = "";

    let reader = new FileReader();
    let listItem = document.createElement("li");
    let fileName = fileInput.files[0].name;
    let fileSize = (fileInput.files[0].size / 1024).toFixed(1);
    listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}KB</p>`;
    if (fileSize >= 1024) {
        fileSize = (fileSize / 1024).toFixed(1);
        listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}MB</p>`;
    }
    fileList.appendChild(listItem);

    // Set a timer to periodically check the value of 'og' after a delay
    setTimeout(() => {
        // Check if 'og' input is empty
        if (ogInput.value.trim() === '') {
            // Clear the file input value, file list, and number of files text
            fileInput.value = null;
            fileList.innerHTML = "";
            numOfFiles.textContent = "0 Files Selected";
        }
    }, 3000); // Adjust the delay (in milliseconds) according to your requirements
});


function Copysummary() {
  // Get the text field
  var copyText = document.getElementById("summary");

  // Select the text field
  copyText.select();
  copyText.setSelectionRange(0, 99999); // For mobile devices

  // Copy the text inside the text field
  navigator.clipboard.writeText(copyText.value);
  }
</script>

  <script type="text/javascript">
    //export as pdf
function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  
  // Retrieve the text content of the element
  const summaryText = document.getElementById("summary").innerText || document.getElementById("summary").textContent;
  
  // Add the text content to the PDF
  doc.text(summaryText, 20, 20); // Adjust the x and y coordinates as needed
  
  // Save the PDF
  doc.save("newFile.pdf");
}

function deleteSummary(date) {
    // Make AJAX request to the PHP file
    $.ajax({
        url: 'Summary.php', // Replace 'delete.php' with the path to your PHP file
        method: 'POST',
        data: { date: date }, // Send the date as data
        success: function(response) {
            // Handle the response from the PHP file
            location.reload();
            console.log('Delete request successful');
            // Do something with the response if needed
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('Error sending delete request:', error);
        }
    });
}
function retrieve(datad) {
    // Make AJAX request to the PHP file
    $.ajax({
        url: 'Summary.php',
        method: 'POST',
        data: { datas: datad },
        success: function(response) {
    // Assuming response is already a JavaScript object, not JSON
    var responseData = response;
    
    // Access the fields of the response object
    var question = responseData.question;
    var answer = responseData.answer;
    
    // Set the values to the appropriate HTML elements
// Empty the elements before setting new values
// Clear the textarea before setting new values
$('#og').val('').val(question);
$('#summary').val('').val(answer);

    // $('#og').val(question);
    // $('#summary').val(answer);

    // Log the values for verification
    console.log("Question: " + question);
    console.log("Answer: " + answer);
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

// document.querySelector('#manualForm').addEventListener('submit', (e) => {
//       e.preventDefault();
//       //let formdata = new FormData(document.querySelector('#manualForm'));
//       let subjectName = document.querySelector('#nameSubject');
//       let question = document.querySelector('#question');
//       let answer = document.querySelector('#answer');
//       if (subjectName.value != "" && subjectName.value != null &&
//         question.value != "" && question.value != null &&
//         answer.value != "" && answer.value != null) {

//         $.ajax({
//           url: 'subjectReview.php',
//           data: {
//             'action': 'addSubjectReview',
//             'subjectName': subjectName.value,
//             'question': question.value,
//             'answer': answer.value
//           },
//           //data: formdata,
//           method: 'POST',
      
//           success: function (res) {
//             alert(res);
//             location.reload();
//           }
//         });

//       } else {
//         alert("Insert value for all input!");
//       }

//     });
    </script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</html>
  
