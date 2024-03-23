<!DOCTYPE html>
<?php
require_once __DIR__ . '../../vendor/autoload.php';
session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$userEmail = $_SESSION['email'];
$query = new MongoDB\Driver\Query(['user_email' => $userEmail]);
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE); 
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>


<style>
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

        toolsDiv.style.transition = 'transform 0.3s ease-out';
      });
    });
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
      event.preventDefault();

      var input = document.getElementById('PRename');
      var value = input.value.trim(); 

      var errorSpan = document.getElementById('rename-error');

      if (value === '') {
        errorSpan.textContent = 'Please enter a valid name.';
        return false; 
      }

      var nameParts = value.split(' ').filter(part => part !== '');

      if (nameParts.length < 2) {
        errorSpan.textContent = 'Please enter both first name and last name.'; 
        return false; 
      }

      // Check if both names start with a letter
      var isValid = nameParts.every(part => /^[A-Za-z]/.test(part));

      if (!isValid) {
        errorSpan.textContent = 'Names should start with a letter.'; 
        return false; 
      } else {
        errorSpan.textContent = ''; 
      }

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
            </ul> 
          </nav>
        </div>
        <div class="overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
        <?php
        require '../jwt.php';

        $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

        $database = $connection->Learniverse;
        $Usercollection = $database->users;

        $data = array(
          "email" => $_SESSION['email']
        );

        $fetch = $Usercollection->findOne($data);

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
<div class="intro-card-container">
          <div class="intro-card">
            <h2>Hello 👋</h2>
            <p>How would you like to learn?</p>
            <a id="summarize">
              <div>
                <h4>✍️ Multiple Choice</h4>
                <p>
                  Strengthen your understanding on every concept by solving
                  multiple choice questions
                </p>
              </div>
            </a>
            <a id="summarizeTrueFalse">
              <div>
                <h4>✍️ True or False</h4>
                <p>
                  Strengthen your understanding on every concept by solving
                  true or false questions
                </p>
              </div>
            </a>
            <img class="close-icon" src="icons/close.svg" alt="" />
          </div>
         </div>
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

        <div class="questions-modal-container">
        <div class="questions-modal">
          <h2>Quiz</h2>
          <img class="quizResultCloseIcon" src="icons/close.svg" alt="" />
        </div>
      </div>

    <div class="container">
      <div class="summarize-heading">
        <h1>Generated Quizzes</h1>
        <p>Generate quizzes to test your knowledge.</p>
      </div>

        <div class="button-container">
        <div class="summary-wrapper">

        <form enctype="multipart/form-data" method="post" action="" id="uploadForm">
          <input type="file" id="file-input" />
          <label id="" for="file-input">
            <i class="fa-solid fa-arrow-up-from-bracket"></i>
            &nbsp; Choose Files To Upload
          </label>
        </form>
        <label id="myLabel" onclick="showModal()" >
          <i class="fa-solid fa-arrow-up-from-bracket"></i>
          &nbsp; My uploaded files
        </label>
        <label  id="generateBtn" style="margin: 0 10px;" class="disabled">
        <i class="fa-solid fa-wand-magic-sparkles disabled"></i>
          &nbsp; Generate
        </label>
        </div>
        <div id="num-of-files"></div>
        <ul id="files-list"></ul>
      </div>

<?php
    $database = $connection->Learniverse;
    $Usercollection = $database->users;
    $FileCollection = $database->Quizzes;

    
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
              return isset($user['quizzes']) ? $user['quizzes'] : [];
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
    width: 80%;
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
  margin-top: -15%;
  margin-bottom: 20%;
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

@media screen and (max-width: 768px) {
  .table {
    width: 100%;
    margin: 10px; /* Adjust margins for small screens */
    box-shadow: none; /* Optional: remove shadow on small screens */
  }
  
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
                <td>Recent Quizzes</td>
                <td>Score</td>
                <td>Time Created</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filesList as $file) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($file['name']); ?></td>
                    <td>
                      
                    <?php 
                      $score = 0;
                      $total = 0;
                      foreach ($file['result'] as $result) {
                          if ($result['correct'] === 'true') {
                              // $score = $score + $result['score'];
                              $score = $score + 1;
                          }
                          // $total = $total + $result['score'];
                          $total = $total + 1;
                      }
                      echo $score . '/' . $total;
                      ?>
                    </td>
                    <td>
                    <?php 
                          if (!empty($file['date'])) {
                              echo date('Y-m-d ', strtotime($file['date'])); 
                          }
                      ?>
                    </td>
                    <td>
                    <a class="viewResultModal view-quiz" id="updateFile" data-result-value="<?php echo htmlspecialchars(json_encode($file['result'])); ?>">
                        <button class="file-edit btn">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </a>


                    <button class="file-edit btn download-pdf" onclick="saveQuizAsPDF('<?php echo $file['id']; ?>')">
                                <i class="fas fa-download" aria-hidden="true"></i>
                        </button>

                        <button class="file-edit btn save-quiz" onclick="saveQuizAsPDF('<?php echo $file['id']; ?>')">
                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                        </button>

                        <button class="file-edit btn delete-quiz" onclick="deleteQuiz('<?php echo $file['id']; ?>')">
                        <i class="fas fa-trash"></i>
                        </button>


                  </td>
               
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

