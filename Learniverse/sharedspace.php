<?php

use MongoDB\Driver\Manager;

require 'session.php'; ?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Shared Space</title>
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="sharedspaceCSS.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

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
            var overlay = $('.overlay');
            overlay.css("display", "flex");
        }

        function hideForm() {
            var overlay = $('.overlay');
            overlay.hide();
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
                    <h1>Shared Spaces</h1>
                    <button id="newSpaceBTN" onclick="showForm()">New/Join Space</button>
                </div>
                <div id="spaces">
                    <div id="noSpaceMSG"><img src="images/rocket-clouds.png">
                        <span>You are not a part of any <span style="color: #fdae9b;">Space</span> yet. Start by creating a new one!</span>
                    </div>
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
                    if (count($spaces) != 0) {
                    ?>
                        <div id="adminSpaces">
                            <span class="spacesTitle">Owned Spaces</span>
                        <?php
                        foreach ($spaces as $space) {
                            echo "<div class='cont'><div title='space color' class='spaceColor' style='background-color:" . $space['color'] . ";'></div><div onclick='window.location.href=\"viewspace.php?space=" . $space['spaceID'] . "\"' class='spaceDiv' title='" . $space['name'] . "'><span>" . $space['name'] . "</span><span class='spaceInfo'><i title='admin' class='fa-solid fa-user-tie'></i><span> You </span> <i title='members' class='fa-solid fa-user'></i><span>" . count($space['members']) . "</span></span></div></div>";
                        }
                    } ?>
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
                                    echo "<div class='cont'><div title='space color' class='spaceColor' style='background-color:" . $space['color'] . ";'></div><div onclick='window.location.href=\"viewspace.php?space=" . $space['spaceID'] . "\"' class='spaceDiv' title='" . $space['name'] . "'><span>" . $space['name'] . "</span><span class='spaceInfo'><i title='admin' class='fa-solid fa-user-tie'></i><span>" . $admin->firstname . " " .  $admin->lastname . "</span> <i title='members' class='fa-solid fa-user'></i><span>" . count($space['members']) . "</span></span></div></div>";
                                }
                            }
                            ?>
                        </div>
                        <?php
                        //get spaces where active user is a member of
                        $filterMember = ['pendingMembers' => $_SESSION['email']];
                        $queryMember = new MongoDB\Driver\Query($filterMember);
                        $spaces = $manager->executeQuery($collectionName, $queryMember);
                        $space = [];
                        foreach ($spaces as $s) {
                            $space[] = $s;
                        }
                        $spaces = json_decode(json_encode($space), true);
                        if (count($spaces) != 0) {
                        ?>
                            <div id="pendingSpaces">
                                <span class="spacesTitle">Pending Spaces</span>
                            <?php
                            foreach ($spaces as $space) {
                                $query = new MongoDB\Driver\Query(['email' => $space['admin']]);
                                $adminCursor = $manager->executeQuery('Learniverse.users', $query);
                                $admin = $adminCursor->toArray()[0];
                                echo "<div class='spaceDiv' title='awating admission'><span>" . $space['name'] . "</span><i title='cancel request' data-sid='" . $space['spaceID'] . "' class='fa-solid fa-trash removePending'></i></div>";
                            }
                        }
                            ?>
                            </div>
                </div>
            </div>
        </div>

        <!-- overlay -->
        <div class="overlay">
            <div class="modal">
                <h2 id="overlayTitle">Create a New Space</h2>
                <form id="newSpaceForm">
                    <label>Space Name</label> <input required id="spaceName" name="spaceName" type="text" placeholder="Enter the space name" autocomplete="off">
                    <input id="color" type="color" name="color" list="presets">
                    <datalist id="presets">
                        <option value="#2724ff">Blue</option>
                        <option value="#783fa6">Purple</option>
                        <option value="#ffaddd">Light Pink</option>
                        <option value="#ff3d81">Coral Pink</option>
                        <option value="#bb1111">Crimson Red</option>
                        <option value="#61c2ff">Light Blue</option>
                        <option value="#74e2cf">Turquoise</option>
                        <option value="#54ab63">Forest Green</option>
                        <option value="#e0c200">Gold</option>
                        <option value="#ffa061">Orange</option>

                    </datalist>
                    <button type="submit" class="formSubmitBTN">Create</button>
                </form>
                <h3>OR</h3>
                <form id="joinSpaceForm" method="post">
                    <label>Join a Space</label> <input required id="spaceID" name="spaceID" type="text" placeholder="Enter the space Code">
                    <input id="c1" type="color" name="c1" disabled style="visibility: hidden;" list="presets">
                    <button type="submit" class="formSubmitBTN">Join</button>
                </form>
                <p class="errorMessage" style="color:red"></p>
            </div>
        </div>
        <script>
            noSpaceMsg = document.getElementById('noSpaceMSG');
            document.addEventListener('DOMContentLoaded', function() {
                if ($('.spaceDiv').length === 0)
                    noSpaceMsg.style.display = 'flex';
                // Get all elements with the class "removePending"
                var removePendingElements = $('.removePending');

                // Loop through each element
                removePendingElements.each(function() {
                    var element = $(this);

                    // Attach click event listener
                    element.on('click', function() {
                        var spaceID = element.attr('data-sid');
                        Swal.fire({
                            title: 'Heads Up!',
                            text: 'Are you sure you want to no longer request access to this space?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'No',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Perform AJAX request
                                $.ajax({
                                    url: "pendingMemberProcess.php",
                                    method: 'post',
                                    data: {
                                        operation: 'reject',
                                        spacename: "",
                                        spaceid: spaceID,
                                        member: '<?php echo $_SESSION['email']; ?>'
                                    },
                                    success: function(res) {
                                        element.closest(".spaceDiv").remove();
                                        // Check if #pendingSpaces has no .spaceDiv
                                        if ($('#pendingSpaces .spaceDiv').length === 0) {
                                            $('#pendingSpaces').hide(); // Hide the parent container
                                            if ($('.spaceDiv').length === 0)
                                                noSpaceMsg.style.display = 'flex';
                                        }
                                    }
                                });
                            }
                        });
                    });
                });
            });

            overlay = document.getElementsByClassName("overlay")[0];
            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    hideForm();
                }
            });

            var spaceNameInput = document.getElementById('spaceName');
            var spaceIdInput = document.getElementById('spaceID');

            spaceNameInput.addEventListener('input', function() {
                spaceIdInput.value = "";
                $("#joinSpaceForm button").hide();
                $("#newSpaceForm button").show()
            });

            spaceIdInput.addEventListener('input', function() {
                spaceNameInput.value = "";
                $("#newSpaceForm button").hide();
                $("#joinSpaceForm button").show();
            });

            $("#newSpaceForm").on("submit", function() {
                $.ajax({
                    url: "addSharedSpace.php",
                    method: "post",
                    data: {
                        spaceName: $("#spaceName").val(),
                        color: $("#color").val()
                    },
                    success: function(response) {
                        console.log(response);
                        r = JSON.parse(response);
                        $("#newSpaceForm").css("display", "block");
                        $(".errorMessage").text("*" + r.msg);
                        window.location.href = "viewspace.php?space=" + r.createdSpace;
                    },
                });
            });

            $("#joinSpaceForm").on("submit", function() {
                $.ajax({
                    url: "addSharedSpace.php",
                    method: "post",
                    data: {
                        spaceID: $("#spaceID").val()
                    },
                    success: function(response) {
                        $("#joinSpaceForm").css("display", "block");
                        $(".errorMessage").text("*" + response);
                    },
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