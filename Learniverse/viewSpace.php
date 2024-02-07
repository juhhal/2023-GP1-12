<?php
require "session.php";
//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
// MongoDB query
$filter = ['email' => $_SESSION['email']];
$query = new MongoDB\Driver\Query($filter);
$result = $manager->executeQuery("Learniverse.users", $query);
$user = $result->toArray()[0];

$filter = ['spaceID' => new MongoDB\BSON\Regex($_GET['space'])];
$query = new MongoDB\Driver\Query($filter);

// MongoDB collection name
$collectionName = "Learniverse.sharedSpace";

// Execute the query
$result = $manager->executeQuery($collectionName, $query);
$space = $result->toArray()[0];
if ($_SESSION['email'] != $space->admin && !in_array($_SESSION['email'], $space->members)) {
    header("Location:sharedSpace.php");
}
//retrieve space admin info
$query = new MongoDB\Driver\Query(['email' => $space->admin]);
$result = $manager->executeQuery("Learniverse.users", $query);
$admin = $result->toArray()[0];

?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <title>Shared space</title>
    <link rel="stylesheet" href="viewSpaceCSS.css">
    <link rel="stylesheet" href="header-footer.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- calendar -->
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Include FullCalendar JS & CSS library -->
    <link href="js/fullcalendar/lib/main.css" rel="stylesheet" />
    <script src="js/fullcalendar/lib/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                views: {
                    dayGridMonth: { // name of view
                        titleFormat: {
                            year: 'numeric',
                            month: 'long',
                        }
                        // other view-specific options here
                    }
                },
                initialView: 'dayGridMonth',
                editable: true,
                height: 650,
                events: 'fetchEvents.php?spaceID=<?php echo $space->spaceID ?>',

                selectable: true,
                select: async function(start, end, allDay) {
                    const {
                        value: formValues
                    } = await Swal.fire({
                        title: 'Add Event',
                        confirmButtonText: 'Submit',
                        showCloseButton: true,
                        showCancelButton: true,
                        html: '<input id="swalEvtTitle" class="swal2-input" placeholder="Enter title">' +
                            '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description"></textarea>' +
                            '<div class="rem">Email Reminder &nbsp;<label class="switch"><input id="swalEvtReminder" type="checkbox" value="reminder"><span class="slider round"></span></label></div>',
                        focusConfirm: false,
                        preConfirm: () => {
                            return [
                                document.getElementById('swalEvtTitle').value,
                                document.getElementById('swalEvtDesc').value,
                                document.getElementById('swalEvtReminder').checked

                            ]
                        }
                    });

                    if (formValues) {
                        // Add event
                        fetch("eventHandler.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({
                                    request_type: 'addEvent',
                                    start: start.startStr,
                                    end: start.endStr,
                                    event_data: formValues,
                                    spaceID: '<?php echo $space->spaceID ?>'
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == 1) {
                                    Swal.fire('Event added successfully!', '', 'success');
                                } else {
                                    Swal.fire(data.error, '', 'error');
                                }

                                // Refetch events from all sources and rerender
                                calendar.refetchEvents();
                            })
                            .catch(console.error);
                    }
                },

                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    // change the border color
                    info.el.style.borderColor = 'white';

                    Swal.fire({
                        title: info.event.title,
                        //text: info.event.extendedProps.description,
                        icon: 'info',
                        html: '<p>' + info.event.extendedProps.description + '</p>',
                        showCloseButton: true,
                        showCancelButton: false,
                        showDenyButton: true,
                        cancelButtonText: 'Close',
                        confirmButtonText: 'Delete',
                        denyButtonText: 'Edit',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Delete event
                            fetch("eventHandler.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        request_type: 'deleteEvent',
                                        event_id: info.event.id,
                                        spaceID: '<?php echo $space->spaceID ?>'
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status == 1) {
                                        Swal.fire('Event deleted successfully!', '', 'success');
                                    } else {
                                        Swal.fire(data.error, '', 'error');
                                    }

                                    // Refetch events from all sources and rerender
                                    calendar.refetchEvents();
                                })
                                .catch(console.error);
                        } else if (result.isDenied) {
                            // Edit and update event
                            Swal.fire({
                                title: 'Edit Event',
                                html: '<input id="swalEvtTitle_edit" class="swal2-input" placeholder="Enter title" value="' + info.event.title + '">' +
                                    '<textarea id="swalEvtDesc_edit" class="swal2-input" placeholder="Enter description">' + info.event.extendedProps.description + '</textarea>' +
                                    '<div class="rem">Email Reminder &nbsp;<label class="switch"><input id="swalEvtRem_edit" type="checkbox" ' + (info.event.extendedProps.reminder === true ? 'checked' : '') + '><span class="slider round"></span></label></div>',
                                focusConfirm: false,
                                confirmButtonText: 'Submit',
                                preConfirm: () => {
                                    return [
                                        document.getElementById('swalEvtTitle_edit').value,
                                        document.getElementById('swalEvtDesc_edit').value,
                                        document.getElementById('swalEvtRem_edit').checked

                                    ]
                                }
                            }).then((result) => {
                                if (result.value) {
                                    // Edit event
                                    fetch("eventHandler.php", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json"
                                            },
                                            body: JSON.stringify({
                                                request_type: 'editEvent',
                                                start: info.event.startStr,
                                                end: info.event.endStr,
                                                event_id: info.event.id,
                                                event_data: result.value,
                                                spaceID: '<?php echo $space->spaceID ?>'
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status == 1) {
                                                Swal.fire('Event updated successfully!', '', 'success');
                                            } else {
                                                Swal.fire(data.error, '', 'error');
                                            }

                                            // Refetch events from all sources and rerender
                                            calendar.refetchEvents();
                                        })
                                        .catch(console.error);
                                }
                            });
                        } else {
                            Swal.close();
                        }
                    });
                }
            });

            calendar.render();
        });
    </script>
    <!-- SHOUQ SECTION: -->
    <script type='text/javascript'>
        $(document).ready(function() {

            $('#addMessage').submit(function(e) {
                // Prevent form submission
                e.preventDefault();

                // Get the form data
                var message = $('#message').val();
                var spaceID = $('#spaceID').val();

                // Send AJAX request to the server
                $.ajax({
                    url: 'addMessage.php',
                    type: 'POST',
                    data: {
                        message: message,
                        spaceID: spaceID
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.message === "faild") {
                            // display failure message
                            <?php
                            echo "showAlert(\"ERROR\", \"Something went wrong, try to write you message again.\");"
                            ?>
                        } else {
                            // Display the chat message
                            displayMessage(response.message);

                        }
                    }
                });
            });

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

        function showAlert(title, message) {
            Swal.fire({
                html: '<br><h1>' + title + '</h1>' +
                    '' +
                    '' +
                    '<p>' + message + '</p>' +
                    '' +
                    '',
                showCancelButton: false,
                showConfirmButton: false,
                cancelButtonText: 'Close',
                buttonsStyling: false,
                showCloseButton: true
            });
        }

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;

            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function showForm() {
            var form = $('.workarea_item form');
            form.toggle();
        }

        function displayMessage(messageData) {
            var messageContainer = document.getElementById("showMessages");
            var messageHTML = "<div class='chat userchat'><span class='username'>" + messageData.writtenBy + "</span><br><span class='data'>" + messageData.message + "</span><span class='date'>" + messageData.date + "</span></div>";
            messageContainer.innerHTML += messageHTML;
            document.getElementById("message").value = "";
        }

        function displayMessage(messageData) {
            var messageContainer = document.getElementById("showMessages");
            var messageElement = document.createElement("div");
            messageElement.classList.add("chat", "userchat");
            messageElement.innerHTML = "<span class='username'>" + messageData.writtenBy + "</span><span class='data'>" + messageData.message + "</span><span class='date'>" + messageData.date + "</span>";
            messageContainer.appendChild(messageElement);
            document.getElementById("message").value = "";
        }

        function reloadMessages() {
            $.ajax({
                url: 'reloadChat.php',
                type: 'GET',
                data: {
                    spaceID: '<?php echo $_GET['space']; ?>'
                },
                dataType: 'html',
                success: function(response) {
                    $('#showMessages').html(response);
                }
            });
        }
        setInterval(reloadMessages, 2000);
    </script>

</head>

<body>

    <header>
        <div class="header-container">
            <div class="flex-parent">
                <div class="header_logo">
                    <img src="LOGO.png">
                    <div>Learniverse</div>
                </div>
                <div class="header_nav">
                    <nav id="navbar" class="nav__wrap collapse navbar-collapse">
                        <ul class="nav__menu">
                            <li>
                                <a href="index.php">Home</a>
                            </li>
                            <li>
                                <a href="community.php">Community</a>
                            </li>
                            <li class="active">
                                <a href="Workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <?php
                require_once __DIR__ . '/vendor/autoload.php';

                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");
                $database = $connection->Learniverse;
                $Usercollection = $database->users;
                $data = array(
                    "email" => $_SESSION['email']
                );
                $fetch = $Usercollection->findOne($data);
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
                        <li><a href="reset.php?q=viewspace.php?space=<?php echo $_GET['space']; ?>"><i class='far fa-edit'></i> Change password</a></li>
                        <li><a href='#'><i class='far fa-question-circle'></i> Help </a></li>
                        <hr>
                        <li><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div id="tools_div">
            <ul class="tool_list">
                <li class="tool_item">
                    <a href="workspace.php"> Calendar & To-Do
                    </a>
                </li>
                <li class="tool_item">
                    <a href="theFiles.php?q=My Files"> My Files</a>
                </li>
                <li class="tool_item">
                    Quiz
                </li>
                <li class="tool_item">
                    Flashcard
                </li>
                <li class="tool_item">
                    Summarization
                </li>
                <li class="tool_item">
                    Study Planner
                </li>
                <li class="tool_item"><a href="Notes/notes.php">
                        Notes</a>
                </li>
                <li class="tool_item">
                    <a href="pomodoro.php">
                        Pomodoro</a>
                </li>
                <li class="tool_item"><a href="gpa.php">
                        GPA Calculator</a>
                </li>
                <li class="tool_item"><a href="sharedspace.php">
                        Shared spaces</a>
                </li>
                <li class="tool_item">
                    Meeting Room
                </li>
                <li class="tool_item"><a href="community.php">
                        Community</a>
                </li>
            </ul>
        </div>

        <div class="workarea">

            <div class="workarea_item">
                <h1><?php echo $space->name ?></h1>
                <!-- Tab links -->
                <div class="tabs">
                    <button class="tablinks" onclick="openTab(event, 'Feed')">Chat</button>
                    <button class="tablinks" onclick="openTab(event, 'Tasks')">Tasks</button>
                    <button class="tablinks" onclick="openTab(event, 'Files')">Files</button>
                    <button class="tablinks" onclick="openTab(event, 'Members')">Members <?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span>"; ?></button>
                </div>

                <!-- Tab content -->
                <div id="Feed" class="tabcontent">
                    <h2>Chat</h2>
                    <div id="showMessages"><?php $feeds = $space->feed;
                                            foreach ($feeds as $feed) {
                                                if ($feed->writtenBy == $user->username)
                                                    echo "<div class='chat userchat'><span class='username'>" . $feed->writtenBy . "</span><span class='data'>" . $feed->message . "</span><span class='date'>" . $feed->date . "</span></div>";
                                                else
                                                    echo "<div class='chat'><span class='username'>" . $feed->writtenBy . "</span><span class='data'>" . $feed->message . "</span><span class='date'>" . $feed->date . "</span></div>";
                                            } ?></div>
                    <form id="addMessage" method="post" action="addMessage.php">
                        <input id="spaceID" name="spaceID" value="<?php echo $_GET['space']; ?>" hidden>
                        <input required autofocus id="message" name="message" placeholder="Type a message">&nbsp; &nbsp;<button id="submitChat" type='submit'>Send</button>
                    </form>
                </div>

                <div id="Tasks" class="tabcontent">
                    <button id="addTaskBTN"><i class="fa-solid fa-plus"></i> Add Task</button>
                    <h3>Tasks</h3>
                    <select id="groupBY">
                        <option disabled selected>
                            Group By
                        </option>
                        <option>
                            Assignee
                        </option>
                    </select>
                    <div class="taskDiv">
                        <span class="taskHead">
                            <span class="taskName">Develop a Leave Space button for members in a space</span>
                            <span class="more"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                        </span>
                        <!-- <span class="creator">Creator: Alice</span> -->
                        <span class="taskInfo">
                            <span class="dueDate"><i class="fa-solid fa-calendar-days"></i> 2024-02-10</span>
                            <select class="assignee">
                                <option selected>Shouq Alotaibi</option>
                                <option>Anwar Bafadhl</option>
                                </option>
                            </select>
                        </span>
                    </div>
                    <div class="taskDiv">
                        <span class="taskHead">
                            <span class="taskName">Develop a Leave Space button for members in a space</span>
                            <span class="more"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                        </span>
                        <!-- <span class="creator">Creator: Alice</span> -->
                        <span class="taskInfo">
                            <span class="dueDate"><i class="fa-solid fa-calendar-days"></i> 2024-02-10</span>
                            <select class="assignee">
                                <option>Shouq Alotaibi</option>
                                <option selected>Anwar Bafadhl</option>
                                </option>
                            </select>
                        </span>
                    </div>
                    <div class="taskDiv">
                        <span class="taskHead">
                            <span class="taskName">Develop a Leave Space button for members in a space</span>
                            <span class="more"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                        </span>
                        <!-- <span class="creator">Creator: Alice</span> -->
                        <span class="taskInfo">
                            <span class="dueDate"><i class="fa-solid fa-calendar-days"></i> 2024-02-10</span>
                            <select class="assignee unassigned">
                                <option selected>Unassigned</option>
                                <option>Shouq Alotaibi</option>
                                <option>Anwar Bafadhl</option>
                                </option>
                            </select>
                        </span>
                    </div>

                    <div class="overlay" id="addTaskDiv">
                        <div class="modal">
                            <h2>Add a New Task</h2>
                            <form id="add-task-form" action="">
                                <div class="form-inputs">
                                    <input id="task-name-input" type="text" name="task_name" placeholder="Task name" autocomplete="off" maxlength="500" autofocus />
                                    <input id="task-description-input" type="text" name="description" placeholder="Description" autocomplete="off" />
                                </div>
                                <div class="extra-fields">
                                    <div>
                                        <input class="due-date-picker" type="date" />
                                        <select class="assignee-selector">
                                            <option value="none" selected>
                                                Unassigned
                                            </option>
                                            <option value="me">
                                                Shouq Alotaibi
                                            </option>
                                            <option value="Anwar">
                                                Anwar Bafadhl
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="cancel-submit-container">
                                    <button class="cancel-button" type="button">Cancel</button>
                                    <button class="add-task-submit-button" type="button" disabled>
                                        Add task
                                    </button>
                                </div>
                            </form>
                            <!-- <form id="addtask-form" method="post" action="addTask.php">
                                <input required type="text" id="taskDesc" name="taskDesc" placeholder="Task Name">
                                <input id="taskDue" name="taskDue" type="datetime-local" title="Set the deadline of this task"><br>
                                <button id="submitTaskBTN" type="submit">Add task</button> <button type="reset" onclick="resetAddTask()">Cancel</button>
                            </form> -->
                        </div>
                    </div>

                    <script>
                        var addTaskBTN = document.getElementById('addTaskBTN');
                        var overlayTask = document.getElementById('addTaskDiv');

                        addTaskBTN.addEventListener('click', function() {
                            overlayTask.style.display = 'flex';
                        });

                        overlayTask.addEventListener('click', function(event) {
                            if (event.target === overlayTask) {
                                overlayTask.style.display = 'none';
                            }
                        });
                    </script>
                </div>

                <div id="Files" class="tabcontent">
                    <h3>Files</h3>
                    <br>
                    <button id="uploadfiles" onclick="showForm()">upload File</button>
                    <form id="uploadNewFile" method="post" action="upload.php" style="display: none;">
                        <br>
                        <label>Upload a new file</label> <input id="newFile" name="newFile" type="file">
                        <button type="submit" class="formSubmitBTN">Upload</button>
                        <h3>OR</h3>
                    </form>
                    <form id="uploadExistingFile" method="post" action="upload.php" style="display: none;">
                        <label>Upload an existing file</label> <input id="existingFile" name="existingFile" type="file">
                        <button type="submit" class="formSubmitBTN">Upload</button>
                    </form>
                </div>

                <div id="Members" class="tabcontent">
                    <button id="spaceInviteBTN"><i class="fa-solid fa-user-plus"></i> Invite Members <?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span>"; ?></button>
                    <h3>Admin</h3>
                    <?php echo "<li id='adminName'><i title='admin' class='fa-solid fa-user-tie'></i> $admin->firstname $admin->lastname </li>" ?>
                    <h3>Members (<?php echo count($space->members) ?>)</h3>
                    <?php

                    echo "<ul class='memberList'>";

                    // Access individual members
                    foreach ($space->members as $member) {
                        $query = new MongoDB\Driver\Query(['email' => $member]);
                        $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                        $memberName = $memberCursor->toArray()[0];
                        $kickButton = null;
                        if ($space->admin === $_SESSION['email'])
                            $kickButton =  "<button class='kick'><i class='fa-solid fa-circle-exclamation'></i> kick</button>";
                        // print the member
                        echo "<div class='memberName'><li><i title='members' class='fa-solid fa-user'></i> $memberName->firstname $memberName->lastname  </li>";
                        echo "<div class='memberInfo'>Email: <span class='member-email'>$member</span> <form action='mailto:$member'><button title='Send an Email' type='submit'><i style='color:#faf7ff' class='fa-solid fa-paper-plane'></i></button></form> 
                               $kickButton</div></div>";
                    }

                    echo "</ul>";
                    ?>
                    <script>
                        // Get all the memberName elements
                        const memberNames = document.querySelectorAll('.memberName');

                        // Iterate over each memberName element
                        memberNames.forEach(function(memberName) {
                            // Find the corresponding memberInfo div
                            const memberInfo = memberName.querySelector('.memberInfo');
                            const memberEmail = memberInfo.querySelector('.member-email').textContent;

                            memberInfo.addEventListener('mouseover', function() {
                                memberInfo.style.display = 'block';
                            });

                            memberInfo.addEventListener('mouseout', function() {
                                memberInfo.style.display = 'none';
                            });

                            // Find the <li> element inside memberName
                            const listItem = memberName.querySelector('li');

                            // Add event listener for hover
                            listItem.addEventListener('mouseover', function() {
                                memberInfo.style.display = 'block';
                            });

                            listItem.addEventListener('mouseout', function() {
                                memberInfo.style.display = 'none';
                            });

                            // Add event listener for click
                            listItem.addEventListener('click', function() {
                                memberInfo.style.display = 'block';
                            });
                            if (<?php echo ($space->admin === $_SESSION['email']) ? 1 : 0; ?>) {
                                const kickBTN = memberInfo.querySelector('.kick');
                                kickBTN.addEventListener('click', function() {
                                    Swal.fire({
                                        title: 'Heads Up!',
                                        text: 'Are you sure you want to kick ' + memberEmail + '?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes',
                                        cancelButtonText: 'No',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                url: 'pendingMemberProcess.php',
                                                method: 'post',
                                                data: {
                                                    operation: 'kick',
                                                    member: memberEmail,
                                                    spaceid: '<?php echo $space->spaceID ?>',
                                                    spacename: '<?php echo $space->name ?>'
                                                }
                                            });
                                        }
                                    });
                                });
                            }
                        });
                    </script>

                    <!-- invite member div after clicking button -->

                    <div class="overlay" id="inviteDiv">
                        <div class="modal">
                            <h2>Invite New Members</h2>
                            <p class="guideline">Copy and send the following Code to invite your friends to this space!</p>
                            <input type="text" id="invitationCode" value="<?php echo $space->spaceID ?>" readonly>
                            <button id="copyButton"><i class="fa-regular fa-copy"></i> Copy</button>
                            <h3><?php if ($space->admin === $_SESSION['email'] && count($space->pendingMembers) > 0) {
                                    echo "<span class= 'pendingNotif'>" . count($space->pendingMembers) . "</span> Join Requests"; ?></h3>
                            <ul id="joinRequestsList">
                                <?php foreach ($space->pendingMembers as $member) { ?>
                                    <li>
                                        <?php echo "<span class='member-email'>$member</span>"; ?>
                                        <span>
                                            <button class="acceptButton">Accept <i class="fa-solid fa-check"></i></button>
                                            <button class="rejectButton">Reject <i class="fa-solid fa-xmark"></i></button>
                                        </span>
                                    </li>
                            <?php
                                    }
                                }
                            ?>
                            </ul>
                        </div>
                    </div>

                    <script>
                        var spaceInviteBTN = document.getElementById('spaceInviteBTN');
                        var overlay = document.getElementById('inviteDiv');
                        var invitationCode = document.getElementById('invitationCode');
                        var copyButton = document.getElementById('copyButton');
                        var acceptButtons = document.getElementsByClassName('acceptButton');
                        var rejectButtons = document.getElementsByClassName('rejectButton');
                        var joinRequestsList = document.getElementById('joinRequestsList');

                        spaceInviteBTN.addEventListener('click', function() {
                            overlay.style.display = 'flex';
                            copyButton.disabled = false;
                            copyButton.innerHTML = '<i class="fa-regular fa-copy"></i> Copy';

                            Array.from(acceptButtons).forEach(function(button) {
                                var listItem = button.parentNode.parentNode;
                                var member = listItem.querySelector('.member-email').textContent.trim();
                                button.addEventListener('click', function() {

                                    $.ajax({
                                        url: "pendingMemberProcess.php",
                                        method: "POST",
                                        data: {
                                            operation: "accept",
                                            member: member,
                                            spaceid: "<?php echo $space->spaceID ?>",
                                            spacename: "<?php echo $space->name ?>"
                                        },
                                        success: function(response) {
                                            // On successful response, remove the list item from the DOM
                                            joinRequestsList.removeChild(listItem);
                                            console.log(response); // Optional: Display the response in the console
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(error); // Optional: Log any errors to the console
                                        }
                                    });
                                });
                            });

                            Array.from(rejectButtons).forEach(function(button) {
                                var listItem = button.parentNode.parentNode;
                                var member = listItem.querySelector('.member-email').textContent.trim();
                                button.addEventListener('click', function() {

                                    $.ajax({
                                        url: "pendingMemberProcess.php",
                                        method: "POST",
                                        data: {
                                            operation: "reject",
                                            member: member,
                                            spaceid: "<?php echo $space->spaceID ?>",
                                            spacename: "<?php echo $space->name ?>"
                                        },
                                        success: function(response) {
                                            // On successful response, remove the list item from the DOM
                                            joinRequestsList.removeChild(listItem);
                                            console.log(response); // Optional: Display the response in the console
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(error); // Optional: Log any errors to the console
                                        }
                                    });
                                });
                            });
                        });

                        overlay.addEventListener('click', function(event) {
                            if (event.target === overlay) {
                                overlay.style.display = 'none';
                            }
                        });

                        copyButton.addEventListener('click', function() {
                            invitationCode.select();
                            document.execCommand('copy');

                            copyButton.textContent = 'Copied!';
                            copyButton.disabled = true;
                        });
                    </script>
                </div>
            </div>
            <div class="overview workarea_item">
                <div class="overview-container">
                    <div class="container calendar">
                        <div class="wrapper">

                            <!-- Calendar container -->
                            <div id="calendar"></div>

                        </div>
                    </div>
                    <div class="container chart">
                        <canvas id="myChart"></canvas>
                    </div>
                    <div class="updates-container">
                        <div id="updates">
                            <?php
                            foreach ($space->members as $member) {
                                echo $member;
                            }
                            ?>
                        </div>
                    </div>

                    <?php
                    $completedCount = 8;
                    $uncompletedCount = 7;

                    $data = [$completedCount, $uncompletedCount];
                    ?>

                    <script>
                        // Example data
                        var data = <?php echo json_encode($data); ?>;

                        // Create the chart
                        var ctx = document.getElementById('myChart').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ["Completed", "Uncompleted"],
                                datasets: [{
                                    data: data,
                                    backgroundColor: ["rgba(75, 192, 192, 0.2)", "rgba(255, 99, 132, 0.2)"],
                                    borderColor: ["rgba(75, 192, 192, 1)", "rgba(255, 99, 132, 1)"],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                var label = context.label || '';
                                                var value = context.parsed || 0;
                                                var dataset = context.dataset || {};
                                                var total = dataset.data.reduce(function(previousValue, currentValue) {
                                                    return previousValue + currentValue;
                                                });
                                                var percentage = Math.round((value / total) * 100);
                                                return label + ': ' + value + ' (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>

    </main>
    <footer id="footer" style="margin-top: 7%;">

        <div id="copyright">Learniverse &copy; 2023</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>