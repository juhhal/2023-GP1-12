<!DOCTYPE html>

<?php


 session_start(); 

 if (isset($_GET['data'])) {
    // Decode the JSON string into a PHP array
    $flashcardsData = json_decode(urldecode($_GET['data']), true);
    $subject = $_GET['subjectName'];
 } 
?>
<head>
    <meta charset="UTF-8">
    <title>flashcards</title>
    <link rel="stylesheet" href="../emptyWorkspace.css">
    <link rel="stylesheet" href="../header-footer.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <link rel="stylesheet" href="flashcard.css">
    <script src="../jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="../profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- CUSTOMER SUPPORT STYLESHEET -->
    <script src="../customerSupport.js"></script>
    <link rel="stylesheet" href="../customerSupport.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    </script>
    <!-- SHOUQ SECTION: -->
    <script type='text/javascript'>
        $(document).ready(function() {
            var dropdownButton = document.querySelector('.dropdown-button');
            var dropdownMenu = document.querySelector('.Pdropdown-menu');
            dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
            $("#rename-form").hide();
            if ($("#rename-form").css("display") === "none" || $("#rename-form").is(":hidden"))
                cancelRename();

            document.querySelector(".Pdropdown-menu").addEventListener("mouseleave", function() {
                cancelRename();
            });

            var isSidebarOpen = false;
            var isButtonClicked = false;
            var sidebarTongue = document.querySelector('#sidebar-tongue');
            var sidebarDiv = document.querySelector('#tools_div');

            // Event listener for hover on the sidebar tongue button
            sidebarTongue.addEventListener('mouseenter', function(event) {
                if (!isSidebarOpen) {
                    w3_open();
                    isSidebarOpen = true;

                } else if (!isButtonClicked) {
                    w3_close();
                    isSidebarOpen = false;
                }
            });

            sidebarTongue.addEventListener('click', function(event) {
                if (!isButtonClicked && isSidebarOpen) {
                    isButtonClicked = true;
                } else {
                    w3_close();
                    isButtonClicked = false;
                    isSidebarOpen = false;
                }
            });

            sidebarDiv.addEventListener('mouseleave', function(event) {
                if (!isButtonClicked && isSidebarOpen) {
                    w3_close();
                    isSidebarOpen = false;
                }
            });


            // PROFILE DROPDOWN MENU
        function Rename() {
            $('#Pname').hide();
            $('#Puser-icon').hide();
            $("#rename-form").show();
            $("#PRename").focus();
            if ($("#rename-form").submitted) {
                cancelRename();
            }
        };

        function cancelRename() {
            $("#rename-form").get(0).reset();
            $("#rename-form").hide();
            $('#Pname').show();
            $('#Puser-icon').show();
        };

        function validateForm(event) {
    event.preventDefault(); // Prevent the form from submitting by default

    var input = document.getElementById('PRename');
    var value = input.value.trim(); // Trim whitespace from the input value

    var errorSpan = document.getElementById('rename-error');

    if (value === '') {
        errorSpan.textContent = 'Please enter a valid name.'; // Display the error message
        return false; // Cancel form submission
    }

    var nameParts = value.split(' ').filter(part => part !== ''); // Split on whitespace and remove empty parts

    if (nameParts.length < 2) {
        errorSpan.textContent = 'Please enter both first name and last name.'; // Display the error message
        return false; // Cancel form submission
    }

    // Check if both names start with a letter
    var isValid = nameParts.every(part => /^[A-Za-z]/.test(part));

    if (!isValid) {
        errorSpan.textContent = 'Names should start with a letter.'; // Display the error message
        return false; // Cancel form submission
    } else {
        errorSpan.textContent = ''; // Clear the error message if it's not needed
    }

    // If the validation passes, you can proceed with form submission
    document.getElementById('rename-form').submit();
}

            window.addEventListener('scroll', function() {
                var toolsDiv = document.querySelector('#tools_div');
                var toolList = document.querySelector('.tool_list');
                var footer = document.getElementsByTagName('footer')[0];
                var header = document.getElementsByTagName('header')[0];
                var toolsDivHeight = toolsDiv.offsetHeight;
                var headerHeight = header.offsetHeight;
                var windowHeight = window.innerHeight;
                var footerTop = footer.offsetTop;
                var footerOffset = footerTop - windowHeight;
                var toolListOffsetTop = toolList.offsetTop;

                if (window.pageYOffset > footerOffset) {
                    toolsDiv.style.position = 'absolute';
                    toolsDiv.style.top = Math.max(headerHeight, footerTop - toolsDivHeight) + 'px';
                    toolsDiv.style.bottom = '0';
                    toolList.style.transform = 'translateY()';
                } else {
                    toolsDiv.style.position = 'fixed';
                    toolsDiv.style.top = headerHeight + 'px';
                    toolsDiv.style.bottom = '';
                    toolList.style.transform = 'translateY(' + (headerHeight - toolListOffsetTop) + 'px)';
                }

                // Apply smooth transition
                toolsDiv.style.transition = 'transform 0.3s ease-out';
            });
        });

        function w3_open() {
            document.getElementsByClassName("workarea")[0].style.marginLeft = "auto";
            document.getElementById("tools_div").style.transition = '1s';
            document.getElementById("sidebar-tongue").style.transition = '1s';
            document.getElementsByClassName("workarea")[0].style.transition = '1s';
            document.getElementsByClassName("workarea")[0].style.marginLeft = "10%";
            document.getElementById("tools_div").style.marginLeft = '0';
            document.getElementById("sidebar-tongue").style.marginLeft = '13.5%';
            document.getElementById("sidebar-tongue").textContent = "<";
            document.getElementById("sidebar-tongue").style.boxShadow = "none";
        }

        function w3_close() {
            document.getElementById("sidebar-tongue").style.transition = '1s';
            document.getElementById("tools_div").style.transition = '1s';
            document.getElementsByClassName("workarea")[0].style.marginLeft = "0";
            document.getElementById("sidebar-tongue").textContent = ">";
            document.getElementById("tools_div").style.marginLeft = "-13.9%";
            document.getElementById("sidebar-tongue").style.marginLeft = '0';
        }
    </script>
