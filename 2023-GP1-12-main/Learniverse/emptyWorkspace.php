<!DOCTYPE html>


<head>
    <meta charset="UTF-8">
    <title>EMPTY</title>
    <link rel="stylesheet" href="emptyWorkspace.css">
    <link rel="stylesheet" href="header-footer.css">

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


            // PROFILE DROPDOWN MENU
            var dropdownButton = document.querySelector('.dropdown-button');
            var dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownButton.addEventListener('click', function() {
                dropdownMenu.marginRight = 'auto';
                dropdownMenu.classList.toggle('show');
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
                                <a href="index.html">Home</a>
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
                require_once __DIR__ . '/vendor/autoload.php';

                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
                $_SESSION['email'] = "emptyuser@email.com"; //DELETE THIS LINE WHEN USING TEMPLATE
                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;

                $data = array(
                    "email" => $_SESSION['email']
                );

                $fetch = $Usercollection->findOne($data);
                ?>
                <div class="dropdown">
                    <button class="dropdown-button"><img src='LOGO.png'> <?php echo $fetch['firstname']; ?></button>
                    <ul class="dropdown-menu">
                        <li class='name not-hover'><i class="fas fa-user"></i> <?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></li>
                        <li class='hr not-hover'><?php echo $fetch['email']; ?></li>
                        <li><i class='far fa-edit'></i> <a href='edit_profile.php?dd=<?php echo sha1($fetch['_id']); ?>'>Edit Profile</a></li>
                        <li class='hr'><i class='far fa-question-circle'></i> <a href='#'>Help</a></li>
                        <li><i class='fas fa-sign-out-alt'></i> <a href='logout.php'>Sign out</a></li>
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
                <li class="tool_item"><a href="myFiles.php?q=My Files"><img src="images/file.png">
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
                <!-- INSERT ITEMS HERE -->
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

</html>