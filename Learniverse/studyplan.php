<?php
require "session.php";

//connect to db
$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Study Planner</title>
    <link rel="stylesheet" href="viewPostCSS.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="studyplan.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Include FullCalendar JS & CSS library -->
    <link href="js/fullcalendar/lib/main.css" rel="stylesheet" />
    <script src="js/fullcalendar/lib/main.js"></script>
    <script>
        var calendar = null;
    </script>
    <!-- SHOUQ SECTION: -->
    <script>
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

        var selectedMats = [];

        function loadDirectories() {
            fetch('listDirectories.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('directoryButtons').innerHTML = html;
                })
                .catch(err => {
                    console.error('Failed to load directories', err);
                });
        }

        function loadFiles(directoryName) {
            fetch(`listFiles.php?directory=${directoryName}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('fileList').innerHTML = html;

                    //color already selected files
                    var fileListDiv = document.getElementById("fileList");
                    var divs = fileListDiv.getElementsByClassName("fileItem");
                    for (var i = 0; i < divs.length; i++) {
                        var div = divs[i];
                        var fileName = div.getAttribute("data-dir");
                        if (selectedMats.includes(fileName)) {
                            console.log("preview: " + fileName);
                            // File is selected
                            div.classList.add("selectedFileDiv");
                        } else {
                            // File is not selected
                            div.classList.remove("selectedFileDiv");
                        }
                    }
                })
                .catch(err => {
                    console.error('Failed to load files', err);
                });
        }

        // Modify the showModal function to call loadDirectories initially
        function showModal() {
            document.getElementById('fileModal').style.display = 'flex';
            document.getElementById('fileModal').style.zIndex = 5;
            loadDirectories(); // Load directories first
            loadFiles('Uploaded Files');
        }

        function selectFile(fileName, div) {
            // Handle file selection
            var fileIndex = selectedMats.indexOf(fileName);
            if (fileIndex === -1) {
                // File not selected, add it to the array
                selectedMats.push(fileName);
                div.classList.add("selectedFileDiv");
            } else {
                // File already selected, remove it from the array
                selectedMats.splice(fileIndex, 1);
                div.classList.remove("selectedFileDiv");
            }
            console.log(selectedMats);
        }

        function hideModal() {
            document.getElementById('fileModal').style.display = 'none';
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
                        <li><a href='reset.php?q=addCommunityPost.php'><i class='far fa-edit'></i> Change password</a></li>
                        <li><a href='#'><i class='far fa-question-circle'></i> Help</a></li>
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
                    <a href="theFiles.php"> My Files</a>
                </li>
                <li class="tool_item">
                    <a href="quizes/index.php"> Quizzes</a>
                </li>
                <li class="tool_item">
                    <a href="flashcard.php"> Flashcards</a>
                </li>
                <li class="tool_item">
                    <a href="summarization/summarization.php"> Summarization</a>
                </li>
                <li class="tool_item">
                    <a href="studyplan.php"> Study Plan</a>
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
                        Shared spaces</a></li>
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
                <div class="top-shelf">
                    <h1>Study Planner</h1>
                    <button id="newStudyPlan">New Plan</button>
                </div>
                <table id="allPlans">
                    <tr>
                        <td>Plan Name</td>
                        <td>Time Created</td>
                        <td>Action</td>
                    </tr>
                    <?php
                    $filter = ["user_id" => $_SESSION['email']];
                    $sort = ['creation_date' => -1]; // Sort in descending order by creation_date
                    $query = new MongoDB\Driver\Query($filter, ['sort' => $sort]);

                    // Execute the query
                    $cursor = $manager->executeQuery("Learniverse.studyPlan", $query);
                    $planCount = 0;
                    foreach ($cursor as $plan) {
                        $planCount++; ?>
                        <tr id="<?php echo $plan->planID ?>" class="planRow">
                            <td class="plan-name"><span onclick="display(<?php echo $plan->planID ?>);"><?php echo $plan->name ?></span></td>
                            <td><?php echo $plan->creation_date ?></td>
                            <td>
                                <i class="fa fa-eye viewPlan" aria-hidden="true" title="View Plan"></i>
                                <i class="fas fa-trash deletePlan" title="Delete Plan"></i>
                            </td>
                        </tr>
                    <?php
                    }
                    if ($planCount == 0)
                        echo "<tr><td colspan='3'>No Plans Have Been Created Yet.</td></tr>"
                    ?>
                </table>
            </div>
        </div>
        <div class="overlay" id="newPlanOverlay">
            <div class="modal">
                <h2 id="overlayTitle">Create a New Study Plan</h2>
                <form id="new-plan-form" name="new-plan-form" method="post" action="processStudyPlan.php" enctype="multipart/form-data">
                    Plan Name: <input required autocomplete="off" type="text" name="new-plan-name" id="new-plan-name">
                    Plan start: <input required name="start" type="date"> Plan end: <input name="end" type="date">
                    Study Material: <button id="uploadMats" onclick="showModal()">Upload from My Files</button> or <input id="localMats" name="localMats[]" value="Upload from my device" type="file" multiple>
                    <input id="generatePlan" type="submit" value="Generate">
                </form>
            </div>
        </div>
        <div class="overlay" id="display">
            <div class="modal">
                <div class="container calendar">
                    <div class="wrapper">
                        <span>
                            <h2>Viewing <span id="studyPlanNameView"></span> Plan</h2>
                        </span>
                        <!-- Calendar container -->
                        <div id="calendar"></div>

                    </div>
                </div>
            </div>
        </div>
        <div id="fileModal" style="display:none;">
            <div class="modal-container">

                <div id="modalContent">
                    <!-- Directory buttons will be loaded here -->
                    <div id="directoryButtons"></div>

                    <!-- File list will be loaded here -->
                    <div id="fileList"></div>
                </div>
                <button onclick="hideModal();">Close</button>
            </div>
        </div>
        <script>
            // Retrieve the form element
            var form = document.getElementById("new-plan-form");

            // Add an event listener to the form's submit event
            form.addEventListener("submit", function(event) {
                // Create an input element to hold the selectedMats data
                event.preventDefault();
                var matsInput = document.createElement("input");
                matsInput.type = "hidden";
                matsInput.name = "myFilesMats";
                matsInput.value = JSON.stringify(selectedMats);

                // Append the input element to the form
                form.appendChild(matsInput);
                if (selectedMats.length > 0 || document.getElementById("localMats").files.length > 0) {
                    // Check if the submit button is clicked
                    var submitButton = document.getElementById("generatePlan");
                    if (submitButton === document.activeElement) {
                        // Safely submit the form
                        form.submit();
                    }
                }
            });


            var overlayNewPlan = document.getElementById('newPlanOverlay');
            var newPlanBTN = document.getElementById('newStudyPlan');
            var overlayDisplay = document.getElementById('display');
            var allPlans = document.getElementById('allPlans');
            var planRows = document.getElementsByClassName('planRow');
            $(document).ready(function() {
                newPlanBTN.addEventListener('click', function() {
                    overlayNewPlan.style.display = 'flex';
                    selectedMats = [];
                });

                overlayNewPlan.addEventListener('click', function(event) {
                    if (event.target === overlayNewPlan) {
                        overlayNewPlan.style.display = 'none';
                    }
                });

                overlayDisplay.addEventListener('click', function(event) {
                    if (event.target === overlayDisplay) {
                        overlayDisplay.style.display = 'none';
                    }
                });
            });

            function display(plan) {
                planID = plan;
                overlayDisplay.style.display = 'flex';
                var calendarEl = document.getElementById('calendar');

                calendar = new FullCalendar.Calendar(calendarEl, {
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
                    events: 'fetchEvents.php?planID=' + planID,

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
                                        planID: planID
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
                                            planID: planID
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
                                                    color: info.event.backgroundColor,
                                                    planID: planID
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
            }

            document.addEventListener('DOMContentLoaded', function() {
                var deleteButtons = document.getElementsByClassName('deletePlan');
                var viewButtons = document.getElementsByClassName('viewPlan');

                // Add event listener to delete buttons
                Array.from(deleteButtons).forEach(function(button) {
                    button.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Heads Up!',
                            text: 'Are you sure you want to delete this plan?',
                            icon: 'warning',
                            confirmButtonText: 'Yes',
                            showCancelButton: true,
                            cancelButtonText: 'No',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var trId = button.closest('tr').id;
                                // Perform delete action using trId
                                console.log('Deleting plan with ID: ' + trId);
                                // Data to send
                                var data = {
                                    planID: trId,
                                    deletePlan: true
                                };

                                $.ajax({
                                    url: 'processStudyPlan.php',
                                    type: 'POST',
                                    data: data,
                                    success: function(response) {
                                        if (response == 1) {
                                            console.log('Plan deleted successfully');
                                            button.closest('tr').remove();
                                            if (planRows.length == 0) {
                                                var tbody = document.querySelector("#allPlans tbody");
                                                tbody.innerHTML += "<tr><td colspan='3'>No Plans Have Been Created Yet.</td></tr>";
                                            }
                                        } else console.error('Error deleting plan');

                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error deleting plan');
                                    }
                                });
                            }
                        });

                    });
                });

                // Add event listener to view buttons
                Array.from(viewButtons).forEach(function(button) {
                    button.addEventListener('click', function() {
                        var tr = button.closest('tr');
                        var trId = tr.id;
                        // Perform view action using trId
                        planID = trId;
                        planName = tr.getElementsByClassName("plan-name")[0].textContent;
                        document.getElementById("studyPlanNameView").textContent = planName;
                        display(planID);
                        console.log('Viewing plan with ID: ' + trId);
                    });
                });
            });
        </script>
    </main>
    <footer style="margin-top:30%" id="footer" style="margin-top: 7%;">

        <div id="copyright">Learniverse &copy; 2024</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>