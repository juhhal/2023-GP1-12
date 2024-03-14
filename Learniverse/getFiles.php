<?php
session_start();
require 'jwt.php';
require_once __DIR__ . '/vendor/autoload.php';
$client = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
$database = $client->selectDatabase('Learniverse');
$usersCollection = $database->selectCollection('users');

// Get the email from the session
$email = $_SESSION['email'];

// Query the database for the user
$userDocument = $usersCollection->findOne(['email' => $email]);

// If user found, retrieve the _id
$user_id = null;
if ($userDocument) {
    $user_id = $userDocument->_id;
}

// Now $user_id contains the _id field for the user with the specified email

// Create directory path with user ID
$userDirectory = "user_files".DIRECTORY_SEPARATOR."{$user_id}";

// Check if folder is provided in the POST request
if (isset($_POST['folder']) && isset($_POST['userDirectory'])) {
    // Retrieve folder name and user directory path from POST data
    $folderName = $_POST['folder'];
    $userDirectories = $_POST['userDirectory'];
    // Construct the full path to the folder
    $directoryPath = $userDirectory . DIRECTORY_SEPARATOR . $folderName;
    // Check if the folder exists
    if (is_dir($directoryPath)) {
        // Get the list of files in the folder
        $files = scandir($directoryPath);

        // Output HTML content for the file list
        echo "<ul id='file-list' style='display: flex; flex-wrap: wrap; justify-content: space-around;'>"; // Adding styles for flexbox layout
        foreach ($files as $file) {
            // Exclude "." and ".." directories and hidden files
            if ($file != "." && $file != ".." && !is_dir($directoryPath . DIRECTORY_SEPARATOR . $file) && substr($file, 0, 1) != '.') {
                // Create a unique ID for the file item
                $fileID = 'file_' . uniqid();
                // Create the file item and append it using jQuery
                echo "<script>
                    $(document).ready(function() {
                        var fileElement = $('<span>').addClass('box').attr('id', 'box_" . $fileID . "');
                        var iframeElement = $('<iframe>').css('overflow', 'hidden').attr('src', '" . $directoryPath . DIRECTORY_SEPARATOR . $file . "');
                        var collectionElement = $('<span>').addClass('collection');
                        var fileLink = $('<a>').attr('target', '_blank').addClass('file').attr('href', '". $directoryPath . DIRECTORY_SEPARATOR . $file . "').text('" . htmlspecialchars($file) . "');
                        var basePath = 'images". DIRECTORY_SEPARATOR ."'; // Define the base path for images
                        var deleteBtn = $('<img>').addClass('icon deleteic').attr('src', 'images" . DIRECTORY_SEPARATOR . "delete.jpeg').attr('id', 'deleteFile');

        var filePath = '" . $directoryPath . DIRECTORY_SEPARATOR . $file . "';
        var menuIcon = $('<img>').addClass('menu-icon').attr('src', 'images/dots.svg').css({
            'position': 'absolute',
            'top': '10px',
            'right': '10px',
            'cursor': 'pointer'
        }).attr('id', 'menuIcon_" . $fileID . "');

        var deleteBtn = $('<img>').addClass('icon deleteic').attr('src', 'images/delete.jpeg');

        var fileLink = $('<a>').attr('target', '_blank').addClass('file').attr('href', '". $directoryPath . DIRECTORY_SEPARATOR . $file . "').text('" . htmlspecialchars($file) . "');

        var summarizeBtn = $('<button>').addClass('custom-btn').text('Summarize').on('click', function() {
            window.location.href = 'summarization/summarization.php?file=' + encodeURIComponent(filePath);
        }).css('display', 'none');
    
        var quizBtn = $('<button>').addClass('custom-btn').text('Quizzes').on('click', function() {
            window.location.href = 'quizes/index.php?file=' + encodeURIComponent(filePath);
        }).css('display', 'none');
    
        var flashcardsBtn = $('<button>').addClass('custom-btn').text('Flashcards').on('click', function() {
            window.location.href = 'flashcard.php?file=' + encodeURIComponent(filePath);
        }).css('display', 'none');

        var collectionElement = $('<div>').addClass('collection').append(menuIcon, deleteBtn, fileLink, summarizeBtn, quizBtn, flashcardsBtn);

        var isOpen = false; // Flag to track if the menu is open

        // Function to toggle the visibility of menu options
        function toggleMenu() {
            deleteBtn.toggle();
            summarizeBtn.toggle();
            quizBtn.toggle();
            flashcardsBtn.toggle();
            isOpen = !isOpen; // Toggle the flag
        }
    
        // Click event for menuIcon
        menuIcon.click(function(event) {
            toggleMenu();
            event.stopPropagation(); // Prevent this click from propagating to the document
        });
    
        // Document click event to hide the menu options if the menu is open
        $(document).click(function(event) {
            if(isOpen) {
                toggleMenu(); // Hide the menu options
            }
        });
    
        // Prevent clicks within the menu from propagating to the document
        $('.collection').click(function(event) {
            event.stopPropagation();
        });

        var iframeElement = $('<iframe>').css('overflow', 'hidden').attr('src', '" . $directoryPath . DIRECTORY_SEPARATOR . $file . "');

        // Append the collectionElement to the fileElement
        var fileElement = $('<span>').addClass('box').attr('id', 'box_" . $fileID . "');
        fileElement.append(iframeElement).append(collectionElement);
        $('#file-list').append(fileElement);

  

                        deleteBtn.click(function() {
                            var filePath = '" . $directoryPath .  DIRECTORY_SEPARATOR . $file . "';
                            Swal.fire({
                                title: \"Are you sure?\",
                                text: \"Once deleted, you will not be able to recover this file!\",
                                icon: \"warning\",
                                showCancelButton: true, // Make sure the cancel button is shown
                                confirmButtonText: 'Yes, delete it!', // Text for the confirm button
                                cancelButtonText: 'No, cancel!', // Text for the cancel button
                                reverseButtons: true // Reverse the button positions if desired
                            }).then((result) => {
                                if (result.isConfirmed) { // Check if the confirm button was clicked
                                    // User confirmed, proceed with deletion
                                    $.ajax({
                                        url: 'deleteFile.php', // Assuming deleteFile.php is in the same directory
                                        type: 'POST',
                                        data: {
                                            action: 'deleteFile',
                                            filePath: filePath
                                        },
                                        success: function(response) {
                                            // Handle success response
                                            var result = JSON.parse(response);
                                            if (result.status === 'success') {
                                                // File deleted successfully
                                                Swal.fire(\"Deleted!\", result.message, \"success\").then(() => {
                                                    $('#box_" . $fileID . "').remove();
                                                });
                                            } else {
                                                // Failed to delete file
                                                Swal.fire(\"Error!\", result.message, \"error\");
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            // Handle error response
                                            console.error(xhr.responseText);
                                            Swal.fire(\"Error!\", \"Failed to delete file.\", \"error\");
                                        }
                                    });
                                } // No need for an else statement when the cancel button is clicked; nothing happens
                            });
        
                        });
        
                        // collectionElement.append(fileLink).append(deleteBtn);//.append(summaryBtn).append(questionBtn).append(flashcardBtn);
                        // var fileElement = $('<span>').addClass('box').attr('id', 'box_" . $fileID . "');
                        // fileElement.append(collectionElement);
                        // fileElement.append(iframeElement); // Moved collectionElement outside of iframeElement appending
                        // $('#file-list').append(fileElement);
                    });
                </script>";

        
            }
        }
        echo "</ul>";

    } else {
        echo "Folder not found.";
    }
} else {
    echo "Invalid request.";
}
?>
