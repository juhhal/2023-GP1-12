<!DOCTYPE html>
<html>
<?php require 'session.php';
require 'dbConfig.php'; ?>

<head>
  <meta charset="UTF-8" />
  <title>Pomodoro</title>
  <!-- import style sheets, js code and icons-->

  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600&amp;display=swap" rel="stylesheet" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="pomodoro.css" />
  <link rel="stylesheet" href="header-footer.css" />
  <link rel="stylesheet" href="profile.css" />
  <link rel="stylesheet" href="header-footer.css" />
  <link rel="stylesheet" href="side-bar.css" />
  <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
  <script src="pomodoro.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <?php

  // Create a filter to find the document by user email
  $filter = ['user_id' => $_SESSION['email']];

  // Create a query object with the filter
  $query = new MongoDB\Driver\Query($filter);

  // Replace 'yourDatabaseName' and 'yourCollectionName' with the actual names
  $cursor = $manager->executeQuery('Learniverse.Pomodoro', $query);

  // Get the first matching document
  $userSettings = current($cursor->toArray());

  // Convert data to JSON for use in JavaScript
  $jsonData = json_encode($userSettings);
  ?>
  <script>
    var settings;
    //load page from previous user changes
    document.addEventListener('DOMContentLoaded', function() {
      // Parse the PHP JSON data into a JavaScript object
      settings = <?php echo $jsonData; ?> || {};

      //set background based on user saved preference
      if (settings.Theme && settings.Theme !== "none") {
        document.body.style.backgroundImage = 'url(' + settings.Theme + ')';
        $('#themeSelect').val(settings.Theme).trigger('change');
      }
      //set timers based on user saved preferences
      setPomodoroTimer(settings['pomodoro timer'], 0);
      setShortTimer(settings['short timer']);
      setLongTimer(settings['long timer']);
    });
  </script>
  <script type="text/javascript">
    //side-bar script
    $(document).ready(function() {

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
    //Side-bar
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
</head>

<body class="banner-open" onload="setCurrent(); whatTimer();">
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
        $googleID = $fetch->google_user_id;

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

            <?php if ($googleID === null) {
              echo "<li><a href='reset.php'><i class='far fa-edit'></i> Change password</a></li>";
            } ?>

            <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
            <hr>
            <li id="logout"><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out </a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <main>
    <div id="tools_div">
      <ul class="tool_list">
        <li class="tool_item">
          <a href="workspace.php"><img src="images/calendar.png" /> Calendar & To-Do
          </a>
        </li>
        <li class="tool_item">
          <a href="theFiles.php?q=My Files"><img src="images/file.png" /> My Files</a>
        </li>
        <li class="tool_item">
          <img src="images/quiz.png" />
          Quiz
        </li>
        <li class="tool_item">
          <img src="images/flash-cards.png" />
          Flashcard
        </li>
        <li class="tool_item">
          <img src="images/summarization.png" />
          Summarization
        </li>
        <li class="tool_item">
          <img src="images/study-planner.png" />
          Study Planner
        </li>
        <li class="tool_item"><a href="Notes/notes.php"><img src="images/notes.png">
            Notes</a>
        </li>
        <li class="tool_item">
          <a href="pomodoro.php"><img src="images/pomodoro-technique.png">
            Pomodoro</a>
        </li>
        <li class="tool_item"><a href="gpa.php"><img src="images/gpa.png">
            GPA Calculator</a>
        </li>
        <li class="tool_item">
          <img src="images/collaboration.png" />
          Shared spaces
        </li>
        <li class="tool_item">
          <img src="images/meeting-room.png" />
          Meeting Room
        </li>
        <li class="tool_item"><a href="community.php">
            <img src="images/communities.png" />
            Community</a>
        </li>
      </ul>
    </div>
    <!-- <div id="bg-wrapper">
        <img
          id="bg-image"
          width="100%"
          height="100%"
          opacity="0.5"
          src="images/shore.jpg"
          style="display: block"
          
        />
      </div> -->
    <div class="workarea">
      <div class="workarea_item">
        <div style="padding-top: 10%;"></div>
        <div id="pomodoro-container">
          <div id="pomodoro-durations">
            <div class="form-check">
              <label id="pomodoro" class="form-check-label btn">
                Pomodoro
                <input class="form-check-input" type="radio" name="timerType" id="exampleRadios1" value="pomodoro" checked onchange="whatTimer();" />
              </label>
              <div class="pomo-counter">&nbsp;</div>
            </div>
            <div class="form-check">
              <label id="short" class="form-check-label btn short">
                Short Break
                <input class="form-check-input" type="radio" name="timerType" id="exampleRadios2" value="short" onchange="whatTimer();" />
              </label>
            </div>
            <div class="form-check">
              <label id="long" class="form-check-label btn long">
                Long Break
                <input class="form-check-input" type="radio" name="timerType" id="exampleRadios3" value="long" onchange="whatTimer();" />
              </label>
            </div>
          </div>

          <div id="pomodoro-timer"></div>

          <div id="pomodoro-clock-actions">
            <button id="pomodoro-start" class="btn btn-primary" onclick="startTimer();">
              <span class="shown" id="play-icon">start</span>
            </button>
            <button id="pomodoro-stop" class="no-style bg-transparent" onclick="reset();">
              <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50" style="enable-background: new 0 0 50 50" xml:space="preserve">
                <style type="text/css">
                  .st0 {
                    fill: #ffffff;
                  }
                </style>
                <path class="st0" d="M48.3,3.4c-0.6-0.3-1.3-0.1-1.8,0.3L42.4,8l-0.6-0.5C37.3,3.1,31.3,0.7,25,0.7c0,0,0,0,0,0
                                    c-6.5,0-12.6,2.5-17.2,7.1C3.2,12.4,0.7,18.5,0.7,25c0,6.5,2.5,12.6,7.1,17.2c4.6,4.6,10.7,7.1,17.2,7.1c6,0,11.8-2.2,16.3-6.3
                                    c0.1-0.1,0.1-0.2,0.1-0.3c0-0.1,0-0.2-0.1-0.3l-4-4c-0.2-0.2-0.4-0.2-0.5,0c-3.2,2.9-7.4,4.4-11.7,4.4c-4.8,0-9.3-1.9-12.6-5.2
                                    c-3.3-3.3-5.2-7.8-5.2-12.4c0-4.8,1.8-9.3,5.2-12.7C15.8,9,20.2,7.2,25,7.2c0,0,0.1,0,0.1,0c4.5,0,8.7,1.7,12,4.8l0.6,0.6L33,17.4
                                c-0.5,0.5-0.6,1.1-0.3,1.8c0.3,0.6,0.8,1,1.5,1h13.6c0.9,0,1.6-0.7,1.6-1.6V4.9C49.3,4.3,48.9,3.7,48.3,3.4z"></path>
              </svg>
            </button>
            <button id="pomodoro-settings" class="no-style bg-transparent" onclick="setting();">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" style="enable-background: new 0 0 36 36" xml:space="preserve">
                <path fill="white" d="m34.6 23.3-3.2-2.9-.3-.3.1-.4c.1-.6.1-1.2.1-1.8 0-.6 0-1.2-.1-1.8l-.1-.4.3-.3 3.2-2.9c.3-.3.4-.7.3-1-.3-.8-.7-1.6-1.1-2.4l-.3-.6c-.5-.8-1-1.5-1.5-2.2-.2-.2-.4-.3-.7-.3H31l-4.1 1.3-.4.1-.3-.2c-.8-.6-1.9-1.3-3.1-1.8l-.3-.1-.1-.4-1-3.9c-.1-.4-.4-.7-.7-.7-1-.2-2-.2-3-.2s-2 .1-3 .2c-.4.1-.7.3-.7.7l-.9 4.2-.1.4-.4.1c-1.1.4-2.1 1-3 1.8l-.3.2-.4-.1-4-1.3h-.3c-.3 0-.5.1-.7.3-.6.7-1.1 1.4-1.6 2.2l-.3.5c-.4.8-.8 1.6-1.1 2.4-.1.3 0 .7.3 1l3.2 2.9.3.3-.1.4c-.1.6-.1 1.2-.1 1.8 0 .6 0 1.2.1 1.8l.1.4-.3.3-3.2 2.9c-.3.3-.4.7-.3 1 .3.8.7 1.6 1.1 2.4l.3.6c.4.7 1 1.5 1.5 2.2.2.2.4.3.7.3h.3l4.1-1.3.4-.1.3.2c.9.7 2 1.3 3 1.8l.4.1.1.4.9 4.2c.1.4.4.6.8.7 1 .2 1.9.2 2.9.2s2-.1 3-.2c.4-.1.7-.4.8-.7l.9-4.2.1-.4.3-.1c1.2-.5 2.2-1.1 3.1-1.8l.3-.2.4.1L31 30h.3c.3 0 .5-.1.7-.3.6-.7 1.1-1.4 1.5-2.2l.3-.6c.4-.8.8-1.6 1.1-2.4.1-.6-.1-1-.3-1.2zm-12-.7c-1.2 1.2-2.9 1.9-4.6 1.9s-3.4-.7-4.6-1.9c-1.2-1.2-1.9-2.9-1.9-4.7 0-1.7.7-3.4 1.9-4.6 1.2-1.3 2.9-1.9 4.6-1.9 1.7 0 3.4.7 4.6 1.9 1.2 1.2 1.9 2.9 1.9 4.6.1 1.8-.6 3.5-1.9 4.7z"></path>
              </svg>
            </button>
          </div>
        </div>
        <br /><br /><br /><br /><br /><br /><br /><br />
        <div id="bottom">
          <div id="bottom-mid"></div>
          <div id="bottom-right">
            <button id="full">
              <!--?xml version="1.0" encoding="UTF-8"?-->
              <svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">
                <path d="M43.42,11.89V.5H6.04C2.98,.59,.5,3.14,.5,6.19V43.42H11.89V11.89h31.53Z"></path>
                <path d="M127.5,43.42V6.19c0-3.14-2.55-5.69-5.69-5.69h-37.23V11.89h31.53v31.53h11.39Z"></path>
                <path d="M6.19,127.5H43.42v-11.39H11.89v-31.53H.5v37.4c.08,3.03,2.63,5.52,5.69,5.52Z"></path>
                <path d="M84.58,116.11v11.39h37.23c3.14,0,5.69-2.55,5.69-5.69v-37.23h-11.39v31.53h-31.53Z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div id="modal-dialog" class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" id="close-btn" class="btn-close" onclick="setting();"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex align-items-start">
                <div class="nav flex-column align-items-start nav-pills me-3" id="settModal-tab">
                  <button class="nav-link actives" id="settModal-general-tab" type="button" onclick="show(1);">
                    General
                  </button>
                  <button class="nav-link" id="settModal-timers-tab" type="button" onclick="show(2);">
                    Timers
                  </button>
                  <button class="nav-link" id="settModal-sounds-tab" type="button" onclick="show(3);">
                    Sounds
                  </button>
                </div>
                <div class="tab-content" id="settModal-tabContent">
                  <div class="tab-pane" id="settModal-timers">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-3 d-none">
                          <span>Timer Settings</span>
                        </div>
                        <div class="col-12">
                          <div class="row">
                            <div class="col-md-4">
                              <div class="mb-4">
                                <label class="form-label">Pomodoro</label>
                                <input type="number" class="form-control" id="pomBreakLength" step="1" min="1" max="90" value="25" />
                                <div class="form-text">minutes</div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="mb-4">
                                <label class="form-label">Short Break</label>
                                <input type="number" class="form-control" id="shortBreakLength" step="1" min="1" max="90" value="5" />
                                <div class="form-text">minutes</div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="mb-4">
                                <label class="form-label">Long Break</label>
                                <input type="number" class="form-control" id="longBreakLength" step="1" min="1" max="90" value="10" />
                                <div class="form-text">minutes</div>

                              </div>
                            </div>
                          </div>
                          <div class="form-check form-switch mb-4" style="padding-left: 0;">
                            <div class="mt-2 text-secondary">
                              This Pomodoro has a '1 short break / 1 long break' cycle
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="settModal-sounds">
                    <div class="container-fluid">
                      <div class="mb-4">
                        <label class="form-label">Select background sound:</label>
                        <select id="backgroundSound" class="form-select" onchange="changeSetting();">
                          <option value="none">None</option>
                          <option value="fireplace">Fire crackling</option>
                          <option value="river">Calm river</option>
                          <option value="lofi">Lofi</option>
                        </select>
                      </div>
                      <div class="mb-4">
                        <label class="form-label">background volume</label>
                        <input type="range" class="form-range" min="0" max="1" step="0.05" id="timerSoundVolume" onchange="changeAudio(this.value);" />
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fad show actives" id="settModal-general">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-3 d-none">
                          <span>General Settings</span>
                        </div>
                        <div class="col-12">
                          <div class="mb-4">
                            <label class="form-label">Select theme:</label>
                            <select id="themeSelect" class="form-select" onchange="changeSetting();">
                              >
                              <option value="none">None</option>
                              <option value="images/shore.jpg">
                                Rocky Beach
                              </option>
                              <option value="images/fire.jpg">Fireplace</option>
                              <option value="images/bg2.jpg">Art</option>

                              <option value="images/library.jpeg">Library</option>
                              <option value="images/cloud.jpg">Clouds</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="resetUserSettings" class="btn btn-outline-danger" onclick="ResetAll();">
                Reset all
              </button>

              <button type="button" id="saveUserSettings" class="btn btn-primary" onclick="changeSetting(1);">
                Confirm
              </button>
            </div>
          </div>
          <audio id="audio" src="" loop="true"></audio>
          <!-- </div> -->
        </div>
      </div>
    </div>
  </main>
  <footer id="footer">
    <div class="footer-div" id="socials">
      <h4>Follow Us on Social Media</h4>

      <a href="https://twitter.com/learniversewebsite" target="_blank"><img src="images/twitter.png" alt="@Learniverse"></a>

    </div>
    <div class="footer-div" id="contacts">
      <h4>Contact Us</h4>

      <a href="mailto:learniverse.website@gmail.com" target="_blank"><img src="images/gmail.png" alt="learniverse.website@gmail.com"></a>

    </div>
    <img id="footerLogo" src="LOGO.png" alt="Learniverse">
    <div id="copyright">Learniverse &copy; 2023</div>
  </footer>
  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>
</body>

</html>