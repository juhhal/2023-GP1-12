<!DOCTYPE html>

<?php
require_once '../vendor/autoload.php';

session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// retrieve the todo list of this user
$userEmail = $_SESSION['email'];
$query = new MongoDB\Driver\Query(['user_email' => $userEmail]);
$cursor = $manager->executeQuery('Learniverse.doc', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);

echo '<script>console.log(' . json_encode($result_json) . ');</script>';
$FOLDERS = []; 
$FOLDERS = json_decode(json_encode($result_json), true);

?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Files</title>
  <link rel="stylesheet" href="../theFiles.css">
  <link rel="stylesheet" href="../header-footer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <link rel="apple-touch-icon" sizes="180x180" href="../favicon_io/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../favicon_io/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../favicon_io/favicon-16x16.png">
  <link rel="manifest" href="../favicon_io/site.webmanifest">
  <link rel="stylesheet" href="index.css">
  <script src="../jquery.js"></script>
  <script src="https://cdn.tiny.cloud/1/jqchvmrvo8t50p8bodpapx40rcckse9f6slian9li7d12hvs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
      tinymce.init({
        selector: '#mytextarea',
        menubar: false,
        toolbar: 'undo redo | bold italic underline | numlist bullist | forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent',
        // add colors to text color selector

        plugins: 'lists colorpicker',
      });
  </script>
  <script src="../js/sweetalert2.all.min.js"></script>


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

      sidebarTongue?.addEventListener('click', function(event) {
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

    function newFolder() {
    Swal.fire({
      title: 'Enter your Notebook Name',
      input: 'text',
      showCancelButton: true,
      inputValidator: async (value) => {
        if (!value) {
          return 'You need to enter something!';
        } else {
          await $.ajax({
            url: "addFolder.php",
            method: "POST",
            data: {
              name: value
            },
            success: async function(res) {
              const addfolderRes = JSON.parse(res);
              if (addfolderRes?.error) {
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: addfolderRes.error,
                });
              } else {
                await Swal.fire({
                  icon: 'success',
                  title: 'Folder added successfully',
                  showConfirmButton: false,
                  timer: 1500
                });
                window.location.reload();
              }
            },
          });
        }
      }
    });
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
        <li class="tool_item">
          <a href="/workspace.php"> Calendar & To-Do
          </a>
        </li>
        <li class="tool_item">
          <a href="/theFiles.php"> My Files</a>
        </li>
        <li class="tool_item">
        <a href="/quizes/"> Quiz
          </a>
        </li>
        <li class="tool_item">
        <a href="/flashcard"> Flashcard
          </a>
        </li>
        <li class="tool_item">
        <a href="/summarization/summarization.php"> Summarization
          </a>
        </li>
        <li class="tool_item"><a href="/studyplan.php">
        Study Planner</a>
                  </li>
        <li class="tool_item"><a href="/Notes/notes.php">
            Notes</a>
        </li>
        <li class="tool_item">
          <a href="/pomodoro.php">
            Pomodoro</a>
        </li>
        <li class="tool_item"><a href="/gpa.php">
            GPA Calculator</a>
        </li>
        <li class="tool_item"><a href="/sharedspace.php">
        Shared spaces</a>
                 </li>
        <li class="tool_item">
          Meeting Room
        </li>
        <li class="tool_item"><a href="/community.php">
            Community</a>
        </li>
      </ul>
    </div>

    <div class="workarea">
      <nav>
        <!-- // if there the folder array length is 0 don't show new note -->
        <?php if (count($FOLDERS) > 0) { ?>
      <a href="notes.php?folder=<?php echo urlencode($_GET['folder'] ?? $FOLDERS[0]['name']); ?>&mode=create" style="color: black;">
          <img src="file-circle-plus-solid.svg" /> New Note
      </a>
      <?php } ?>

        <!-- <a href="fetchHome.php">
                <img src="house-solid.svg" /> Dashboard </a> -->
        <button onclick="newFolder()" style="color: black; cursor: pointer;">
          <img src="folder-plus.svg" /> New Folder </button>

          <?php foreach ($FOLDERS as $folderName => $folder) { ?>
    <div class="eachFolder" id="folder_<?php echo $folder['name']; ?>">
        <a href="#" class="folderHeader" style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            <span style="display: flex; align-items: center;">
                <img src="folder-solid.svg" />
                <span><?php echo $folder['name']; ?></span>
            </span>
                <img class="deleteFolder icon" src="trash.svg" onclick="deleteFolder('<?php echo $folder['name']; ?>')" style="position: absolute; right:0; width: 20px; height: 20px; padding-right: 0; margin-right: 15px;" />
                <img class="icon" src="plus.svg" onclick="window.location.href='notes.php?folder=<?php echo $folder['name']; ?>&mode=create'" style="position: absolute; right: 30px; width: 20px; height: 20px; padding-right: 0; margin-right: 15px;" />
        </a>

        <div class="folderContent">
            <?php if (!empty($folder['notes'])): ?>
                <!-- Print titles of notes in this folder -->
                <?php foreach ($folder['notes'] as $note): ?>
                   <div class="note" id="note_<?php echo $note['id']; ?>">
                        <a href="notes.php?folder=<?php echo $folder['name']; ?>&noteId=<?php echo $note['id']; ?>&mode=view"><?php echo $note['title']; ?></a>
                        <div class="actions">
                            <img src="trash.svg" style="padding: 0; cursor: pointer; width: 20px; height: 20px; padding-right: 0;" onclick="deleteNote('<?php echo $folder['name']; ?>', '<?php echo $note['id']; ?>')">
                            <a href="notes.php?folder=<?php echo $folder['name']; ?>&noteId=<?php echo $note['id']; ?>&mode=edit" style="margin:0 !important;">
                                <img src="edit.svg" style="padding: 0; width: 20px; height: 20px;">
                            </a>
                        </div>
                   </div>
                <?php endforeach; ?>
            <?php else: ?>
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
            <input name="title" type="text" placeholder="Note Title" id="title" class="title-input" required 
              value="<?php
              $noteId = $_GET['noteId'];
              $folderName = $_GET['folder'];
              $note = ($folder = array_filter($FOLDERS, function($item) use ($folderName) { return $item['name'] === $folderName; })) ? current($folder)['notes'][array_search($noteId, array_column(current($folder)['notes'], 'id'))] : null;
              echo $note['title'];
              ?>" />
            <textarea id="mytextarea">
              <?php
              $noteId = $_GET['noteId'];
              $folderName = $_GET['folder'];
              $note = ($folder = array_filter($FOLDERS, function($item) use ($folderName) { return $item['name'] === $folderName; })) ? current($folder)['notes'][array_search($noteId, array_column(current($folder)['notes'], 'id'))] : null;
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
        $note = ($folder = array_filter($FOLDERS, function($item) use ($folderName) { return $item['name'] === $folderName; })) ? current($folder)['notes'][array_search($noteId, array_column(current($folder)['notes'], 'id'))] : null;
        ?>
        <div id="note_<?php echo $noteId; ?>" class="noteView">
        <div>
        <span class="note-date"><?php echo $note['date']; ?></span>
        <img src="edit.svg" onclick="window.location.href='notes.php?folder=<?php echo $folderName; ?>&noteId=<?php echo $noteId; ?>&mode=edit'" style="padding: 0; float: right; cursor: pointer;" width="20px" height="20px">
        <img src="document.svg" class="pdf-icon" style="padding: 0; float: right; cursor: pointer; padding-right: 10px;" width="20px" height="20px" />
        </div>
        <span class="note-folder-name">Folder Name: <?php echo $folderName ?></span>
          <h2 class="note-title">Title: <?php echo $note['title']; ?></h2>
          <div class="note-content"><?php echo $note['content']; ?></div>
        </div>
      <?php } ?>

      </div>
    </div>
  </main>
  <footer id="footer" style="margin-top: 7%;">
    <div id="copyright">Learniverse &copy; 2024</div>
</footer>

  <div role="button" id="sidebar-tongue" style="margin-left: 0;">
    &gt;
  </div>
</body>
<script src="../jquery.js"></script>

<script>
  function deleteNote(folder, noteid) {
    console.log(folder, noteid);
    Swal.fire({
      title: 'Are you sure?',
      text: 'You won\'t be able to revert this!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "deleteNote.php",
          method: "POST",
          data: {
            folderName: folder,
            noteid
          },
          success: function(res) {
            document.querySelector(`#note_${noteid}`).style.display = 'none';
          }
        });
      }
    });
  }



  function deleteFolder(e) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "deleteFolder.php",
            method: "POST",
            data: {
              name: e
            },
            success: function(res) {
              document.querySelector(`#folder_${e}`).style.display = 'none';
              window.location.href = 'notes.php';
            }
          });
        }
      });
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
        Swal.fire({
          icon: 'error',
          title: 'Please fill in all fields',
          showConfirmButton: false,
          timer: 1500
        });
        return;
      }
      const folderName = '<?php echo isset($_GET['folder']) ? htmlspecialchars($_GET['folder']) : ''; ?>';
      $.ajax({
        url: "addNote.php",
        method: "POST",
        data: {
          title: $('#title').val(),
          content: tinymce.get('mytextarea').getContent(),
          folderName: folderName
        },
        success: async function(res) {
          const data = JSON.parse(res);
          const noteId = data.noteId;
          await Swal.fire({
            icon: 'success',
            title: 'Note added successfully',
            showConfirmButton: false,
            timer: 1500
          });
          window.location.href = `notes.php?folder=<?php echo $_GET['folder']; ?>&noteId=${noteId}&mode=view`;
        }
      });
    });

    $('#editNoteform').submit(function(e) {
      e.preventDefault();
      if ($('#title').val() === '' || tinymce.get('mytextarea').getContent() === '') {
        Swal.fire({
          icon: 'error',
          title: 'Please fill in all fields',
          showConfirmButton: false,
          timer: 1500
        });
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
          folderName: folder,
          noteId: noteId
        },
        success: async function(res) {
          const data = JSON.parse(res);
          const noteId = data.noteId;
          await Swal.fire({
            icon: 'success',
            title: 'Note edited successfully',
            showConfirmButton: false,
            timer: 1500
          });
          window.location.href = `notes.php?folder=${folder}&noteId=${noteId}&mode=view`;
        }
      });
    });
  });

  window.jsPDF = window.jspdf.jsPDF;

  document.querySelector('.pdf-icon').addEventListener('click', function() {
    const doc = new jsPDF({
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
    });

    const content = document.querySelector('.note-content p').textContent;
    const noteDate = document.querySelector('.note-date').textContent;
    const noteTitle = document.querySelector('.note-title').textContent;

    let yPos = 10;

    doc.setFontSize(16); 
    doc.text(noteTitle, 10, yPos);
    yPos += 10; 

    doc.setFontSize(12); 
    doc.text(noteDate, 10, yPos);
    yPos += 10; 

    doc.setFontSize(12); 
    let contentLines = doc.splitTextToSize(content, 180); 
    doc.text(contentLines, 10, yPos);

    doc.save('notes.pdf');
});

</script>



</html>