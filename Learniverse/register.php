<?php
session_start();

// Require the MongoDB library
require_once __DIR__ . '/vendor/autoload.php';

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

// Initialize variables
$username = "";
$firstname = "";
$lastname = "";
$email = "";
$password = "";
$response = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(htmlspecialchars($_POST["username"]));
    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $email = strtolower($_POST["email"]);
    $password = $_POST["password"];

    $password = sha1(htmlspecialchars($_POST["password"]));
    // Sanitize and validate email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Perform register validation
    $findemail = $Usercollection->findOne(['email' => $email]);
    $findusername = $Usercollection->findOne(['username' => $username]);

    if (strlen($_POST["password"]) < 12)
        $response['passwordError'] = "Password must be at least 12 character.";
    if (!preg_match('/^[A-Za-z]/', $firstname))
        $response['fnameError'] = "The first letter the first name must be an alphabetical character.";
    if (!preg_match('/^[A-Za-z]/', $lastname))
        $response['lnameError'] = "The first letter of the last name must be an alphabetical character.";
    if ($findemail)
        $response['emailError'] = 'You are already registred! Try to login';
    if ($findusername)
        $response['usernameError'] = 'That username is taken. Try another one.';

    if (empty($response)) {
        $newUser = ['google_user_id' => null, 'username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password, 'files_count' => 0, 'file_names' => ""];
        $result = $Usercollection->insertOne($newUser);

        if ($result) {
            $_SESSION['email'] = $email;
            require 'initialize_tools.php';
            // Successful register
            header("Location: workspace.php");
            exit();
        } else {
            $response['generalError'] = 'Registration failed. Please try again later.';
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <title>Learniverse | Register</title>

</head>

<body>

    <div class="split left">
        <div class="centered">

            <img src="LOGO.png" class="logo">
            <br>

            <form id="form" action="" method="POST" onsubmit="validateForm(event)">
                <h2>Create a New Account</h2>

                <br>

                <div class="response" id="generalError" style="color:red;"><?php echo (isset($response['generalError'])) ? $response['generalError'] : "";
                                                                            ?></div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="" class="form-control" value="" required>
                        <div class="response" id="usernameError" style="color:red;"><?php echo (isset($response['usernameError'])) ? $response['usernameError'] : ""; ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" placeholder="Start with a letter" class="form-control" value="" required>
                        <div class="response" id="fnameError" style="color:red;"><?php echo (isset($response['fnameError'])) ? $response['fnameError'] : ""; ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Start with a letter" class="form-control" value="" required>
                        <div class="response" id="lnameError" style="color:red;"><?php echo (isset($response['lnameError'])) ? $response['lnameError'] : ""; ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com" class="form-control" value="" required>
                        <div class="response" id="emailError" style="color:red;"><?php echo (isset($response['emailError'])) ? $response['emailError'] : ""; ?></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="12 character" class="form-control" value="" required>
                        <div class="response" id="passwordError" style="color:red;"><?php echo (isset($response['passwordError'])) ? $response['passwordError'] : ""; ?></div>
                    </div>
                </div>

                <input type="submit" class="button" id="signup" name="signup" value="Sign up">

                <h6> OR </h6>

                <p class='login'>Already have an account? <a href="login.php">Sign in</a></p>

            </form>

        </div>
    </div>


    <div class="split right">
        <img src="images/signup.png" alt='Advertising picture'>
        <div class="centered">
            <p id="content"> Not a member yet?<br>
                Dive in and become a part of our ever-growing universe</p>

        </div>
    </div>

</body>
<script src="jquery.js"></script>
<script>
    window.onload = function() {
        <?php $response = [] ?>
    }

    document.addEventListener('DOMContentLoaded', function() {
        var inputs = document.querySelectorAll('input');

        inputs.forEach(function(input) {
            input.addEventListener('input', function() {
                var errorDiv = this.nextElementSibling;
                errorDiv.innerHTML = ''; // Clear the error message
            });
        });
    });

    function validateForm(event) {
        event.preventDefault(); // Prevent the form from submitting by default
        console.log(event)
        var username = document.getElementById('username');

        var error = document.getElementById('usernameError');

        if (username.value.trim() == '') {
            error.textContent = 'Please enter a valid nonempty username.'; // Display the error message
            return false; // Cancel form submission
        } else {
            error.textContent = ''; // Clear the error message if it's not needed
        }

        // If the validation passes, you can proceed with form submission
        document.querySelector('form').submit();
    }
</script>

</html