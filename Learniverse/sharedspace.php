<?php

use MongoDB\Driver\Manager;

require 'session.php'; ?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Shared Space</title>
    <link rel="stylesheet" href="emptyWorkspace.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="sharedspacecss.css">

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
                        <li><a href='reset.php?q=workspace.php'><i class='far fa-edit'></i> Change password</a></li>
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
                <li class="tool_item"><a href="workspace.php"><img src="images/calendar.png">
                        Calendar & To-Do </li>
                <li class="tool_item"><a href="thefiles.php?q=My Files"><img src="images/file.png">
                        My Files</a>
                </li>
                <li class="tool_item"><img src="images/quiz.png">
                    Quiz
                </li>
                <li class="tool_item"><img src="images/flash-cards.png">
                    Flashcard
                </li>
                <li class="tool_item"><img src="images/summarization.png">
                    Summarization
                </li>
                <li class="tool_item"><img src="images/study-planner.png">
                    Study Planner
                </li>
                <li class="tool_item"><img src="images/notes.png">
                    Notes
                </li>
                <li class="tool_item"><img src="images/pomodoro-technique.png">
                    Pomodoro
                </li>
                <li class="tool_item"><img src="images/gpa.png">
                    GPA Calculator
                </li>
                <li class="tool_item"><img src="images/collaboration.png">
                    Shared spaces
                </li>
                <li class="tool_item"><img src="images/meeting-room.png">
                    Meeting Room
                </li>
                <li class="tool_item"><img src="images/communities.png">
                    Community
                </li>
            </ul>
        </div>

        <div class="workarea">

            <div class="workarea_item">
                <div class="top-shelf">
                    <h1>Shared Space</h1>
                    <button id="newSpaceBTN" onclick="showForm()">New Space</button>
                </div>
                <form id="newSpaceForm" method="post" action="addSharedSpace.php" style="display: none;">
                    <label>Space Name</label> <input id="spaceName" name="spaceName" type="text">
                    <button type="submit" class="formSubmitBTN">Create</button>
                    <h3>OR</h3>
                </form>

                <form id="joinSpaceForm" method="post" action="addSharedSpace.php" style="display: none;">
                    <label>Join a Space</label> <input id="spaceID" name="spaceID" type="text">
                    <button type="submit" class="formSubmitBTN">Join</button>
                </form>
                <div id="spaces">
                    <div id="adminSpaces">
                        <span class="spacesTitle">Your Spaces:</span>
                        <?php
                        $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

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
                        foreach ($spaces as $space) {
                            echo "<div data-spaceID='".$space['spaceID']."' class='spaceDiv'><span>" . $space['name'] . "</span><span class='spaceInfo'><i title='admin' class='fa-solid fa-user-tie'></i><span>" . $fetch['firstname'] . " " .  $fetch['lastname'] . "</span> <i title='members' class='fa-solid fa-user'></i><span>" . (count($space['members']) + 1) . "</span></span></div>";
                        }
                        ?>

                    </div>
                    <div id="otherSpaces">
                        <span class="spacesTitle">Spaces You are a Member Of:</span>
                        <?php
                        //get spaces where active user is a member of
                        $filterMember = ['members' => $_SESSION['email']];
                        $queryMember = new MongoDB\Driver\Query($filterMember);
                        $spaces = $manager->executeQuery($collectionName, $queryMember);
                        $space = [];
                        foreach ($spaces as $s) {
                            $space[] = $s;
                        }
                        $spaces = json_decode(json_encode($space), true);

                        foreach ($spaces as $space) {
                            $query = new MongoDB\Driver\Query(['email' => $space['admin']]);
                            $adminCursor = $manager->executeQuery('Learniverse.users', $query);
                            $admin = $adminCursor->toArray()[0];
                            echo "<div data-spaceID='".$space['spaceID']."' class='spaceDiv'><span>" . $space['name'] . "</span><span class='spaceInfo'><i title='admin' class='fa-solid fa-user-tie'></i><span>" . $admin->firstname . " " .  $admin->lastname . "</span> <i title='members' class='fa-solid fa-user'></i><span>" . (count($space['members']) + 1) . "</span></span></div>";
                        }
                        ?>
                    </div>
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