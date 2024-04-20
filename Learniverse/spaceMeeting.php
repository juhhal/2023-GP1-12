<?php

use MongoDB\Driver\Manager;

require 'session.php';
header('Permissions-Policy: compute-pressure=*');
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Meeting Room</title>
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="RoomCSS.css">
    <link rel="stylesheet" href="invite.css">


    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="jquery.js"></script>
    <style>
        #meetingIframe {
            top: 0;
            left: 0;
            width: 100%;
            height: 80vh;
            border: none;
        }
    </style>
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
        // document.addEventListener("DOMContentLoaded", function() {
        //     const apiKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmFwcGVhci5pbiIsImF1ZCI6Imh0dHBzOi8vYXBpLmFwcGVhci5pbi92MSIsImV4cCI6OTAwNzE5OTI1NDc0MDk5MSwiaWF0IjoxNzEwODU1NzA1LCJvcmdhbml6YXRpb25JZCI6MjE5NDI0LCJqdGkiOiI3NzE0ZTdiMS1iNWY5LTQ2NDAtODZmMS02ZjY1ZjUzN2RmNzMifQ.7OG0tFfvhPGxkLbUFmJ_VZSb-K0t9NY5-Ds0Se8jI5o";
        //     const roomId = '<?php //echo $_GET['room']; 
                                ?>';
        //     const isHost = <?php //echo $_GET['host']; 
                                ?>;
        //     const url = `https://shouqtesting.whereby.dev/v1/meetings/${roomId}`;
        //     const headers = {
        //         'Authorization': 'Bearer ' + apiKey,
        //         'Content-Type': 'application/json'
        //     };

        //     fetch(url, {
        //             method: 'GET',
        //             headers: headers
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             var iframe = document.getElementById("meetingIframe");
        //             if (isHost) {
        //                 iframe.src = data.hostRoomUrl;
        //             } else {
        //                 iframe.src = data.roomUrl;
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //         });
        // });
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
                    <h1><?php echo $_GET['space'] ?> Meeting Room</h1>
                    <?php if ($_GET['host'] === 'true') { ?>
                        <button id="sendInvitation"><i class="fa fa-send"></i> Invite</button>
                    <?php } ?>
                    <div class="overlay" id="inviteOverlay">
                        <div class="modal" id="inviteModal">
                            <p class="guideline">Send the link via Email to invite your friends to the meeting!</p>
                            <input type="text" id="searchInput" placeholder="Search user names or Type email"><button id="sendEmailButton">Send Email</button>
                            <ul id="searchResults"></ul>

                            <h3>OR</h3>

                            <p class="guideline">Copy and send the link to invite your friends to the meeting!</p>
                            <input type="text" id="invitationCode" value="<?php echo $_GET['invite'] ?>" readonly><button id="copyButton" onclick="copyInvitationCode()"><i class="fa-solid fa-link"></i> Copy</button>
                        </div>
                    </div>
                </div>

                <div id="iframeContainer">
                    <iframe id="meetingIframe" src="<?php echo $_GET['room'] ?>" allow='camera; microphone; fullscreen; speaker; display-capture; screen-wake-lock; compute-pressure' frameborder="0"></iframe>
                    <?php

                    use PHPMailer\PHPMailer\PHPMailer;

                    $manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
                    if (isset($_GET['spaceID'])) {
                        //send alert email of alert to members
                        $query = new MongoDB\Driver\Query(['spaceID' => $_GET['spaceID']]);
                        $cursor = $manager->executeQuery("Learniverse.sharedSpace", $query);
                        $space = $cursor->toArray()[0];
                        $spacename = $space->name;
                        foreach ($space->members as $member) {
                            $query = new MongoDB\Driver\Query(['email' => $member->email]);
                            $cursor = $manager->executeQuery("Learniverse.users", $query);
                            $data = $cursor->toArray()[0];
                            $firstname = $data->firstname;
                            $lastname = $data->lastname;
                            $smtpUsername = 'Learniverse.website@gmail.com';
                            $smtpPassword = 'hnrl utwf fxup rnyd';
                            $smtpHost = 'smtp.gmail.com';
                            $smtpPort = 587;

                            // Create a new PHPMailer instance
                            $mail = new PHPMailer;

                            // Enable SMTP debugging
                            $mail->SMTPDebug = 0;

                            // Set the SMTP settings
                            $mail->isSMTP();
                            $mail->Host = $smtpHost;
                            $mail->Port = $smtpPort;
                            $mail->SMTPSecure = 'tls';
                            $mail->SMTPAuth = true;
                            $mail->Username = $smtpUsername;
                            $mail->Password = $smtpPassword;

                            // Set the email content
                            $mail->setFrom('Learniverse.website@gmail.com');
                            $mail->addAddress($member->email);
                            $mail->Subject = 'A Meeting is in Session.';
                            $mail->Body = "Dear " . $firstname . " " . $lastname . ",\n\nA meeting session has started in space: $spacename. Dont miss out! \n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

                            // Send the email
                            if ($mail->send()) {
                            } else {
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
    <?php
    $client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
    $database = $client->selectDatabase('Learniverse');

    function findUserEmail($email)
    {
        global $database;
        $usersCollection = $database->selectCollection('users');
        $userDocument = $usersCollection->find(['email' => $email]);
        foreach ($userDocument as $user) {
            $firstname = $user->firstname;
            $lastname = $user->lastname;

            return "$firstname $lastname";
        }
    }

    function findUsername($email)
    {
        global $database;
        $usersCollection = $database->selectCollection('users');
        $userDocument = $usersCollection->find(['email' => $email]);
        foreach ($userDocument as $user) {
            return "$user->username";
        }
    }

    $usersCollection = $database->selectCollection('sharedSpace');
    $currentUser = $_SESSION['email'];
    $users = [];
    // Query the database for the user
    //get members under the current user spaces
    $userDocuments = $usersCollection->find(['admin' => $currentUser]);
    foreach ($userDocuments as $document) {
        foreach ($document->members as $member) {
            $users[] = $member->email;
        }
    }

    $userDocuments = $usersCollection->find(['members.email' => $currentUser]);
    foreach ($userDocuments as $document) {
        if ($document->admin != $currentUser)
            $users[] = $document->admin;

        foreach ($document->members as $member)
            if ($member->email != $currentUser) {
                $users[] = $member->email;
            }
    }

    $unqiueUsers = array_unique($users, SORT_REGULAR);
    $users = [];
    foreach ($unqiueUsers as $user)
        $users[] = [
            'email' => $user,
            'name' => findUserEmail($user),
            'username' => findUsername($user)
        ];

    $usersJSON = json_encode($users);
    if ($_GET['host'] === 'true') {
    ?>
        <script>
            $(document).ready(function() {

                // Sample user data with names and email addresses
                var users = <?php echo ($usersJSON) ?>;
                var overlay = document.getElementById('inviteOverlay');
                // Event handler for opening the overlay
                document.getElementById('sendInvitation').addEventListener('click', function() {
                    overlay.style.display = 'flex';
                });

                // Event handler for closing the overlay
                overlay.addEventListener('click', function(event) {
                    if (event.target === this) {
                        $(this).css('display', 'none');
                    }
                });

                // Selected user names
                var selectedUsersEmails = [];

                // Function to perform the search
                function performSearch(query) {
                    var results = [];
                    if (query) {
                        var regex = new RegExp(query, 'i');
                        results = users.filter(function(user) {
                            return regex.test(user.name);
                        });
                    } else {
                        results = users;
                    }
                    displayResults(results);

                    // Clear the search results if query is empty
                    if (!query) {
                        $('#searchResults').empty();
                    }
                }

                // Function to display the search results
                function displayResults(results) {
                    var searchResultsList = $('#searchResults');
                    searchResultsList.empty();

                    if (results.length > 0) {
                        for (var i = 0; i < results.length; i++) {
                            var user = results[i];
                            var listItem = $('<li>')
                                .text(user.name + " ( @" + user.username + " )")
                                .attr('data-email', user.email)
                                .click(function() {
                                    $(this).toggleClass('selected');
                                });
                            searchResultsList.append(listItem);
                        }
                    } else {
                        var listItem = $('<li>').text('No results found');
                        searchResultsList.append(listItem);
                    }
                }

                // Event handler for search input focus
                $('#searchInput').on('focus', function() {
                    performSearch('');
                });

                // Event handler for search input
                $('#searchInput').on('input', function() {
                    var query = $(this).val();
                    performSearch(query);
                });


                // Event handler for sending email
                $('#sendEmailButton').on('click', function() {
                    selectedUsersEmails = [];
                    $('#searchResults li.selected').each(function() {
                        var userEmail = $(this).attr('data-email');
                        selectedUsersEmails.push(userEmail);
                    });

                    if (selectedUsersEmails.length > 0) {
                        sendEmails(selectedUsersEmails);
                    } else {
                        // No selected users, check if email entered
                        var enteredEmail = $('#searchInput').val();
                        if (validateEmail(enteredEmail)) {
                            sendEmail(enteredEmail);
                        }
                    }
                });

                // Function to validate email format
                function validateEmail(email) {
                    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return regex.test(email);
                }

                // Function to send emails to selected users
                function sendEmails(userEmails) {
                    var selectedUsers = users.filter(function(user) {
                        return userEmails.includes(user.email);
                    });
                    selectedUsers = JSON.stringify(selectedUsers);

                    $.ajax({
                        url: 'send_invitation.php',
                        method: 'POST',
                        data: {
                            meetingLink: '<?php echo $_GET['invite'] ?>',
                            selectedUsers: selectedUsers
                        },
                        success: function(response) {
                            console.log('Email sent successfully');
                            console.log(response);
                            document.getElementById("searchResults").insertAdjacentHTML("afterend", "<p style='color:green'>Emails sent successfully.</p>");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Failed to send email');
                            console.log(textStatus + errorThrown);
                        }
                    });
                }

                // Function to send email to entered email
                function sendEmail(email) {
                    $.ajax({
                        url: 'send_invitation.php',
                        method: 'POST',
                        data: {
                            meetingLink: '<?php echo $_GET['invite'] ?>',
                            enteredEmail: email
                        },
                        success: function(response) {
                            console.log('Email sent successfully');
                            console.log(response);
                            document.getElementById("searchResults").insertAdjacentHTML("afterend", "<p style='color:green'>Email sent successfully.</p>");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('Failed to send email');
                            console.log(textStatus + errorThrown);
                        }
                    });
                }

                // Function to copy the invitation code
                function copyInvitationCode() {
                    var invitationCode = document.getElementById('invitationCode');
                    invitationCode.select();
                    invitationCode.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    // Update button text after copying
                    var copyButton = document.getElementById('copyButton');
                    copyButton.textContent = 'Copied!';
                    copyButton.disabled = true;
                }

                // Event handler for copying the invitation code
                $('#copyButton').on('click', function() {
                    copyInvitationCode();
                });
            });
        </script>
    <?php } ?>
    <footer id="footer" style="margin-top: 7%;">

        <div id="copyright">Learniverse &copy; 2024</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>