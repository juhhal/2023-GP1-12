<?php session_start();
$q = $_GET['q']; ?>
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

        <form id="forget_form" method="POST">
            <h1>Reset Password</h1>
            <br>

            <div class="form-row">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="" required>
                </div>
            </div>
            <p class="status"></p>

            <div class="form-row">
                <div class="input-group button-container">
                    <input class="button" id="Reset" name="Reset" type="submit" value="Send Reset Link">
                    <br><br>
                    <input class="button" id="Back" name="Back" type="button" value="Back" onclick='goBack(event);'>
                </div>
            </div>

        </form>

    </div>
    </div>

</body>

<script src="jquery.js"></script>
<script>
    var q = "<?php echo $_GET['q']; ?>";

    function redirect(url) {
        window.location.href = url;
    }

    function goBack(event) {
        event.preventDefault();
        <?php if (isset($_SESSION["email"])) : ?>
            <?php if ($_GET['q'] === "theFiles.php") : ?>
                redirect("theFiles.php?q=My Files");
            <?php elseif ($_GET['q'] === "workspace.php") : ?>
                redirect("workspace.php");
            <?php elseif ($_GET['q'] === "pomodoro.php") : ?>
                redirect("pomodoro.php");
            <?php elseif ($_GET['q'] === "addCommunityPost.php") : ?>
                redirect("addCommunityPost.php");
            <?php elseif ($_GET['q'] === "gpa.php") : ?>
                redirect("gpa.php");
            <?php elseif ($_GET['q'] === "community.php") : ?>
                redirect("community.php");
            <?php elseif ($_GET['q'] === "notes.php") : ?>
                redirect("Notes/notes.php");
            <?php elseif ($_GET['q'] === "index.php") : ?>
                redirect("index.php");
            <?php else : ?>
                redirect(q);
            <?php endif; ?>
        <?php else : ?>
            redirect("login.php");
        <?php endif; ?>
    }


    document.querySelector('#forget_form').addEventListener('submit', (e) => {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "sendreset.php?q=" + q,
            data: $('#forget_form').serialize(),
            success: function(response) {
                if (response != false) {
                    document.querySelector('p.status').innerText = 'Reset Link has been sent to your E-mail.';
                    document.querySelector('p.status').style.color = "green";
                } else {
                    document.querySelector('p.status').innerText = 'Email does not exist';
                    document.querySelector('p.status').style.color = "red";
                }
            },
            error: function(msg) {
                alert("ERROR sending reset email: " + msg);
                document.querySelector('p.status').innerText = 'Email does not exist';
            }

        });
    })
</script>

</html>