<?php
require "session.php";

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Community</title>
    <link rel="stylesheet" href="viewPostCSS.css">
    <link rel="stylesheet" href="header-footer.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- rich text editor -->
    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/crr1vwkewrlr1xvvlr90xyibpryt3v70vmn1i18wagfzh6as/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- SHOUQ SECTION: -->
    <script>
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
    </script>
</head>

<body>
    <!-- Place the following <script> and <textarea> tags in your HTML's <body> -->
    <script>
        tinymce.init({
            selector: '#post_area',
            plugins: 'tinycomments mentions anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [{
                    value: 'First.Name',
                    title: 'First Name'
                },
                {
                    value: 'Email',
                    title: 'Email'
                },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    </script>

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
                            <li class="active">
                                <a href="community.php">Community</a>
                            </li>
                            <li>
                                <a href="Workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <form id="searchCommunity" action="searchCommunity.php" method="get" role="search">
                    <label for="search">Search Community</label>
                    <div class="tooltip">
                        <input id="search" name="searchTerm" type="search" placeholder='<?php if (isset($_GET['searchTerm'])) echo $_GET['searchTerm'];
                                                                                        else echo "Search..."; ?>' autocomplete="off" />
                        <button type="submit">Go</button>
                        <div class="tooltiptext">
                            <b>Quantum Theory</b> <i>search for titles containing Quantum Theory</i><br>
                            <b>[Quantum Theory]</b> <i>search within Quantum Theory tag</i><br>
                            <b>user:Sara</b> <i>search posts by username Sara</i><br>
                            <b>likes:3</b> <i>search posts with 3+ likes</i> <br>
                        </div>
                    </div>
                </form>

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
                $googleID = $fetch['google_user_id'];

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

                        <?php if ($googleID === null) {
                            echo "<li><a href='reset.php?q=workspace.php'><i class='far fa-edit'></i> Change password</a></li>";
                        } ?>

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
                        Calendar & To-Do </a></li>
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
                <li class="tool_item"><a href="pomodoro.php"><img src="images/pomodoro-technique.png">
                    Pomodoro</a>
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
                <li class="tool_item"><a href="community.php"><img src="images/communities.png">
                        Community</a>
                </li>
            </ul>
        </div>

        <div class="workarea">

            <div class="workarea_item">
                <div id="topShelf">
                    <h1>Community</h1>
                    <!-- <span class="addPostBTN" role='button' onclick="window.location='addCommunityPost.php'">Add Post</span> -->
                </div>
                <!-- INSERT ITEMS HERE -->
                <script>
                    $("#addPostForm").submit(function(event) {
                            // Create a new Date object
                            var currentDate = new Date();

                            // Extract the year, month, day, hours, and minutes from the current date
                            var year = currentDate.getFullYear();
                            var month = String(currentDate.getMonth() + 1).padStart(2, '0');
                            var day = String(currentDate.getDate()).padStart(2, '0');
                            var hours = String(currentDate.getHours()).padStart(2, '0');
                            var minutes = String(currentDate.getMinutes()).padStart(2, '0');

                            // Format the date string
                            var formattedDate = year + '-' + month + '-' + day + ' at ' + hours + ':' + minutes;
                            $("#postDate").val(formattedDate);
                        });
                </script>
                <form id="addPostForm" method="post" action="addPost.php">
                    <?php

                    use MongoDB\BSON\ObjectID;

                    $title = "";
                    $content = "";
                    $tags = "";
                    if (isset($_GET['postID'])) {
                        $filter = ["_id" => new ObjectID($_GET['postID'])];
                        // Construct the query
                        $query = new MongoDB\Driver\Query($filter);
                        // Execute the query
                        $cursor = $manager->executeQuery("Learniverse.community", $query);
                        // Get the result
                        $result = current($cursor->toArray());
                        if ($result) {
                            $title = $result->title;
                            $content = $result->content;
                            $tags = $result->tags;
                        }
                        // echo "<script>alert('" . $title . "');</script>";
                    }
                    ?>
                    <script>
                        // // Get the formatted text from your database
                        // formattedText = "<?php //print ($content)
                                            ?>";

                        // // Set the content of the editor
                        // tinymce.activeEditor.setContent(formattedText);
                    </script>
                    <label for="postTitle">
                        <h3>Title</h3>
                    </label>
                    <textarea required autofocus id="postTitle" name="postTitle" placeholder="Your Post's Title"><?php echo ($title) ?></textarea>
                    <!-- ADD POST FIELD -->
                    <label for="post_area">
                        <h3>Post Content</h3>
                    </label>
                    <textarea id="post_area" name='post_content' placeholder="Compose Your Post Here:"><?php echo ($content) ?></textarea>
                    <label for="postTags">
                        <h3>Tags</h3>
                    </label>
                    <textarea id="postTags" name="postTags" placeholder="e.g. science, chemistry, atoms"><?php if(is_array($tags))echo implode(", ", $tags); else echo $tags; ?></textarea>
                    <input type="text" id="postDate" name='postDate' hidden>
                    <?php
                    if (isset($_GET['postID']))
                        echo "<input type='hidden' id='postID' name='postID' value='".$_GET['postID']."' hidden>"
                    ?>
                    <input type="submit">
                </form>
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