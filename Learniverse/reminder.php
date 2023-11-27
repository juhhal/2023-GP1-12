#!/opt/homebrew/bin/php
<?php
require 'jwt.php';

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;


// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

$command = new MongoDB\Driver\Command([
    'distinct' => 'calendar',
    'key' => 'user_id'
]);

$cursor = $manager->executeCommand('Learniverse', $command);
$response = $cursor->toArray()[0];
$user_ids = $response->values;

// Print or use the user_ids array
foreach ($user_ids as $user_id) {
    $filter = ['user_id' => $user_id];
    $query = new MongoDB\Driver\Query($filter);
    $cursor = $manager->executeQuery('Learniverse.calendar', $query);
    $result_array = $cursor->toArray();

    // Check if there are any results for the current user_id
    if (!empty($result_array)) {
        // Process the results for the current user_id
        // You can perform any desired actions here
        foreach ($result_array as $result) {
            foreach ($result->List as $event) {
                // Check if the reminder is true
                if (isset($event->reminder) && $event->reminder === true) {
                    $eventDate = $event->start;
                    // Get the current date and the date for tomorrow
                    $currentDate = new DateTime();
                    $tomorrow = new DateTime('tomorrow');

                    // Modify the event date to a DateTime object
                    $eventDateTime = DateTime::createFromFormat('Y-m-d', $eventDate);

                    // Check if the event is due tomorrow
                    if (
                        $eventDateTime->format('Y-m-d') === $tomorrow->format('Y-m-d')
                    ){
                    // Process the event with reminder true
                    // Select the database and collection
                    $database = $connection->Learniverse;
                    $Usercollection = $database->users;

                    $user = $Usercollection->findOne(['email' => $user_id]);

                        $headers = array(
                            'alg' => 'HS256',
                            'typ' => 'JWT'
                        );
                        $payload = array(
                            'email' => $user['email'],
                            'exp' => (time() + 36000)
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
                        $mail->Subject = 'Upcoming Event Reminder!';
                        $mail->Body = "Dear ".$user['firstname'].",\n\nWe wanted to remind you that you have '".$event->title."' scheduled for tomorrow. Please be prepared and don't forget to be prepared.\n\nBest regards,\nLearniverse";
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
            }
        }
    }
}
error_log('Cron job started', 0);
// Perform your PHP operations here
error_log('Cron job finished', 0);
