<!DOCTYPE html>
<?php
require 'session.php';

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// retrieve the todo list of this user
$query = new MongoDB\Driver\Query(array('user_id' => $_SESSION['email']));
$cursor = $manager->executeQuery('Learniverse.To-do-list', $query);
$result_array = $cursor->toArray();
$result_json = json_decode(json_encode($result_array), true);
$todo = $result_json[0]['todo_list'][0]['tasks'];
?>


<head>
    <meta charset="UTF-8">
    <title>Calnedar and To-Do</title>
    <link rel="stylesheet" href="workspaceCSS.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">

    <!-- Custom stylesheet -->
    <link href="css/style.css" rel="stylesheet" />

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- Include FullCalendar JS & CSS library -->
    <link href="js/fullcalendar/lib/main.css" rel="stylesheet" />
    <script src="js/fullcalendar/lib/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 650,
                events: 'fetchEvents.php',

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
                                    event_data: formValues
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
                                        event_id: info.event.id
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
                                                event_data: result.value
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
            $("#addtask-form").hide();
            $('.editTask-form').hide();
            $('.rescheduleDueDate').hide();
            hamster();
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox != null)
                    ischecked(checkbox.id);
            });

            // Call the function initially
            checkDueDates();

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

        function hamster() {
            if (document.querySelectorAll(".task").length < 1) {
                $("#notasks").show();
            } else {
                $("#notasks").hide();
            }
        };

        function ischecked(id) {
            var checkbox = document.getElementById(id);
            var label = document.getElementById("label" + id.slice(4));
            if (checkbox != null && checkbox.checked) {
                checkbox.style.accentColor = '#fdae9b';
                label.style.fontStyle = 'italic';
                label.style.textDecoration = 'line-through';
            } else {
                label.style.fontStyle = 'normal';
                label.style.textDecoration = 'none';
            };
            updateStatus(id);
        };

        function addTask() {
            $("#addtask-form").show();
            $("#addtaskBTN").hide();
            $("#addtask-span").hide();
            var addTaskName = document.getElementById('taskDesc');
            var submitTaskBTN = document.getElementById('submitTaskBTN');
            
            addTaskName.focus();
            addTaskName.addEventListener('input', function() {
                if (addTaskName.value.trim() !== '') {
                    submitTaskBTN.disabled = false;
                } else {
                    submitTaskBTN.disabled = true;

                }
            });
        };

        function editTask(number) {
            $('#task' + number).hide();
            $('#label' + number).hide();
            $("#editTask-form" + number).show();
            $("#editTask-form" + number + " #taskRename").focus();
            if ($("#editTask-form" + number).submitted) {
                cancelEdit(number);
            }
        };

        function resetAddTask() {
            $("#addtask-form").hide();
            $("#addtaskBTN").show();
            $("#addtask-span").show();
        }

        function cancelEdit(number) {
            $("#editTask-form" + number).hide();
            $('#task' + number).show();
            $('#label' + number).show();
        }

        function reschedule(number) {
            form = $('#rescheduleDueDate' + number);
            form.show();
            dueinput = document.getElementById("reschedule-due" + number);
            dueinput.focus();
            dueinput.addEventListener("blur", function() {
                form.hide();
                form.submit();
            });
        };

        function checkDueDates() {
            // Get all the task elements in the todo list
            const tasks = document.querySelectorAll('.task');
            // Loop through each task and check its due date and time
            tasks.forEach(task => {
                if (!task.querySelector('input[type="checkbox"]').checked && task.querySelector('.dueText').textContent != "") {
                    const dueDateStr = task.querySelector('span.due').textContent;
                    const dueTEXT = task.querySelector('.dueText');
                    // Parse the due date and time string
                    const [dateStr, timeStr] = dueDateStr.split(' at ');
                    const [year, month, day] = dateStr.split('-');
                    const [hours, minutes] = timeStr.split(':');

                    // Create a Date object with the parsed values
                    const dueDate = new Date(`${year}`, month - 1, day, hours, minutes);
                    // Check if the due date and time have passed
                    if (dueDate < new Date()) {
                        // Task is overdue
                        dueTEXT.innerHTML = "Overdue: <span class='due'>" + dueDateStr + "</span>";
                        dueTEXT.style.color = '#fe6741';
                        // You can perform additional actions here, such as displaying an alert or updating the task's appearance
                    }
                }
            });
        }


        // Call the function every specified interval (e.g., every minute)
        setInterval(checkDueDates, 1000);

        function updateStatus(id) {
            checkbox = document.getElementById(id);
            name = document.getElementById(id + "-name").textContent;
            // Send the AJAX request
            $.ajax({
                url: 'updateTaskStatus.php',
                type: 'POST',
                data: {
                    task_name: name,
                    status: checkbox.checked
                },
                success: function(response) {
                    // Request completed successfully
                    console.log("update status: " + response);
                },
                error: function(error) {
                    // Handle error
                    console.error("update status: " + error);
                }
            });
        };


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
            document.addEventListener('DOMContentLoaded', function() {
                calendar.render();
            });
        }

        function w3_close() {
            document.getElementById("sidebar-tongue").style.transition = '1s';
            document.getElementById("tools_div").style.transition = '1s';
            document.getElementsByClassName("workarea")[0].style.marginLeft = "0";
            document.getElementById("sidebar-tongue").textContent = ">";
            document.getElementById("tools_div").style.marginLeft = "-13.9%";
            document.getElementById("sidebar-tongue").style.marginLeft = '0';
            document.addEventListener('DOMContentLoaded', function() {
                calendar.render();
            });
        }
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
                                <a href="#">Community</a>
                            </li>
                            <li class="active">
                                <a href="workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>
                <?php
                require_once __DIR__ . '/vendor/autoload.php';

                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;

                $data = array(
                    "email" => $_SESSION['email']
                );

                $fetch = $Usercollection->findOne($data);
                $googleID = $fetch['google_user_id'];

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

                        <?php if ($googleID === null) {
                            echo "<li><a href='reset.php'><i class='far fa-edit'></i> Change password</a></li>";
                        } ?>

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
                <li class="tool_item"><a href="workspace.php"><img src="images/calendar.png">
                        Calendar & To-Do </li>
                <li class="tool_item"><a href="theFiles.php?q=My Files"><img src="images/file.png">
                        My Files</a>
                </li>
                <li class="tool_item"><img src="images/quiz.png">
                    Quiz
                </li>
                <li class="tool_item"><img src="images/flash-cards.png">
                    Flashcard
                </li>
                <li class="tool_item"><img src="images/summarization.png">
                    Summarization
                </li>
                <li class="tool_item"><img src="images/study-planner.png">
                    Study Planner
                </li>
                <li class="tool_item"><img src="images/notes.png">
                    Notes
                </li>
                <li class="tool_item"><img src="images/pomodoro-technique.png">
                    Pomodoro
                </li>
                <li class="tool_item"><img src="images/gpa.png">
                    GPA Calculator
                </li>
                <li class="tool_item"><img src="images/collaboration.png">
                    Shared spaces
                </li>
                <li class="tool_item"><img src="images/meeting-room.png">
                    Meeting Room
                </li>
                <li class="tool_item"><img src="images/communities.png">
                    Community
                </li>
            </ul>
        </div>

        <div class="workarea">
            <!-- ##################################################### -->
            <!-- CALENDAR -->
            <div class="workarea_item" id="calendar_area">
                <div class="container">
                    <div class="wrapper">

                        <!-- Calendar container -->
                        <div id="calendar"></div>

                    </div>
                </div>
            </div>
            <!-- ##################################################### -->
            <!-- TODO -->
            <div class="workarea_item" id="todo_area">
                <h3>My To-Do List</h3>
                <div class="todolist">
                    <ul>

                        <div id='notasks'><img src='images/hotballoon.png'>
                            <p><i>Poof!</i></p>
                            <p>Your to-do list is a clean slate, ready for you to conquer new horizons.</p>
                        </div>

                        <?php
                        //print all tasks associated with this user
                        $i = 1;
                        $due = "";
                        foreach ($todo as $task) {
                            //echo "<script>alert('" . $task['due'] . "')</script>";
                            if ($task['due'] === "") {
                                $due = "<span class='dueText'><span class='due'></span></span>";
                            } else {
                                $datetime = explode("T", $task['due']);
                                $due = "<span class='dueText'>Due: <span class='due'>" . $datetime[0] . " at " . $datetime[1] . "</span></span>";
                            }

                            if ($task['checked']) {
                                print("
                            <li class='task'>
                                 <img src='images/bin.png' class='deleteBTN' data-task='" . $task['task_name'] . "'> <img src='images/rescheduling.png' id='reschedule' onclick='reschedule($i);'><img src='images/edit.png' onclick='editTask($i);'> <input checked id='task$i' type='checkbox' onchange='ischecked(this.id);'>
                                <label id='label$i' for='task$i'>
                                    <p class='editableP taskName'><span id='task$i-name'>" . $task['task_name'] . "</span><br>$due</p>
                                </label>

                                <form id='editTask-form$i' class='editTask-form' method='post' action='editTask.php'>
                                    <input type='text' id='taskRename' name='taskRename' value='" . $task['task_name'] . "'>
                                    <input id='newDue' name='newDue' type='datetime-local' value='" . $task['due'] . "'><br>
                                    <input id='oldName' name='oldName' type='hidden' value='" . $task['task_name'] . "'>
                                    <button type='submit'>Confirm</button> <button type='reset' onclick='cancelEdit($i);'>Cancel</button>
                                </form>

                                <form id='rescheduleDueDate$i' class='rescheduleDueDate' action='rescheduleDue.php' method='post'>
                                    <input id='tName' name='tName' type='hidden' value='" . $task['task_name'] . "'>
                                    <label for='reschedule-due$i'>Due: </label> <input id='reschedule-due$i' name='reschedule-due' type='datetime-local' value='" . $task['due'] . "'>
                                </form>
                            </li>             
                            ");
                            } else {
                                print("
                                <li class='task'>
                                     <img src='images/bin.png' class='deleteBTN' data-task='" . $task['task_name'] . "'><img src='images/rescheduling.png' id='reschedule' onclick='reschedule($i);'><img src='images/edit.png' onclick='editTask($i);'> <input id='task$i' type='checkbox' onchange='ischecked(this.id);'>
                                    <label id='label$i' for='task$i'>
                                        <p class='editableP taskName'><span id='task$i-name'>" . $task['task_name'] . "</span><br>$due</p>
                                    </label>
    
                                    <form id='editTask-form$i' class='editTask-form' method='post' action='editTask.php'>
                                <input type='text' id='taskRename' name='taskRename' value='" . $task['task_name'] . "'>
                                <input id='newDue' name='newDue' type='datetime-local' value='" . $task['due'] . "'><br>
                                <input id='oldName' name='oldName' type='hidden' value='" . $task['task_name'] . "'>
                                <button type='submit'>Confirm</button> <button type='reset' onclick='cancelEdit($i);'>Cancel</button>
                            </form>

                            <form id='rescheduleDueDate$i' class='rescheduleDueDate' action='rescheduleDue.php' method='post'>
                                <input id='tName' name='tName' type='hidden' value='" . $task['task_name'] . "'>
                                <label for='reschedule-due$i'>Due: </label> <input id='reschedule-due$i' name='reschedule-due' type='datetime-local' value='" . $task['due'] . "'>
                             </form>
                                </li>             
                                ");
                            }
                            $i++;
                        }

                        ?>
                        <script>
                            $(document).ready(function() {
                                // Attach click event listener to delete buttons
                                $('.deleteBTN').on('click', function() {
                                    // Get the task name from the data attribute
                                    var deleteBTN = $(this);
                                    var taskName = $(this).data('task');
                                    // Send an AJAX request to the PHP script
                                    $.ajax({
                                        url: 'deleteTask.php',
                                        type: 'POST',
                                        data: {
                                            taskName: taskName
                                        },
                                        success: function(response) {
                                            // Display the response from the PHP script
                                            console.log(response);

                                            //remove the task from the DOM if the deletion was successful
                                            if (response === 'Task deleted successfully.') {
                                                deleteBTN.closest('li').remove();
                                                hamster();
                                            }
                                        },
                                        error: function() {
                                            // Handle any errors that occur during the AJAX request
                                            console.log('Error occurred while deleting the task.');
                                        }
                                    });
                                });
                            });
                        </script>
                        <!-- <li>
                            <img src="images/bin.png" id="deleteBTN">
                            <img src="images/rescheduling.png" id="reschedule" onclick="reschedule(this);"><img src="images/edit.png" onclick="editTask();">
                            <input id="task11" type="checkbox" onchange="ischecked(this.id);"> <label id="label11" for="task11"> Start coding AI project.</label>
                            <form id='editTask-form' method='post' action='editTask.php'>
                                <label for="taskRename">Task Name:</label> <input type="text" id="taskRename" name="taskRename" placeholder="Enter the task description">
                                <label for="newDue">Due: </label> <input id="newDue" name="newDue" type="datetime-local"><br>
                                <input id="oldName" name="oldName" type="hidden" value="Code GP1 new interface">
                                <button type="submit">Confirm</button> <button type="reset" onclick="$('#editTask-form').hide();">Cancel</button>
                            </form>
                        </li> -->

                    </ul>
                    <button id="addtaskBTN" onclick="addTask()">+ </button><label for="addtaskBTN" id="addtask-span"> Add Task</label>

                    <form id="addtask-form" method="post" action="addTask.php">
                        <input required type="text" id="taskDesc" name="taskDesc" placeholder="Task Name">
                        <input id="taskDue" name="taskDue" type="datetime-local"><br>
                        <button id="submitTaskBTN" type="submit">Add task</button> <button type="reset" onclick="resetAddTask()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-div" id="socials">
            <h4>Follow Us on Social Media</h4>

            <a href="https://twitter.com/learniversewebsite" target="_blank"><img src="images/twitter.png" alt="@Learniverse"></a>

        </div>
        <div class="footer-div" id="contacts">
            <h4>Contact Us</h4>

            <a href="mailto:learniverse.website@gmail.com" target="_blank"><img src="images/gmail.png" alt="learniverse.website@gmail.com"></a>

        </div>
        <img id="footerLogo" src="LOGO.png" alt="Learniverse">
        <div id="copyright">Learniverse &copy; 2023</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>