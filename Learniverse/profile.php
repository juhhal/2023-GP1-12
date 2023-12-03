<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
} else {

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
?>
    <html>

    <head>
        <title>Profile</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="profile.css">
        <title>Learniverse | Profile</title>
        <script src = 'jquery.js'></script>
        <script>
            $(document).ready(function() {
                $("#rename-form").hide();
            });
            var dropdownButton = document.querySelector('.dropdown-button');
            var dropdownMenu = document.querySelector('.dropdown-menu');

            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('show');
            });

            function Rename() {
                $('#name').hide();
                $('#user-icon').hide();
                $("#rename-form").show();
                $("#Rename").focus();
                if ($("#rename-form").submitted) {
                    cancelRename();
                }
            };

            function cancelRename() {
                $("#rename-form").hide();
                $('#name').show();
                $('#user-icon').show();
            };
        </script>
    </head>

    <body>
        <?php $headers = array(
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
            <button class="dropdown-button"><img src='LOGO.png'> <?php echo ' ' . $fetch['firstname']; ?></button>
            <ul class="dropdown-menu">
                <li class='editName center'>
                    <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
                    <span id='name'><i class="fas fa-user" id='user-icon'></i> <?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
                    <form id='rename-form' class='rename-form' method='POST' action='updateName.php'>
                        <input type='text' id='Rename' name='Rename' value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                        <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
                    </form>
                <li class='center'><?php echo $fetch['email']; ?></li>
                <hr>
                <li><a href='reset.php?q=workspace.php'><i class='far fa-edit'></i> Change password</a></li>
                <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
                <hr>
                <li><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out </a></li>
            </ul>
        </div>
    </body>

    </html>
<?php } ?>