<?php
    require 'jwt.php';
    if(is_jwt_valid($_GET['token']) === false) header('Location: index.html');
    
    $arr = (explode('.', $_GET['token']));
    
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

        <form id="register_form" action="adduser.php" method="POST">
            <h1>Password Change</h1>
            <br>
            <p class="status"></p>

<div class="form-row">
                <div class="input-group">
                    <input type="hidden" id="email" name="email" class="form-control" value="" required>
                </div>
            </div>
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

            <input class="button" id="signup" name="Signup" type="submit" value="Update Password">

            <p>Already have an account? <a href="login.php">Sign in</a></p>

        </form>

    </div>
</div>


<div class="split right">
    <div class="centered">
    </div>
</div>

</body>

<script src="./jquery.js"></script>
<script>
document.querySelector('#email').value = <?php echo base64_decode($arr[1]) ?>.email
    document.querySelector('#register_form').addEventListener('submit', (e) => {
        
        e.preventDefault();
        
        if(document.querySelector('#password').value != document.querySelector('#confirmpassword').value) return
        
        $.ajax({
            type: "POST",
            url: "updatepassword.php",
            data: $('#register_form').serialize(),
            success: function(response) {
                if (JSON.parse(response).message) {
                    document.querySelector('p.status').innerText = 'Wrong Email'
                } else {
                    document.querySelector('p.status').innerText = 'Password Successfully Updated'
                    
                }
            }
        });
    })
</script>

</html>