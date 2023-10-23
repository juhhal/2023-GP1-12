<?php

// Include the Google API PHP client library
require_once __DIR__ . '/vendor/autoload.php';

// Set up the Google API client
$client = new Google_Client();
$client->setClientId('218276306114-td0jcm6u4etdm7b6b9bsimvba7m3inn1.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost:3000/login.php');
$client->addScope('email');
$client->addScope('https://www.googleapis.com/auth/userinfo.profile');

// Create a MongoDB connection
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$oauthService = new Google\Service\Oauth2($client);

// Google login page
if (isset($_GET['code'])) {
    // Exchange the authorization code for an access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    $profile = $oauthService->userinfo->get();

    // Access the profile information
    $firstName = $profile->given_name;
    $lastName = $profile->family_name;
    $email = $profile->email;

    // Get user information from Google API
    $googleUser = $client->verifyIdToken($token['id_token']);
    $userId = $googleUser['sub']; // Unique identifier for the user

    // Check if the user exists in MongoDB
    $usersCollection = $connection->Learniverse->users;
    $existingUser = $usersCollection->findOne(['google_user_id' => $userId]);

    if ($existingUser) {
        // User already exists, authenticate them in your application
        $_SESSION['email'] = $existingUser['email'];
        header("Location: workspace.php");
    } else {
        // User is new, create a new user record in MongoDB
        $newUser = [
            'google_user_id' => $userId,
            'username' =>$userId,
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email
        ];
        $insertResult = $usersCollection->insertOne($newUser);

        if ($insertResult->getInsertedCount() > 0) {
            echo "<script>alert('i am in.');</script>";
            // User record created successfully, authenticate the user
            $_SESSION['email'] = $email;
            require 'initialize_tools.php';
            header("Location: workspace.php");
        } else {
            echo "Error creating user.";
        }
    }
} else {
    // Generate the Google login URL with the state parameter
    $authUrl = $client->createAuthUrl();
    echo "<a id='google' href='$authUrl'><p>Continue with </p> <img src='google.png' alt='Google Logo'> </a>";
}
?>