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
    <link rel="stylesheet" href="password.css">

    <title>Learniverse | Change Password</title>
</head>

<body>
        <div class="card">

        <form id="change_form" action="updatepassword.php" method="POST">
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

            <input class="button" id="Reset" name="Reset" type="submit" value="Update Password">

        </form>

    </div>
</div>

</body>
<script src="./jquery.js"></script>
<script>
document.querySelector('#email').value = <?php echo base64_decode($arr[1]) ?>.email
    document.querySelector('#change_form').addEventListener('submit', (e) => {
        
        e.preventDefault();
        
        if(document.querySelector('#password').value != document.querySelector('#confirmpassword').value) return
        
        $.ajax({
            type: "POST",
            url: "updatepassword.php",
            data: $('#change_form').serialize(),
            success: function(response) {
                if (JSON.parse(response).message) {
                    document.querySelector('p.status').innerText = 'Wrong Email'
                    document.querySelector('p.status').style.color = "red";
                } else {
                    document.querySelector('p.status').innerText = 'Password Successfully Updated'
                    document.querySelector('p.status').style.color = "green";
                    
                }
            }
        });
    })
</script>
</html>