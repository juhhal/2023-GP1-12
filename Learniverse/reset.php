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

        <form id="register_form" method="POST">
            <h1>Forget Password</h1>
            <br>
            <p class="status"></p>

            <div class="form-row">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="" required>
                </div>
            </div>


            <input class="button" id="signup" name="Signup" type="submit" value="Send Reset Link">


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
    document.querySelector('#register_form').addEventListener('submit', (e) => {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "sendreset.php",
            data: $('#register_form').serialize(),
            success: function(response) {
                if (JSON.parse(response).message) {
                    document.querySelector('p.status').innerText = 'Email does not exist'
                } else {
                    document.querySelector('p.status').innerText = 'Reset Link Sended to your E-mail'
                }
            }
        });
    })
</script>

</html>