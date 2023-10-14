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
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="profile.css">
    <title>Learniverse | Profile</title>
    <script>
        var dropdownButton = document.querySelector('.dropdown-button');
        var dropdownMenu = document.querySelector('.dropdown-menu');

        dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
    </script>
</head>

<body>

    <div class="dropdown">
        <button class="dropdown-button"><img src='LOGO.png'> <?php echo $fetch['firstname']; ?></button>
        <ul class="dropdown-menu">
            <li class='name not-hover'><i class="fas fa-user"></i> <?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></li>
            <li class='hr not-hover'><?php echo $fetch['email']; ?></li>
            <li><i class='far fa-edit'></i> <a href='edit_profile.php?dd=<?php echo sha1($fetch['_id']); ?>'>Edit Profile</a></li>
            <li class='hr'><i class='far fa-question-circle'></i> <a href='#'>Help</a></li>
            <li><i class='fas fa-sign-out-alt'></i> <a href='logout.php'>Sign out</a></li>
        </ul>
    </div>
</body>