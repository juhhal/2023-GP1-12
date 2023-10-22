<!DOCTYPE html>
<html>
<?php session_start();
error_reporting(0);

?>

<head>
    <meta charset="UTF-8">
    <title>My Files</title>
    <link rel="stylesheet" href="theFiles.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">

    </script>
    <!-- SHOUQ SECTION: -->
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
                                <a href="#">Community</a>
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
                        <li id = "logout"><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out </a></li>
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
                <li class="tool_item"><a href="theFiles.php?q=My Files"><img src="images/file.png">
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
            <?php $_GET['q'] = "My Files" ?>
            <div class="workarea_item">
                <div id="container">
                    <div id="title-upload">
                        <h1><?php if (isset($_GET['q'])) echo $_GET['q']; ?></h1>
                        <?php if (isset($_GET['q'])) { ?>
                            <form enctype="multipart/form-data" id="uploadFORM">
                                <input type="file" name="file" id="file">
                                <label for="file">
                                    <h3>Upload File</h3>
                                    <img src="images/upload.png" />
                                </label>
                            </form>
                    </div>
                    <span id="formContanier">
                        <span class="uploadedfile">
                            <p></p>
                            <span id="loadingbar"></span>
                        </span>

                        <div id="allfiles">

                            <?php

                            if ($_GET['q'] == 'My Files') {

                                include('allfiles.php');
                            }
                            ?>
                        </div>
                    </span> <?php } ?>
                </div>
            </div>
        </div>
    </main>
    <footer>
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
<script src="jquery.js"></script>
<script>
    document.querySelector('#file').addEventListener('change', (e) => {
        document.querySelector('.uploadedfile').style.display = 'flex';
        //console.log(document.querySelector('input').value.split("\\"))
        document.querySelector('.uploadedfile p').innerText = document.querySelector('#file').value.split("\\")[document.querySelector('#file').value.split("\\").length - 1];
        e.preventDefault();
        let formdata = new FormData(document.querySelector('#uploadFORM'));
        document.querySelector('#loadingbar').style.width = '50%';
        $.ajax({
            url: 'upload.php',
            data: formdata,
            method: 'POST',
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res)
                document.querySelector('#loadingbar').style.width = '100%';
                document.querySelector('.uploadedfile').style.display = "none";
                $.ajax({
                    url: 'allfiles.php',
                    data: "",
                    method: 'POST',
                    success: function(res) {
                        document.querySelector('#allfiles').innerHTML = res
                        console.log(res)

                    }
                })
            }
        })
    })

    if (document.querySelectorAll('iframe') !== null) {
        document.querySelectorAll('iframe').forEach(e => {
            e.contentWindow.document.querySelector('html').style.overflow = 'hidden'
        });

        document.querySelectorAll('.three').forEach(e => {
            e.addEventListener('click', (event) => {
                console.log(document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display)
                if (document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display == 'flex')
                    document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display = 'none'
                else
                    document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display = 'flex'
            });
        });

        document.querySelectorAll('.deleteic').forEach(e => {
            e.addEventListener('click', (event) => {
                if (confirm('Are you sure you want to delete this file?')) {
                    $.ajax({
                        url: 'delete.php',
                        data: {
                            value: event.target.attributes['data-p'].value
                        },
                        method: 'POST',
                        success: function(res) {
                            console.log(res)
                            document.querySelector('#' + event.target.attributes['data-value'].value).style.display = 'none'

                        }
                    });
                }

            });
        });
    }
</script>

</html>