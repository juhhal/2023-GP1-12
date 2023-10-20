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
            <form id="forget_form" method="POST">
                <h1>Forget Password</h1>
                <br>

                <div class="form-row">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="" required>
                    </div>
                </div>
                <p class="status"></p>


                <input class="button" id="Reset" name="Reset" type="submit" value="Send Reset Link">

            </form>

        </div>
    </div>


    <div class="split right">
        <div class="centered">
        </div>
    </div>

</body>

<script src="jquery.js"></script>
<script>
    document.querySelector('#forget_form').addEventListener('submit', (e) => {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "sendreset.php",
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