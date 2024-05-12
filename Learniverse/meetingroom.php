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

  <!-- CUSTOMER SUPPORT STYLESHEET -->
  <script src="../customerSupport.js"></script>
  <link rel="stylesheet" href="../customerSupport.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- <script src="endpoint.js" type='text/javascript'></script> -->

  <!-- SHOUQ SECTION: -->
  <script type='text/javascript'>
    $(document).ready(function() {
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
                <a href="workspace.php">My Workspace</a>
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
            <li onclick="customerSupport()"><a href='#'><i class='far fa-question-circle'></i> Customer Support</a></li>
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
        <li class="tool_item"><a href="meetingroom.php">
            Meeting Room</a>
        </li>
        <li class="tool_item"><a href="community.php">
            Community</a>
        </li>
      </ul>
    </div>
    <div class="workarea">
      <div class="workarea_item">
        <div class="top-shelf">
          <h1>My Meeting Rooms</h1>
          <div id="StartMeeting">
            <button id="startMeetingBtn" value="create">Start/Join Public Meeting Room</button>
          </div>
        </div>
        <div class="overlay">
          <div class="modal">
            <h2 id="overlayTitle">New Meeting Room</h2>
            <div id="newMeetingForm">
              <label>Meeting Name</label> <input required id="meetingName" name="meetingName" type="text" placeholder="Enter the meeting name" autocomplete="off">
              <button class="formSubmitBTN">Create</button>
            </div>
            <h3>OR</h3>
            <div id="joinMeetingForm">
              <label>Join a Meeting By Link</label> <input required id="meetingLink" name="meetingLink" type="text" placeholder="Enter the meeting Link">
              <button onclick="joinMeeting()" class="formSubmitBTN">Join</button>
            </div>
            <p class="errorMessage" style="color:red"></p>
          </div>
        </div>
        <div class="allMeetings">

          <!-- <div id="runningMeetings">
            <h2>Active Rooms</h2>
            <div class="cont">
              <div class="spaceColor" style="background-color: navy;"></div>
              <div class='spaceDiv'><i class='fa-solid fa-video'></i> <span>Project X</span>
                <div title="attendees" id="attendees"><i class="fas fa-user"> </i> 3</div>
              </div>

            </div>
          </div> -->
          <div id="spaces">
            <h2>Shared Spaces Rooms</h2>
            <div id="spaceRooms">
              <?php
              $jsonPayload = file_get_contents('php://input');
              if (!empty($jsonPayload)) {
                echo "in";
                $data = json_decode($jsonPayload, true);
                $eventType = $data['type'];
                $meetingId = $data['data']['meetingId'];

                // Define your logic to update the space status
                if ($eventType === 'room.session.started') {
                  // Update the span with id=$meetingID to active
                  echo "<script>document.getElementById('$meetingId').classList.remove('inactive');document.getElementById('$meetingId').classList.add('active');</script>";
                  echo "<script>document.getElementById('$meetingId').textContent = 'Active <i class='fa-solid fa-podcast'></i>';</script>";
                } elseif ($eventType === 'room.session.ended') {
                  // Update the span with id=$meetingID to inactive
                  echo "<script>document.getElementById('$meetingId').classList.remove('active');document.getElementById('$meetingId').classList.add('inactive');</script>";
                  echo "<script>document.getElementById('$meetingId').textContent = 'Inactive';</script>";
                } elseif ($eventType === 'room.client.joined') {
                  // Handle missing properties
                  echo 'it works';
                } else {
                  echo 'Missing required properties in the payload';
                  exit;
                }
              }

              $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
              //get spaces where the active user is an admin of
              $filter = ['admin' => $_SESSION['email']];
              // MongoDB query
              $query = new MongoDB\Driver\Query($filter);
              // MongoDB collection name
              $collectionName = "Learniverse.sharedSpace";
              // Execute the query
              $spaces = $manager->executeQuery($collectionName, $query);
              $space = [];
              foreach ($spaces as $s) {
                $space[] = $s;
              }
              $spaces = json_decode(json_encode($space), true);
              if (count($spaces) != 0) {
              ?>
                <div id="adminSpaces">
                  <span class="spacesTitle">Owned Spaces</span>
                <?php
                foreach ($spaces as $space) {
                  echo "<div class='cont'><div title='space color' class='spaceColor' style='background-color:" . $space['color'] . ";'></div><div onclick='window.open(\"spacemeeting.php?room=" . $space['hostUrl'] . "&space=" . $space['name'] . "%27s&host=true&invite=" . $space['roomUrl'] . "\")' class='spaceDiv' title='" . $space['name'] . "'><i class='fa-solid fa-video'></i> <span>" . $space['name'] . "</span><span id ='" . $space['meetingID'] . "' class='inactive'>Inactive</span></span></div></div>";
                }
              }
                ?>
                </div>
                <div id="otherSpaces">
                  <?php
                  //get spaces where active user is a member of
                  $filterMember = ['members.email' => $_SESSION['email']];
                  $queryMember = new MongoDB\Driver\Query($filterMember);
                  $spaces = $manager->executeQuery($collectionName, $queryMember);
                  $space = [];
                  foreach ($spaces as $s) {
                    $space[] = $s;
                  }
                  $spaces = json_decode(json_encode($space), true);
                  if (count($spaces) != 0) {
                  ?>
                    <span class="spacesTitle">Joined Spaces</span>

                  <?php
                    foreach ($spaces as $space) {
                      $query = new MongoDB\Driver\Query(['email' => $space['admin']]);
                      $adminCursor = $manager->executeQuery('Learniverse.users', $query);
                      $admin = $adminCursor->toArray()[0];
                      echo "<div class='cont'><div title='space color' class='spaceColor' style='background-color:" . $space['color'] . ";'></div><div onclick='window.open(\"spacemeeting.php?room=" . $space['roomUrl'] . "&space=" . $space['name'] . "%27s&host=false\")' class='spaceDiv' title='" . $space['name'] . "'><i class='fa-solid fa-video'></i> <span>" . $space['name'] . "</span><span id ='" . $space['meetingID'] . "' class='inactive'>Inactive</span></span></div></div>";
                    }
                  }
                  ?>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      overlay = document.getElementsByClassName("overlay")[0];

      function joinMeeting() {
        var roomUrl = document.getElementById("meetingLink").value;
        window.open("spacemeeting.php?room=" + roomUrl + "&host=false&space=External");
        overlay.style.display = "none";
      }

      $('#startMeetingBtn').on('click', function(e) {
        overlay.style.display = "flex";
        overlay.addEventListener('click', function(event) {
          if (event.target === overlay) {
            overlay.style.display = "none";
          }
        });

        var meetingName = document.getElementById('meetingName');
        var meetingLink = document.getElementById('meetingLink');

        meetingName.addEventListener('input', function() {
          meetingLink.value = "";
          $("#joinMeetingForm button").hide();
          $("#newMeetingForm button").show()
        });

        meetingLink.addEventListener('input', function() {
          meetingName.value = "";
          $("#newMeetingForm button").hide()
          $("#joinMeetingForm button").show();
        });


      });

      // Get the form data
      var startMeetingBtn = $('#startMeetingBtn').val();
      $("#newMeetingForm button").on('click', function() {
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
              // document.getElementById("meetingLink").href = response.roomUrl;
              // document.getElementById("meetingLink").style.display = "block";
              window.open("spacemeeting.php?room=" + response.hostRoomUrl + "&host=true&space=" + meetingName.value + "'s&invite=" + response.roomUrl);
              overlay.style.display = "none";
            } else {
              document.getElementById("Status").innerHTML = "Status code:" + response.error.code;
              document.getElementById("Status").style.display = "block";
            }
          }
        });
      });
    </script>
  </main>
  <footer id="footer" style="margin-top: 7%;">

    <div id="copyright">Learniverse &copy; 2024</div>
  </footer>

  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>
</body>

</html>