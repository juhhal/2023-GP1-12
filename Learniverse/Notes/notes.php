<!DOCTYPE html>

<?php
require_once '../vendor/autoload.php';

session_start();
error_reporting(0);
ini_set('display_errors', 1);

//print_r($_SESSION);
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// retrieve the todo list of this user
$query = new MongoDB\Driver\Query(array('email' => $_SESSION['email']));
$cursor = $manager->executeQuery('Learniverse.users', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true)[0];

echo '<script>console.log(' . json_encode($result_json) . ');</script>';
$FOLDERS = [];
$FOLDERS = is_string($result_json['folders']) ? json_decode($result_json['folders'], true) : $result_json['folders'];
$a = $result_json['_id'];
//echo $a['$oid'];
//print_r($FOLDERS);
$noteNumber = "";
// if (isset($_GET['new'])) {



//   $val = ((int)end($FOLDERS[$_GET['folder']]) + 1);
//   $noteNumber = $a['$oid'] . "_" . $_GET['folder'] . "_" . $val . ".php";
//   copy("index.php", $a['$oid'] . "_" . $_GET['folder'] . "_" . $val . ".php");

//   $arr = get_object_vars($FOLDERS[$_GET['folder']]);
//   $arr['_' . $val] =  $val;
//   $FOLDERS[$_GET['folder']] = $arr;

//   //print_r($FOLDERS);

//   $bulk = new MongoDB\Driver\BulkWrite;


//   $bulk->update(
//     ['email' => $_SESSION['email']],
//     ['$set' => ['folders' => json_encode($FOLDERS)]],
//     ['multi' => false, 'upsert' => false]
//   );



//   $result = $manager->executeBulkWrite('Learniverse.users', $bulk);
// } else if (isset($_GET['noteNumber'])) {
//   $noteNumber = $a['$oid'] . "_" . $_GET['folder'] . "_" . $_GET['noteNumber'] . ".php";
// }