</head>

<body>
    <header>
        <div class="header-container">
            <div class="flex-parent">
                <div class="header_logo">
                    <img src="../LOGO.png">
                    <div>Learniverse</div>
                </div>
                <div class="header_nav">
                    <nav id="navbar" class="nav__wrap collapse navbar-collapse">
                        <ul class="nav__menu">
                            <li>
                                <a href="../index.php">Home</a>
                            </li>
                            <li>
                                <a href="../community.php">Community</a>
                            </li>
                            <li class="active">
                                <a href="../workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <?php
                require_once __DIR__ . '../../vendor/autoload.php';

                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;

                $data = array(
                    "email" => $_SESSION['email']
                );

                $fetch = $Usercollection->findOne($data);
                //$googleID = $fetch['google_user_id'];

                ?>
                <div class="dropdown">
                    <button class="dropdown-button">
                        <i class="fas fa-user" id='Puser-icon'> </i>
                        <?php echo ' ' . $fetch['firstname']; ?></button>
                    <ul class="Pdropdown-menu">
                        <li class='editName center'>
                            <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
                            <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
                            <form id='rename-form' class='rename-form' method='POST' action='updateName.php?q=workspace.php' onsubmit="return validateForm(event)" ;>
                                <input type='text' id='PRename' name='Rename' required value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                                <span id='rename-error' style='color: red;'></span><br>
                                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
                            </form>
                        </li>
                        <li class='center'>Username: <?php echo $fetch['username']; ?></li>
                        <li class='center'><?php echo $fetch['email']; ?></li>
                        <hr>
                        <li><a href='reset.php?q=workspace.php'><i class='far fa-edit'></i> Change password</a></li>
                        <li onclick="customerSupport()"><a href='#'><i class='far fa-question-circle'></i> Customer Support</a></li>
                        <hr>
                        <li><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main>
     <ul class="tool_list">
                <li class="tool_item">
                    <a href="../workspace.php"> Calendar & To-Do
                    </a>
                </li>
                <li class="tool_item">
                    <a href="../theFiles.php"> My Files</a>
                </li>
                <li class="tool_item">
                    <a href="../quizes/index.php"> Quizzes</a>
                </li>
                <li class="tool_item">
                    <a href="../flashcard.php"> Flashcards</a>
                </li>
                <li class="tool_item">
                    <a href="../summarization/summarization.php"> Summarization</a>
                </li>
                <li class="tool_item">
                    <a href="../studyplan.php"> Study Plan</a>
                </li>
                <li class="tool_item"><a href="../Notes/notes.php">
                        Notes</a>
                </li>
                <li class="tool_item">
                    <a href="../pomodoro.php">
                        Pomodoro</a>
                </li>
                <li class="tool_item"><a href="../gpa.php">
                        GPA Calculator</a>
                </li>
                <li class="tool_item"><a href="../sharedspace.php">
                        Shared spaces</a></li>
                <li class="tool_item"><a href="../meetingroom.php">
                        Meeting Room</a>
                </li>
                <li class="tool_item"><a href="../community.php">
                        Community</a>
                </li>
            </ul>
        </div>

        <div class="workarea">

            <div class="workarea_item" style="flex:1;">
            <!-- <h1>Empty</h1> -->

            <div id="flashcards-container">
   
        <!-- Flashcards will be generated by PHP -->
        <?php
        // Sample JSON data (replace this with your actual JSON data passed via URL)


        if (!empty($flashcardsData['success'])) {
            // Loop through each flashcard and display it
            foreach ($flashcardsData['success'] as $card) {
                echo '<div class="flashcard-container">
                <button class="edit-btn">
                  <img src="../Notes/edit.svg" alt="Edit flashcard" width="15px" height="15px">
                </button>
                <div class="flashcard">
                        <div class="card" data-id="' . $card['cardNumber'] . '">
                        <div class="card-face front">' . htmlspecialchars($card['answer']) . '</div>
                            <div class="card-face back">' . htmlspecialchars($card['content']) . '</div>
                        </div>
                      </div>
                </div>';
            }
        }
        ?>
    </div>

   <div class="button">
   <a  style="margin: 0 10px;" class="disabled" href="../flashcard.php">
       
       <i class="fa-solid fa-wand-magic-sparkles"></i>
         &nbsp; Return to Flashcards
       </a> 
   <a  style="margin: 0 10px;" class="disabled" href="quiz.php?data=<?php echo urlencode(json_encode($flashcardsData)); ?>&subject=<?php echo $subject; ?>">
       
        <i class="fa-solid fa-wand-magic-sparkles"></i>
          &nbsp; Start Learning
        </a> 
   </div>

    <script>
        document.querySelectorAll('.flashcard').forEach(function(flashcard) {
            flashcard.addEventListener('click', function() {
                flashcard.querySelector('.card').classList.toggle('is-flipped');
            });
        });

    </script>
            </div>
        </div>
    </main>
    <footer id="footer" style="margin-top: 28%;">

    <div id="editModal" class="editModal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Edit Flashcard</h2>
    <form id="edit-form" method="POST" action="editFlashcard.php">
        <input type="hidden" name="flashcardId" value="">
        <input type="text" name="question" placeholder="Question" required>
        <textarea name="answer" placeholder="Answer" required></textarea>
        <button type="submit">Save</button>
    </form>
  </div>


  <script>

