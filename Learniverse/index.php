<?php
session_start();
error_reporting(0);
require 'jwt.php';
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;
if (isset($_SESSION['email'])) {
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
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Learniverse | Homepage</title>

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>
    <script>
        $(document).ready(function() {
            $("#rename-form").hide();

            if ($("#rename-form").css("display") === "none" || $("#rename-form").is(":hidden"))
                cancelRename();

            document.querySelector(".Pdropdown-menu").addEventListener("mouseleave", function() {
                cancelRename();
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
    </script>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="flex-parent">
                <div class="header_logo" onclick="window.Location='index.php'">
                    <img src="LOGO.png">
                    <div>Learniverse</div>
                </div>
                <div class="header_nav">
                    <nav id="navbar">
                        <ul class="nav__menu">
                            <li class="active">
                                <a href="index.php">Home</a>
                            </li>
                            <li>
                                <a href="community.php">Community</a>
                            </li>
                            <?php if (isset($_SESSION['email'])) echo '<li><a href="workspace.php">My Workspace</a></li>' ?>
                        </ul> <!-- end menu -->
                    </nav>
                </div>

                <?php if (!isset($_SESSION['email'])) {
                    echo '   <div id="login-register"><a href="register.php">Register</a> | <a href="login.php">Login</a></div>';
                } else {
                    echo '<div class="dropdown">
                        <button class="dropdown-button">
                            <i class="fas fa-user" id="Puser-icon"> </i>';
                    echo ' ' . $fetch['firstname'];
                    echo '</button>
                        <ul class="Pdropdown-menu">
                            <li class="editName center">
                                <i id="editIcon" class="fas fa-user-edit" onclick="Rename()"></i>
                                <span id="Pname">' . $fetch['firstname'] . " " .  $fetch['lastname'] . ' </span>
                                <form id="rename-form" class="rename-form" method="POST" action="updateName.php?q = index.php" onsubmit="return validateForm(event)">
                                    <input type="text" id="PRename" name="Rename" required value="' . $fetch['firstname'] . " " .  $fetch['lastname'] . '"><br>
                                    <span id="rename-error" style="color: red;"></span><br>
                                    <button type="submit">Save</button> <button type="reset" onclick="cancelRename();">Cancel</button>
                                </form>
                            </li>
                            <li class="center">Username: ' . $fetch['username'] . '</li>
                            <li class="center">' . $fetch['email'] . '</li>
                            <hr>';

                    if ($googleID === null) {
                        echo "<li><a href='reset.php?q=index.php'><i class='far fa-edit'></i> Change password</a></li>";
                    }
                    echo '
    
                            <li><a href="#"><i class="far fa-question-circle"></i> Help </a></li>
                            <hr>
                            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign out</a></li>
                        </ul>
                    </div>';
                } ?>

            </div>
        </div>
        </div>
        <h1>Learniverse</h1>
        <p>Your All-in-One Learning Companion</p>
    </header>
    <main>
        <div class="homepage_div">
            <div class="homepage_image">
                <img src="images/hc1.png">
            </div>
            <div class="homepage_content">
                <h4> Embark on a Transformative Learning Journey</h4>
                <p>Discover a world of innovation at Learniverse, where learning becomes an exhilarating journey.
                    Experience AI-generated tools that challenge and engage you. <br> <a href="register.php" style="text-decoration: none; color: #fdae9b;">Join Learniverse</a> today and unlock your
                    true learning potential like never before.</p>
            </div>
        </div>
        <div class="homepage_div">
            <div class="homepage_content">
                <h4> Revolutionize Your Learning Experience</h4>
                <p>Seamlessly manage your study schedule with our intuitive calendar and stay organized with our
                    note-taking features.
                    Collaborate in shared spaces and online meeting rooms to exchange ideas, and learn from others in
                    the community.</p>
            </div>
            <div class="homepage_image">
                <img src="images/hc2.png">
            </div>
        </div>
        <div id="finalwords">
            <img src="images/parashoot.png">
            <h4> <a href="register.php">Join </a>Learniverse and embrace the future of learning!</h4>
        </div>
    </main>
    <footer id="footer" style="margin-top: 7%;">

<div id="copyright">Learniverse &copy; 2023</div>
</footer>
</body>

</html>