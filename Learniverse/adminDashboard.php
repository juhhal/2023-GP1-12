<?php
require 'session.php';
error_reporting(0);

require_once __DIR__ . '/vendor/autoload.php';

$manager = new MongoDB\Driver\Manager("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Create a MongoDB client
$connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

// Select the database and collection
$database = $connection->Learniverse;
$Usercollection = $database->users;

$data = array(
    "email" => $_SESSION['email']
);

$admin = $Usercollection->findOne($data);
$query = new MongoDB\Driver\Query([]);
$cursor = $manager->executeQuery('Learniverse.complaint', $query);
$allComplaints = $cursor->toArray();

$query = new MongoDB\Driver\Query([]);
$cursor = $manager->executeQuery('Learniverse.postReports', $query);
$allReports = $cursor->toArray();
?>

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="adminDashboardCSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">
    <script src="jquery.js"></script>

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">
    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />


</head>

<body style="background-color: #Faf7ff;">
    <script>
        function changeStatus(cstatus, complaintID) {
            // Send an AJAX request to change the status
            $.ajax({
                url: 'changeStatus.php',
                type: 'POST',
                data: {
                    complaintID: complaintID,
                    cstatus: cstatus
                },
                success: function(response) {
                    window.location.reload();
                    // Get the complaint element
                    const complaintElement = document.getElementById('complaint' + complaintID);

                    // Get the new sections based on the data-status attribute
                    const newSections = document.querySelectorAll('[data-status="' + cstatus + '"]');
                    console.log('newSections:', newSections);

                    if (complaintElement && newSections.length > 0) {
                        const targetSection = newSections[0];
                        const buttonElement = complaintElement.querySelector('button'); // Assuming the button is a child of the complaint element

                        if (buttonElement) {
                            buttonElement.style.display = 'none'; // Hide the button by setting its display property to 'none'
                        } else {
                            const newButton = document.createElement('button'); // Create a new button element
                            newButton.textContent = 'Mark as Resolved'; // Set the button text

                            const resolvedSpan = document.createElement('span'); // Create a new span element
                            resolvedSpan.className = 'resolved'; // Set the class name of the span element

                            // Append the resolved span before the new button
                            complaintElement.appendChild(resolvedSpan);
                            complaintElement.appendChild(newButton);
                        }

                        const clonedComplaint = complaintElement.cloneNode(true);
                        targetSection.parentNode.insertBefore(clonedComplaint, targetSection); // Insert the cloned complaint element before the target section
                        complaintElement.remove(); // Remove the original complaint element
                    } else {
                        console.error('Failed to move complaint element. Either complaintElement or newSections is null or empty.');
                    }
                }
            });
        }

        function DeletePost(postId, reportId) {
            Swal.fire({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this post!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'deleteReport.php',
                            type: 'POST',
                            data: {
                                postId: postId
                            },
                            success: function(response) {
                                if (response.status === "success") {
                                    // File deleted successfully
                                    Swal.fire("Deleted!", response.message, "success").then(() => {
                                        // Remove the corresponding Row element
                                        DeleteRaw(reportId);
                                    });
                                } else {
                                    // Failed to delete file
                                    Swal.fire("Error!", response.message, "error");
                                }
                            }
                        });
                    }
                });
        }


        function DeleteRaw(reportId) {
            // Send an AJAX request to delete the raw
            $.ajax({
                url: 'deleteRaw.php',
                type: 'POST',
                data: {
                    reportId: reportId
                },
                success: function(response) {
                    if (response.status === "success") {
                        $('#post' + reportId).remove();
                    }
                }
            });
        }

        function deleteComplaint(event, complaintID) {
            event.stopPropagation();
            // Send an AJAX request to delete the raw
            $.ajax({
                url: 'deletecomplaint.php',
                type: 'POST',
                data: {
                    complaintID: complaintID
                },
                success: function(response) {
                    if (response.status === "success") {
                        $('#complaint' + complaintID).remove();
                    }
                }
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
    </script>
    <header>
        <div class="header-container">
            <div class="flex-parent">
                <div class="header_logo">
                    <img src="LOGO.png">
                    <div>Learniverse</div>
                </div>
                <?php
                require_once __DIR__ . '/vendor/autoload.php';

                // Create a MongoDB client
                $connection = new MongoDB\Client("mongodb+srv://learniversewebsite:032AZJHFD1OQWsPA@cluster0.biq1icd.mongodb.net/");

                // Select the database and collection
                $database = $connection->Learniverse;
                $Usercollection = $database->users;
                $Admincollection = $database->admins;

                $data = array(
                    "email" => $_SESSION['email']
                );

                $fetch = $Admincollection->findOne($data);
                // $googleID = $fetch['google_user_id'];

                ?>
                <div class="dropdown">
                    <button class="dropdown-button">
                        <i class="fas fa-user" id='Puser-icon'> </i>
                        <?php echo ' ' . $fetch['firstname']; ?></button>
                    <ul class="Pdropdown-menu">
                        <li class='editName center'>
                            <span id='Pname'><?php echo $fetch['firstname'] . " " .  $fetch['lastname']; ?></span>
                        </li>
                        <li class='center'><?php echo $fetch['email']; ?></li>
                        <hr>
                        <li><a href='reset.php?q=adminDashboard.php'><i class='far fa-edit'></i> Change password</a></li>
                        <hr>
                        <li><a href='logout.php'><i class='fas fa-sign-out-alt'></i> Sign out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main style="margin-top: 5%;">
        <!-- Tab links -->
        <div class="tabs">
            <button class="tablinks" onclick="openTab(event, 'Dashboard')">Dashboard <i class="fa-solid fa-chart-pie"></i></button>
            <button class="tablinks" onclick="openTab(event, 'Community')">Post reports <i class='far fa-envelope-open' title="Community"></i></button>
            <button class="tablinks" onclick="openTab(event, 'customerReport')">Complaints <i class='far fa-question-circle' title="customerReport"></i></button>
        </div>
        <!-- Tab content -->
        <div id="Dashboard" class="tabcontent">
            <iframe style="background: #FFFFFF;border: none;border-radius: 2px;box-shadow: 0 2px 10px 0 rgba(70, 76, 79, .2);" width="680" height="440" src="https://charts.mongodb.com/charts-project-0-zztmo/embed/charts?id=662eb7bb-b67a-489a-8917-55d2db819e9f&maxDataAge=60&theme=light&autoRefresh=true"></iframe>
            <iframe style="background: #FFFFFF;border: none;border-radius: 2px;box-shadow: 0 2px 10px 0 rgba(70, 76, 79, .2);" width="680" height="440" src="https://charts.mongodb.com/charts-project-0-zztmo/embed/charts?id=663c920c-50e1-4911-876f-91b0f60fec97&maxDataAge=60&theme=light&autoRefresh=true"></iframe>
            <iframe style="background: #FFFFFF;border: none;border-radius: 2px;box-shadow: 0 2px 10px 0 rgba(70, 76, 79, .2);" width="680" height="440" src="https://charts.mongodb.com/charts-project-0-zztmo/embed/charts?id=66407a86-8094-4fae-8d96-4fed98930e00&maxDataAge=60&theme=light&autoRefresh=true"></iframe>
            <iframe style="background: #FFFFFF;border: none;border-radius: 2px;box-shadow: 0 2px 10px 0 rgba(70, 76, 79, .2);" width="680" height="440" src="https://charts.mongodb.com/charts-project-0-zztmo/embed/charts?id=66408001-dc15-468e-8359-e161bf4dcfc8&maxDataAge=60&theme=light&autoRefresh=true"></iframe>
        </div>

        <div id="Community" class="tabcontent">
            <table class="table table-hover table-nowrap table-striped table-bordered mt-10">
                <thead class="table-light">
                    <tr>
                        <th scope="col">id</th>
                        <th scope="col">title</th>
                        <th scope="col">date</th>
                        <th scope="col">Reported By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = new MongoDB\Driver\Query([]);
                    $cursor = $manager->executeQuery('Learniverse.postReports', $query);
                    $reports = $cursor->toArray();

                    $postIds = array();

                    foreach ($reports as $report) {
                        $postId = $report->postID;

                        if (!in_array($postId, $postIds)) {
                            $postIds[] = $postId;

                            $query = new MongoDB\Driver\Query(["_id" => new MongoDB\BSON\ObjectId($postId)]);
                            $cursor = $manager->executeQuery('Learniverse.community', $query);
                            $matchingPost = $cursor->toArray();

                            foreach ($matchingPost as $post) {

                                echo "
                        <tr id='post" . $report->_id . "'>
            <td data-label='reportId'>
           $report->_id 
            </td>
            <td data-label='title'>
              <a href='viewPost.php?postID=" . $postId . "' target='_blank'>
                $post->title
              </a>
            </td>
            <td data-label='reportDate'>
              <span>
                $report->reportDate
              </span>
            </td>
            <td data-label='Email'>
              <span>
                $report->reportedBy
              </span>
            </td>

            <td data-label=' class='text-end'>
              <div>
                <a
                  href='#'
                  class='text-muted'
                  onclick=\"DeletePost('" . $postId . "', '" . $report->_id . "')\"
                >
                  <i class='bi bi-trash'></i>
                </a>
               
                <a
                  href='#'
                  class='text-muted'
                  data-bs-toggle='modal'
                  data-bs-target='#viewReportsModal'
                  onclick=\"viewReports('" . $postId . "')\"
                >
                  <i class='bi bi-eye'></i>
                </a>
              </div>
            </td>
          </tr>
          ";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            function viewReports(postId) {
                const reports = <?php echo json_encode($allReports); ?>;
                const matchingReports = reports.filter(report => report.postID === postId);
                const reportsContainer = document.getElementById('reportsContainer');
                reportsContainer.innerHTML = '';

                matchingReports.forEach(report => {
                    const reportElement = document.createElement('div');
                    reportElement.className = 'report';
                    reportElement.textContent = report.description;

                    const dateElement = document.createElement('span');
                    dateElement.className = 'date';
                    dateElement.textContent = report.reportDate;

                    reportElement.appendChild(dateElement);
                    reportsContainer.appendChild(reportElement);
                });
            }
        </script>
        </div>

        <div id="customerReport" class="tabcontent">
            <?php
            $query = new MongoDB\Driver\Query(["status" => "unresolved"]);
            $cursor = $manager->executeQuery('Learniverse.complaint', $query);
            $complaints = $cursor->toArray();

            if (empty($complaints)) {
                echo "<h4 style='margin: 20px 0;'>No unresolved complaints to display.</h4>";
            } else {
                echo "<h4 style='margin: 20px 0;'>Unresolved complaints</h4>";
                echo "<div class='row'>";
                foreach ($complaints as $complaint) {
                    $data = array(
                        "email" => $complaint->user
                    );

                    $fetch = $Usercollection->findOne($data);

                    echo
                    "<div class='col-md-4' id='complaint" . $complaint->complaintID . "'>
                    <div class='card'>
                      <div class='card-body'>
                        <h5 class='card-title'>Ticket #" . $complaint->complaintID . "</h5>
                        <h6 class='card-subtitle mb-2 text-muted'>Customer: " . $fetch['firstname'] . " " . $fetch['lastname'] . "</h6>
                        <p class='card-text'><strong>Date:</strong> " . $complaint->date . "</p>
                        <p class='card-text'>
                          Issue: " . $complaint->complaint . "
                        </p>
                        <p class='card-text'><strong>Status:</strong> " . $complaint->status . "</p>
                        <div style='display: flex; align-items:center; gap:10px'>
                        <button href='#' class='card-link btn btn-primary m-0 w-100' data-bs-toggle='modal' data-bs-target='#ticketModal' onclick=\"viewDetails('" . $complaint->complaintID . "')\">
                          View Details
                        </button>
                        <button href='#' class='card-link btn btn-danger m-0 w-100' onclick=\"deleteComplaint(event, '" . $complaint->complaintID . "')\">Delete</button>
                        </div>
                      </div>
                    </div>
                    </div>";
                }
                echo "</div>";
            }

            $query = new MongoDB\Driver\Query(["status" => "pending"]);
            $cursor = $manager->executeQuery('Learniverse.complaint', $query);
            $complaints = $cursor->toArray();

            if (empty($complaints)) {
                echo "<h4 style='margin: 20px 0;'>No pending complaints to display.</h4>";
            } else {
                echo "<h4 style='margin: 20px 0;'>Pending complaints</h4>";
                echo "<div class='row'>";
                foreach ($complaints as $complaint) {
                    // Select the database and collection
                    $database = $connection->Learniverse;
                    $Usercollection = $database->users;

                    $data = array(
                        "email" => $complaint->user
                    );

                    $fetch = $Usercollection->findOne($data);

                    echo "
                    <div class='col-md-4' id='complaint" . $complaint->complaintID . "'>
                    <div class='card'>
                      <div class='card-body'>
                        <h5 class='card-title
                        '>Ticket #" . $complaint->complaintID . "</h5>
                        <h6 class='card-subtitle mb-2 text-muted'>Customer: " . $fetch['firstname'] . " " . $fetch['lastname'] . "</h6>
                        <p class='card-text'><strong>Date:</strong> " . $complaint->date . "</p>
                        <p class='card-text'>
                          Issue: " . $complaint->complaint . "
                        </p>
                        <p class='card-text'><strong>Status:</strong> " . $complaint->status . "</p>
                        <div style='display: flex; align-items:center; gap:10px'>
                        <button href='#' class='card-link btn btn-danger m-0 w-100' onclick=\"deleteComplaint(event, '" . $complaint->complaintID . "')\">Delete</button>
                        <button href='#' class='card-link btn btn-success m-0 w-100' onclick=\"changeStatus('solved', '" . $complaint->complaintID . "')\">Resolve</button>
                        </div>
                      </div>
                    </div>
                    </div>";
                }
                echo "</div>";
            }


            $query = new MongoDB\Driver\Query(["status" => "solved"]);
            $cursor = $manager->executeQuery('Learniverse.complaint', $query);
            $complaints = $cursor->toArray();

            if (empty($complaints)) {
                echo "<h4 style='margin: 20px 0;'>No Resolved complaints to display.</h4>";
            } else {
                echo "<h4 style='margin: 20px 0;'>Resolved complaints</h4>";
                echo "<div class='row'>";
                foreach ($complaints as $complaint) {
                    // Select the database and collection
                    $database = $connection->Learniverse;
                    $Usercollection = $database->users;

                    $data = array(
                        "email" => $complaint->user
                    );

                    $fetch = $Usercollection->findOne($data);

                    echo "
                  <div class='col-md-4' id='complaint" . $complaint->complaintID . "'>
                  <div class='card'>
                    <div class='card-body'>
                      <h5 class='card-title'>Ticket #" . $complaint->complaintID . "</h5>
                      <h6 class='card-subtitle mb-2 text-muted'>Customer: " . $fetch['firstname'] . " " . $fetch['lastname'] . "</h6>
                      <p class='card-text'><strong>Date:</strong> " . $complaint->date . "</p>
                      <p class='card-text'>
                        Issue: " . $complaint->complaint . "
                      </p>
                      <p class='card-text'><strong>Status:</strong> " . $complaint->status . "</p>
                      <div style='display: flex; align-items:center; gap:10px'>
                      <button href='#' class='card-link btn btn-danger m-0 w-100' onclick=\"deleteComplaint(event, '" . $complaint->complaintID . "')\">Delete</button>
                      </div>
                    </div>
                  </div>
                  </div>";
                }
                echo "</div>";
            }
            ?>
        </div>

        <script>
            var complaintTitles = document.getElementsByClassName("complaintTitle");
            var overlays = document.getElementsByClassName("overlay");

            for (var i = 0; i < complaintTitles.length; i++) {
                complaintTitles[i].addEventListener('click', function() {
                    var overlay = this.parentElement.nextElementSibling;
                    overlay.style.display = "flex";
                });
            }

            for (var i = 0; i < overlays.length; i++) {
                overlays[i].addEventListener('click', function(event) {
                    if (event.target === this) {
                        this.style.display = "none";
                    }
                });
            }

            var replyInputs = document.querySelectorAll('#reply');

            for (var i = 0; i < replyInputs.length; i++) {
                replyInputs[i].addEventListener('input', function() {
                    var sendButton = this.nextElementSibling;
                    sendButton.style.display = "inline-block";
                });
            }
        </script>
    </main>
    <script>
        $(document).ready(function() {
            $('#replyForm').submit(function(e) {
                e.preventDefault(); // Prevent the default form submission behavior

                var formData = new FormData($('#replyForm')[0]); // Serialize the form data
                // Send AJAX request to the server
                $.ajax({
                    url: 'sendReply.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(response) {
                        if (response.status === 'success') {
                            changeStatus('pending', response.complaintID);
                            // Display success message
                            Swal.fire('Sent!', response.message, 'success');
                            window.location.reload();
                        } else {
                            // Display failure message
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        Swal.fire('Error!', 'An error occurred while sending the reply.', 'error');
                    }
                });
            });
        });

        function viewDetails(id) {
            const complaints = <?php echo json_encode($allComplaints); ?>;
            const complaint = complaints.find(complaint => complaint.complaintID === id);
            const customerName = document.getElementById('ticketCustomerName');
            const ticketDate = document.getElementById('ticketDate');
            const ticketBody = document.getElementById('ticketBody');
            const ticketDetailsLabel = document.getElementById('ticketDetailsLabel');
            const complaintID = document.getElementById('complaintID');
            const user = document.getElementById('user');
            const replyTextarea = document.getElementById('reply');


            ticketDetailsLabel.textContent = `Ticket #${complaint.complaintID}`;
            customerName.textContent = `Customer: ${complaint.user}`;
            ticketDate.textContent = `Date: ${complaint.date}`;
            ticketBody.textContent = `Details: ${complaint.complaint}`;
            complaintID.value = complaint.complaintID;
            user.value = complaint.user;
            replyTextarea.value = `Dear ${complaint.user},\nThank you for reaching out to us and sharing your concerns.\n\nThank you for using Learniverse.\n\nSincerely,\nThe Learniverse Team\n${complaint.admin}`;
        }

        const reports = <?php echo json_encode($allReports); ?>;

        console.log({
            reports
        })
    </script>

    <div class="modal fade bg-transparent" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsLabel">Ticket #12345</h5>
                    <button type="button" class="btn btn-neutral" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="ticketCustomerName"><strong>Customer:</strong> </p>
                    <p id="ticketDate"><strong>Date:</strong> </p>
                    <p id="ticketBody">
                        <strong>Details:</strong>
                    </p>

                    <!-- reply form -->
                    <form id="replyForm" method="POST" action="">
                        <input type="hidden" id="complaintID" name="complaintID" value="">
                        <input type="hidden" id="user" name="user" value="">
                        <div class="form-group">
                            <label for="reply">Reply</label>
                            <textarea class="form-control" id="reply" rows="3" name="reply" placeholder="Enter your reply here">
              </textarea>
                        </div>
                        <button type="submit" class="btn btn-primary m-0 w-100 d-block" id="send">
                            Submit
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Resolve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bg-transparent" id="viewReportsModal" tabindex="-1" aria-labelledby="viewReportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewReportsModalLabel">Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reportsContainer">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




</body>