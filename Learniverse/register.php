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
$response = "";
$responseClass = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(htmlspecialchars($_POST["username"]));
    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $email = strtolower($_POST["email"]);
    $password = sha1(htmlspecialchars($_POST["password"]));

    // Sanitize and validate email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = 'Invalid email address.';
        $responseClass = 'error';
    } else {
    // Perform register validation
    $findemail = $Usercollection->findOne(['email' => $email]);
    $findusername = $Usercollection->findOne(['username' => $username]);
    if ($findemail) {
        $response = 'Your are already registred! Try to login';
        $responseClass = 'error';
    } else if ($findusername) {
        $response = 'That username is taken. Try another one.';
        $responseClass = 'error';
    } else {
        $newUser = ['username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $email, 'password' => $password];
        $result = $Usercollection->insertOne($newUser);

        if ($result) {
            $_SESSION['email'] = $email;
            // Successful register
            header("Location: workspace.html");
            exit();
        } else {
            $response = 'Registration failed. Please try again later.';
            $responseClass = 'error';
        }
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

    <title>Learniverse | Register</title>

</head>

<body>

    <div class="split left">
        <div class="centered">

            <img src="LOGO.png" class="logo">
            <br>

            <form id="form" action="" method="POST">
                <h1>Create a New Account</h1>

                <br>

                <div id="response" class="<?php echo $responseClass; ?>"><?php echo $response; ?></div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" value="" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" value="" required>
                    </div>
                </div>

                <input type="submit" class="button" id="signup" name="signup" value="Sign up">

                <!--<h6> OR </h6>

                <button class="button" id="glogin" type="" name="glogin">Log in with Google </button>-->

                <br>

                <p>Already have an account? <a href="login.php">Sign in</a></p>

            </form>

        </div>
    </div>


    <div class="split right">
        <div class="centered">
        </div>
    </div>

</body>

</html>