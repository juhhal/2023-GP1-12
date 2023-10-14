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
$email = "";
$password = "";
$response = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower($_POST["email"]);
    $password = sha1(htmlspecialchars($_POST["password"]));

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = 'Invalid email address.';
    } else {

    // Perform login validation
    $user = $Usercollection->findOne(['email' => $email, 'password' => $password]);

    if ($user) {

        $_SESSION['email'] = $user['email'];
        // Successful login
        header("Location: workspace.html");
        exit();
    } else {
        // Invalid username or password
        $response = "Incorrect email address or password";
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Learniverse | Login</title>
    <script>
        $(document).ready(function() {

            $('#response').hide();

            $('#form').submit(function(e) {
                e.preventDefault(); // Prevent form submission

                // Get the form data
                var email = $('#email').val();
                var password = $('#password').val();

                // Send AJAX request to the server
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        email: email,
                        password: password
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Redirect to the home page
                            window.location.href = "workspace.html";
                        } else {
                            // Display failure message
                            $('#response').text(response.message).show();
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

            <form id="form" method="POST">

                <h1>Log in</h1>

                <br>

                <div id="response"></div>

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
                <div id="response" style="color:red;"><?php echo $response; ?></div>
                <br>

                <a href="reset.php">Forget your password?</a>

                <br><br>

                <input class="button" id="login" type="submit" value="Login" name="login">

                <!--<h6> OR </h6>

                <button class="button" id="glogin" type="" name="glogin">Log in with Google </button>-->

                <br> <br>

                <p>You don't have an account? <a href="register.php">Sign up</a></p>

            </form>

        </div>
    </div>

    <div class="split right">
        <div class="centered">
        </div>
    </div>
</body>

</html>