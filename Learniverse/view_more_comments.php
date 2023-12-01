<?php
session_start();

$guest_account = null;
//check if user is a guest to hide the profile menu
if (!isset($_SESSION['email'])) {
    $guest_account = true;
    $visibility = 'none';
} else {
    $guest_account = false;
    $visibility = 'block';
}

//connect to database
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$options = [
    'skip' => 3,
];

$query = new MongoDB\Driver\Query(['post_id' => $_GET['postID']], $options);

$comments = $manager->executeQuery('Learniverse.comments', $query);
$result = "";
foreach ($comments as $oneComment) {
    $commenter_firstname = "";
    $commenter_lastname = "";
    $commenter_username = "";
    $comment_Date = "";
    $comment = "";
    $commentId = "";
    $commenter_email = "";
    $edited_date = "";
    if ($oneComment) {
        $commenter_firstname = $oneComment->firstname;
        $commenter_lastname = $oneComment->lastname;
        $commenter_username = $oneComment->username;
        $comment_Date = $oneComment->commented_at;
        $comment = $oneComment->comment;
        $commentId = $oneComment->_id;
        $commenter_email = $oneComment->email;
        $edited_date = $oneComment->edited_at;
    };

    $result = $result . "<div class='oneCommnet'><p class='commentContent'>" . $comment . "</p>";
    if (!$guest_account && $commenter_email == $_SESSION['email'])
        $result = $result . "<span class='editComment'><img src='images/edit.png' alt='edit' width='20px' height='20px' onclick='reWriteComment($comment, $commentId);'></span><span class='deleteComment'><img src='images/bin.png' alt='bin' width='20px' height='20px' onclick='DeleteComment(\"" . $commentId . "\", \"" . $_GET['postID'] . "\");'></span>";
    $result = $result . "<span class='commentInfo'>
    By: " . $commenter_firstname . " " . " $commenter_lastname (@" . " $commenter_username) </span><br><span class = 'commentdate'>";
    if ($edited_date != "")
        $result = $result . "Edited At " . $edited_date;
    else
        $result = $result . "At " . $comment_Date;
    $result = $result . "</span></div>";
}

echo json_encode($result);
