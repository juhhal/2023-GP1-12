<?php
require "session.php";

// Set the timezone to Asia/Riyadh (Saudi Arabia)
date_default_timezone_set('Asia/Riyadh');

if (!isset($_GET['space'])) header('Location:sharedspace.php');
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
$memberCount = count($space->members);
$pmemberCount = count($space->pendingMembers);

$found = false;
foreach ($space->members as $member) {
    if ($member->email === $_SESSION['email']) {
        $found = true;
        break;
    }
}

if ($_SESSION['email'] != $space->admin && !$found) {
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
        var memberCount = <?php echo $memberCount ?>;
        var pmemberCount = <?php echo $pmemberCount ?>;
        var calendar = null;

        document.addEventListener('DOMContentLoaded', function() {
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
                                document.getElementById('swalEvtDesc').value + " Event Added By <?php echo "$user->firstname $user->lastname" ?>, From Space: <?php echo $space->name ?>",
                                document.getElementById('swalEvtReminder').checked

                            ]
                        }
                    });

                    if (formValues) {
                        // Add event
                        //get event color
                        <?php $color = "#3788d8"; //default color
                        if ($space->admin != $_SESSION['email'])
                            foreach ($space->members as $member)
                                if ($member->email === $_SESSION['email']) $color = $member->color; //get member color
                        ?>
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
                                    spaceID: '<?php echo $space->spaceID ?>',
                                    color: '<?php echo $color ?>'
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == 1) {
                                    Swal.fire('Event added successfully!', '', 'success');
                                    //save event to admins workspace
                                    fetch("eventHandler.php", {
                                        method: "post",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify({
                                            request_type: 'addEvent',
                                            start: start.startStr,
                                            end: start.endStr,
                                            event_data: formValues,
                                            spaceID: '<?php echo $space->spaceID ?>',
                                            color: '<?php echo $space->color ?>',
                                            member: '<?php echo $space->admin ?>',
                                            refID: '<?php echo $space->spaceID ?>-' + data.eventID
                                        })
                                    });
                                    <?php
                                    foreach ($space->members as $m) { ?>
                                        fetch("eventHandler.php", {
                                            method: "post",
                                            headers: {
                                                "Content-Type": "application/json"
                                            },
                                            body: JSON.stringify({
                                                request_type: 'addEvent',
                                                start: start.startStr,
                                                end: start.endStr,
                                                event_data: formValues,
                                                spaceID: '<?php echo $space->spaceID ?>',
                                                color: '<?php echo $space->color ?>',
                                                member: '<?php echo $m->email ?>',
                                                refID: '<?php echo $space->spaceID ?>-' + data.eventID
                                            }),
                                            success: function(response) {
                                                console.log(response);
                                            }
                                        });
                                    <?php }
                                    ?>
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
                                        spaceID: '<?php echo $space->spaceID ?>',
                                        color: '<?php echo $color ?>'
                                    }),
                                })
                                .then(response => response.json())
                                .then(response => {
                                    if (response.status == 1) {
                                        Swal.fire('Event deleted successfully!', '', 'success');
                                        // delete from admin's workspace
                                        fetch("eventHandler.php", {
                                            method: "POST",
                                            headers: {
                                                "Content-Type": "application/json"
                                            },
                                            body: JSON.stringify({
                                                request_type: 'deleteEvent',
                                                event_id: info.event.id,
                                                spaceID: '<?php echo $space->spaceID ?>',
                                                member: '<?php echo $space->admin ?>',
                                                refID: '<?php echo $space->spaceID ?>-' + info.event.id,
                                            }),
                                        });
                                        // delete from members workspace
                                        <?php
                                        foreach ($space->members as $m) { ?>
                                            fetch("eventHandler.php", {
                                                method: "post",
                                                headers: {
                                                    "Content-Type": "application/json"
                                                },
                                                body: JSON.stringify({
                                                    request_type: 'deleteEvent',
                                                    event_id: info.event.id,
                                                    spaceID: '<?php echo $space->spaceID ?>',
                                                    refID: '<?php echo $space->spaceID ?>-' + info.event.id,
                                                    member: '<?php echo $m->email ?>'
                                                }),
                                                success: function(response) {
                                                    console.log(response);
                                                }
                                            });
                                        <?php }
                                        ?>
                                    } else {
                                        Swal.fire(response.error, '', 'error');
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
                                                spaceID: '<?php echo $space->spaceID ?>',
                                                color: '<?php echo $color ?>'
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.status == 1) {
                                                Swal.fire('Event updated successfully!', '', 'success');
                                                //update event for admin
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
                                                        spaceID: '<?php echo $space->spaceID ?>',
                                                        color: '<?php echo $space->color ?>',
                                                        refID: '<?php echo $space->spaceID ?>-' + info.event.id,
                                                        member: '<?php echo $space->admin ?>'
                                                    })
                                                });

                                                //update event for members
                                                <?php
                                                foreach ($space->members as $m) { ?>
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
                                                            spaceID: '<?php echo $space->spaceID ?>',
                                                            color: '<?php echo $space->color ?>',
                                                            refID: '<?php echo $space->spaceID ?>-' + info.event.id,
                                                            member: '<?php echo $m->email ?>'
                                                        })
                                                    });
                                                <?php } ?>
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
            //end calendar

        });
    </script>
    <!-- SHOUQ SECTION: -->
    <script type='text/javascript'>
        $(document).ready(function() {

            if (<?php echo count($space->logUpdates); ?> == 0) {
                document.getElementById('logUpdates').style.display = 'none';
            }

            if (<?php echo count($space->tasks); ?> == 0) {
                document.getElementById('myChart').style.display = 'none';
            }

            $('#addMessage').submit(function(e) {
                // Prevent form submission
                e.preventDefault();

                // Get the form data
                var message = $('#message').val();
                var spaceID = $('#spaceID').val();

                var currentDate = new Date();

                // Extract the year, month, day, hours, and minutes from the current date
                var year = currentDate.getFullYear();
                var month = String(currentDate.getMonth() + 1).padStart(2, '0');
                var day = String(currentDate.getDate()).padStart(2, '0');
                var hours = String(currentDate.getHours()).padStart(2, '0');
                var minutes = String(currentDate.getMinutes()).padStart(2, '0');

                var messgaeDate = `${year}-${month}-${day}T${hours}:${minutes}`;
                // Send AJAX request to the server
                $.ajax({
                    url: 'addMessage.php',
                    type: 'POST',
                    data: {
                        message: message,
                        spaceID: spaceID,
                        date: messgaeDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.message === "faild") {
                            // display failure message
                            <?php
                            echo "showAlert(\"ERROR\", \"Something went wrong, try to write you message again.\");"
                            ?>
                        } else {
                            $('#message').val('');
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
            var dateString = messageData.date;
            var date = new Date(dateString);
            var options = {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            };
            var time = date.toLocaleTimeString([], options);
            var messageHTML = "<div class='chat userchat'><span class='username'>" + messageData.writtenBy + "</span><br><span class='data'>" + messageData.message + "</span><span class='date'>" + time + "</span></div>";
            messageContainer.innerHTML += messageHTML;
            messageContainer.scrollTop = messageContainer.scrollHeight;

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
                    // Scroll to the bottom of the container
                    document.getElementById("showMessages").scrollTop = document.getElementById("showMessages").scrollHeight;
                }
            });
        }

        window.onload = function() {
            var messageContainer = document.getElementById("showMessages");
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }

        function reloadLogs() {
            $.ajax({
                url: 'reloadLogs.php',
                type: 'GET',
                data: {
                    spaceID: '<?php echo $_GET['space']; ?>'
                },
                dataType: 'html',
                success: function(response) {
                    $('#logUpdates').html(response);
                }
            });
        }
        setInterval(function() {
            reloadMessages();
            reloadLogs();
            // Add more functions here
        }, 2000);

        function checkTask(taskID) {
            checkbox = document.getElementById(taskID);
            $.ajax({
                url: "processSpaceTask.php",
                method: "post",
                data: {
                    operation: "checkTask",
                    taskID: taskID,
                    spaceID: '<?php echo $space->spaceID ?>',
                    checked: checkbox.checked
                }
            });
        }

        function formatDateTime(dateTimeString) {
            var options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            };

            var dateTime = new Date(dateTimeString);
            var formattedDateTime = dateTime.toLocaleString('en-US', options);
            formattedDateTime = formattedDateTime.replace(/\//g, '-');
            formattedDateTime = formattedDateTime.replace(',', ' at');

            var parts = formattedDateTime.split(' ');
            var datePart = parts[0].split('-').reverse().join('-');

            formattedDateTime = datePart + ' ' + parts[1] + " " + parts[2] + " " + parts[3];

            return formattedDateTime;
        }

        function DeleteFile(fileId, filePath, spaceID) {
            // Send an AJAX request to delete the file
            $.ajax({
                url: 'deleteFileSharedSpace.php',
                type: 'POST',
                data: {
                    fileId: fileId,
                    filePath: filePath,
                    spaceID: spaceID
                },
                success: function(response) {
                    var response = JSON.parse(response);
                    if (response.status === "success") {
                        // File deleted successfully
                        Swal.fire("Deleted!", response.message, "success").then(() => {
                            // Remove the corresponding box element from the DOM
                            $('#box_' + fileId).remove();
                        });
                    } else {
                        // Failed to delete file
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        }

        function saveToMyFiles(fileID, fileName) {
            // Send AJAX request to the server
            $.ajax({
                url: 'duplicate_file.php',
                type: 'POST',
                data: {
                    fileID: fileID,
                    fileName: fileName
                },
                success: function(response) {
                    var response = JSON.parse(response);
                    if (response.status === "success") {
                        // File duplicated successfully
                        Swal.fire("Success!", response.message, "success").then(() => {});
                    } else {
                        // Failed to duplicate file
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "Failed to duplicate the file.", "error");
                }
            });
        }

        function quickDue(taskID, name, label) {
            var picker = label.getElementsByClassName("quickDue")[0];
            picker.showPicker();
            picker.addEventListener("change", function() {
                value = picker.value;
                $.ajax({
                    url: "processSpaceTask.php",
                    method: "post",
                    data: {
                        newDue: value,
                        taskID: taskID,
                        taskName: name,
                        spaceID: '<?php echo $space->spaceID ?>',
                        operation: "quickDue"
                    },
                    success: function(res) {
                        console.log(res);
                        label.parentNode.getElementsByTagName("span")[0].textContent = formatDateTime(value);
                        calendar.refetchEvents();
                    }
                });
            })

        }

        function displayFile(response) {
            // Extract file information from the response
            var fileID = response.fileID;
            var fileName = response.file_name;
            var filePath = response.file_path;

            // Create the HTML elements
            var boxElement = document.createElement('span');
            boxElement.className = 'box';
            boxElement.id = 'box_' + fileID;

            var fileInfoDiv = document.createElement('div');
            fileInfoDiv.className = 'fileInfoDiv';

            var fileLink = document.createElement('a');
            fileLink.target = '_blank';
            fileLink.className = 'file';
            fileLink.href = filePath;
            fileLink.textContent = fileName;

            var fileButtonsDiv = document.createElement('div');
            fileButtonsDiv.className = 'fileButtons';

            var saveFileIcon = document.createElement('i');
            saveFileIcon.title = 'save to my files';
            saveFileIcon.className = 'fa-solid fa-floppy-disk saveFile';
            saveFileIcon.onclick = function() {
                saveToMyFiles(fileID, fileName);
            };

            var deleteFileIcon = document.createElement('i');
            deleteFileIcon.title = 'delete file';
            deleteFileIcon.className = 'fa-solid fa-trash icon deleteic';
            deleteFileIcon.setAttribute('data-p', 0);
            deleteFileIcon.setAttribute('data-value', 'box_' + fileID);
            deleteFileIcon.onclick = function() {
                DeleteFile(fileID, filePath, 'some_space');
            };

            var iframeElement = document.createElement('iframe');
            iframeElement.scrolling = 'no';
            iframeElement.src = filePath;

            // Append the elements to the container hierarchy
            fileButtonsDiv.appendChild(saveFileIcon);
            fileButtonsDiv.appendChild(deleteFileIcon);

            fileInfoDiv.appendChild(fileLink);
            fileInfoDiv.appendChild(fileButtonsDiv);

            boxElement.appendChild(fileInfoDiv);
            boxElement.appendChild(document.createElement('br'));
            boxElement.appendChild(iframeElement);

            // Append the container to the desired parent element in the DOM
            var parentElement = document.getElementById('parentElementId');
            parentElement.appendChild(boxElement);
        }

        $(document).ready(function() {

            // Retrieve the form element
            var form = document.getElementById("uploadExistingFileForm");

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
                if (selectedMats.length > 0)
                    //Submit the form
                    form.submit();
            });
        });
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
                <li class="tool_item"><a href="meetingroom.php">
                        Meeting Room</a>
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
                    <button class="tablinks" onclick="openTab(event, 'Feed')"><i class="fa-solid fa-comments" title="chat"></i></button>
                    <button class="tablinks" onclick="openTab(event, 'Tasks')"><i class="fa-solid fa-list-check" title="tasks"></i></button>
                    <button class="tablinks" onclick="openTab(event, 'Files')"><i class="fa-solid fa-file" title="files"></i></button>
                    <button class="tablinks" onclick="openTab(event, 'Meeting-room')"><i class="fa-solid fa-chalkboard-user" title="meeting room"></i></button>
                    <button class="tablinks" onclick="openTab(event, 'Members')"><i class="fa-solid fa-user-large" title="members"></i> <?php if ($space->admin === $_SESSION['email'] && ($pmemberCount) > 0) echo "<span class= 'pendingNotif'></span>"; ?></button>
                </div>
                <!-- Tab content -->
                <div id="Feed" class="tabcontent scrollable">
                    <div id="showMessages">
                        <?php
                        $feeds = $space->feed;
                        foreach ($feeds as $feed) {
                            $filter = ['username' => $feed->writtenBy];
                            $query = new MongoDB\Driver\Query($filter);
                            $result = $manager->executeQuery("Learniverse.users", $query);
                            $chatUser = $result->toArray()[0];
                            $members = $space->members;

                            $userColor = ""; // Initialize userColor variable

                            foreach ($members as $member) {
                                if ($member->email === $chatUser->email) {
                                    $userColor = $member->color;
                                    break; // Exit the loop once the color is found
                                } else if ($space->admin === $chatUser->email) {
                                    $userColor = $space->color;
                                    break;
                                }
                            }

                            $time = date('h:i A', strtotime($feed->date)); // Format the date to display time only

                            if ($feed->writtenBy == $user->username) {
                                echo "<div class='chat userchat'><span class='data'>" . $feed->message . "</span><span class='date'>" . $time . "</span></div>";
                            } else {
                                echo "<div class='chat'><span class='username' style='color:" . $userColor . "';>" . $feed->writtenBy . "</span><span class='data'>" . $feed->message . "</span><span class='date'>" . $time . "</span></div>";
                            }
                        }
                        ?>
                    </div>
                    <form id="addMessage" method="post" action="addMessage.php">
                        <input id="spaceID" name="spaceID" value="<?php echo $_GET['space']; ?>" hidden>
                        <input required autofocus id="message" name="message" placeholder="Type a message">&nbsp; &nbsp;<button id="submitChat" type='submit'>Send</button>
                    </form>
                </div>

                <div id="Tasks" class="tabcontent">
                    <button id="addTaskBTN"><i class="fa-solid fa-plus"></i> Add Task</button>
                    <h3>Tasks</h3>
                    <div class="tasksContainer">
                        <?php foreach ($space->tasks as $task) {
                            $due = "";
                            if ($task->due != "") {
                                $dateTime = new DateTime($task->due);
                                $formattedDateTime = $dateTime->format('Y-m-d \a\t h:i A');
                                $due = $formattedDateTime;
                            } ?>
                            <div class="taskDiv <?php echo ($task->checked) ? "checkedTask" : "uncheckedTask"; ?>">
                                <span class="taskHead">
                                    <input class="taskID" type="hidden" readonly value="<?php echo $task->taskID ?>" name="taskID">
                                    <input type="checkbox" id="<?php echo $task->taskID ?>" name="taskCheck" class="taskCheck" <?php echo ($task->checked) ? "checked" : ""; ?> onchange="checkTask('<?php echo $task->taskID ?>')">
                                    <span class="taskName"><?php echo $task->task_name ?></span>
                                    <span class="more"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                                </span>
                                <span class="creator"><?php
                                                        $query = new MongoDB\Driver\Query(['email' => $task->creator]);
                                                        $cursor = $manager->executeQuery('Learniverse.users', $query);
                                                        $creator = $cursor->toArray()[0];
                                                        echo " $creator->firstname $creator->lastname" ?>
                                </span>
                                <span class="lastEditedBy"><?php
                                                            if ($task->lastEditedBy != "") {
                                                                $query = new MongoDB\Driver\Query(['email' => $task->lastEditedBy]);
                                                                $cursor = $manager->executeQuery('Learniverse.users', $query);
                                                                $editor = $cursor->toArray()[0];
                                                                echo " $editor->firstname $editor->lastname";
                                                            } else echo "";  ?>
                                </span>
                                <span class="taskInfo">
                                    <span class="description"><?php echo $task->description ?></span>
                                    <span class="dueDate"><label class="quickDueLabel" title="reschedule" onclick="quickDue('<?php echo $task->taskID . '\', \'' . $task->task_name ?>', this)"><i class=" fa-solid fa-calendar-days"></i> <input class="quickDue" name="quickDue" type="datetime-local" style="display: none;"></label><span><?php echo $due ?></span></span>
                                    <form>
                                        <select class="assignee <?php echo ($task->assignee === "unassigned") ? "unassigned" : ""; ?>" title="reassign">
                                            <option <?php echo ($task->assignee === "unassigned") ? "selected class='unassigned'" : ""; ?> value="unassigned">Unassigned</option>
                                            <option <?php echo ($task->assignee === $space->admin) ? "selected " : "";
                                                    echo "value=$space->admin" ?>><?php echo "$admin->firstname $admin->lastname"; ?></option>
                                            <?php foreach ($space->members as $member) {
                                                $query = new MongoDB\Driver\Query(['email' => $member->email]);
                                                $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                                                $memberName = $memberCursor->toArray()[0];
                                                $selected = "";
                                                if ($task->assignee === $member->email)
                                                    $selected = "selected";
                                            ?>
                                                <option <?php echo "$selected value=$member->email"; ?>>
                                                    <?php echo "$memberName->firstname $memberName->lastname"; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </form>
                                </span>
                            </div>
                        <?php } ?>
                        <script>
                            var assigneeSelects = document.querySelectorAll('.assignee');

                            // Loop through each assignee select element
                            assigneeSelects.forEach(function(select) {
                                // Add onchange event listener
                                select.addEventListener('change', function() {
                                    // Get the task ID from the hidden input within the taskDiv
                                    var taskID = $(this).closest('.taskDiv').find('.taskID').val();

                                    // Get the selected value of the assignee select
                                    var selectedValue = $(this).val();

                                    // Create an object with the data to be sent in the AJAX request
                                    var requestData = {
                                        operation: "updateAssignee",
                                        spaceID: '<?php echo $space->spaceID ?>',
                                        taskID: taskID,
                                        assignee: selectedValue
                                    };

                                    // Send the AJAX request
                                    $.ajax({
                                        url: 'processSpaceTask.php',
                                        type: 'POST',
                                        data: requestData,
                                        success: function(response) {
                                            // Handle the AJAX response
                                            console.log(response);
                                        },
                                        error: function(xhr, status, error) {
                                            // Handle AJAX errors
                                            console.error(error);
                                        }
                                    });
                                });
                            });
                        </script>
                        <div class="overlay" id="addTaskDiv">
                            <div class="modal">
                                <h2 id="overlayTitle">Add a New Task</h2>
                                <form id="add-task-form" name="add-task-form" method="post">
                                    <div class="form-inputs">
                                        <input type="hidden" readonly value="<?php echo $space->spaceID ?>" name="spaceID">
                                        <input type="hidden" id="taskCheck" name="taskCheck">
                                        <input required id="task-name-input" type="text" name="task_name" placeholder="Task name" autocomplete="off" maxlength="500" autofocus />
                                        <input id="task-description-input" type="text" name="description" placeholder="Description" autocomplete="off" />
                                    </div>
                                    <div class="extra-fields">
                                        <div>
                                            <input id="taskDue" class="due-date-picker" type="datetime-local" name="due" />
                                            <select class="assignee-selector" name="assignee">
                                                <option value="unassigned" selected>
                                                    Unassigned
                                                </option>
                                                <option value="<?php echo $space->admin ?>">
                                                    <?php echo $admin->firstname . " " . $admin->lastname ?>
                                                </option>
                                                <?php foreach ($space->members as $member) {
                                                    $query = new MongoDB\Driver\Query(['email' => $member->email]);
                                                    $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                                                    $memberName = $memberCursor->toArray()[0]; ?>
                                                    <option value="<?php echo $member->email ?>">
                                                        <?php echo "$memberName->firstname $memberName->lastname" ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="creator-editor">
                                        <div class="creator"></div>
                                        <div class="lastEditedBy"></div>
                                    </div>
                                    <div class="cancel-submit-container">
                                        <button class="delete-task-button" title="delete task" type="button"><i class="fa-solid fa-trash"></i></button>
                                        <button class="cancel-button" type="button">Cancel</button>
                                        <button class="add-task-submit-button" type="button" disabled>Add task</button>
                                        <button class="add-task-update-button" type="button" disabled>Update Task</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        var addTaskBTN = document.getElementById('addTaskBTN');
                        var overlayTask = document.getElementById('addTaskDiv');
                        var tasksContainer = document.getElementsByClassName("tasksContainer")[0];
                        var addTaskForm = document.getElementById('add-task-form');
                        var taskNameINPUT = document.getElementById('task-name-input');
                        var submitAddTaskBTN = document.getElementsByClassName('add-task-submit-button')[0];
                        var cancelAddTaskBTN = document.getElementsByClassName('cancel-button')[0];

                        $(document).ready(function() {
                            //sort mechanism
                            $('#groupBy').on('change', function() {
                                var selectedValue = $(this).val();

                                // Show all tasks
                                $('.taskDiv').show();

                                // Sort tasks based on the selected option
                                if (selectedValue === 'duedate') {
                                    $('.taskDiv').sort(function(a, b) {
                                        var dateA = new Date($(a).find('.dueDate').text());
                                        var dateB = new Date($(b).find('.dueDate').text());
                                        return dateA - dateB;
                                    }).appendTo('.tasksContainer');
                                } else if (selectedValue === 'assignee') {
                                    $('.taskDiv').sort(function(a, b) {
                                        var assigneeA = $(a).find('.assignee').val();
                                        var assigneeB = $(b).find('.assignee').val();
                                        return assigneeA.localeCompare(assigneeB);
                                    }).appendTo('.tasksContainer');
                                } else if (selectedValue === 'creator') {
                                    $('.taskDiv').sort(function(a, b) {
                                        var creatorA = $(a).find('.creator').text().trim();
                                        var creatorB = $(b).find('.creator').text().trim();
                                        return creatorA.localeCompare(creatorB);
                                    }).appendTo('.tasksContainer');
                                } else if (selectedValue === 'incompleted') {
                                    $('.checkedTask').hide();
                                } else if (selectedValue === 'completed') {
                                    $('.uncheckedTask').hide();
                                }
                            });

                            $(".more").click(function() {
                                $("#overlayTitle").text("Edit This Task");

                                var taskDiv = $(this).closest(".taskDiv");
                                var taskName = taskDiv.find(".taskName").text().trim();
                                var creator = taskDiv.find(".creator").text().trim();
                                var editor = taskDiv.find(".lastEditedBy").text().trim();
                                var dueDate = taskDiv.find(".dueDate").text().trim().replace(" at ", "T").trim();
                                var assignee = taskDiv.find(".assignee").val();
                                var description = taskDiv.find(".description").text().trim();
                                var taskID = taskDiv.find(".taskID").val();
                                var taskCheck = taskDiv.find(".taskCheck").prop("checked");

                                // Fill the overlay fields with the values
                                $("#add-task-form #task-name-input").val(taskName);
                                $("#add-task-form #task-description-input").val(description);
                                $("#add-task-form #creator-input").val(creator);
                                $("#add-task-form #taskDue").val(dueDate);
                                $("#add-task-form .assignee-selector").val(assignee);
                                $("#add-task-form #taskCheck").prop("checked", taskCheck);
                                $("#add-task-form .creator").text("Creator: " + creator);
                                if (editor != "") {
                                    $("#add-task-form .lastEditedBy").text("Last Edited By: " + editor);
                                    $("#add-task-form .lastEditedBy").show();
                                } else $("#add-task-form .lastEditedBy").hide();

                                //target the form submit button and change it from add to save
                                $("#add-task-form .add-task-submit-button").hide();
                                $("#add-task-form .add-task-update-button").show();
                                $("#add-task-form .delete-task-button").show();
                                $("#add-task-form .creator-editor").show();
                                $("#add-task-form .creator").show();

                                var saveBTN = $("#add-task-form .add-task-update-button");
                                var deleteBTN = $("#add-task-form .delete-task-button");

                                deleteBTN.on('click', function() {
                                    Swal.fire({
                                        title: 'Heads Up!',
                                        text: 'Are you sure you want to delete this task?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes',
                                        cancelButtonText: 'No',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                url: 'processSpaceTask.php',
                                                method: 'post',
                                                data: {
                                                    operation: 'deleteTask',
                                                    taskID: taskID,
                                                    spaceID: '<?php echo $space->spaceID ?>'
                                                },
                                                success: function(response) {
                                                    taskDiv.remove();
                                                    console.log(response);
                                                    overlayTask.style.display = 'none';
                                                    calendar.refetchEvents();
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error(error);
                                                }
                                            });
                                        }
                                    });
                                });
                                // Enable save button when form fields change
                                $("#add-task-form input, #add-task-form select").on("input", function() {
                                    if (taskNameINPUT.value.trim() !== '') {
                                        saveBTN.prop("disabled", false);
                                    } else {
                                        saveBTN.prop("disabled", true);
                                    }
                                });

                                saveBTN.attr('id', 'editTask');
                                //edit task operation starts here
                                saveBTN.on('click', function() {
                                    const formData = new FormData(addTaskForm);
                                    formData.append('operation', 'editTask');
                                    formData.append('taskID', taskID);

                                    $.ajax({
                                        url: "processSpaceTask.php",
                                        method: "post",
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        success: function(response) {
                                            // Handle success response from the server
                                            console.log(response);
                                            overlayTask.style.display = 'none';
                                            calendar.refetchEvents();
                                        },
                                        error: function(xhr, status, error) {
                                            // Handle error response from the server
                                            console.error(error);
                                        }
                                    });
                                });

                                // Show the overlay
                                overlayTask.style.display = 'flex';
                                $("#addTaskDiv").show();
                            });
                        });

                        addTaskBTN.addEventListener('click', function() {
                            overlayTask.style.display = 'flex';
                            $("#overlayTitle").text("Add a New Task");

                            $("#add-task-form .add-task-submit-button").show();
                            $("#add-task-form .add-task-update-button").hide();
                            $("#add-task-form .delete-task-button").hide();
                            $("#add-task-form .creator-editor").hide();
                        });

                        overlayTask.addEventListener('click', function(event) {
                            if (event.target === overlayTask) {
                                overlayTask.style.display = 'none';
                                addTaskForm.reset();
                            }
                        });

                        submitAddTaskBTN.addEventListener('click', function() {
                            const formData = new FormData(addTaskForm);
                            //operation addTask starts here
                            formData.append('operation', 'addTask');

                            $.ajax({
                                url: "processSpaceTask.php",
                                method: "post",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    // Handle success response from the server
                                    taskID = response;
                                    overlayTask.style.display = 'none';
                                    calendar.refetchEvents();
                                    // append the newly created tasks to the task container
                                    // Create a new div element
                                    var newTaskDiv = document.createElement('div');
                                    newTaskDiv.className = 'taskDiv uncheckedTask';

                                    // Construct the inner HTML for the new task div
                                    newTaskDiv.className = 'taskDiv uncheckedTask';

                                    // Construct the inner HTML for the new task div
                                    var innerHTML =
                                        '<span class="taskHead">' +
                                        '  <input class="taskID" type="hidden" readonly value="' + taskID + '" name="taskID">' +
                                        '  <input type="checkbox" id="' + taskID + '" name="taskCheck" class="taskCheck" onchange="checkTask(\'' + taskID + '\')">' +
                                        '  <span class="taskName">' + formData.get('task_name') + '</span>' +
                                        '  <span class="more"><i class="fa-solid fa-ellipsis-vertical"></i></span>' +
                                        '</span>' +
                                        '<span class="creator"> <?php echo $fetch['firstname'] . " " . $fetch['lastname'] ?> </span>' +
                                        '<span class="lastEditedBy"></span>' +
                                        '<span class="taskInfo">' +
                                        '  <span class="description">' + formData.get('description') + '</span>' +
                                        '  <span class="dueDate">' +
                                        '    <label class="quickDueLabel" title="reschedule" onclick="quickDue(\'' + taskID + '\', \'' + formData.get('task_name') + '\', this)">' +
                                        '      <i class="fa-solid fa-calendar-days"></i>' +
                                        '      <input class="quickDue" name="quickDue" type="datetime-local" style="display: none;">' +
                                        '    </label>' +
                                        '    <span>' + formData.get('due') + '</span>' +
                                        '  </span>' +
                                        '  <form>';

                                    // Retrieve the assignee options dynamically from the $space object
                                    var assigneeOptions = <?php $assigness[] = "$admin->firstname $admin->lastname";
                                                            foreach ($space->members as $member) {
                                                                $query = new MongoDB\Driver\Query(['email' => $member->email]);
                                                                $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                                                                $memberName = $memberCursor->toArray()[0];
                                                                $assigness[] = "$memberName->firstname $memberName->lastname";
                                                            }
                                                            echo json_encode($assigness) ?>;
                                    var selectOps = "";
                                    var op1 = "<option  value='unassgined'>Unassigned</option>";
                                    var unassignedClass = "";
                                    // Append the assignee options to the inner HTML
                                    for (var i = 0; i < assigneeOptions.length; i++) {
                                        var assigneeOption = assigneeOptions[i];
                                        var isSelected = assigneeOption.email === formData.get('assignee');
                                        var selectedAttribute = isSelected ? 'selected' : '';
                                        unassignedClass = isSelected ? '' : 'unassigned';

                                        selectOps +=
                                            '<option value="' + assigneeOption.email + '" ' + selectedAttribute + '>' + assigneeOption + '</option>';
                                    }
                                    if (unassignedClass === "unassigned")
                                        op1 = '<option selected class="unassigned" value="unassigned">Unassigned</option>';
                                    innerHTML +=
                                        '<select class="assignee ' + unassignedClass + '"' + ' title="reassign">' +
                                        op1 + selectOps + '</select>' +
                                        '  </form>' +
                                        '</span>';

                                    // Set the inner HTML of the new task div
                                    newTaskDiv.innerHTML = innerHTML;

                                    // Append the new task div to the tasksContainer
                                    tasksContainer.appendChild(newTaskDiv);
                                },
                                error: function(xhr, status, error) {
                                    // Handle error response from the server
                                    console.error(error);
                                }
                            });
                        });

                        cancelAddTaskBTN.addEventListener('click', function() {
                            addTaskForm.reset();
                            overlayTask.style.display = 'none';
                        });

                        taskNameINPUT.addEventListener('input', function() {
                            if (taskNameINPUT.value.trim() !== '') {
                                submitAddTaskBTN.disabled = false;
                            } else {
                                submitAddTaskBTN.disabled = true;
                            }
                        });

                        // Get the due input element
                        var taskDue = document.getElementById('taskDue');

                        var currentDate = new Date();

                        // Extract the year, month, day, hours, and minutes from the current date
                        var year = currentDate.getFullYear();
                        var month = String(currentDate.getMonth() + 1).padStart(2, '0');
                        var day = String(currentDate.getDate()).padStart(2, '0');
                        var hours = String(currentDate.getHours()).padStart(2, '0');
                        var minutes = String(currentDate.getMinutes()).padStart(2, '0');

                        const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

                        // Set the minimum value to the current datetime
                        taskDue.min = formattedDate;

                        // add functionality to more button
                        $(".more").click(function() {
                            $("#overlayTitle").text("Edit This Task");

                            var taskDiv = $(this).closest(".taskDiv");
                            var taskName = taskDiv.find(".taskName").text().trim();
                            var creator = taskDiv.find(".creator").text().trim();
                            var editor = taskDiv.find(".lastEditedBy").text().trim();
                            var dueDate = taskDiv.find(".dueDate").text().trim().replace(" at ", "T").trim();
                            var assignee = taskDiv.find(".assignee").val();
                            var description = taskDiv.find(".description").text().trim();
                            var taskID = taskDiv.find(".taskID").val();
                            var taskCheck = taskDiv.find(".taskCheck").prop("checked");

                            // Fill the overlay fields with the values
                            $("#add-task-form #task-name-input").val(taskName);
                            $("#add-task-form #task-description-input").val(description);
                            $("#add-task-form #creator-input").val(creator);
                            $("#add-task-form #taskDue").val(dueDate);
                            $("#add-task-form .assignee-selector").val(assignee);
                            $("#add-task-form #taskCheck").prop("checked", taskCheck);
                            $("#add-task-form .creator").text("Creator: " + creator);
                            if (editor != "") {
                                $("#add-task-form .lastEditedBy").text("Last Edited By: " + editor);
                                $("#add-task-form .lastEditedBy").show();
                            } else $("#add-task-form .lastEditedBy").hide();

                            //target the form submit button and change it from add to save
                            $("#add-task-form .add-task-submit-button").hide();
                            $("#add-task-form .add-task-update-button").show();
                            $("#add-task-form .delete-task-button").show();
                            $("#add-task-form .creator-editor").show();
                            $("#add-task-form .creator").show();

                            var saveBTN = $("#add-task-form .add-task-update-button");
                            var deleteBTN = $("#add-task-form .delete-task-button");

                            deleteBTN.on('click', function() {
                                Swal.fire({
                                    title: 'Heads Up!',
                                    text: 'Are you sure you want to delete this task?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes',
                                    cancelButtonText: 'No',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: 'processSpaceTask.php',
                                            method: 'post',
                                            data: {
                                                operation: 'deleteTask',
                                                taskID: taskID,
                                                spaceID: '<?php echo $space->spaceID ?>'
                                            },
                                            success: function(response) {
                                                taskDiv.remove();
                                                console.log(response);
                                                overlayTask.style.display = 'none';
                                                calendar.refetchEvents();
                                            },
                                            error: function(xhr, status, error) {
                                                console.error(error);
                                            }
                                        });
                                    }
                                });
                            });
                            // Enable save button when form fields change
                            $("#add-task-form input, #add-task-form select").on("input", function() {
                                if (taskNameINPUT.value.trim() !== '') {
                                    saveBTN.prop("disabled", false);
                                } else {
                                    saveBTN.prop("disabled", true);
                                }
                            });

                            saveBTN.attr('id', 'editTask');
                            //edit task operation starts here
                            saveBTN.on('click', function() {
                                const formData = new FormData(addTaskForm);
                                formData.append('operation', 'editTask');
                                formData.append('taskID', taskID);

                                $.ajax({
                                    url: "processSpaceTask.php",
                                    method: "post",
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        // Handle success response from the server
                                        console.log(response);
                                        overlayTask.style.display = 'none';
                                        calendar.refetchEvents();
                                    },
                                    error: function(xhr, status, error) {
                                        // Handle error response from the server
                                        console.error(error);
                                    }
                                });
                            });

                            // Show the overlay
                            overlayTask.style.display = 'flex';
                            $("#addTaskDiv").show();
                        });

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
                </div>

                <div id="Files" class="tabcontent">
                    <form id="uploadNewFile" method="post" action="uploadFilesSharedSpace.php" enctype="multipart/form-data">
                        <br>
                        <label>Upload a new file</label>
                        <div class="justify">
                            <input id="newFile" name="newFile" type="file">
                            <input id="spaceID" name="spaceID" value="<?php echo $space->spaceID ?>" hidden>
                            <button type="submit" class="formSubmitBTN"><i class="fa-solid fa-upload"></i></button>
                        </div>
                        <h3>OR</h3>
                    </form>
                    <form id="uploadExistingFileForm" method="post" action="uploadFilesSharedSpace.php">
                        <div class="justify">
                            <input id="spaceID" name="spaceID" value="<?php echo $space->spaceID ?>" hidden>
                            <button id="uploadMats" onclick="showModal()">Upload from My Files</button>
                            <button id="uploadfile" type="submit"><i class="fa-solid fa-upload"></i></button>
                        </div>
                    </form>
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
                    <div class="showFiles">
                        <?php
                        $files = $space->files;
                        $directoryPath = "FILES/";
                        $i = 1;
                        foreach ($files as $file) {
                        ?>
                            <span class="box" id="box_<?php echo $file->fileID; ?>">
                                <div class="fileInfoDiv">
                                    <a target='blank' class="file" href="<?php echo $directoryPath . $file->fileID . "_" . $file->file_name; ?>"><?php echo $file->file_name; ?></a>
                                    <div class="fileButtons">
                                        <i title="save to my files" class="fa-solid fa-floppy-disk saveFile" onclick="saveToMyFiles('<?php echo $file->fileID; ?>',  '<?php echo $file->file_name ?>')"></i>
                                        <?php if ($_SESSION['email'] == $space->admin || $file->owner == $_SESSION['email']) { ?><i title="delete file" class="fa-solid fa-trash icon deleteic" data-p="<?php echo $i++; ?>" data-value="box_<?php echo $file->fileID; ?>" onclick="DeleteFile('<?php echo $file->fileID; ?>',  '<?php echo $file->file_path ?>', '<?php echo $_GET['space']; ?>')"></i><?php } ?>
                                    </div>
                                </div>
                                <br>
                                <iframe scrolling="no" src="<?php echo $directoryPath . $file->fileID . "_" . $file->file_name; ?>"></iframe>

                            </span>
                        <?php } ?>
                    </div>
                </div>

                <div id="Meeting-room" class="tabcontent">
                    <button id="startMeetingBTN"><i class="fa-solid fa-right-to-bracket"></i> Join <b> <?php echo $space->name ?>'s</b> Meeting</button>
                </div>
                <div id="Members" class="tabcontent">
                    <button id="spaceInviteBTN"><i class="fa-solid fa-user-plus"></i> Invite Members <?php if ($space->admin === $_SESSION['email'] && ($pmemberCount) > 0) echo "<span class= 'pendingNotif'></span>"; ?></button>
                    <h3>Admin</h3>
                    <?php echo "<li id='adminName'><i title='admin' class='fa-solid fa-user-tie'></i> $admin->firstname $admin->lastname </li>" ?>
                    <h3 id="memberCOUNT"></h3>
                    <?php

                    echo "<ul class='memberList'>";

                    // Access individual members
                    foreach ($space->members as $member) {
                        $query = new MongoDB\Driver\Query(['email' => $member->email]);
                        $memberCursor = $manager->executeQuery('Learniverse.users', $query);
                        $memberName = $memberCursor->toArray()[0];
                        $kickButton = null;
                        if ($space->admin === $_SESSION['email'])
                            $kickButton =  "<button class='kick'><i class='fa-solid fa-circle-exclamation'></i> kick</button>";
                        // print the member
                        echo "<div class='memberName'><li style='color:$member->color'><i title='members' class='fa-solid fa-user'></i> $memberName->firstname $memberName->lastname  </li>";
                        echo "<div class='memberInfo'>Email: <span class='member-email'>$member->email</span> <form action='mailto:$member->email'><button title='Send an Email' type='submit'><i style='color:#faf7ff' class='fa-solid fa-paper-plane'></i></button></form> 
                               $kickButton</div></div>";
                    }

                    echo "</ul>";
                    ?>
                    <script>
                        $('#startMeetingBTN').on('click', function() {
                            window.open("spaceMeeting.php?room=<?php echo ($space->admin === $_SESSION['email']) ? $space->hostUrl : $space->roomUrl; ?>&space=<?php echo $space->name . "'s" ?>&spaceID=<?php echo $space->spaceID ?>&host=<?php echo ($space->admin === $_SESSION['email']) ? 'true' . "&invite=$space->roomUrl" : 'false'; ?>");
                        });

                        function updateMemberCount() {
                            $("#memberCOUNT").text("Members (" + memberCount + ")");
                            $(".pendingNotif").text(pmemberCount);

                            if (pmemberCount === 0) {
                                $(".pendingNotif").remove();
                                $("#joinrequtitle").remove();
                            }
                        }
                        updateMemberCount();
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
                                                },
                                                success: function(response) {
                                                    memberName.remove();
                                                    memberCount--;
                                                    updateMemberCount();
                                                    console.log("kicked a member");
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
                            <h3 id="joinrequtitle"><?php if ($space->admin === $_SESSION['email'] && ($pmemberCount) > 0) {
                                                        echo "<span class= 'pendingNotif'></span> Join Requests"; ?></h3>
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
                        updateMemberCount();
                        var spaceInviteBTN = document.getElementById('spaceInviteBTN');
                        var overlay = document.getElementById('inviteDiv');
                        var invitationCode = document.getElementById('invitationCode');
                        var copyButton = document.getElementById('copyButton');
                        var acceptButtons = document.getElementsByClassName('acceptButton');
                        var rejectButtons = document.getElementsByClassName('rejectButton');
                        var joinRequestsList = document.getElementById('joinRequestsList');
                        var memberList = $(".memberList");
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
                                            if (response != "already a member!") {
                                                memberCount++;
                                                pmemberCount--;
                                                console.log("added member");
                                                joinRequestsList.removeChild(listItem);
                                                updateMemberCount();
                                                var newMember = $("<div>")
                                                    .addClass("memberName")
                                                    .append(
                                                        $("<li style='color:" + response.color + "'>").html("<i title='members' class='fa-solid fa-user'></i> " + response.name)
                                                    );
                                                var kick = null;
                                                if (<?php echo ($space->admin === $_SESSION['email']) ? 1 : 0 ?>)
                                                    kick = $("<button>").addClass("kick").html("<i class='fa-solid fa-circle-exclamation'></i> kick");
                                                kick.on('click', function() {
                                                    Swal.fire({
                                                        title: 'Heads Up!',
                                                        text: 'Are you sure you want to kick ' + response.name + '?',
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
                                                                    member: member,
                                                                    spaceid: '<?php echo $space->spaceID ?>',
                                                                    spacename: '<?php echo $space->name ?>'
                                                                },
                                                                success: function(response) {
                                                                    newMember.remove();
                                                                    memberCount--;
                                                                    updateMemberCount();
                                                                    console.log("kicked a member");
                                                                }
                                                            });
                                                        }
                                                    });
                                                });

                                                var memberInfoDiv = $("<div>")
                                                    .addClass("memberInfo")
                                                    .html(
                                                        "Email: <span class='member-email'>" + member + "</span> <form action='mailto:" + member + "'><button title='Send an Email' type='submit'><i style='color:#faf7ff' class='fa-solid fa-paper-plane'></i></button></form>"
                                                    )
                                                    .append(kick);

                                                memberInfoDiv.hover(
                                                    function() {
                                                        memberInfoDiv.css("display", "block")
                                                    },
                                                    function() {
                                                        //hover-out event
                                                        memberInfoDiv.css("display", "none");
                                                    });


                                                newMember.append(memberInfoDiv);

                                                newMember.hover(
                                                    function() {
                                                        memberInfoDiv.css("display", "block")
                                                    },
                                                    function() {
                                                        //hover-out event
                                                        memberInfoDiv.css("display", "none");
                                                    });


                                                memberList.append(newMember);
                                                console.log(response);
                                            } else { //already a member, auto reject
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
                                                        pmemberCount--;
                                                        updateMemberCount();
                                                        joinRequestsList.removeChild(listItem);
                                                        console.log(response); // Optional: Display the response in the console
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error(error); // Optional: Log any errors to the console
                                                    }
                                                });
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.error(error);
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
                                            pmemberCount -= 1;
                                            updateMemberCount();
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
                <button id="leaveSpaceBTN"><?php echo ($space->admin === $_SESSION['email']) ? 'Delete' : 'Leave' ?> Space</button>
                <div class="overview-container">
                    <div class="container calendar">
                        <div class="wrapper">

                            <!-- Calendar container -->
                            <div id="calendar"></div>

                        </div>
                    </div>
                    <div class="container chart">
                        <canvas id="myChart"></canvas>
                        <div id="logUpdates">
                            <<?php
                                $logs = $space->logUpdates;
                                if (count($logs) != 0) {
                                    echo "<script>document.getElementById('logUpdates').style.display = 'block';</script>";
                                }

                                $currentDay = null;
                                foreach ($logs as $log) {
                                    $type = $log->type;
                                    $logDate = $log->date;
                                    $day = date('F j, Y', strtotime($logDate));

                                    if ($day !== $currentDay) {
                                        echo "<div class='day-label'>$day</div>";
                                        $currentDay = $day;
                                    }
                                    switch ($type) {
                                        case "assign":
                                            if ($_SESSION['email'] === $log->assignee) {
                                                $filter = ['email' => $log->assignor];
                                                $query = new MongoDB\Driver\Query($filter);
                                                $result = $manager->executeQuery("Learniverse.users", $query);
                                                $assignor = $result->toArray()[0] ?? null;

                                                if ($assignor) {
                                                    $assignorName = $assignor->firstname ?? '' . ' ' . $assignor->lastname ?? '';
                                                    echo "<span class='log assign-log'>" . $assignorName . " <b>ASSIGNED</b> a task for you</span>";
                                                }
                                                break;
                                            } else if ($_SESSION['email'] === $log->assignor) {
                                                $filter = ['email' => $log->assignee];
                                                $query = new MongoDB\Driver\Query($filter);
                                                $result = $manager->executeQuery("Learniverse.users", $query);
                                                $assignee = $result->toArray()[0] ?? null;

                                                if ($assignee) {
                                                    $assigneeName = $assignee->firstname ?? '' . ' ' . $assignee->lastname ?? '';
                                                    echo "<span class='log assign-log'>You <b>ASSIGNED</b> a task for " . $assigneeName . "</span>";
                                                }
                                                break;
                                            }

                                            if ($log->assignor === $log->assignee) {
                                                break;
                                            } else {
                                                $filter = ['email' => $log->assignee];
                                                $query = new MongoDB\Driver\Query($filter);
                                                $result = $manager->executeQuery("Learniverse.users", $query);
                                                $assignee = $result->toArray()[0] ?? null;
                                                $assigneeName = $assignee ? ($assignee->firstname ?? '') . ' ' . ($assignee->lastname ?? '') : '';

                                                $filter = ['email' => $log->assignor];
                                                $query = new MongoDB\Driver\Query($filter);
                                                $result = $manager->executeQuery("Learniverse.users", $query);
                                                $assignor = $result->toArray()[0] ?? null;
                                                $assignorName = $assignor ? ($assignor->firstname ?? '') . ' ' . ($assignor->lastname ?? '') : '';

                                                echo "<span class='log assign-log'>" . $assignorName . " <b>ASSIGNED</b> a task for " . $assigneeName . "</span>";
                                                break;
                                            }
                                        case "create":
                                            $filter = ['email' => $log->creator];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $creator = $result->toArray()[0] ?? null;
                                            if ($creator) {
                                                $creatorName = $creator->firstname . ' ' . $creator->lastname;
                                                if ($_SESSION['email'] === $log->creator) {
                                                    echo "<span class='log create-log'>You <b>CREATED</b> a new task</span>";
                                                } else {
                                                    echo "<span class='log create-log'>" . $creatorName . " <b>CREATED</b> a new task</span>";
                                                }
                                            }
                                            break;
                                        case "delete":
                                            $filter = ['email' => $log->deletor];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $deletor = $result->toArray()[0] ?? null;
                                            if ($deletor) {
                                                $deletorName = $deletor->firstname . ' ' . $deletor->lastname;
                                                if ($_SESSION['email'] === $log->deletor) {
                                                    echo "<span class='log delete-log'>You <b>DELETED</b> a task</span>";
                                                } else {
                                                    echo "<span class='log delete-log'>" . $deletorName . " <b>DELETED</b> a task</span>";
                                                }
                                            }
                                            break;
                                        case "edit":
                                            $filter = ['email' => $log->editor];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $editor = $result->toArray()[0] ?? null;
                                            if ($editor) {
                                                $editorName = $editor->firstname . ' ' . $editor->lastname;
                                                if ($_SESSION['email'] === $log->editor) {
                                                    echo "<span class='log edit-log'>You <b>EDITED</b> a task</span>";
                                                } else {
                                                    echo "<span class='log edit-log'>" . $editorName . " <b>EDITED</b> a task</span>";
                                                }
                                            }
                                            break;
                                        case "join":
                                            $filter = ['email' => $log->memberName];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $member = $result->toArray()[0] ?? null;
                                            if ($member) {
                                                $memberName = $member->firstname . ' ' . $member->lastname;
                                                echo "<span class='log join-log'>" . $memberName . " <b>JOINED</b> this space</span>";
                                            }
                                            break;
                                        case "leave":
                                            $filter = ['email' => $log->memberName];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $member = $result->toArray()[0] ?? null;
                                            if ($member) {
                                                $memberName = $member->firstname . ' ' . $member->lastname;
                                                echo "<span class='log leave-log'>" . $memberName . " <b>LEFT</b></span>";
                                            }
                                            break;
                                        case "upload":
                                            $filter = ['email' => $log->owner];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $owner = $result->toArray()[0] ?? null;
                                            if ($owner) {
                                                $ownerName = $owner->firstname . ' ' . $owner->lastname;
                                                if ($_SESSION['email'] === $log->owner) {
                                                    echo "<span class='log upload-log'>You <b>Upload</b> a new file <b>\" " . $log->fileName . "\"</b></span>";
                                                } else {
                                                    echo "<span class='log upload-log'>" . $ownerName . " <b>Upload</b> a new file <b>\" " . $log->fileName . "\"</b></span>";
                                                }
                                            }
                                            break;
                                        case "deletefile":
                                            $filter = ['email' => $log->deleter];
                                            $query = new MongoDB\Driver\Query($filter);
                                            $result = $manager->executeQuery("Learniverse.users", $query);
                                            $deleter = $result->toArray()[0] ?? null;
                                            if ($deleter) {
                                                $deleterName = $deleter->firstname . ' ' . $deleter->lastname;
                                                if ($_SESSION['email'] === $log->deleter) {
                                                    echo "<span class='log deletefile-log'>You <b>Delete</b> a file <b>\" " . $log->fileName . "\"</b></span>";
                                                } else {
                                                    echo "<span class='log deletefile-log'>" . $deleterName . " <b>Delete</b> a file <b>\" " . $log->fileName . "\"</b></span>";
                                                }
                                            }
                                            break;
                                    }
                                }
                                ?> </div>
                                <script>
                                    var logUpdatesDiv = document.getElementById("logUpdates");
                                    logUpdatesDiv.scrollTop = logUpdatesDiv.scrollHeight;
                                    $("#leaveSpaceBTN").on('click', function() {
                                        Swal.fire({
                                            title: 'Heads Up!',
                                            text: 'Are you sure you want to <?php echo ($space->admin === $_SESSION['email']) ? "permenantly delete $space->name " : "leave $space->name " ?>?',
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
                                                        operation: '<?php echo ($space->admin === $_SESSION['email']) ? 'deleteSpace' : 'kick'; ?>',
                                                        member: '<?php echo $_SESSION['email'] ?>',
                                                        spaceid: '<?php echo $space->spaceID ?>',
                                                        spacename: '<?php echo $space->name ?>'
                                                    },
                                                    success: function(response) {
                                                        console.log(response);
                                                        window.location.href = "sharedspace.php";
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error(error);
                                                    }
                                                });
                                            }
                                        });
                                    });

                                    function addLogUpdateToPage(logUpdate) {
                                        var $logUpdates = $('#logUpdates');
                                        var $newLogUpdate = $('<span class="log">' + logUpdate + '</span>');
                                        $logUpdates.append($newLogUpdate);

                                        // Scroll to the new log update
                                        $logUpdates.animate({
                                            scrollTop: $logUpdates.prop('scrollHeight')
                                        }, 500);
                                    }
                                </script>
                        </div>

                        <?php
                        $tasks = $space->tasks;
                        if (count($tasks) != 0) {
                            echo "<script>document.getElementById('myChart').style.display = 'block';</script>";
                        }
                        $count = 0;
                        foreach ($tasks as $task) {
                            if ($task->checked)
                                $count++;
                        }
                        $completedCount = $count;
                        $uncompletedCount = count($tasks) - $count;

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
        </div>
    </main>
    <footer id="footer" style="margin-top: 7%;">

        <div id="copyright">Learniverse &copy; 2024</div>
    </footer>

    <div role="button" id="sidebar-tongue" style="margin-left: 0;">
        &gt;
    </div>
</body>

</html>