<!DOCTYPE html>
<html>
<?php 
session_start();



?>

<head>
    <meta charset="UTF-8">
    <title>My Files</title>
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="js/sweetalert2.all.min.js"></script>
    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="theFiles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                                <a href="community.php">Community</a>
                            </li>
                            <li class="active">
                                <a href="workspace.php">My Workspace</a>
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
                //$googleID = $fetch->google_user_id;

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
                        <li><a href='reset.php?q=theFiles.php'><i class='far fa-edit'></i> Change password</a></li>
                        <li><a href='#'><i class='far fa-question-circle'></i> Help</a></li>
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


            <?php $_GET['q'] = "My Files" ?>
            <div class="workarea_item">
                <div id="container">
                    <div id="title-upload">
                        <h1><?php if (isset($_GET['q'])) echo $_GET['q']; ?></h1>
                        <?php if (isset($_GET['q'])) { ?>
                            <form enctype="multipart/form-data" id="uploadFORM">
                            <input type="file" name="file" id="file" accept="application/pdf">
                                <label for="file">
                                    <h3>Upload File (PDF only)</h3>
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
//GETTING PATH
// Connect to MongoDB
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('users');

// Get the email from the session
$email = $_SESSION['email'];

// Query the database for the user
$userDocument = $usersCollection->findOne(['email' => $email]);

// If user found, retrieve the _id
$user_id = null;
if ($userDocument) {
    $user_id = $userDocument->_id;
}

// directory path with user ID
$userDirectory = "user_files".$DIRECTORY_SEPARATOR."{$user_id}";




// IF DIR DOESN'T EXIST INITIALIZE
if (!is_dir($userDirectory)) {
    // Create the main user directory with permissions 0755 (or any desired permissions)
    if (!mkdir($userDirectory, 0755, true)) {
        die("Failed to create user directory.");
    }

    // Initialize subdirectories
    $subDirectories = ['Uploaded Files', 'Shared Spaces', 'Summaries', 'Flashcards', 'Quizzes'];
    foreach ($subDirectories as $subDir) {
        $subDirPath = "{$userDirectory}".$DIRECTORY_SEPARATOR."{$subDir}";
        // Check if the subdirectory already exists
        if (!is_dir($subDirPath)) {
            // Create the subdirectory with permissions 0755 (or any desired permissions)
            if (!mkdir($subDirPath, 0755, true)) {
                die("Failed to create {$subDir} directory.");
            }
        }
    }
}

//READ DIRECTORY
$folders = scandir($userDirectory);
?>

<!-- DISPLAY FILES -->
<ul id="folder-list">
    <?php 
    // Assume $folders is an array of folder names

    // Name of the uploaded folder
    $uploadedFolder = 'Shared Spaces';

    // Make sure the uploaded folder is in the array and remove it
    if(($key = array_search($uploadedFolder, $folders)) !== false) {
        unset($folders[$key]);
    }

    // Prepend the uploaded folder to the beginning of the array
    array_unshift($folders, $uploadedFolder);

    // Name of the uploaded folder
    $uploadedFolder = 'Uploaded Files';

    // Make sure the uploaded folder is in the array and remove it
    if(($key = array_search($uploadedFolder, $folders)) !== false) {
        unset($folders[$key]);
    }

    // Prepend the uploaded folder to the beginning of the array
    array_unshift($folders, $uploadedFolder);

    foreach ($folders as $folder):
        // Use a condition to exclude '.', '..', and '.DS_Store'
        if ($folder !== '.' && $folder !== '..' && $folder !== '.DS_Store'):
    ?>
        <li class="folder <?php echo ($folder === 'Uploaded Files') ? 'selected' : ''; ?>" data-folder="<?php echo htmlspecialchars($folder); ?>">
            <?php echo htmlspecialchars($folder); ?>
        </li>
    <?php 
        endif;
    endforeach; 
    ?>
</ul>




<div id="file-list"></div>

<script>
var userDirectory = "<?php echo $userDirectory; ?>";

// JavaScript
$(document).ready(function(){
    var userDirectory = "<?php echo $userDirectory; ?>";
    var folderName ='Uploaded Files';    //make by session
        // AJAX request to fetch files in the clicked folder
        $.ajax({
    url: 'getFiles.php', // PHP script to fetch files
    type: 'POST',
    data: { folder: folderName, userDirectory: userDirectory },
    success: function (response) {
        if (response.length === 0) {
            console.log('in');
    // Response is empty
    $('#file-list').html('<p>No files found.</p>').show();
} else {
    console.log(response.length);

    // Response is not empty
    $('#file-list').html(response).show();
}

    }
});


    // Attach click event to folders
    $('.folder').click(function(){
        // Remove 'selected' class from all folders
        $('.folder').removeClass('selected');
        
        // Add 'selected' class to the clicked folder
        $(this).addClass('selected');
        
        var folderName = $(this).data('folder');
        // AJAX request to fetch files in the clicked folder
        $.ajax({
            url: 'getFiles.php', // PHP script to fetch files
            type: 'POST',
            data: {folder: folderName, userDirectory: userDirectory},
            success: function(response){
                // Hide folder list
                // $('#folder-list').hide();
                // Show file list and populate with response
                if (response.length === 95) {
            console.log('in');
    // Response is empty
    $('#file-list').html('<p>No files found.</p>').show();
} else {
    console.log(response.length);

    // Response is not empty
    $('#file-list').html(response).show();
}            }
        });
    });
});





$(document).ready(function(){
    $('#file').change(function(){
        var user_id = '<?php echo $user_id; ?>'; // Get the user ID
        var formData = new FormData();
        formData.append('file', $(this)[0].files[0]);
        formData.append('folder', 'uploaded'); // Specify destination folder
        formData.append('user_id', user_id); // Include user ID
        
        $.ajax({
            url: 'uploadFile.php', // PHP script to handle file upload
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                // Handle success response
                console.log(response);
                location.reload();
            },
            error: function(xhr, status, error){
                // Handle error
                console.error(xhr.responseText);
            }
        });
    });
});


</script>


                        </div>
                    </span> <?php } ?>
                </div>
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

<script src="jquery.js"></script>


</html>
