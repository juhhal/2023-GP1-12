<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="styles.css">
    <title>Learniverse | Loginpage</title>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="flex-parent">
                <div class="header_logo">
                    <img src="LOGO.png">
                    <div>Learniverse</div>
                </div>
                <div class="header_nav">
                    <nav id="navbar" class="nav__wrap collapse navbar-collapse">
                        <ul class="nav__menu">
                            <li class="active">
                                <a href="index.html">Home</a>
                            </li>
                            <li>
                                <a href="#">Community</a>
                            </li>
                            <li>
                                <a href="workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <div class="loginBTN"><a href="register.php">Register</a></div>
                <div class="loginBTN"><a href="login.php">Login</a></div>
            </div>
        </div>
    </header>


    <br><br>
    <form id="login_form" action="action.php" method="POST">


        <h1>Log in</h1>
        <br>
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

        <br>

        <input class="button" id="login" type="submit" value="Login" name="Login">
        <h6> OR </h6>
        <button class="button" id="glogin" type="" name="glogin">Log in with Goggle <!--<img src="google.jpeg">--></button>

        <br> <br>
        <p>You don't have an account? <a href="register.php">Sign up</a></p>
    </form>

</body>

<script src="./jquery.js"></script>
<script>

    document.querySelector('form').addEventListener('submit', (e) => {
        e.preventDefault();
        $.ajax({
        type: "POST",
        url: "action.php",
        data: $('form').serialize(),
        success: function (response) {
            if(JSON.parse(response)) {
                document.querySelector('p').innerText = 'Successfully log in'
            }
            else {
                document.querySelector('p').innerText = 'Your Email or Password incorrect./n Please Try again.'
            }
        }
    });
    })
    
</script>

</html>