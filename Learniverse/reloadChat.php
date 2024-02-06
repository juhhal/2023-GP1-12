<?php 
require "session.php";

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$filter = ['spaceID' => new MongoDB\BSON\Regex($_GET['spaceID'])];
$query = new MongoDB\Driver\Query($filter);

// MongoDB collection name
$collectionName = "Learniverse.sharedSpace";

// Execute the query
$result = $manager->executeQuery($collectionName, $query);
$space = $result->toArray()[0];

$filter = ['email' => $_SESSION['email']];
$query = new MongoDB\Driver\Query($filter);
$result = $manager->executeQuery("Learniverse.users", $query);
$user = $result->toArray()[0];


// Fetch the updated messages
$feeds = $space->feed;

// Generate the HTML for the updated messages
$html = '';
foreach ($feeds as $feed) {
  if ($feed->writtenBy == $user->username)
    $html .= "<div class='chat userchat'><span class='username'>" . $feed->writtenBy . "</span><br><span class='data'>" . $feed->message . "</span><span class='date'>" . $feed->date . "</span></div>";
  else
    $html .= "<div class='chat'><span class='username'>" . $feed->writtenBy . "</span><br><span class='data'>" . $feed->message . "</span><span class='date'>" . $feed->date . "</span></div>";
}

// Return the generated HTML
echo $html;
?>