<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="styles.css">

    <title>Register</title>
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
                                <a href="#">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <div class="loginBTN"><a href="register.php">Register</a></div>
                <div class="loginBTN"><a href="#">Login</a></div>
            </div>
        </div>
    </header>
    <h1>Create a New Account</h1>
    <p></p>
    <form method="POST">
        <span>
            <label for="username">Username</label>
            <input type="text" id="username" name="username">
        </span>
        <span>
            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname">
        </span>
        <span>
            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname">
        </span>
        <span>
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </span>
        <span>
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </span>
        <input type="submit" value="Create a New Account">
    </form>
    <a id="link" href="index.html">Already have an account? Sign in</a>
</body>
<script src="./jquery.js"></script>
<script>
    document.querySelector('form').addEventListener('submit', (e) => {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "adduser.php",
            data: $('form').serialize(),
            success: function(response) {
                if (JSON.parse(response)) {
                    document.querySelector('p').innerText = 'Successfully Registered'
                } else {
                    document.querySelector('p').innerText = 'You are already Registered'
                }
            }
        });
    })
</script>

</html>