?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Notes</title>
  <link rel="stylesheet" href="../theFiles.css">
  <link rel="stylesheet" href="../header-footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="apple-touch-icon" sizes="180x180" href="../favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../favicon_io/favicon-16x16.png">
  <link rel="manifest" href="../favicon_io/site.webmanifest">
  <link rel="stylesheet" href="index.css">
  <script src="../jquery.js"></script>
  <script src="https://cdn.tiny.cloud/1/no-origin/tinymce/6.7.2-32/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector: '#mytextarea',
      menubar: false,
      toolbar: 'undo redo | bold italic underline | numlist bullist | forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent',
      // add colors to text color selector

      plugins: 'lists colorpicker',
    });
  </script>


  <!-- PROFILE STYLESHEET -->
  <link rel="stylesheet" href="../profile.css">

  </script>
  <!-- SHOUQ SECTION: -->
  <script type='text/javascript'>
    $(document).ready(function() {
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
      sidebarTongue?.addEventListener('mouseenter', function(event) {
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
        require '../jwt.php';

        // Create a MongoDB client
        $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

        // Select the database and collection
        $database = $connection->Learniverse;
        $Usercollection = $database->users;

        $data = array(
          "email" => $_SESSION['email']
        );

        $fetch = $Usercollection->findOne($data);
        // $googleID = $fetch->google_user_id;

        $headers = array(
          'alg' => 'HS256',
          'typ' => 'JWT'
        );
        $payload = array(
          'email' => $fetch['email'],
          'exp' => (time() + 36000)
        );

        $jwttoken = generate_jwt($headers, $payload);
        ?>
        <div class="dropdown">
          <button class="dropdown-button">
            <i class="fas fa-user" id='Puser-icon'> </i>
            <?php echo $fetch['firstname']; ?></button>
          <ul class="Pdropdown-menu">
            <li class='editName center'>
              <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
              <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
              <form id='rename-form' class='rename-form' method='POST' action='../updateName.php?q=thefiles.php' onsubmit="return validateForm(event)">
                <input type='text' id='PRename' name='Rename' required value='<?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?>'><br>
                <span id='rename-error' style='color: red;'></span><br>
                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
              </form>
            </li>
            <li class='center'>Username: <?php echo $fetch['username']; ?></li>
            <li class='center'><?php echo $fetch['email']; ?></li>
            <hr>

            <?php //if ($googleID === null) {
            echo "<li><a href='../reset.php?q=thefiles.php'><i class='far fa-edit'></i> Change password</a></li>";
            ?>

            <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
            <hr>
            <li id="logout"><a href='../logout.php'><i class='fas fa-sign-out-alt'></i> Sign out </a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div id="tools_div">
      <ul class="tool_list">
        <li class="tool_item"><a href="../workspace.php"><img src="../images/calendar.png">
            Calendar & To-Do </li>
        <li class="tool_item"><a href="../theFiles.php?q=My Files"><img src="../images/file.png">
            My Files</a>
        </li>
        <li class="tool_item"><img src="../images/quiz.png">
          Quiz
        </li>
        <li class="tool_item"><img src="../images/flash-cards.png">
          Flashcard
        </li>
        <li class="tool_item"><img src="../images/summarization.png">
          Summarization
        </li>
        <li class="tool_item"><img src="../images/study-planner.png">
          Study Planner
        </li>
        <li class="tool_item"><a href="notes.php"><img src="../images/notes.png">
          Notes</a>
        </li>
        <li class="tool_item"><a href="../pomodoro.php"><img src="../images/pomodoro-technique.png">
            Pomodoro</a>
        </li>
        <li class="tool_item"><a href="../gpa.php"><img src="../images/gpa.png">
                        GPA Calculator</a>
                </li>
        <li class="tool_item"><img src="../images/collaboration.png">
          Shared spaces
        </li>
        <li class="tool_item"><img src="../images/meeting-room.png">
          Meeting Room
        </li>
        <li class="tool_item"><a href="../community.php"><img src="../images/communities.png">
          Community
        </li>
      </ul>
    </div>

    <div class="workarea">
      <nav>
        <a href="notes.php?folder=<?php echo urlencode($_GET['folder']); ?>&mode=create" style="color: black;">
          <img src="file-circle-plus-solid.svg" /> New Note
        </a>

        <!-- <a href="fetchHome.php">
                <img src="house-solid.svg" /> Dashboard </a> -->
        <button onclick="newFolder()" style="color: black; cursor: pointer;">
          <img src="folder-plus.svg" /> New Folder </button>

        <?php foreach ($FOLDERS as $folderName => $folder) { ?>
          <div class="eachFolder" id="folder_<?php echo $folderName; ?>">
            <a href="#" class="folderHeader" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
              <span style="display: flex; align-items: center;">
                <img src="folder-solid.svg" />
                <span><?php echo $folderName; ?></span>
              </span>
              <?php if ($folderName != 'Notes') { ?>
                <img class="deleteFolder" src="trash.svg" onclick="deleteFolder('<?php echo $folderName; ?>')" style="position: absolute; right:0; " />
                <img src="plus.svg" onclick="window.location.href='notes.php?folder=<?php echo $folderName; ?>&mode=create'" style="position: absolute; right: 30px;" />
              <?php } ?>
            </a>

            <div class="folderContent">
              <?php if (!empty($folder['notes'])) : ?>
                <!-- Print titles of notes in this folder -->
                <?php foreach ($folder['notes'] as $note) : ?>
                  <div class="note">
                    <a href="notes.php?folder=<?php echo $folderName; ?>&noteId=<?php echo $note['id']; ?>&mode=view">
                      <?php echo $note['title']; ?>
                      <div class="actions">
                        <img src="trash.svg" style="padding: 0;" onclick="deleteNote('<?php echo $folderName; ?>', '<?php echo $note['id']; ?>')">
                        <a href="notes.php?folder=<?php echo $folderName; ?>&noteId=<?php echo $note['id']; ?>&mode=edit">
                          <img src="edit.svg" style="padding: 0;">
                        </a>
                      </div>
                    </a>
                  </div>
                <?php endforeach; ?>
              <?php else : ?>
                <p style="padding: 0 20px">No notes available.</p>
              <?php endif; ?>
            </div>


          </div>
        <?php } ?>




        <div id="previewSelected"></div>
      </nav>
      <div id="create">
        <?php
        if (isset($_GET['mode']) && $_GET['mode'] === 'create') { ?>
          <span class="folderName">Folder: <?php echo $_GET['folder']; ?></span>
          <form id="addNoteForm" class="note-form">
            <input name="title" type="text" placeholder="Note Title" id="title" class="title-input" required />
            <textarea id="mytextarea"></textarea>
            <button type="submit" name="submit" class="save-note-btn">Save</button>
          </form>
        <?php } ?>
        <?php
        if (isset($_GET['mode']) && $_GET['mode'] === 'edit') { ?>
          <form id="editNoteform" class="note-form">
            <input name="title" type="text" placeholder="Note Title" id="title" class="title-input" required value="<?php
                                                                                                                    $noteId = $_GET['noteId'];
                                                                                                                    $folderName = $_GET['folder'];
                                                                                                                    $note = $FOLDERS[$folderName]['notes'][array_search($noteId, array_column($FOLDERS[$folderName]['notes'], 'id'))];
                                                                                                                    echo $note['title'];
                                                                                                                    ?>" />
            <textarea id="mytextarea">
              <?php
              // get content from selected note by id
              $noteId = $_GET['noteId'];
              $folderName = $_GET['folder'];
              $note = $FOLDERS[$folderName]['notes'][array_search($noteId, array_column($FOLDERS[$folderName]['notes'], 'id'))];
              echo $note['content'];
              ?>
            </textarea>
            <button type="submit" name="submit" class="save-note-btn">Save</button>
          </form>
        <?php } ?>


        <?php if (isset($_GET['mode']) && $_GET['mode'] === 'view' && isset($_GET['noteId'])) { ?>
          <?php
          $noteId = $_GET['noteId'];
          $folderName = $_GET['folder'];
          $note = $FOLDERS[$folderName]['notes'][array_search($noteId, array_column($FOLDERS[$folderName]['notes'], 'id'))];
          ?>
          <div id="note_<?php echo $noteId; ?>" class="noteView">
            <div>
              <span class="note-date"><?php echo $note['date']; ?></span>
              <img src="edit.svg" onclick="window.location.href='notes.php?folder=<?php echo $folderName; ?>&noteId=<?php echo $noteId; ?>&mode=edit'" style="padding: 0; float: right; cursor: pointer;" width="15px" height="15px">
            </div>
            <span class="note-folder-name">Folder Name: <?php echo $folderName ?></span>
            <h2 class="note-title">Title: <?php echo $note['title']; ?></h2>
            <div class="note-content"><?php echo $note['content']; ?></div>
          </div>
        <?php } ?>

      </div>
    </div>
  </main>
  <footer>
    <div class="footer-div" id="socials">
      <h4>Follow Us on Social Media</h4>

      <a href="https://twitter.com/learniversewebsite" target="_blank"><img src="../images/twitter.png" alt="@Learniverse"></a>

    </div>
    <div class="footer-div" id="contacts">
      <h4>Contact Us</h4>

      <a href="mailto:learniverse.website@gmail.com" target="_blank"><img src="../images/gmail.png" alt="learniverse.website@gmail.com"></a>

    </div>
    <img id="footerLogo" src="../LOGO.png" alt="Learniverse">
    <div id="copyright">Learniverse &copy; 2023</div>
  </footer>

  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>
</body>
<script src="../jquery.js"></script>

<script>
  function deleteNote(folder, noteid) {
    if (window.confirm('Are you sure you want to delete this note?')) {
      $.ajax({
        url: "deleteNote.php",
        method: "POST",
        data: {
          folder,
          noteid
        },
        success: function(res) {
          document.querySelector(`#note_${noteid}`).style.display = 'none';
        }
      });
    }
  }


  function deleteFolder(e) {
    if (window.confirm('Are you sure you want to delete this folder?')) {
      $.ajax({
        url: "deleteFolder.php",
        method: "POST",
        data: {
          name: e
        },
        success: function(res) {
          document.querySelector(`#folder_${e}`).style.display = 'none';
        }
      });
    }
  }

  function newFolder() {
    const val = prompt('Enter your Notebook Name')

    if (val) {
      $.ajax({
        url: "addFolder.php",
        method: "POST",
        data: {
          name: val
        },
        success: function(res) {
          console.log(res)
          window.location.reload()
        }
      })
    }
  }



  $(document).ready(function() {

    var folderContents = $('.folderContent');
    folderContents.hide();
    $('.eachFolder a').on('click', function() {
      var content = $(this).next();
      folderContents.hide().removeClass('active');
      content.slideToggle().toggleClass('active');
    });


    $('#addNoteForm').submit(function(e) {
      e.preventDefault();
      if ($('#title').val() === '' || tinymce.get('mytextarea').getContent() === '') {
        alert('Please fill in all fields');
        return;
      }
      $.ajax({
        url: "addNote.php",
        method: "POST",
        data: {
          title: $('#title').val(),
          content: tinymce.get('mytextarea').getContent(),
          folder: '<?php echo $_GET['folder']; ?>',
        },
        success: function(res) {
          const data = JSON.parse(res);
          const noteId = data.noteId;
          alert('Note added successfully');
          window.location.href = `notes.php?folder=<?php echo $_GET['folder']; ?>&noteId=${noteId}&mode=view`;
        }
      });
    });

    $('#editNoteform').submit(function(e) {
      e.preventDefault();
      if ($('#title').val() === '' || tinymce.get('mytextarea').getContent() === '') {
        alert('Please fill in all fields');
        return;
      }
      const folder = '<?php echo isset($_GET['folder']) ? htmlspecialchars($_GET['folder']) : ''; ?>';
      const noteId = '<?php echo isset($_GET['noteId']) ? htmlspecialchars($_GET['noteId']) : ''; ?>';

      $.ajax({
        url: "editNote.php",
        method: "POST",
        data: {
          title: $('#title').val(),
          content: tinymce.get('mytextarea').getContent(),
          folder: folder,
          noteId: noteId
        },
        success: function(res) {
          const data = JSON.parse(res);
          const noteId = data.noteId;
          alert('Note edited successfully');
          window.location.href = `notes.php?folder=${folder}&noteId=${noteId}&mode=view`;
        }
      });
    });
  });
</script>

</html>