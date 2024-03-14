<?php

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatServer implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection when it is opened
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        // Process the received message, save it to the database, and prepare the message data
        $messageData = "Processed message: " . $message;

        // Save the message to the database
        $this->saveMessageToDatabase($message);

        // Broadcast the message to all connected clients
        foreach ($this->clients as $client) {
            $client->send($messageData);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection when it is closed
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        // Handle any errors that occur
        $conn->close();
    }

    private function saveMessageToDatabase($message)
    {
        // Adjust this function to save the message to your database
        // For demonstration purposes, we'll assume a MongoDB connection

        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        $database = "your_database";
        $collection = "your_collection";

        $document = [
            "message" => $message,
            "timestamp" => time()
        ];

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);

        $manager->executeBulkWrite("$database.$collection", $bulk);
    }
}

// Run the WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    3000
);

$server->run();
