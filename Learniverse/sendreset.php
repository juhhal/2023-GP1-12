<?php

require 'jwt.php';

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;
$Admincollection = $database->admins;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower($_POST["email"]);
    $user = $Usercollection->findOne(['email' => $email]);
    $admin = $Admincollection->findOne(['email' => $email]);

    if (!$user) {
        if (!$admin) {
            $data = false;
            echo $data;
            exit();
        } else {
            $headers = array(
                'alg' => 'HS256',
                'typ' => 'JWT'
            );
            $payload = array(
                'email' => $admin['email'],
                'exp' => (time() + 36000),
                'q' => $_GET['q']
            );

            $jwttoken = generate_jwt($headers, $payload);

            $smtpUsername = 'Learniverse.website@gmail.com';
            $smtpPassword = 'hnrl utwf fxup rnyd';
            $smtpHost = 'smtp.gmail.com';
            $smtpPort = 587;
            // Create a new PHPMailer instance

            $mail = new PHPMailer;

            // Enable SMTP debugging
            $mail->SMTPDebug = 2;

            // Set the SMTP settings
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->Port = $smtpPort;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUsername;
            $mail->Password = $smtpPassword;

            // Set the email content
            $mail->setFrom('Learniverse.website@gmail.com');
            $mail->addAddress($user['email']);
            $mail->Subject = 'Reset Your Learniverse Password';
            $mail->Body = "Dear Learniverse Admin,\n\nWe received a request to reset your Learniverse account password. To complete this process, please click on the link below:\n\nhttps://www.learniverse.website/passwordchange.php?token=" . $jwttoken . "\n\nOnce you click the link, you will be prompted to create a new password. Please choose a strong and unique password to ensure the security of your account.\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

            // Send the email
            if ($mail->send()) {
                $data = ['message' => true];
                echo json_encode($data);
            } else {
                $data = false;
                echo $data;
            }
        }
    } else {
        $headers = array(
            'alg' => 'HS256',
            'typ' => 'JWT'
        );
        $payload = array(
            'email' => $user['email'],
            'exp' => (time() + 36000),
            'q' => $_GET['q']
        );

        $jwttoken = generate_jwt($headers, $payload);

        $smtpUsername = 'Learniverse.website@gmail.com';
        $smtpPassword = 'hnrl utwf fxup rnyd';
        $smtpHost = 'smtp.gmail.com';
        $smtpPort = 587;
        // Create a new PHPMailer instance

        $mail = new PHPMailer;

        // Enable SMTP debugging
        $mail->SMTPDebug = 2;

        // Set the SMTP settings
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->Port = $smtpPort;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;

        // Set the email content
        $mail->setFrom('Learniverse.website@gmail.com');
        $mail->addAddress($user['email']);
        $mail->Subject = 'Reset Your Learniverse Password';
        $mail->Body = "Dear Learniverse User,\n\nWe received a request to reset your Learniverse account password. To complete this process, please click on the link below:\n\nhttps://www.learniverse.website/passwordchange.php?token=" . $jwttoken . "\n\nOnce you click the link, you will be prompted to create a new password. Please choose a strong and unique password to ensure the security of your account.\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team";

        // Send the email
        if ($mail->send()) {
            $data = ['message' => true];
            echo json_encode($data);
        } else {
            $data = false;
            echo $data;
        }
    }
}