const editBtn = document.querySelectorAll('.edit-btn');
const editModal = document.querySelector('.editModal');
const closeBtn = document.querySelector('.close');
const editForm = document.getElementById('edit-form');

editBtn.forEach(function(btn) {
    btn.addEventListener('click', function() {
        editModal.classList.add('active');
        const flashcard = btn.closest('.flashcard-container');
        const question = flashcard.querySelector('.front').textContent;
        const answer = flashcard.querySelector('.back').textContent;
        const flashcardId = flashcard.querySelector('.card').getAttribute('data-id');
        editForm.querySelector('input[name="question"]').value = question;
        editForm.querySelector('textarea[name="answer"]').value = answer;
        editForm.querySelector('input[name="flashcardId"]').value = flashcardId;
    });
});

closeBtn.addEventListener('click', function() {
    editModal.classList.remove('active');
});

editForm.addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(editForm);
    const question = formData.get('question');
    const answer = formData.get('answer');
    const flashcardId = formData.get('flashcardId');
    const subjectName = "<?php echo $subject; ?>".trim();

    const data = {
        subjectName,
        cardNumber: flashcardId,
        content: answer,
        answer: question
    };
console.log(data);
    $.ajax({
        url: 'updateFlashcard.php',
        method: 'POST',
        data,
        success: async function(response) {
    document.getElementById('edit-form').reset(); 
    editModal.classList.remove('active'); 

    console.log('just response', response); 
    const data = response.data.subjects.find(subject => subject.subjectName === subjectName);
    const flashcards = data.flashcards;
    console.log({data,  flashcards  })


    if (response.success) {
        await Swal.fire({
            icon: 'success',
            title: 'Flashcard updated successfully.',
            showConfirmButton: false,
            timer: 1500
        });
        window.location.href = 'displayFlashcards.php?data=' + JSON.stringify({ success: flashcards }) + '&subjectName=' + subjectName;

    } else {
        alert(response.message || 'Failed to update flashcard.');
    }
},
    error: function(xhr, status, error) {
    console.error("Error: ", status, error);
    alert('An error occurred while updating the flashcard.'); 
}

    });

});
  </script>

</div>

<div id="copyright">Learniverse &copy; 2023</div>
</footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>

    <script>

function startQuiz(datad) {
window.location.href = '/flashcard/quiz.php?data=' + datad;
}
    </script>
</body>

</html>