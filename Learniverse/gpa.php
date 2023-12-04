<!DOCTYPE html>
<?php
include("gpabackend.php");
$user = null;
$googleID = null;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION["email"])) {
    $user = getUser($_SESSION["email"]);
}
if ($user == null) {
    header("Location: index.php");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <title>Grade Calculator </title>
    <link rel="stylesheet" href="workspaceCSS.css">
    <link rel="stylesheet" href="header-footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="stylegpa.css">

    <link rel="apple-touch-icon" sizes="180x180" href="favicon_io/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon_io/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon_io/favicon-16x16.png">
    <link rel="manifest" href="favicon_io/site.webmanifest">

    <!-- PROFILE STYLESHEET -->
    <link rel="stylesheet" href="profile.css">

    <!-- Custom stylesheet -->
    <link href="css/style.css" rel="stylesheet" />

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- GPA STYLESHEET -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

    <!-- GPA FUNCTIONS -->
    <script>
        gpaType = "";

        // function reinit() {
        //     $('.marks').on("change keyup input", function() {
        //         updateSubjectRating(this);
        //         updateSubjectPoint(this);
        //         recalc();
        //     });

        //     $('.grade').on("change keyup", function() {
        //         updateSubjectDegree(this);
        //         updateSubjectPoint(this);
        //         recalc();
        //     });

        //     $('.hours').on("change keyup", function() {
        //         updateSubjectPoint(this);
        //         recalc();
        //     });
        // }
        // reinit();
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

        function updateSubjectRating(inputElement) { // If marks = 95 then grade is A
            var degreeValue = parseFloat($(inputElement).val());
            var subjectRatingSelect = $(inputElement).closest('.subject').find('.grade');
            if (!isNaN(degreeValue)) {
                if (degreeValue >= 95) {
                    subjectRatingSelect.val('A+');
                } else if (degreeValue >= 90) {
                    subjectRatingSelect.val('A');
                } else if (degreeValue >= 85) {
                    subjectRatingSelect.val('B+');
                } else if (degreeValue >= 80) {
                    subjectRatingSelect.val('B');
                } else if (degreeValue >= 75) {
                    subjectRatingSelect.val('C+');
                } else if (degreeValue >= 70) {
                    subjectRatingSelect.val('C');
                } else if (degreeValue >= 60) {
                    subjectRatingSelect.val('D+');
                } else if (degreeValue >= 55) {
                    subjectRatingSelect.val('D');
                } else {
                    subjectRatingSelect.val('F');
                }
            } else {
                subjectRatingSelect.val('');
            }
        }

        function updateSubjectDegree(selectElement) { // if A then marks = 90
            console.log("updating marks based on grade");
            var selectedRating = $(selectElement).val();
            var subjectDegreeInput = $(selectElement).closest('.subject').find('.marks');
            switch (selectedRating) {
                case 'A+':
                    subjectDegreeInput.val('95');
                    break;
                case 'A':
                    subjectDegreeInput.val('90');
                    break;
                case 'B+':
                    subjectDegreeInput.val('85');
                    break;
                case 'B':
                    subjectDegreeInput.val('80');
                    break;
                case 'C+':
                    subjectDegreeInput.val('75');
                    break;
                case 'C':
                    subjectDegreeInput.val('70');
                    break;
                case 'D+':
                    subjectDegreeInput.val('60');
                    break;
                case 'D':
                    subjectDegreeInput.val('55');
                    break;
                case 'F':
                    subjectDegreeInput.val('0');
                    break;
                default:
                    subjectDegreeInput.val('');
                    break;
            }
        }

        function getGPA() {
            var selectedGpaSystem = gpaType;
            var gpa = parseInt(selectedGpaSystem);
            return gpa;
        }

        function GPA_Ratio(subjectRating) {
            gpa = getGPA();
            gpaValue = 0;
            switch (subjectRating) {
                case 'A+':
                    gpaValue = gpa === 5 ? 5 : 4;
                    break;
                case 'A':
                    gpaValue = gpa === 5 ? 4.75 : 3.75;
                    break;
                case 'B+':
                    gpaValue = gpa === 5 ? 4.5 : 3.5;
                    break;
                case 'B':
                    gpaValue = gpa === 5 ? 4 : 3;
                    break;
                case 'C+':
                    gpaValue = gpa === 5 ? 3.5 : 2.5;
                    break;
                case 'C':
                    gpaValue = gpa === 5 ? 3 : 2;
                    break;
                case 'D+':
                    gpaValue = gpa === 5 ? 2.5 : 1.5;
                    break;
                case 'D':
                    gpaValue = gpa === 5 ? 2 : 1;
                    break;
                case 'F':
                    gpaValue = gpa === 5 ? 0 : 0;
                    break;
                default:
                    gpaValue = gpa === 5 ? 0 : 0;
                    break;
            }
            return gpaValue;
        }

        function updateSubjectPoint(inputElement) {
            console.log("updating subject points ", getGPA());
            var subject = $(inputElement).closest('.subject');
            var subjectHour = parseFloat(subject.find('.hours').val());
            var subjectRating = subject.find('.grade').val();
            gpa = getGPA();
            if (gpa === 100) return;
            gpaValue = GPA_Ratio(subjectRating);
            var subjectPoint = (subjectHour * gpaValue).toFixed(2);
            if (isNaN(subjectPoint)) {
                subject.find('.points').val('0.0');
                subject.find('.point').val('0.0');
                return 0;
            } else {
                subject.find('.points').val(subjectPoint);
                subject.find('.point').val(subjectPoint);
                return subjectPoint;
            }
        }

        function recalc() {
            // Code to recalculate GPA based on the input values
            console.log('Recalculating GPA...');

            let totalPoints = 0.0;
            let totalHours = 0.0;
            if (getGPA() === 100) { //highschool gpa
                const marksElements = document.getElementsByClassName('marks');
                for (let i = 0; i < marksElements.length; i++) {
                    totalPoints += parseFloat(marksElements[i].value);
                }
                gpa = totalPoints / marksElements.length;
                if (isNaN(gpa)) {
                    gpa = 0.0;
                    $("#gpaResult").val(gpa);
                    $("#gpa").val(gpa);
                    return gpa;
                } else {
                    $("#gpaResult").val(gpa);
                    $("#gpa").val(gpa);
                    return gpa;
                }
            } else {
                const pointsElements = document.getElementsByClassName('points');
                const hoursElements = document.getElementsByClassName('hours');
                for (let i = 0; i < pointsElements.length; i++) {
                    totalPoints += parseFloat(pointsElements[i].value);
                    totalHours += parseFloat(hoursElements[i].value);
                }
                if (totalHours != 0) {
                    gpa = totalPoints / totalHours;
                    gpa = gpa.toFixed(2)
                    $("#gpaResult").val(gpa);
                    $("#gpa").val(gpa);
                    return gpa;
                }
                $("#gpaResult").val(0);
                $("#gpa").val(0);
                return 0;
            }
        }
    </script>
    <!-- Sidebar --->
    <script>
        var isSidebarOpen = false;
        var isButtonClicked = false;
        document.addEventListener('DOMContentLoaded', function() {
            var sidebarTongue = document.querySelector('#sidebar-tongue');
            console.log(sidebarTongue)
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

            function w3_open() {
                document.getElementsByClassName("mainpage")[0].style.marginLeft = "auto";
                document.getElementById("tools_div").style.transition = '1s';
                document.getElementById("sidebar-tongue").style.transition = '1s';
                document.getElementsByClassName("mainpage")[0].style.transition = '1s';
                document.getElementsByClassName("mainpage")[0].style.marginLeft = "12%";
                document.getElementById("tools_div").style.marginLeft = '0';
                document.getElementById("sidebar-tongue").style.marginLeft = '12%';
                document.getElementById("sidebar-tongue").textContent = "<";
                document.getElementById("sidebar-tongue").style.boxShadow = "none";
                document.addEventListener('DOMContentLoaded', function() {});
            }

            function w3_close() {
                document.getElementById("sidebar-tongue").style.transition = '1s';
                document.getElementById("tools_div").style.transition = '1s';
                document.getElementsByClassName("mainpage")[0].style.marginLeft = "0";
                document.getElementById("sidebar-tongue").textContent = ">";
                document.getElementById("tools_div").style.marginLeft = "-13.9%";
                document.getElementById("sidebar-tongue").style.marginLeft = '0';
                document.addEventListener('DOMContentLoaded', function() {});
            }
        });

        function newGPA() {
            let semesterCount = 0;
            let subjectCount = 0;
            gpaForm = null;
            Swal.fire({
                title: 'New GPA',
                html: `
      <form id="gpaForm1">
        <label class="txt" for="gpaName">GPA Name:</label>
        <input type="text" id="gpaName" name="gpaName" required>
        <br>
        <label class="txt" for="year">Year:</label>
        <input type="text" id="year" name="year" required>
        <br>
        <label class="txt" for="gpaType">GPA System Type:</label>
        <br><br>
        <input type="radio" id="type5" name="gpaType" value="5" required>
        <label class="radio" for="type5">5.0</label>
        <input type="radio" id="type4" name="gpaType" value="4" required>
        <label class="radio" for="type4">4.0</label>
        <input type="radio" id="type100" name="gpaType" value="100" required>
        <label class="radio" for="type100">100</label>
        <br>
      </form>`,
                showConfirmButton: true,
                showCancelButton: true,
                cancelButtonText: 'Close',
                confirmButtonText: 'Next',
                preConfirm: () => {
                    const form = document.getElementById("gpaForm1");
                    if (!form.checkValidity()) {
                        Swal.showValidationMessage('All fields are required');
                        return false; // Return false to prevent the modal from closing
                    }
                },
            }).then((result) => {
                const gpaName = document.getElementById('gpaName').value;
                const year = document.getElementById('year').value;
                gpaType = document.querySelector('input[name="gpaType"]:checked').value;

                const displayHtml = `
                <h3>GPA Name: ${gpaName}</h3><br><br>
          <b>Year: ${year}</b><br><br>
          <b>GPA System Type: ${gpaType}</b><br><br>
          <form id="gpaForm2">
          <input type="hidden" name="gpaName" value="${gpaName}">
          <input type="hidden" name="gpaYear" value="${year}">
          <input type="hidden" name="gpaType" value="${gpaType}">
          <span> <b>GPA:</b> <input disabled id="gpaResult" name="gpaResult" placeholder="0.0"></span> 
          </form><br><br>
          <button type="button" id="addSemesterBtn">Add Semester</button>
        `;

                Swal.fire({
                    title: 'New GPA',
                    html: displayHtml,
                    showConfirmButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Save',
                    didOpen: () => {
                        gpaForm = document.getElementById('gpaForm2');
                        const addSemesterBtn = document.getElementById('addSemesterBtn');
                        const semesterContainer = document.createElement('div');
                        semesterContainer.id = 'semesterContainer';
                        Swal.getHtmlContainer().appendChild(semesterContainer);

                        addSemesterBtn.addEventListener('click', () => {
                            semesterCount++;
                            subjectCount = 0;
                            const semesterDiv = document.createElement('div');
                            semesterDiv.classList.add('semester');
                            semesterDiv.innerHTML = `
        <h3>Semester ${semesterCount}</h3>
        <br>
        <div class="subjectContainer" id="subjectContainer${semesterCount}"></div><br><br>
        <button type="button" class="addSubjectBtn" data-semester="${semesterCount}">
          Add Subject
        </button>
        <br><br>
      `;
                            semesterContainer.appendChild(semesterDiv);
                            gpaForm.appendChild(semesterContainer);
                            const subjectContainer = document.getElementById(`subjectContainer${semesterCount}`);
                            const addSubjectBtn = document.querySelector(`[data-semester="${semesterCount}"]`);
                            addSubjectBtn.addEventListener('click', () => {
                                subjectCount++;
                                const subjectDiv = document.createElement('div');
                                subjectDiv.classList.add('subject');
                                subjectDiv.innerHTML = `<br><br><br>
          <label for="subjectName">Subject Name:</label>
          <input type="text" class="subjectName fill" name="subjectNameSem${semesterCount}Sub${subjectCount}" required>
          <label for="marks">Marks:</label>
          <input type="number" class="marks fill" name="marksSem${semesterCount}Sub${subjectCount}" min="0" max="100" required onchange="updateSubjectPoint(this); recalc();" oninput="updateSubjectRating(this); updateSubjectPoint(this); recalc();">
          <label for="hours" >Hours:</label>
          <input type="number" class="hours fill" name="hoursSem${semesterCount}Sub${subjectCount}" min="0" placeholder="0" oninput="updateSubjectPoint(this); recalc();" onchange="updateSubjectPoint(this); recalc();">
          <label for="grade">Grade:</label>
          <select name="gradeSem${semesterCount}Sub${subjectCount}" class="grade" required onchange="updateSubjectDegree(this); updateSubjectPoint(this); recalc();">
            <option value="">Select Grade</option>
            <option value="A+">A+</option>
            <option value="A">A</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="C+">C+</option>
            <option value="C">C</option>
            <option value="D+">D+</option>
            <option value="D">D</option>
            <option value="F">F</option>
          </select>
          <label for="points fill">Points: </label>
          <input disabled name="pointSem${semesterCount}Sub${subjectCount}" class="points fill" placeholder="0.0">
          <input type="hidden" name="pointsSem${semesterCount}Sub${subjectCount}" class="point">
          `;
                                subjectContainer.appendChild(subjectDiv);
                            });
                        });
                    },
                    preConfirm: () => {
                        const form = document.getElementById("gpaForm2");
                        if (!form.checkValidity()) {
                            Swal.showValidationMessage('All fields are required');
                            return false; // Return false to prevent the modal from closing
                        }
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(gpaForm);
                        const gpaValue = recalc(); // Calculate the GPA value using the recalc() function

                        // Append the GPA value to the FormData object
                        formData.append('gpa', gpaValue);
                        $.ajax({
                            url: 'gpabackend.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response);
                                Swal.fire({
                                    title: 'GPA Saved',
                                    text: 'GPA has been saved successfully.',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                                location.reload();
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'An error occurred while saving the GPA.',
                                    icon: 'error',
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            },
                        });
                    }
                });
            });
        }
    </script>
    <!-- GPA -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>


<style>
    .AddMore {
        background-color: #bf97d8;
        color: white;
    }
</style>


<body>
    <header>
        <div class="header-container" style="width:100vw;">
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
                                <a href="workspace.php">My Workspace</a>
                            </li>
                        </ul> <!-- end menu -->
                    </nav>
                </div>

                <div class="dropdown">
                    <button class="dropdown-button">
                        <i class="fas fa-user" id='Puser-icon'> </i>
                        <?php echo ' ' . $user['firstname']; ?>
                    </button>
                    <ul class="Pdropdown-menu">
                        <li class='editName center'>
                            <i id='editIcon' class='fas fa-user-edit' onclick='Rename()'></i>
                            <span id='Pname'>
                                <?php echo $user['firstname'] . " " .  $user['lastname']; ?>
                            </span>
                            <form id='rename-form' class='rename-form' method='POST' action='updateName.php?q=workspace.php' onsubmit="return validateForm(event)" ;>
                                <input type='text' id='PRename' name='Rename' required value='<?php echo $user['
                                    firstname'] . " " . $user['lastname']; ?>'><br>
                                <span id='rename-error' style='color: red;'></span><br>
                                <button type='submit'>Save</button> <button type='reset' onclick='cancelRename();'>Cancel</button>
                            </form>
                        </li>
                        <li class='center'>Username:
                            <?php echo $user['username']; ?>
                        </li>
                        <li class='center'>
                            <?php echo $user['email']; ?>
                        </li>
                        <hr>

                        <?php if ($googleID === null) {
                            echo "<li><a href='reset.php?q=workspace.php'><i class='far fa-edit'></i> Change password</a></li>";
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
                <li class="tool_item">
                    Shared spaces
                </li>
                <li class="tool_item">
                    Meeting Room
                </li>
                <li class="tool_item"><a href="community.php">
                        Community</a>
                </li>
            </ul>
        </div>

        <div class="main-content mainpage">
            <div class="row">
                <div class="col-md-6">
                    <h1>My GPA</h1>
                </div>
                <div class="col-md-6" style="position:relative">
                    <button type="button" id="new-gpa-btn" class="btn btn-primary" onclick="newGPA()">
                        New GPA
                    </button>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="gallery">
                <p id="load-msg">Loading GPAs...</p>
                <!-- GPA CELL -->
                <div class="gpacell d-none">
                    <p class="gpacell-name">College</p>
                    <p class="gpacell-year">2019</p>
                    <h5 class="gpacell-rate">4.53/5</h5>
                    <button class="gpacell-delete btn btn-primary btn-xs" value=-1 data-toggle="modal" data-target="#myModal"><i class="fas fa-trash" aria-hidden="true"></i></button>
                    <input hidden class="item-hours" .val();value=-1 type="number">
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

<!-- gpa model: -->
<!-- 

<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg" class="modal-cont">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add new GPA</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
             Modal Body 
            <div class="modal-body">
                <div class="gpa-container">
                    <div class="row non-update">
                        <div class="col-lg-12 col-md-12 col-sm-12 step1">
                            <div class="fieldset">
                                <h4><span>GPA System</span></h4>
                                <table>
                                    <tbody class="">
                                        <tr>
                                            <td>
                                                <input type="radio" checked="checked" value="5" name="gpaSystem" id="gpaSystem5" style="width: 40px;">
                                            </td>
                                            <td>
                                                <label for="gpaSystem5">5.00</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="radio" value="4" name="gpaSystem" id="gpaSystem4" style="width: 40px;">
                                            </td>
                                            <td>
                                                <label for="gpaSystem4">4.00</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="radio" value="100" name="gpaSystem" id="gpaSystem100" style="width: 40px;">
                                            </td>
                                            <td>
                                                <label for="gpaSystem100">100%</label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 step2 d-none">
                            <div class="fieldset">
                                <h4><span>GPA Result</span></h4>
                                <table>
                                    <tbody>
                                        <tr class="non-hs">
                                            <td>
                                                <label>Total Points: &ThickSpace;</label>
                                            </td>
                                            <td>
                                                <label id="totalPoint"></label>
                                            </td>
                                        </tr>
                                        <tr class="non-hs">
                                            <td>
                                                <label>Total Hours: &ThickSpace;</label>
                                            </td>
                                            <td>
                                                <label id="totalHour"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>GPA: &ThickSpace;</label>
                                            </td>
                                            <td>
                                                <label id="totalRate"></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label>Enter Year: &ThickSpace;</label>
                                            </td>
                                            <td>
                                                <input type="text" name="gpa-year" id="gpa-year" class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row update-only d-none">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label>Previous GPA: &ThickSpace;</label>
                                        </td>
                                        <td style="width:15rem;">
                                            <input type="number" name="gpa-old" id="gpa-old" class="form-control" value="0">
                                            <input hidden type="number" name="hours-old" id="hours-old" class="form-control" value="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label>New GPA: &ThickSpace;</label>
                                        </td>
                                        <td style="width:15rem;">
                                            <label id="gpa-new"></label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row step2 d-none">
                        <div class="col-lg-12" style="overflow: auto;overflow-y: hidden; padding-top: 20px">
                            <table class="table table-condensed" id="tabl">
                                <thead>
                                    <tr>
                                        <td>
                                            <h4>Subject Name</h4>
                                        </td>
                                        <td>
                                            <h4>Degree</h4>
                                        </td>
                                        <td class="non-hs">
                                            <h4>Hours</h4>
                                        </td>
                                        <td>
                                            <h4>Rate</h4>
                                        </td>
                                        <td class="non-hs">
                                            <h4>Points</h4>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody id="subTable">
                                    <tr>
                                        <td>
                                            <input type="text" name="name" class="subjectName form-control">
                                        </td>
                                        <td><input type="number" class="subjectDegree form-control" min="0" max="100"></td>
                                        <td class="non-hs"><input type="number" class="subjectHour form-control"></td>
                                        <td>
                                            <select class="subjectRating form-control">
                                                <option value="">Not specified</option>
                                                <option value="A+">A+</option>
                                                <option value="A">A</option>
                                                <option value="B+">B+</option>
                                                <option value="B">B</option>
                                                <option value="C+">C+</option>
                                                <option value="C">C</option>
                                                <option value="D+">D+</option>
                                                <option value="D">D</option>
                                                <option value="F">F</option>
                                            </select>
                                        </td>
                                        <td class="non-hs"><label class="subjectPoint"></label></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="AddMore btn">
                                <span>Add Subject <i class="fa fa-plus" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             Modal Footer 
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary step1 non-update" id="next-step">Next</button>
                <button type="button" class="btn btn-primary step2 d-none SaveBtn">Save</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Calculate GPA :-->
<script>
    // let CUR_GPA = -1;
    // subTable = $('#subTable');
    // $('#myModal').on('hide.bs.modal', function() {
    //     console.log('Modal is hidden');
    //     $('.non-update').removeClass('d-none');
    //     $('.update-only').addClass('d-none');
    //     $('#gpa-old').val(0);
    //     $('#hours-old').val(0);

    //     $('.step1').removeClass('d-none');
    //     $('.step2').addClass('d-none');
    //     subTable.children().not(':first').remove();
    //     subTable.find('input').val('');
    //     $('.modal-title').text('Add New GPA');
    //     CUR_GPA = -1;
    // });

    // $('#next-step').on('click', function() {
    //     console.log('click next');
    //     $('.step1').toggleClass('d-none');
    //     $('.step2').toggleClass('d-none');
    // })





    // function recalc() {
    // console.log("recalculating......")
    //     var table = document.get;
    //     var totalPoints = 0;
    //     var totalHours = 0;
    //     var totalDegrees = 0;
    //     var count = 0;
    //     table.find("tr").each(function() {
    //         var currentRow = $(this);
    //         var cell = currentRow.find("td:eq(0)");
    //         curPoints = updateSubjectPoint(cell);
    //         curHours = parseFloat(currentRow.find('.subjectHour').val());
    //         if (!isNaN(curPoints) && !isNaN(curHours)) {
    //             totalPoints += parseFloat(curPoints);
    //             totalHours += parseFloat(curHours);
    //         }
    //         curDegree = parseFloat(currentRow.find('.subjectDegree').val());
    //         if (!isNaN(curDegree)) {
    //             totalDegrees += parseFloat(curDegree);
    //             count += 1;
    //         }
    //     })
    //     const oldgpa = parseFloat($('#gpa-old').val())
    //     const oldhours = parseFloat($('#hours-old').val())
    //     if (getGPA() === 100) {
    //         console.log("highschool")
    //         if (oldgpa !== 0) {
    //             console.log("update 100 old gpa", oldgpa, oldhours);
    //             totalDegrees += (oldgpa * oldhours)
    //             count += oldhours
    //         } else console.log("add 100 new gpa");
    //         var gpa = totalDegrees / count;
    //         $('#totalHour').text(count);
    //         if (isNaN(gpa)) {
    //             $('#totalTermRate').text('');
    //         } else {
    //             $('#totalTermRate').text(gpa.toFixed(2));
    //         }
    //         if (oldgpa !== 0) {
    //             $('#gpa-new').text(gpa.toFixed(2));
    //         }
    //         if (isNaN(gpa)) {
    //             $('#totalRate').text('');
    //         } else {
    //             $('#totalRate').text(gpa.toFixed(2));
    //         }
    //         return isNaN(gpa) ? -1 : gpa;
    //     }

    //     if (oldgpa !== 0) {
    //         console.log("update old gpa", oldgpa, oldhours);
    //         totalPoints += (oldgpa * oldhours);
    //         totalHours += oldhours;
    //     } else console.log("add new gpa");

    //     var gpa = totalPoints / totalHours;
    //     if (!isNaN(gpa)) {
    //         $('#totalTermPoint').text(totalPoints.toFixed(2));
    //         $('#totalTermHour').text(totalHours.toFixed(2));
    //         $('#totalTermRate').text(gpa.toFixed(2));
    //     } else {
    //         $('#totalTermPoint').text('');
    //         $('#totalTermHour').text('');
    //         $('#totalTermRate').text('');
    //     }

    //     if (!isNaN(gpa)) {
    //         $('#totalPoint').text(totalPoints.toFixed(2));
    //         $('#totalHour').text(totalHours.toFixed(2));
    //         $('#totalRate').text(gpa.toFixed(2));
    //     } else {
    //         $('#totalPoint').text('');
    //         $('#totalHour').text('');
    //         $('#totalRate').text('');
    //     }

    //     if (oldgpa !== 0) {
    //         $('#gpa-new').text(gpa.toFixed(2));
    //     }
    //     return isNaN(gpa) ? -1 : gpa;
    // }



    // function reinit() {
    //     $('.subjectDegree').on("change keyup", function() {
    //         updateSubjectRating(this);
    //         updateSubjectPoint(this);
    //         recalc();
    //     });

    //     $('.subjectRating').on("change keyup", function() {
    //         updateSubjectDegree(this);
    //         updateSubjectPoint(this);
    //         recalc();
    //     });

    //     $('.subjectHour').on("change keyup", function() {
    //         updateSubjectPoint(this);
    //         recalc();
    //     });

    //     $('input[name="gpaSystem"]').on('change', function() {
    //         var selectedGpaSystem = $(this).val();
    //         var nonHsElements = $('.non-hs');
    //         if (selectedGpaSystem === '100') {
    //             nonHsElements.addClass('d-none');
    //         } else {
    //             nonHsElements.removeClass('d-none');
    //         }
    //         var table = $("#myTable");
    //         table.find("tr").each(function() {
    //             var currentRow = $(this);
    //             var cell = currentRow.find("td:eq(0)");
    //             updateSubjectPoint(cell);
    //         })
    //         recalc();
    //     });
    //}
    // $(".AddMore").on("click", function() {
    //     var firstRow = $("#subTable tr:first").clone();
    //     console.log(firstRow.get(0));
    //     firstRow.find("input[type=text], input[type=number], select").val("");
    //     firstRow.find('.subjectPoint').text('');
    //     $("#subTable").append(firstRow);
    //     // reinit();
    // });

    // $(".SaveBtn").on("click", function() {
    //     var button = $(this);
    //     button.prop("disabled", true);
    //     var gpa = recalc();
    //     var yr = $('#gpa-year').val();
    //     if (gpa === -1 || (yr === '' && CUR_GPA === -1)) {
    //         alert("Please input all required fields to save");
    //         button.prop("disabled", false);
    //         return;
    //     }
    //     const hours = parseFloat($("#totalHour").text());
    //     var data = {
    //         gpa: gpa,
    //         type: getGPA(),
    //         hours: hours,
    //         year: yr,
    //         id: CUR_GPA,
    //     };

    //     $.ajax({
    //         type: "POST",
    //         url: "gpabackend.php",
    //         data: data,
    //         success: function(response) {
    //             console.log("PHP page returned: ", response);
    //             alert("GPA Saved Successfully");
    //             button.prop("disabled", false);
    //             location.reload();
    //         },
    //         error: function() {
    //             console.log("An error occurred while making the AJAX request.");
    //             button.prop("disabled", false);
    //         }
    //     });
    // });
    // reinit();
  
    $(document).ready(function() {
        var container = $('.gallery');

        $.ajax({
            type: "POST",
            data: {
                getall: true,
            },
            url: "gpabackend.php",
            success: function(data) {
                data = JSON.parse(data);
                console.log("PHP page returned GET: ", data);
                if (data.length === 0) {
                    $('#load-msg').text('No GPA was added yet.')
                } else {
                    $('#load-msg').addClass('d-none');
                }
                $.each(data, function(index, item) {
                    var newGpaCell = container.children('.gpacell:first').clone();
                    newGpaCell.find('.gpacell-name').text(item.name);
                    newGpaCell.find('.gpacell-year').text(item.year);
                    var roundedGpa = parseFloat(item.gpa).toFixed(2);
                    newGpaCell.find('.gpacell-rate').text(roundedGpa + '/' + item.type);
                    newGpaCell.find('.item-hours').val(item.hours);
                    //DELETE GPA
                    newGpaCell.find('.gpacell-delete').val(item._id.$oid).on('click', function(e) {
                        Swal.fire({
                            title: 'Heads Up!',
                            text: 'Are you sure you want to delete this GPA?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                        }).then((result) => {
                            if (result.isConfirmed) { // Check if the user clicked the "Yes, delete it!" button
                                $.ajax({
                                    url: 'gpabackend.php',
                                    method: 'POST',
                                    data: {
                                        id: item._id.$oid,
                                        deleteGPA: true
                                    },
                                    success: function(response) {
                                        // Code to handle the success response
                                        console.log('Request successful:', response);
                                        Swal.fire({
                                            title: 'GPA Deleted',
                                            text: 'GPA has been deleted successfully.',
                                            icon: 'success',
                                            showConfirmButton: false,
                                            timer: 2000,
                                        });
                                        location.reload();
                                    },
                                    error: function(xhr, status, error) {
                                        // Code to handle the error response
                                        console.log('Request failed:', error);
                                    }
                                });
                            }
                        });
                    });
                    // EDIT GPA
                    //     CUR_GPA = $(this).val();
                    //     var gpacellRate = $(this).closest('.gpacell').find('.gpacell-rate');
                    //     var gpaValue = parseFloat(gpacellRate.text().split("/")[1]);
                    //     var oldhours = $(this).closest('.gpacell').find('.item-hours').val();
                    //     console.log(gpaValue, 'radddd');
                    //     $('input[name="gpaSystem"][value="' + gpaValue + '"]').prop("checked", true).trigger('change');
                    //     $('.step1').removeClass('d-none');
                    //     $('.step2').removeClass('d-none');
                    //     $('.non-update').addClass('d-none');
                    //     $('.update-only').removeClass('d-none');
                    //     $('.modal-title').text('Update GPA');
                    //     $('#gpa-old').val(parseFloat(gpacellRate.text().split("/")[0]));
                    //     $('#gpa-new').text(parseFloat(gpacellRate.text().split("/")[0]));
                    //     $('#hours-old').val(oldhours);
                    // });

                    // Add the new GPA cell to the container
                    newGpaCell.removeClass('d-none');
                    container.append(newGpaCell);
                });
            },
            error: function(e) {
                console.log("An error occurred while making the AJAX request.");
                console.log(e);
            }
        });

    })
</script>

</html>