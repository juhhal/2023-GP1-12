<?php
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$options = [
        'skip' => 3,
    ];

$query = new MongoDB\Driver\Query(['post_id' => $GET['postID']], $options);

$comments = $manager->executeQuery('Learniverse.comments', $query);

foreach ($comments as $oneComment) {
    $commenter_firstname = "";
    $commenter_lastname = "";
    $commenter_username = "";
    $comment_Date = "";
    $comment = "";
    $commentId = "";
    if ($oneComment) {
        $commenter_firstname = $oneComment->firstname;
        $commenter_lastname = $oneComment->lastname;
        $commenter_username = $oneComment->username;
        $comment_Date = $oneComment->commented_at;
        $comment = $oneComment->comment;
        $commentId = $oneComment->_id;
    };
$result = $result . "<p class='commentContent'>" . $comment . "</p>
<span class='alterComment'>
    <img src='images/edit.png' alt='edit' width='20px' height='20px' onclick='reWriteComment($commentId);'>
    <img src='images/bin.png' alt='bin' width='20px' height='20px' onclick='DeleteComment(\"" . $commentId . "\", \"" . $_GET['postID'] . "\");'>
</span><br><br>
<span class='commentInfo'>
By: " . $commenter_firstname . " " . " $commenter_lastname (@" . " $commenter_username) 
Commented At: " . $comment_Date . "
</span><br><br><br
<form id='editComment_form$commentId' method='POST' action='editcomment.php'>
<textarea cols='50' id='Recomment' name='Recomment'>$comment</textarea>
<input id='edit_id_post' name='edit_id_post' type='hidden' value='" . $_GET['postID'] . "'>
<input id='edit_id_comment' name='edit_id_comment' type='hidden' value='" . $commentId . "'>
<button type='submit'>Submit</button><button type='reset' onclick='cancelEditComment($commentId);'>Cancel</button>
</form>";
}

echo json_encode(['message' => $result]);