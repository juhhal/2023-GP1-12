<?php

use MongoDB\Driver\Manager;

require 'session.php'; ?>
<!DOCTYPE html>

<head>
  <meta charset="UTF-8">
  <title>Meeting Room</title>
  <link rel="stylesheet" href="header-footer.css">
  <link rel="stylesheet" href="RoomCSS.css">

  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <link rel="manifest" href="favicon_io/site.webmanifest">
  <script src="jquery.js"></script>

  <!-- PROFILE STYLESHEET -->
  <link rel="stylesheet" href="profile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  </script>
  <!-- SHOUQ SECTION: -->
  <script type='text/javascript'>
    $(document).ready(function() {
      document.getElementById("JoinMeeting").style.display = "none";
      var dropdownButton = document.querySelector('.dropdown-button');
      var dropdownMenu = document.querySelector('.Pdropdown-menu');
      dropdownButton.addEventListener('click', function() {
        dropdownMenu.classList.toggle('show');
      });
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
      sidebarTongue.addEventListener('mouseenter', function(event) {
        if (!isSidebarOpen) {
          w3_open();
          isSidebarOpen = true;

        } else if (!isButtonClicked) {
          w3_close();
          isSidebarOpen = false;
        }
      });

      sidebarTongue.addEventListener('click', function(event) {
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

    function showForm() {
      var form = $('.workarea_item form');
      form.toggle();
    }
  </script>
</head>

<body>
  <header>
    <div class="header-container">
      <div class="flex-parent">
        <div class="header_logo">
          <img src="LOGO.png">
          <div>Learniverse</div>
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
        //$googleID = $fetch['google_user_id'];

        ?>
        <div class="dropdown">
          <button class="dropdown-button">
            <i class="fas fa-user" id='Puser-icon'> </i>
            <?php echo ' ' . $fetch['firstname']; ?></button>
          <ul class="Pdropdown-menu">
            <li class='editName center'>
              <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
              <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
              <form id='rename-form' class='rename-form' method='POST' action='updateName.php?q=workspace.php' onsubmit="return validateForm(event)" ;>
                <input type='text' id='PRename' name='Rename' required value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                <span id='rename-error' style='color: red;'></span><br>
                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
              </form>
            </li>
            <li class='center'>Username: <?php echo $fetch['username']; ?></li>
            <li class='center'><?php echo $fetch['email']; ?></li>
            <hr>
            <li><a href='reset.php?q=sharedspace.php'><i class='far fa-edit'></i> Change password</a></li>
            <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
            <hr>
            <li><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out</a></li>
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
          <a href="theFiles.php?q=My Files"> My Files</a>
        </li>
        <li class="tool_item">
          Quiz
        </li>
        <li class="tool_item">
          Flashcard
        </li>
        <li class="tool_item">
          Summarization
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
        <div class="top-shelf">
          <h1>Meeting Room</h1>
        </div>
        <script>
          function showStartInput() {
            document.getElementById("StartMeeting").style.display = "block";
            document.getElementById("JoinMeeting").style.display = "none";
          }

          function showUrlInput() {
            document.getElementById("StartMeeting").style.display = "none";
            document.getElementById("JoinMeeting").style.display = "block";
          }

          function joinMeeting() {
            var roomUrl = document.getElementById("url").value;
            document.getElementById("meetingIframe").src = roomUrl;
            document.getElementById("iframeContainer").style.display = "block";
          }
          $(document).ready(function() {

            $('#CreateRomm').submit(function(e) {
              // Prevent form submission
              e.preventDefault();

              // Get the form data
              var startMeetingBtn = $('#startMeetingBtn').val();

              // Send AJAX request to the server
              $.ajax({
                url: 'createRoom.php',
                type: 'POST',
                data: {
                  startMeetingBtn: startMeetingBtn
                },
                dataType: 'json',
                success: function(response) {
                  if (response.message === "success") {
                    document.getElementById("meetingLink").href = response.roomUrl;
                    document.getElementById("meetingLink").style.display = "block";

                    document.getElementById("meetingIframe").src = response.hostRoomUrl + " allow='camera; microphone; fullscreen; speaker; display-capture; compute-pressure'";
                    document.getElementById("meetingIframe").style.display = "block";
                  } else {
                    document.getElementById("Status").innerHTML = "Status code:" + response.error.code;
                    document.getElementById("Status").style.display = "block";
                  }
                }
              });
            });
          });
        </script>
        <div id="radioOption">
          <input type="radio" id="startRadio" name="inputType" checked="checked" onchange="showStartInput()"> <label for="radio">Create New Room</label>
          <input type="radio" id="urlRadio" name="inputType" onchange="showUrlInput()"> <label for="radio">Paste Room URL</label>
        </div>
        <div id="StartMeeting">
          <form id="CreateRomm" action="createRoom.php">
            <button id="startMeetingBtn" value="create">Create Room</button>
          </form>
        </div>
        <div id="JoinMeeting">
          <label for="url">Room URL:</label>
          <input type="text" id="url" name="url">
          <button id="JoinMeetingBtn" onclick="joinMeeting()">Join Room</button>
        </div>
        <div id="linkContainer">
          <p id="Status" style="display: none;"></p>
          <a id="meetingLink" href="" target="_blank" style="display: none;">Room URL</a>
        </div>
        <div id="iframeContainer">
          <iframe id="meetingIframe" src="" frameborder="0"></iframe>
        </div>
      </div>
    </div>

  </main>
  <footer id="footer" style="margin-top: 7%;">

    <div id="copyright">Learniverse &copy; 2023</div>
  </footer>

  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>
</body>

</html>