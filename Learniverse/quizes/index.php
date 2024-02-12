<!DOCTYPE html>
<?php
require_once '../vendor/autoload.php';
session_start();
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$userEmail = $_SESSION['email'];
$query = new MongoDB\Driver\Query(['user_email' => $userEmail]);
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../profile.css">
  <link rel="stylesheet" href="../header-footer.css">
  <link rel="stylesheet" href="../theFiles.css">
  <link rel="stylesheet" href="index.css">
  <script src="../jquery.js"></script>
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


  <div id="tools_div">
        <ul class="tool_list">
        <li class="tool_item">
          <a href="workspace.php"> Calendar & To-Do
          </a>
        </li>
        <li class="tool_item">
          <a href="theFiles.php?q=My Files"> My Files</a>
        </li>
        <li class="tool_item">
          <a href="/quizes/">Quiz</a>
        </li>
        <li class="tool_item">
          Flashcard
        </li>
        <li class="tool_item">
          <a href="summarization/summarization.php">Summarization</a>
        </li>
        <li class="tool_item">
          Study Planner
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
        <li class="tool_item">
          Shared spaces
        </li>
        <li class="tool_item">
          Meeting Room
        </li>
        <li class="tool_item"><a href="community.php">
            Community</a>
        </li>
      </ul>
        </div>

        <div class="container">
      <div class="btn-container">
        <button id="upload-btn">Upload</button>
        <input id="file-upload" type="file" hidden />
      </div>
      <div class="intro-card-container">
        <div class="intro-card">
          <h2>Hello User 👋</h2>
          <p>How would you like to learn?</p>
          <a href="/quizes/quiz.php">
            <div>
              <h4>✍️ Quiz ?</h4>
              <p>
                Strengthen your understanding on every concept by solving
                multiple choice questions
              </p>
            </div>
          </a>
          <img class="close-icon" src="icons/close.svg" alt="" />
        </div>
      </div>

      <table class="table" id="myTable">
        <thead>
          <tr>
            <th class="table__col-1" scope="col">File Name</th>
            <th class="table__col-2" scope="col">Score</th>
            <th class="table__col-3" scope="col">Upload Time</th>
            <th class="table__col-4" scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Mark</td>
            <td>100</td>
            <td>2/2/2024</td>
            <td>
              <img class="view-icon" src="icons/view.svg" alt="" />
              <img
                id="trash-icon"
                class="trash-icon"
                src="icons/trash.svg"
                alt=""
              />
            </td>
          </tr>
        </tbody>
      </table>
      <div class="questions-modal-container">
        <div class="questions-modal">
          <h2>Quiz</h2>
          <div class="question">
            <h5>What is the capital of Canada?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>What is the longest river in the world?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>Which desert is the largest in the world?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>What country has the most natural lakes?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>In which continent is the Amazon Rainforest located?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>What is the smallest country in the world?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>Mount Everest is located in which mountain range?</h5>
            <p>Answer</p>
          </div>
          <div class="question">
            <h5>What is the capital city of Australia?</h5>
            <p>Answer</p>
          </div>
          <img class="close-icon" src="icons/close.svg" alt="" />
        </div>
      </div>
    </div>

    <footer>
    <div class="footer-div" id="socials">
      <h4>Follow Us on Social Media</h4>

      <a href="https://twitter.com/learniversewebsite" target="_blank"><img src="../images/twitter.png" alt="@Learniverse"></a>

    </div>
      <div class="questions-count">
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
        <div class="question-count"></div>
      </div>
      <div class="question-container">
        <div class="question-progress">
          <img
            src="sky-spark.svg"
            alt="spark"
          />
          <p class="">1 from 10</p>
        </div>
        <div class="answers-container">
          <div>
            <h2 class="question-text">Question?</h2>
          </div>
          <div class="form" id="form1">
            <div form="form1" class="answer-container">
              <input id="answer" name="answerGroup" type="radio" />
              <label for="answer"> Answer One </label>
            </div>
            <div form="form1" class="answer-container">
              <input id="answer1" name="answerGroup" type="radio" />
              <label for="answer1"> Answer Two </label>
            </div>
            <div form="form1" class="answer-container">
              <input id="answer2" name="answerGroup" type="radio" />
              <label for="answer2"> Answer Three </label>
            </div>
          </div>
          <div class="skip-container">
            <p class="">Skip</p>
          </div>
        </div>
      </div>
    </div>

    <footer id="footer" style="margin-top: 7%;">

<div id="copyright">Learniverse &copy; 2023</div>
</footer>



  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>

  <script src="./index.js"></script>
</body>
</html>
  
