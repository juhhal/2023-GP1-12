<?php
session_start();
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

            <form id="form" action="adduser.php" method="POST" onsubmit="validateForm(event)">
                <h2>Create a New Account</h2>

                <p style="color: red;"  id='Response'></p>
                <div class="form-row">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <div class="tooltip">
                            <input type="text" id="username" name="username" class="form-control" value="" required>
                            <div class="tooltiptext">
                                <b>Username</b> must be unquie
                            </div>
                        </div>
                        <p style="color: red;" id="usernameResponse"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="firstname">First Name</label>
                        <div class="tooltip">
                            <input type="text" id="firstname" name="firstname" class="form-control" value="" required>
                            <div class="tooltiptext">
                                <b>First name</b> must start with a letter
                            </div>
                        </div>
                        <p style="color: red;"  id="firstnameResponse"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="lastname">Last Name</label>
                        <div class="tooltip">
                            <input type="text" id="lastname" name="lastname" class="form-control" value="" required>
                            <div class="tooltiptext">
                                <b>Last name</b> must start with a letter
                            </div>
                        </div>
                        <p style="color: red;"  id="lastnameResponse"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <div class="tooltip">
                            <input type="email" id="email" name="email" class="form-control" value="" required>
                            <div class="tooltiptext">
                                <b>Email</b> must be valid email
                            </div>
                        </div>
                        <p style="color: red;"  id="emailResponse"></p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="tooltip">
                            <input type="password" id="password" name="password" class="form-control" value="" required>
                            <div class="tooltiptext">
                                <b>Password</b> must be 12 character
                            </div>
                        </div>
                        <p style="color: red;"  id="passwordResponse"></p>
                    </div>
                </div>
        </div>
        <input type="submit" class="button" id="signup" name="signup" value="Sign up">

        <h6> OR </h6>

        <p class='login'>Already have an account? <a href="login.php">Sign in</a></p>

        </form>

    </div>

    <div class="split right">
        <img src="images/signup.png" alt='Advertising picture'>
        <div class="centered">
            <p id="content"> Not a member yet?<br>
                Dive in and become a part of our ever-growing universe</p>

        </div>
    </div>

</body>
<script src="jquery.js"></script>
<script>

    $(document).ready(function() {
        $('form').submit(function(e) {
            // Prevent form submission
            e.preventDefault();
            var username = $('#username').val();
            var firstname = $('#firstname').val();
            var lastname = $('#lastname').val();
            var email = $('#email').val();
            var password = $('#password').val();

            // Send AJAX request to the server
            $.ajax({
                url: 'adduser.php',
                type: 'POST',
                data: {
                    username: username,
                    firstname: firstname,
                    lastname: lastname,
                    email: email,
                    password: password
                },
                dataType: 'json',
                success: function(response) {
                    $('#passwordResponse').text('');
                    $('#firstnameResponse').text('');
                    $('#lastnameResponse').text('');
                    $('#emailResponse').text('');
                    $('#Response').text('');

                    if (response.message == "Password must be at least 12 character.") {
                        $('#passwordResponse').text(response.message);
                    } else if (response.message == "The first character of the first name must be alphabetical characters.") {
                        // Display failure message
                        $('#firstnameResponse').text(response.message);
                    } else if (response.message == "The first character of the last name must be alphabetical characters.") {
                        // Display failure message
                        $('#lastnameResponse').text(response.message);
                    } else if (response.message == "Invalid email address.") {
                        // Display failure message
                        $('#emailResponse').text(response.message);
                    } else if (response.message == "Your are already registred! Try to login") {
                        // Display failure message
                        $('#emailResponse').text(response.message);
                    } else if (response.message == "That username is taken. Try another one.") {
                        // Display failure message
                        $('#usernameResponse').text(response.message);
                    } else {
                        // Display failure message
                        $('#Response').text(response.message);
                    }

                }
            });
        });
    });

    function validateForm(event) {
        event.preventDefault(); // Prevent the form from submitting by default
        console.log(event)
        var username = document.getElementById('username');

        var error = document.getElementById('Response');

        if (username.value.trim() == '') {
            error.textContent = 'Please enter a valid nonempty username.'; // Display the error message
            return false; // Cancel form submission
        } else {
            error.textContent = ''; // Clear the error message if it's not needed
        }

        // If the validation passes, you can proceed with form submission
        document.querySelector('form').dispatchEvent(new Event('submit'));
    }
</script>

</html>
