<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <title>Learniverse | Login</title>

    <script src="jquery.js"></script>
    <script>
        $(document).ready(function() {

            $('#form').submit(function(e) {
                // Prevent form submission
                e.preventDefault();

                // Get the form data
                var email = $('#email').val();
                var password = $('#password').val();

                // Send AJAX request to the server
                $.ajax({
                    url: 'action.php',
                    type: 'POST',
                    data: {
                        email: email,
                        password: password
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.message === "success") {
                            // Redirect to the workspace page
                            window.location.href = "workspace.php";
                        } else {
                            // Display failure message
                            $('#response').text(response.message);
                        }
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="split left">
        <div class="centered">
            <img src="LOGO.png" class="logo">
            <br><br>
            <form id="form" method="POST" action="action.php">
                <h1>Log in</h1>
                <br>
                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email Address: </label>
                        <input type="email" class="form-control" id="email" name="email" value="" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label for="password">Password: </label>
                        <input type="password" class="form-control" id="password" name="password" value="" required>
                    </div>
                </div>
                <div id="response" style="color:red;"></div>
                <br>
                <a href="reset.php">Forgot your password?</a>
                <br><br>
                <input class="button" id="login" type="submit" value="Login" name="login">

                <h6> OR </h6>

                <?php require 'config.php'; ?>

                <br> <br>
                <p>You don't have an account? <a href="register.php">Sign up</a></p>
            </form>
        </div>
    </div>
    <div class="split right">
    <img src = "images/login.png" alt = 'Advertising picture'>
        <div class="centered">
            <p id = "content"> The future of education is here, and it's exciting, inclusive, and tailored just for you. Set your course, explorer, and let's reach for the stars together</p>
        </div>
    </div>
</body>

</html>