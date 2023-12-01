<?php session_start();

$guest_account = null;
//check if user is a guest to hide the profile menu
if (!isset($_SESSION['email'])) {
    $guest_account = true;
    $visibility = 'none';
} else {
    $guest_account = false;
    $visibility = 'block';
}

if (!isset($_GET['postID'])) {
    header("Location: community.php");
}
//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// MongoDB query
$filter = ['_id' => new MongoDB\BSON\ObjectId($_GET['postID'])];
$query = new MongoDB\Driver\Query($filter);

// MongoDB collection name
$collectionName = "Learniverse.community";

// Execute the query
$result = $manager->executeQuery($collectionName, $query);

// Store the matched document
$matchedDocument = null;
foreach ($result as $document) {
    $matchedDocument = $document;
    break;
}
$post = null;
if ($matchedDocument) {
    // Convert the matched document to an associative array
    $post = json_decode(json_encode($matchedDocument), true);
}
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Community</title>
    <link rel="stylesheet" href="viewPostCSS.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="searchCSS.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/crr1vwkewrlr1xvvlr90xyibpryt3v70vmn1i18wagfzh6as/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            window.location.href = 'addCommunityPost.php';
        }

        function editPost() {
            window.location.href = "addCommunityPost.php?postID=<?php echo $_GET['postID'] ?>";
        }

        function deletePost() {
            Swal.fire({
                title: 'Heads Up!',
                text: 'Are you sure you want to delete this post?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "deletePost.php?postID=<?php echo $_GET['postID'] ?>";
                }
            });
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

        window.addEventListener('unload', function(event) {
            <?php unset($_SESSION['filteredSearch']) ?>
        });
    </script>
</head>

