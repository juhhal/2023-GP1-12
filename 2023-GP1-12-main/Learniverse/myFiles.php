<html>
<?php session_start();
error_reporting(0);

?>

<head>
    <meta charset="UTF-8">
    <title>My Workspace</title>
    <link rel="stylesheet" href="myFiles.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
</head>

<body>
    <!--    <header>-->
    <!--        <div class="header-container">-->
    <!--            <div class="flex-parent">-->
    <!--                <div class="header_logo">-->
    <!--                    <img src="LOGO.png">-->
    <!--                    <div>Learniverse</div>-->
    <!--                </div>-->
    <!--                <div class="header_nav">-->
    <!--                    <nav id="navbar" class="nav__wrap collapse navbar-collapse">-->
    <!--                        <ul class="nav__menu">-->
    <!--                            <li>-->
    <!--                                <a href="index.html">Home</a>-->
    <!--                            </li>-->
    <!--                            <li>-->
    <!--                                <a href="#">Community</a>-->
    <!--                            </li>-->
    <!--                            <li class="active">-->
    <!--                                <a href="workspace.html">My Workspace</a>-->
    <!--                            </li>-->
    <!--                        </ul> &lt;!&ndash; end menu &ndash;&gt;-->
    <!--                    </nav>-->
    <!--                </div>-->
    <!--                <div class="BTN"><a href="logout.php">Logout</a></div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </header>-->

    <main>

        <ul class="tool_list">

            <li class="tool_item"><a href="workspace.php"><img src="images/calendar.png">
                    Calendar and To-Do </a></li>
            <!--        <a href="?q=Upload File" class="tool_item"><img src="images/uploadwhite.png">-->
            <!--Upload File </a>-->

            <a href="?q=My Files" class="tool_item"><img src="images/files (2).png">
                My Files
            </a>
            <li class="tool_item"><img src="images/quiz.png">
                Quiz
            </li>
            <li class="tool_item"><img src="images/flash-cards.png">
                Flashcard
            </li>
            <li class="tool_item"><img src="images/summarization.png">
                Summarization
            </li>
            <li class="tool_item"><img src="images/study-planner.png">
                Study Planner
            </li>
            <li class="tool_item"><img src="images/notes.png">
                Notes
            </li>
            <li class="tool_item"><img src="images/to-do-list.png">
                To-Do List
            </li>
            <li class="tool_item"><img src="images/pomodoro-technique.png">
                Pomodoro
            </li>
            <li class="tool_item"><img src="images/gpa.png">
                GPA Calculator
            </li>
            <li class="tool_item"><img src="images/collaboration.png">
                Shared spaces
            </li>
            <li class="tool_item"><img src="images/meeting-room.png">
                Meeting Room
            </li>
            <li class="tool_item"><img src="images/communities.png">
                Community
            </li>
            <a href="logout.php" class="tool_item"><img src="images/logout.png">
                Logout
            </a>
        </ul>
    </main>
    <div id="container">
        <div class="flex-parent">
            <div class="header_logo">
                <img src="LOGO.png">
                <div>Learniverse</div>
            </div>
            <div class="header_nav">
                <nav id="navbar" class="nav__wrap collapse navbar-collapse">
                    <ul class="nav__menu">
                        <li>
                            <a href="index.html">Home</a>
                        </li>
                        <li>
                            <a href="#">Community</a>
                        </li>
                        <li class="active">
                            <a href="workspace.php?q=My Files">My Workspace</a>
                        </li>
                    </ul> <!-- end menu -->
                </nav>
            </div>
            <div class="BTN"><a href="logout.php">Logout</a></div>
        </div>
        <h1><?php if (isset($_GET['q'])) echo $_GET['q']; ?></h1>
        <?php if (isset($_GET['q'])) { ?>
            <form enctype="multipart/form-data">
                <input type="file" name="file" id="file">
                <span>
                    <h3>Upload File</h3>
                    <img src="images/upload.png" />
                </span>
            </form>
            <span id="formContanier">


                <span class="uploadedfile">
                    <p></p>
                    <span id="loadingbar"></span>
                </span>
                <div id="allfiles">

                    <?php

                    if ($_GET['q'] == 'My Files') {

                        include('allfiles.php');
                    }
                    ?></div>
            </span> <?php } ?>
    </div>
    <!--    <footer>-->
    <!--        <img id="footerLogo" src="LOGO.png" alt="LivingWell">-->
    <!--        <div class="footer-div" id="socials">-->
    <!--            <h4>Follow Us on Social Media</h4>-->
    <!--            <ul>-->
    <!--                <li><a href="https://twitter.com/learniversewebsite" target="_blank"><img src="images/twitter.png"-->
    <!--                            alt="@Learniverse"></a></li>-->
    <!--            </ul>-->
    <!--        </div>-->
    <!--        <div class="footer-div" id="contacts">-->
    <!--            <h4>Contact Us</h4>-->
    <!--            <ul>-->
    <!--                <li><a href="mailto:learniverse.website@gmail.com" target="_blank"><img src="images/gmail.png"-->
    <!--                            alt="learniverse.website@gmail.com"></a></li>-->
    <!--            </ul>-->
    <!--        </div>-->
    <!--        <div id="copyright">Learniverse &copy; 2023</div>-->
    <!--    </footer>-->
</body>
<script src="jquery.js"></script>
<script>
    document.querySelector('input').addEventListener('change', (e) => {
        document.querySelector('.uploadedfile').style.display = 'flex'
        //console.log(document.querySelector('input').value.split("\\"))
        document.querySelector('.uploadedfile p').innerText = document.querySelector('input').value.split("\\")[document.querySelector('input').value.split("\\").length - 1]
        e.preventDefault()
        let formdata = new FormData(document.querySelector('form'))
        document.querySelector('#loadingbar').style.width = '50%';
        $.ajax({
            url: 'upload.php',
            data: formdata,
            method: 'POST',
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res)
                document.querySelector('#loadingbar').style.width = '100%';
                $.ajax({
                    url: 'allfiles.php',
                    data: "",
                    method: 'POST',
                    success: function(res) {
                        document.querySelector('#allfiles').innerHTML = res
                        console.log(res)

                    }
                })
            }
        })
    })

    document.querySelectorAll('iframe').forEach(e => {
        e.contentWindow.document.querySelector('html').style.overflow = 'hidden'
    })

    document.querySelectorAll('.three').forEach(e => {
        e.addEventListener('click', (event) => {
            console.log(document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display)
            if (document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display == 'flex')
                document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display = 'none'
            else
                document.querySelector('#' + event.target.attributes['data-value'].value + ' .queries').style.display = 'flex'
        })
    })

    document.querySelectorAll('.deleteic').forEach(e => {
        e.addEventListener('click', (event) => {
            if (confirm('Are you sure deleting this file ?')) {
                $.ajax({
                    url: 'delete.php',
                    data: {
                        value: event.target.attributes['data-p'].value
                    },
                    method: 'POST',
                    success: function(res) {
                        console.log(res)
                        document.querySelector('#' + event.target.attributes['data-value'].value).style.display = 'none'

                    }
                })
            }

        })
    })
</script>

</html>