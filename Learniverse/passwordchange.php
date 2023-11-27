<?php
require 'jwt.php';
if (is_jwt_valid($_GET['token']) === false) {
    header('Location: index.php');
    exit();
}

$arr = explode('.', $_GET['token']);
$email = base64_decode($arr[1]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="password.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">

    <title>Learniverse | Reset Password</title>
</head>

<body>
    <div class="card">
        <div class="header_logo">
            <img src="LOGO.png">
            <div>Learniverse</div>
        </div>

        <form id="change_form" action="updatepassword.php" method="POST">
            <h1>Reset Password</h1>

            <p class="status"></p>

            <div class="form-row">
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" value="" required>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="password">Confirm Password</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" value="" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="input-group button-container">
                    <input type="hidden" id="email" name="email" class="form-control" value="<?php echo $email; ?>" required>
                    <input class="button button-container" id="Reset" name="Reset" type="submit" value="Update Password">
                </div>
            </div>

        </form>

    </div>
    </div>

</body>
<script src="jquery.js"></script>
<script>
    document.querySelector('#email').value = <?php echo $email; ?>.email
    var q = <?php echo $email; ?>.q;
    document.querySelector('#change_form').addEventListener('submit', (e) => {
        e.preventDefault();

        if (document.querySelector('#password').value != document.querySelector('#confirmpassword').value) {
            document.querySelector('p.status').innerText = 'Passwords do not match';
            document.querySelector('p.status').style.color = 'red';
            return;
        } else {
            $.ajax({
                type: 'POST',
                url: 'updatepassword.php',
                data: $('#change_form').serialize(),
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.message) {
                        document.querySelector('p.status').innerText = "Wrong Email";
                        document.querySelector('p.status').style.color = 'red';
                    } else {
                        document.querySelector('p.status').innerText = 'Password Successfully Updated';
                        document.querySelector('p.status').style.color = 'green';
                        setTimeout(function() {
                            if (q == "thefiles.php") {
                                window.location.href = 'thefiles.php?q=My Files';
                            } else if (q == "workspace.php") {
                                window.location.href = 'workspace.php';
                            } else if (q == "index.php") {
                                window.location.href = 'index.php';
                            } else {
                                window.location.href = 'login.php';
                            }
                        }, 2000);
                    }
                }
            });
        }
    });
</script>

</html>