<body>
    <!-- Place the following <script> and <textarea> tags in your HTML's <body> -->
    <script>
        tinymce.init({
            selector: '#comment',
            plugins: 'tinycomments mentions anchor autolink charmap codesample emoticons image link lists searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent',
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
                    <label for="search">Search community</label>
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
                <span id="clearSearch" title="Clear Search" onclick="location.reload();">Clear Search</span>
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
                $googleID = $fetch['google_user_id'];

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
        <li class="tool_item">
          Shared spaces
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
                <div id="topShelf">
                    <h1>Community</h1>
                    <div id="addPostBTN" class="addPostBTN tooltip" role='button' onclick="checkGuest(this.id)">Add Post
                    </div>
                </div>
                <!-- INSERT ITEMS HERE -->
                <?php
                // Prepare the query
                $filter = ['email' => $post['author']];
                $options = [
                    'projection' => ['firstname' => 1, 'lastname' => 1, 'username' => 1],
                    'limit' => 1
                ];
                $query = new MongoDB\Driver\Query($filter, $options);

                // Execute the query
                $cursor = $manager->executeQuery("Learniverse.users", $query);

                // Get the result
                $result = current($cursor->toArray());
                $first_name = "";
                $last_name = "";
                $username = "";
                $email = $post['author'];
                if ($result) {
                    $first_name = $result->firstname;
                    $last_name = $result->lastname;
                    $username = $result->username;
                };
                ?>
                <script>
                    var postId = '<?php echo $_GET['postID'] ?>';
                    var LIKEbutton = null;
                    var DISLIKEbutton = null;


                    // Function to handle the like action
                    function isLiked() {
                        <?php if ($guest_account)
                            echo "showAlert(\"Enjoying Your Experience?\", \"<a class='link' href='register.php'>Register</a> or <a class='link' href='login.php'>Login</a> to continue your journey!\"); return;
                          "
                        ?>
                        LIKEbutton = $('#likes');
                        DISLIKEbutton = $('#dislikes');
                        // Get the dislikeCount span element
                        var likeCountElement = document.getElementById("likeCount");
                        // Get the current value of dislikeCount as an integer
                        var currentCount = parseInt(likeCountElement.textContent);
                        $.ajax({
                            url: 'likePost.php',
                            type: 'POST',
                            data: {
                                postID: postId,
                                action: 'likes'
                            },
                            dataType: 'text',
                            success: function(response) {
                                console.log(response); // Process the response as needed
                                if (response === 'AUTHOR') {
                                    showAlert("OOPS!", "You cannot like your own post!");
                                } else if (response === 'LIKED' || response === "DISLIKED -> LIKED") {
                                    LIKEbutton.addClass('clicked');
                                    DISLIKEbutton.removeClass('clicked');

                                    // Update the value of the likeCount span
                                    likeCountElement.textContent = currentCount + 1;
                                    if (response === "DISLIKED -> LIKED") {
                                        //Update the reflected chenge from liking an already disliked post
                                        dislikeCount = document.getElementById("dislikeCount");
                                        dislikeCount.textContent = parseInt(document.getElementById("dislikeCount").textContent) - 1;
                                    }
                                } else {
                                    LIKEbutton.removeClass('clicked');
                                    // Update the value of the likeCount span
                                    likeCountElement.textContent = currentCount - 1;
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error: ' + xhr.status);
                            }
                        });
                    }

                    // Function to handle the dislike action
                    function isDisliked() {
                        <?php if ($guest_account) echo "
                            showAlert(\"Enjoying Your Experience?\", \"<a class='link' href='register.php'>Register</a> or <a class='link' href='login.php'>Login</a> to continue your journey!\"); return;
                            "
                        ?>
                        LIKEbutton = $('#likes');
                        DISLIKEbutton = $('#dislikes');
                        // Get the dislikeCount span element
                        var likeCountElement = document.getElementById("dislikeCount");
                        // Get the current value of dislikeCount as an integer
                        var currentCount = parseInt(likeCountElement.textContent);
                        $.ajax({
                            url: 'likePost.php',
                            type: 'POST',
                            data: {
                                postID: postId,
                                action: 'dislikes'
                            },
                            dataType: 'text',
                            success: function(response) {
                                console.log(response); // Process the response as needed
                                if (response === 'AUTHOR') {
                                    showAlert("OOPS!", "You cannot dislike your own post!");
                                } else if (response === 'DISLIKED' || response === "LIKED -> DISLIKED") {
                                    DISLIKEbutton.addClass('clicked');
                                    LIKEbutton.removeClass('clicked');

                                    // Update the value of the likeCount span
                                    likeCountElement.textContent = currentCount + 1;
                                    if (response === "LIKED -> DISLIKED") {
                                        //Update the reflected chenge from disliking an already liked post
                                        dislikeCount = document.getElementById("likeCount");
                                        dislikeCount.textContent = parseInt(document.getElementById("likeCount").textContent) - 1;
                                    }
                                } else {
                                    DISLIKEbutton.removeClass('clicked');
                                    // Update the value of the likeCount span
                                    likeCountElement.textContent = currentCount - 1;
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error: ' + xhr.status);
                            }
                        });
                    }

                    function reportPost() {
                        <?php if ($guest_account)
                            echo "showAlert(\"Enjoying Your Experience?\", \"<a class='link' href='register.php'>Register</a> or <a class='link' href='login.php'>Login</a> to continue your journey!\"); return;
                          "
                        ?>
                        Swal.fire({
                            title: 'Report a Post',
                            html: '<form id="reportPostForm" method="post" action="reportHandler.php">' +
                                '<p>Please provide a brief description of the problem:</p>' +
                                '<div id="characterCount" style="margin-right:10%; text-align: right">0/500</div>' +
                                '<textarea type="text" id="description" name="description" placeholder="A short description of the problem" required rows="5" maxlength="500" style="width: 80%; padding-bottom: 30%;"></textarea>' +
                                '<input type="hidden" id="reportedPost" name="reportedPost" value="' + postId + '">' +
                                '<input type="hidden" id="submissionDate" name="submissionDate" value="' + getCurrentDate() + '">' +
                                '<input type="hidden" id="reportedBy" name="reportedBy" value="' + <?php if (!$guest_account) echo "'" . $_SESSION["email"] . "'" ?> + '">' +
                                '</form>',
                            confirmButtonText: 'Submit',
                            cancelButtonText: 'Close',
                            showCloseButton: true,
                            preConfirm: () => {
                                const description = document.getElementById('description').value;
                                if (description.length > 500) {
                                    Swal.showValidationMessage('Character limit exceeded');
                                }
                                return {
                                    description: description
                                };
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formData = result.value;
                                const reportedPost = document.getElementById('reportedPost').value;
                                const submissionDate = document.getElementById('submissionDate').value;
                                const reportedBy = document.getElementById('reportedBy').value;
                                form = document.getElementById('reportPostForm');
                                form.submit();
                                // Send form data to reportHandling.php using AJAX or form submission
                                // Include the postId, submissionDate, and email in the form data
                                // Display a thank you message to the user
                                Swal.fire('Thank you!', 'Your report has been submitted.', 'success');
                            }
                        });
                        $(document).ready(function() {
                            document.getElementById('description').addEventListener('input', function() {
                                const description = this.value;
                                const characterCountElement = document.getElementById('characterCount');
                                characterCountElement.innerText = description.length + '/500';
                                if (description.length >= 500) {
                                    characterCountElement.style.color = 'red';
                                } else {
                                    characterCountElement.style.color = 'black';
                                }
                            });
                        })
                    }

                    function getCurrentDate() {
                        const currentDate = new Date();
                        const year = currentDate.getFullYear();
                        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                        const day = String(currentDate.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }
                </script>

                <div id='post'>
                    <span id="reportPost" onclick="reportPost()"><img src="images/warning.png"> Report </span>
                    <div class='postHead'>
                        <div class='postLikes'>
                            <div id='likes' onclick='isLiked()' <?php if (!$guest_account && in_array($_SESSION['email'], $post['likedBy'])) echo "class='clicked'" ?>><span id='likeCount'><?php echo count($post['likedBy']) ?></span> <img src='images/like.png'></div>
                            <div id='dislikes' onclick='isDisliked()' <?php if (!$guest_account && in_array($_SESSION['email'], $post['dislikedBy'])) echo "class='clicked'" ?>><span id='dislikeCount'><?php echo count($post['dislikedBy']) ?></span> <img src='images/dislike.png'></div>
                        </div>
                        <h3 id='postTitle'> <?php echo $post['title']; ?></h3>
                        <?php
                        if (!$guest_account && $_SESSION['email'] == $email) echo "<div id='edit-delete-post'><span id='editPost' onclick='editPost()'><img src='images/edit.png'></span><span id='deletePost' onclick='deletePost()'><img src='images/delete.jpeg'></span></div>";
                        echo "<div id='postInfo'><span>Posted By: $first_name $last_name </span><span id='username'> (@$username)</span><span> Posted At: " . $post['posted_at'] . "</span>";
                        if ($post['edited']) echo "<span id='isEdited'>Edited: " . $post['dateEdited'] . " </span>";
                        ?>
                    </div>
                </div>
                <p id='postContent'> <?php echo $post['content'] ?> </p>

                <script>
                    function refresh() {
                        header("Refresh: 0");
                    }

                    function editComment(comment, id) {
                        // Set the content of the textarea
                        tinymce.get('comment').setContent(comment);
                        tinymce.get('comment').focus();
                        $("#commentID").val(id);
                        var targetDiv = $("#commentArea"); //scroll to
                        var offsetTop = targetDiv.offset().top;
                        $("html, body").animate({
                            scrollTop: offsetTop
                        }, 500);
                    }

                    function DeleteComment(commentId, postId) {
                        Swal.fire({
                            title: 'Heads Up!',
                            text: 'Are you sure you want to delete this comment?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                        }).then((result) => {
                            if (result.isConfirmed) { // Check if the user clicked the "Yes, delete it!" button
                                var commentid = commentId;
                                var postid = postId;
                                window.location.href = "deleteComment.php?commentID=" + commentid + "&postID=" + postid;
                            }
                        });
                    }

                    function showAllComments(post_id, limit, offset) {
                        $.ajax({
                            url: 'view_more_comments.php',
                            type: 'GET',
                            data: {
                                postID: post_id,
                                limit: limit,
                                offset: offset
                            },
                            success: function(response) {
                                var comments = JSON.parse(response); // Parse the JSON response
                                $('#ShowmoreButton').remove(); // Remove the "Show Comments" button
                                $('#Showcomments').append(comments); // Append the parsed HTML string
                            },
                            error: function() {
                                console.log('Error occurred while fetching comments.');
                            }
                        });
                    }

                    function CommentcheckGuest() {
                        <?php
                        // $guest_account is a PHP variable that determines if the user is a guest or not
                        if ($guest_account) {
                            echo "showAlert(\"Enjoying Your Experience?\", \"<a class='link' href='register.php'>Register</a> or <a class='link' href='login.php'>Login</a> to continue your journey!\");";
                            echo "return false;"; // Return false to prevent form submission
                        }
                        ?>
                        // If the user is not a guest, allow the form submission
                        return true;
                    }
                </script>
                <?php
                $tags = "";
                if (count($post['tags']) == 0)
                    $tags = "<span>none</span>";
                else
                    foreach ($post['tags'] as $t) {
                        $tags = $tags . "<span class='postTag' onclick=\"window.location='searchCommunity.php?searchTerm=[$t]'\">$t</span>";
                    };
                echo "<div class='postTags'>tags: $tags</div></div>";

                //SHOW COMMENTS SECTION

                function getComments($post_id, $limit, $offset)
                {
                    global $manager;

                    $options = [
                        'limit' => $limit,
                        'skip' => $offset,
                    ];

                    $query = new MongoDB\Driver\Query(['post_id' => $_GET['postID']], $options);

                    $comments = $manager->executeQuery('Learniverse.comments', $query);

                    return $comments;
                }

                $filter = ['post_id' => $_GET['postID']];
                $query = new MongoDB\Driver\Query($filter);

                // Execute the query
                $cursor = $manager->executeQuery("Learniverse.comments", $query);

                // Get the result
                $result = $cursor->toArray();
                $numberOfComments = count($result);
                echo "<br><h2 id='NoOfComment'>" . $numberOfComments . " Comments</h2><div id='Showcomments'>";

                $post_id = $_GET['postID'];
                $limit = 3;
                $offset = 0;

                // Fetch comments
                $comments = getComments($post_id, $limit, $offset);

                // Display comments
                foreach ($comments as $oneComment) {
                    $commenter_firstname = "";
                    $commenter_lastname = "";
                    $commenter_username = "";
                    $comment_Date = "";
                    $comment = "";
                    $commentId = "";
                    $commenter_email = "";
                    $edited_date = "";
                    if ($oneComment) {
                        $commenter_firstname = $oneComment->firstname;
                        $commenter_lastname = $oneComment->lastname;
                        $commenter_username = $oneComment->username;
                        $comment_Date = $oneComment->commented_at;
                        $comment = $oneComment->comment;
                        $commentId = $oneComment->_id;
                        $commenter_email = $oneComment->email;
                        $edited_date = $oneComment->edited_at;
                    };

                    echo "<div class='oneCommnet'><p class='commentContent'>" . $comment . "</p>";
                    echo "<span class='commentInfo'>
                    By: " . $commenter_firstname . " " . " $commenter_lastname (@" . " $commenter_username) </span><br><span class = 'commentdate'>";
                    if ($edited_date != "") echo "Edited At " . $edited_date;
                    else echo "At " . $comment_Date;
                    echo "</span><br>";
                    if (!$guest_account && $commenter_email == $_SESSION['email'])
                    echo "<span class='editComment'><img src='images/edit.png' alt='edit' width='20px' height='20px' onclick='editComment(\"" . $comment . "\", \"" . $commentId . "\");'></span><span class='deleteComment'><img src='images/bin.png' alt='bin' width='20px' height='20px' onclick='DeleteComment(\"" . $commentId . "\", \"" . $_GET['postID'] . "\");'></span>";
                    echo "<br></div>";
                }

                // Calculate the next offset
                $next_offset = $offset + $limit;

                // Check if there are more comments
                $filter = ['post_id' => $post_id];
                $options = ['limit' => 1, 'skip' => $next_offset];
                $countQuery = new MongoDB\Driver\Query($filter, $options);
                $countCursor = $manager->executeQuery('Learniverse.comments', $countQuery);
                $count = iterator_count($countCursor);

                if ($count > 0) {
                    // Display "Show More" link
                    echo '<button id ="ShowmoreButton" onclick="showAllComments(\'' . $post_id . '\', ' . $limit . ', ' . $next_offset . ');">Show More</button>';
                }
                echo "</div>";

                //ADD COMMENT SECTION
                echo "<div id='commentArea'>
                <h2>Your comment:</h2><br>
                <form id='addcomment' method='post' action='addcomment.php' onsubmit='return CommentcheckGuest()';>
                    <textarea cols='50' id='comment' name='comment' placeholder='Write your comment here'></textarea>
                    <input id='id_post' name='id_post' hidden value='" . $_GET['postID'] . "'>
                    <input id='commentID' name='commentID' hidden value=''>    
                    <button id='submitComment' type='submit'>Submit</button>
                </form>
                </div>";
                ?>
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