</div>

</main>
    <footer id="footer" style="margin-top: 7%; margin-top:0%;">

<div id="copyright">Learniverse &copy; 2024</div>
</footer>


  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<script>
// Include pdfjs-dist library

const generateBtn = document.querySelector("#generateBtn");
const introCard = document.querySelector(".intro-card");
const introCardContainer = document.querySelector(".intro-card-container");
const startModalCloseIcon = document.querySelector(".close-icon");



let fileInput = document.getElementById("file-input");
let fileList = document.getElementById("files-list");
let numOfFiles = document.getElementById("num-of-files");
let extractedValue; 
let fileName;
fileInput.addEventListener("change", () => {
    fileList.innerHTML = "";
    numOfFiles.textContent = "";

    let file = fileInput.files[0];
    let fileType = file.type;

    if (fileType === "application/pdf" || fileType === "application/msword" || fileType === "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
        let reader = new FileReader();
        let listItem = document.createElement("li");
        let fileName = file.name;
        let fileSize = (file.size / 1024).toFixed(1);
        listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}KB</p>`;
        if (fileSize >= 1024) {
            fileSize = (fileSize / 1024).toFixed(1);
            listItem.innerHTML = `<p>${fileName}</p><p>${fileSize}MB</p>`;
        }
        fileList.appendChild(listItem);
        generateBtn.classList.remove("disabled");
    } else {
        Swal.fire({
            title: 'Invalid file type',
            text: 'Only PDF and DOC files are allowed.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});
            let loadingOverlay = document.getElementById("loadingOverlay");

            fileInput.addEventListener("change", function() {
    let formData = new FormData();
    formData.append('file', fileInput.files[0]);
    formData.append('readpdf', 'true'); // Include the 'readpdf' parameter

    console.log('FormData:', formData); // Log FormData object

    showLoading(); // Show loading overlay

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '../summarization/extract.php'); 
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            hideLoading(); // Hide loading overlay
            if (xhr.status === 200) {
                // Request was successful, handle response here
                extractedValue = xhr.responseText;
                console.log('Response:', xhr.responseText);
            } else {
                // Request failed, handle error here
                console.error('Error:', xhr.statusText);
            }
        }
    };
    xhr.send(formData);
});


            $('#summarize').click(function() {
              let fileName = fileInput.files[0].name;
              let formData = new FormData();
              formData.append('fileUpload', fileInput.files[0]);

                  showLoading(); 
                  console.log("Sending request to extract.php...");
                  var data = [fileName, extractedValue];   
                  console.log({data})               
                      $.ajax({
                      url: 'extractQuizes.php',
                      type: 'POST',
                      data: formData, 
                      processData: false,
                       contentType: false,

                      success: function(response) {
                        $.ajax({
                          url: 'postQuiz.php',
                          method: 'POST',
                          data: {
                            name: fileName,
                            body: response.data
                          },
                          success: function(res) {
                            console.log(response);
                            window.location.href = 'quiz.php?data=' + encodeURIComponent(JSON.stringify(response.data)) + '&title=' + fileName + '&id=' + res.quizId;
                            hideLoading();
                          },
                          error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                          }
                        });
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
              
          });
          
          $('#summarizeTrueFalse').click(function() {
              let fileName = fileInput.files[0].name;
              let formData = new FormData();
              formData.append('fileUpload', fileInput.files[0]);
              formData.append('quizType', 'trueFalse');

                  showLoading(); 
                  console.log("Sending request to extract.php...");
                  var data = [fileName, extractedValue];   
                      $.ajax({
                      url: 'extractQuizes.php',
                      type: 'POST',
                      data: formData, 
                      processData: false,
                       contentType: false,

                      success: function(response) {
                        $.ajax({
                          url: 'postQuiz.php',
                          method: 'POST',
                          data: {
                            name: fileName,
                            body: response.data,
                          },
                          success: function(res) {
                            window.location.href = 'quiz.php?data=' + encodeURIComponent(JSON.stringify(response.data)) + '&title=' + fileName + '&id=' + res.quizId;
                            hideLoading();
                          },
                          error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error:", textStatus, errorThrown);
                          }
                        });
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
              
          });


          function showLoading() {
                loadingOverlay.style.display = 'block';
            }

            // Function to hide loading overlay
            function hideLoading() {
                loadingOverlay.style.display = 'none';
            }


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
        url: 'backend.php',
        method: 'POST',
        data: { datas: datad },
        success: function(response) {
        var responseData = response;
        
        var question = responseData.question;
        var answer = responseData.answer;

        console.log(responseData);
},

        error: function (xhr, status, error) {
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

const viewResultButtons = document.querySelectorAll('.viewResultModal');
const modalContainer = document.querySelector('.questions-modal-container');
const modal = document.querySelector('.questions-modal');
const quizResultCloseIcon = document.querySelector('.quizResultCloseIcon');


viewResultButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        modalContainer.style.display = 'block';
        modalContainer.style.background = "rgba(0, 0, 0, 0.2)";
        modalContainer.style.zIndex = "1000";
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        console.log({target:button.getAttribute('data-result-value')});
        const resultData =  JSON.parse(button.getAttribute('data-result-value'));
        console.log({resultData})

      resultData.forEach(result => {
        const question = document.createElement('div');
        question.classList.add('question');
        question.innerHTML = `
            <h5>Question: ${result.question}</h5>
            <p>Your Answer: ${result.userAnswer} ${result.correct === 'true' ? '<span class="correct">Correct</span>' : '<span class="wrong">Wrong</span>'}</p>
        `;
        modal.appendChild(question);
    });
    });    
});

quizResultCloseIcon.addEventListener('click', function() {

    modalContainer.style.display = 'none';
    modalContainer.style.background = "rgba(0, 0, 0, 0)";
    modalContainer.style.zIndex = "0";
    document.documentElement.style.overflow = 'auto';
    document.body.style.overflow = 'auto';


    const questions = document.querySelectorAll('.question');
    questions.forEach(question => {
        question.remove();
    });
});


generateBtn.addEventListener("click", () => {
  introCard.style.display = "flex";
  introCardContainer.style.background = "rgba(0, 0, 0, 0.2)";
  introCardContainer.style.zIndex = "1000";
});

startModalCloseIcon.addEventListener("click", () => {
    introCard.style.display = "none";
    introCardContainer.style.background = "";
    introCardContainer.style.zIndex = "-1000";
});

function deleteQuiz(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'deleteQuiz.php',
                method: 'POST',
                data: { quizId: id },
                success: function(response) {
                    location.reload();
                    console.log('Delete request successful');
                },
                error: function(xhr, status, error) {
                    console.error('Error sending delete request:', error);
                }
            });
        }
    });
}
window.jsPDF = window.jspdf.jsPDF;


function saveQuizAsPDF(id) {
    const quizes = <?php echo json_encode($filesList); ?>;
    const quiz = quizes.find(quiz => quiz.id === id);
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        lineHeight: 7
    });

    const result = quiz.result;
    const title = quiz.name;

    doc.setFontSize(10); // Set font size to 10
    doc.text(title, 10, 10);

    let y = 20;

    result.forEach(result => {
        const question = result.question;
        const userAnswer = result.userAnswer;
        const correct = result.correct === 'true' ? 'Correct' : 'Wrong';

        doc.setFontSize(10); // Set font size to 10
        doc.text(`Question: ${question}`, 10, y);
        doc.text(`Your Answer: ${userAnswer} (${correct})`, 10, y + 5); // Adjust line spacing by changing y + 5
        y += 10; // Adjust line spacing by changing y increment
        doc.text('\n \n'); // Add two new lines

        // Check if content exceeds the page height and add a new page if necessary
        if (y >= doc.internal.pageSize.height - 10) {
            doc.addPage();
            y = 10; // Reset y position for the new page
        }
    });

    doc.save(`${title}_quiz.pdf`);
}


tippy('.delete-quiz', {
    content: 'Delete Quiz',
});

tippy('.save-quiz', {
    content: 'Save Quiz',
});

tippy('.download-pdf', {
    content: 'Download PDF',
});

tippy('.view-quiz', {
    content: 'View Quiz',
});


    </script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</html>
  
