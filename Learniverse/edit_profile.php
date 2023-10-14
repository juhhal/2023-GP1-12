<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
} else {

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
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="edit_profile.css">

        <title>Learniverse | Edit Profile</title>
        <script>
            var redirectButton = document.getElementById("cancel");

            redirectButton.addEventListener("click", function() {
                window.location.href = "workspace.html"; 
            });
        </script>
    </head>

    <body>
        <div class="card">
            <form id='edit_profile' action='updateInfo.php' method='POST'>
                <h3>My profile</h3>
                <div class="form-row">
                    <div class="input-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo $fetch['firstname']; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo $fetch['lastname']; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo $fetch['username']; ?>" disabled style=" background: #f0f0f0;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $fetch['email']; ?>" disabled style=" background: #f0f0f0;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <input type="hidden" id="hidden_id" name="hidden_id" class="form-control" value="<?php echo $fetch['_id']; ?>">
                    </div>
                </div>

                <div class="button-container">

                    <input type="submit" class="button" id="cancel" name="cancel" value="Cancel">
                    <input type="submit" class="button" id="edit" name="edit" value="Save">

                </div>

            </form>
        </div>
    </body>
    <script></script>

    </html>
<?php
}
?>