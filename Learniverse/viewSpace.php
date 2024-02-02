<?php
require "session.php";
//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// MongoDB query
$filter = ['spaceID' => new MongoDB\BSON\Regex($_GET['space'])];
$query = new MongoDB\Driver\Query($filter);

// MongoDB collection name
$collectionName = "Learniverse.sharedSpace";

// Execute the query
$result = $manager->executeQuery($collectionName, $query);
$space = $result->toArray()[0];
if ($_SESSION['email'] != $space->admin && !in_array($_SESSION['email'], $space->members)) {
    header("Location:sharedSpace.php");
}

?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Shared space</title>
    <link rel="stylesheet" href="viewSpaceCSS.css">
    <link rel="stylesheet" href="header-footer.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

        function showAlert(title, message) {
            Swal.fire({
                html: '<br><h1>' + title + '</h1>' +
                    '' +
                    '' +
                    '<p>' + message + '</p>' +
                    '' +
                    '',
                showCancelButton: false,
                showConfirmButton: false,
                cancelButtonText: 'Close',
                buttonsStyling: false,
                showCloseButton: true
            });
        }

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;

            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
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
                $database = $connection->Learniverse;
                $Usercollection = $database->users;
                $data = array(
                    "email" => $_SESSION['email']
                );
                $fetch = $Usercollection->findOne($data);
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
                        <li><a href="reset.php?q=viewspace.php?space=<?php echo $_GET['space']; ?>"><i class='far fa-edit'></i> Change password</a></li>
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
                        Shared spaces</a>
                </li>
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
                <h1><?php echo $space->name ?></h1>
                <!-- Tab links -->
                <div class="tabs">
                    <button class="tablinks" onclick="openTab(event, 'Feed')">Feed</button>
                    <button class="tablinks" onclick="openTab(event, 'Project')">Project</button>
                    <button class="tablinks" onclick="openTab(event, 'Members')">Members <?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span>"; ?></button>
                </div>

                <!-- Tab content -->
                <div id="Feed" class="tabcontent">
                    <h3>Feed content</h3>
                </div>

                <div id="Project" class="tabcontent">
                    <h3>Project content</h3>
                </div>

                <div id="Members" class="tabcontent">
                    <button id="spaceInviteBTN"><i class="fa-solid fa-user-plus"></i> Invite Members <?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span>"; ?></button>
                    <h3>Admin</h3>
                    <?php echo "<li id='adminName'><i title='admin' class='fa-solid fa-user-tie'></i> $fetch->firstname $fetch->lastname </li>" ?>
                    <h3>Members (<?php echo count($space->members) ?>)</h3>
                    <?php

                    echo "<ul class='memberList'>";

                    // Access individual members
                    foreach ($space->members as $member) {
                        $query = new MongoDB\Driver\Query(['email' => $member]);
                        $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                        $memberName = $memberCursor->toArray()[0];
                        // print the member
                        echo "<li><i title='members' class='fa-solid fa-user'></i> $memberName->firstname $memberName->lastname  </li>";
                    }

                    echo "</ul>";
                    ?>


                    <!-- invite member div after clicking button -->

                    <div id="overlay">
                        <div id="modal">
                            <h2>Invite New Members</h2>
                            <p class="guideline">Copy and send the following Code to invite your friends to this space!</p>
                            <input type="text" id="invitationCode" value="<?php echo $space->spaceID ?>" readonly>
                            <button id="copyButton"><i class="fa-regular fa-copy"></i> Copy</button>
                            <h3><?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) {
                                    echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span> Join Requests"; ?></h3>
                            <ul id="joinRequestsList">
                                <?php foreach ($space->pendingMembers as $member) { ?>
                                    <li>
                                        <?php echo "<span class='member-email'>$member</span>"; ?>
                                        <span>
                                            <button class="acceptButton">Accept <i class="fa-solid fa-check"></i></button>
                                            <button class="rejectButton">Reject <i class="fa-solid fa-xmark"></i></button>
                                        </span>
                                    </li>
                            <?php
                                    }
                                }
                            ?>
                            </ul>
                        </div>
                    </div>

                    <script>
                        var spaceInviteBTN = document.getElementById('spaceInviteBTN');
                        var overlay = document.getElementById('overlay');
                        var invitationCode = document.getElementById('invitationCode');
                        var copyButton = document.getElementById('copyButton');
                        var acceptButtons = document.getElementsByClassName('acceptButton');
                        var rejectButtons = document.getElementsByClassName('rejectButton');
                        var joinRequestsList = document.getElementById('joinRequestsList');

                        spaceInviteBTN.addEventListener('click', function() {
                            overlay.style.display = 'flex';
                            copyButton.disabled = false;
                            copyButton.innerHTML = '<i class="fa-regular fa-copy"></i> Copy';

                            Array.from(acceptButtons).forEach(function(button) {
                                var listItem = button.parentNode.parentNode;
                                var member = listItem.querySelector('.member-email').textContent.trim();
                                button.addEventListener('click', function() {

                                    $.ajax({
                                        url: "pendingMemberProcess.php",
                                        method: "POST",
                                        data: {
                                            operation: "accept",
                                            member: member,
                                            spaceid: "<?php echo $space->spaceID ?>",
                                            spacename: "<?php echo $space->name ?>"
                                        },
                                        success: function(response) {
                                            // On successful response, remove the list item from the DOM
                                            joinRequestsList.removeChild(listItem);
                                            console.log(response); // Optional: Display the response in the console
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(error); // Optional: Log any errors to the console
                                        }
                                    });
                                });
                            });

                            Array.from(rejectButtons).forEach(function(button) {
                                var listItem = button.parentNode.parentNode;
                                var member = listItem.querySelector('.member-email').textContent.trim();
                                button.addEventListener('click', function() {

                                    $.ajax({
                                        url: "pendingMemberProcess.php",
                                        method: "POST",
                                        data: {
                                            operation: "reject",
                                            member: member,
                                            spaceid: "<?php echo $space->spaceID ?>",
                                            spacename: "<?php echo $space->name ?>"
                                        },
                                        success: function(response) {
                                            // On successful response, remove the list item from the DOM
                                            joinRequestsList.removeChild(listItem);
                                            console.log(response); // Optional: Display the response in the console
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(error); // Optional: Log any errors to the console
                                        }
                                    });
                                });
                            });
                        });

                        overlay.addEventListener('click', function(event) {
                            if (event.target === overlay) {
                                overlay.style.display = 'none';
                            }
                        });

                        copyButton.addEventListener('click', function() {
                            invitationCode.select();
                            document.execCommand('copy');

                            copyButton.textContent = 'Copied!';
                            copyButton.disabled = true;
                        });
                    </script>
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

</html>