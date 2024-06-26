<?php
session_start();

//check if user is a guest to hide the profile menu
if (!isset($_SESSION['email'])) {
    $guest_account = true;
    $visibility = 'none';
} else {
    $guest_account = false;
    $visibility = 'block';
}
//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// MongoDB query
$query = new MongoDB\Driver\Query([], ['sort' => ['posted_at' => -1]]); // Sort by 'posted_at' in descending order
// MongoDB collection name
$collectionName = "Learniverse.community";

// Execute the query
$result = $manager->executeQuery($collectionName, $query);

// Fetch all documents
$posts = [];
if (isset($_SESSION['filteredSearch'])) {
    $posts = json_decode($_SESSION['filteredSearch'], true);
} else {
    foreach ($result as $document) {
        $posts[] = $document;
    }
    $posts = json_decode(json_encode($posts), true);
}
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Community</title>
    <link rel="stylesheet" href="communityCSS.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="searchCSS.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CUSTOMER SUPPORT STYLESHEET -->
    <script src="../customerSupport.js"></script>
    <link rel="stylesheet" href="../customerSupport.css">
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

        function checkGuest() {
            <?php
            // $guest_account is a PHP variable that determines if the user is a guest or not
            if ($guest_account) {
                echo "document.querySelector('.addPostBTN').classList.add('guestCursor');"; // If user is a guest, add the guestCursor class to the div

                echo "showAlert(\"Enjoying Your Experience?\", \"<a class='link' href='register.php'>Register</a> or <a class='link' href='login.php'>Login</a> to continue your journey!\"); return;";

                echo "return;"; // Return without performing any action
            }
            ?>
            // If user is not a guest, relocate to add post page
            window.location.href = 'addCommunityPost.php?q=community.php';
        }

        window.addEventListener('unload', function(event) {
            <?php unset($_SESSION['filteredSearch']) ?>
        });

        function suggestTitles(searchTerm) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var suggestions = JSON.parse(this.responseText);
                    var autocompleteList = document.getElementById("autocomplete-list");
                    autocompleteList.innerHTML = ""; // Clear previous suggestions
                    for (var i = 0; i < suggestions.length; i++) {
                        var suggestion = suggestions[i];
                        var option = document.createElement("option");
                        option.innerText = suggestion;
                        option.title = suggestion;
                        option.onclick = function() {
                            document.getElementById("search").value = this.innerText;
                            document.getElementById("searchCommunity").submit();
                        };
                        autocompleteList.appendChild(option);
                    }
                }
            };
            xmlhttp.open("GET", "autosuggest.php?searchTerm=" + searchTerm, true);
            xmlhttp.send();
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
                            <li class="active">
                                <a href="community.php">Community</a>
                            </li>
                            <li>
                                <a href="workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <form id="searchCommunity" action="searchCommunity.php" method="get" role="search">
                    <label for="search">Search Community</label>
                    <div class="tooltip">
                        <input id="search" name="searchTerm" type="search" placeholder='<?php if (isset($_GET['searchTerm'])) echo $_GET['searchTerm'];
                                                                                        else echo "Search..."; ?>' autocomplete="off" onkeyup="suggestTitles(this.value)" />
                        <button type="submit">Go</button>
                        <div class="tooltiptext">
                            <b>Quantum Theory</b> <i>search for titles containing Quantum Theory</i><br>
                            <b>[Quantum Theory]</b> <i>search within Quantum Theory tag</i><br>
                            <b>user:Sara</b> <i>search posts by username Sara</i><br>
                            <b>likes:3</b> <i>search posts with 3+ likes</i> <br>
                        </div>
                    </div>
                </form>
                <span id="clearSearch" title="Clear Search" onclick="location.reload();">Clear Search</span>
                <datalist id="autocomplete-list"></datalist>
                <?php
                require_once __DIR__ . '/vendor/autoload.php';
                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;
                if ($guest_account) {
                    $data = array(
                        "email" => "anonymous_____anonymous"
                    );
                } else {
                    $data = array(
                        "email" => $_SESSION['email']
                    );
                }
                $fetch = $Usercollection->findOne($data);
                //$googleID = $fetch['google_user_id'];

                ?>
                <div class="dropdown" style="display: <?php echo $visibility ?>">
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
                        <li><a href='reset.php?q=community.php'><i class='far fa-edit'></i> Change password</a></li>
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
                <div id="topShelf">
                    <h1>Community</h1>
                    <!-- Here, you can connect with a diverse community of like-minded learners, 
                    tap into collective wisdom, and exponentially enhance your knowledge at -->
                    <div class="addPostBTN tooltip" role='button' onclick="checkGuest()">Add Post
                    </div>
                </div>
                <!-- INSERT ITEMS HERE -->
                <?php
                $i = 0;
                if (count($posts) < 1) {
                    echo "<div class='emptySearch post'> No posts found..</div>
                    ";
                }
                foreach ($posts as $post) {
                    // Prepare the query
                    $filter = ['email' => $post['author']];
                    $options = [
                        'projection' => ['firstname' => 1, 'lastname' => 1],
                        'limit' => 1
                    ];
                    $query = new MongoDB\Driver\Query($filter, $options);

                    // Execute the query
                    $cursor = $manager->executeQuery("Learniverse.users", $query);

                    // Get the result
                    $result = current($cursor->toArray());
                    $first_name = "";
                    $last_name = "";
                    if ($result) {
                        $first_name = $result->firstname;
                        $last_name = $result->lastname;
                    };
                    $tags = "";
                    if (count($post['tags']) == 0)
                        $tags = "<br>";
                    else
                        foreach ($post['tags'] as $t) {
                            $tags = $tags . "<span class='postTag' onclick=\"window.location='searchCommunity.php?searchTerm=[$t]'\">$t</span>";
                        };
                    echo "
                    <div class='post'>
                    <span class='postTitle'><a href='viewPost.php?postID=" . $post['_id']['$oid'] . "'>" . $post['title'] . "</a></span><span class='postAuthor'>By: $first_name $last_name</span>
                    <div class='postTags'>$tags</div>
                    <div class='postRating'><img src='images/like.png'>" . $post['likes'] . "<img src='images/dislike.png'>" . $post['dislikes'] . "<img src='images/comment.png'>" . $post['comments'] . "<span class='postedDate'>Posted: " . $post['posted_at'] . "</span></div>
                    </div>
                    ";
                }
                ?>

                <!-- ADD POST FIELD
                <textarea id="post_area" placeholder="Write Your Post Here:"></textarea> -->

            </div>
        </div>
    </main>
    <footer id="footer" style="margin-top: 7%;">

        <div id="copyright">Learniverse &copy; 2024</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>
