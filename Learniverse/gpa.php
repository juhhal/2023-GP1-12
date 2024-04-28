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

    <!-- CUSTOMER SUPPORT STYLESHEET -->
    <script src="../customerSupport.js"></script>
    <link rel="stylesheet" href="../customerSupport.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom stylesheet -->
    <link href="css/style.css" rel="stylesheet" />

    <!-- Sweetalert2 -->
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- GPA STYLESHEET -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />


</head>


<style>
    .AddMore {
        background-color: #bf97d8;
        color: white;
    }

    .gpacell .gpacell-edit {
        position: absolute;
        color: white;
        bottom: 10px;
        right: 50px;
        background-color: #ec947e;
    }

    .gpacell .gpacell-edit:hover {
        background-color: #cc7c68;
        color: white;

    }

    .gpacell .gpacell-edit:focus {
        background-color: #cc7c68;
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
                        <ul class="nav__menu" style="font-family: 'Gill Sans', 'Gill Sans MT', 'Calibri', 'Trebuchet MS',
      'sans-serif' !important; ">
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
                                <?php echo $user['firstname'] . " " . $user['lastname']; ?>
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

                        <li onclick="customerSupport()"><a href='#'><i class='far fa-question-circle'></i> Customer Support</a></li>
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
                    <button class="gpacell-delete btn btn-primary btn-xs" value=-1><i class="fas fa-trash" aria-hidden="true"></i></button>
                    <button class="gpacell-edit btn "><i class="fas fa-edit" aria-hidden="true"></i></button>
                    <input hidden class="item-hours" .val();value=-1 type="number">
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
<!-- GPA -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- GPA FUNCTIONS -->
<script>
    gpaType = "";


    function numbersonly(e) {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) { //if the key isn't the backspace key (which we should allow)
            if (unicode < 48 || unicode > 57) //if not a number
                return false //disable key press
        }
    }


    function validDegree(that, e) {
        var input = parseInt(that.value);
        if (input < 0 || input > 100) {
            alert("Value should be between 0 - 100");
            that.value = '';
        }
        return;
    }


    function validHours(that, e) {
        var input = parseInt(that.value);
        if (input < 1 || input > 10) {
            alert("Value should be between 1 - 10");
            that.value = '';
        }
        return;
    }

    function validYear(that, e) {
        var input = parseInt(that.value);
        if (input < 1 || input > 10) {
            alert("Value should be between 1 - 10");
            that.value = '';
        }
        return;
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
        recalc();
    }

    function updateSubjectDegree(selectElement) { // if A then marks = 90
        // console.log("updating marks based on grade");
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
        recalc();
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
        // console.log("updating subject points ", getGPA());
        var subject = $(inputElement).closest('.subject');
        var subjectHour = parseFloat(subject.find('.hours').val());
        var subjectRating = subject.find('.grade').val();
        gpa = getGPA();
        if (gpa === 100) return;
        if (subjectHour < 0) {
            $(".gpaResult").val(0);
            $(".points").val(0);
            return;
        }
        gpaValue = GPA_Ratio(subjectRating);
        var subjectPoint = (subjectHour * gpaValue).toFixed(2);
        if (isNaN(subjectPoint)) {
            subject.find('.points').val('0.0');
            subject.find('.point').val('0.0');
            recalc();
            return 0;
        } else {
            subject.find('.points').val(subjectPoint);
            subject.find('.point').val(subjectPoint);
            recalc();
            return subjectPoint;
        }
    }

    function recalc() {
        // Code to recalculate GPA based on the input values
        // console.log('Recalculating GPA...');

        let totalPoints = 0;
        let totalHours = 0;
        if (getGPA() === 100) { //highschool gpa
            const marksElements = document.getElementsByClassName('marks');
            for (let i = 0; i < marksElements.length; i++) {
                totalPoints += parseFloat(marksElements[i].value);
            }
            gpa = totalPoints / marksElements.length;
            if (isNaN(gpa)) {
                gpa = 0;
                $(".gpaResult").val(gpa);
                $("#gpa").val(gpa);
                return gpa;
            } else {
                $(".gpaResult").val(gpa);
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
                $(".gpaResult").val(gpa);
                $("#gpa").val(gpa);
                return gpa;
            }
            $(".gpaResult").val(0);
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
        // console.log(sidebarTongue)
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

    function makeRed(element) {
        if (element.value < 0)
            element.style.borderColor = "red";
        else {
            element.style.borderColor = "#ccc";
        }
    }

    function hasDuplicateValues(array) {
        const uniqueValues = new Set(array);
        return uniqueValues.size !== array.length;
    }

    function findDuplicateValues(array) {
        const counts = {};
        const duplicates = [];

        for (const value of array) {
            counts[value] = (counts[value] || 0) + 1;
            if (counts[value] === 2) {
                duplicates.push(value);
            }
        }

        return duplicates;
    }

    function highlightDuplicateSubjectNames(elements) {
        const subjectNames = Array.from(elements).map((elem) => elem.value);
        const duplicateNames = findDuplicateValues(subjectNames);

        for (const element of elements) {
            if (duplicateNames.includes(element.value)) {
                makeRed(element);
            }
        }
    }





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
                   <input type="text" id="year" name="year"  min="2010" max="2023" onkeypress="return numbersonly(event)"  maxlength="4" required>
                   <br>
                   <label class="txt" for="gpaType">GPA System Type:</label>
                   <br><br>
                   <input type="radio" id="type5" name="gpaType" value="5" required>
                   <label class="radio" for="type5">5.0</label>
                   <input type="radio" id="type4" name="gpaType" value="4" required>
                   <label class="radio" for="type4">4.0</label>
                   <input type="radio" id="type100" name="gpaType" value="100" required>
                   <label class="radio" for="type100">100</label>
                 </form>`,
            showConfirmButton: true,
            showCancelButton: true,
            cancelButtonText: 'Close',
            confirmButtonText: 'Next',
            preConfirm: () => {
                const form = document.getElementById("gpaForm1");
                const yearField = document.querySelector('#gpaForm1 #year');
                const fieldValue = yearField.value;



                // Regular expression to match only numeric values
                const numericRegex = /^[0-9]+$/;

                if (!numericRegex.test(fieldValue)) {
                    // Display an error message
                    Swal.showValidationMessage('Please Enter a Valid Year.');
                    yearField.value = -1;
                    makeRed(yearField);
                    yearField.value = "";
                    return false; // Return false to prevent the modal from closing
                }
                document.querySelector("#gpaForm1 #gpaName").value.trim();
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
                    <span> <b>GPA:</b> <input disabled class="gpaResult" name="gpaResult" placeholder="0.0"></span> 
                    </form><br>
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
                        let subjectCount = 0;
                        const semesterDiv = document.createElement('div');
                        semesterDiv.classList.add('semester');
                        hoursdisplay = "visible";
                        hourstype = "number";

                        semesterDiv.innerHTML = `
                                <h3>Semester ${semesterCount}</h3>        
                                <div class="subjectContainer" id="subjectContainer${semesterCount}"></div>
                                <button type="button" class="addSubjectBtn" data-semester="${semesterCount}">
                                  Add Subject
                                </button>        
                                <br>
                              `;
                        semesterContainer.appendChild(semesterDiv);
                        gpaForm.appendChild(semesterContainer);
                        const subjectContainer = document.getElementById(`subjectContainer${semesterCount}`);
                        const addSubjectBtn = document.querySelector(`[data-semester="${semesterCount}"]`);
                        addSubjectBtn.addEventListener('click', () => {
                            subjectCount++;
                            const subjectDiv = document.createElement('div');
                            subjectDiv.classList.add('subject');
                            var addCode = `<br>
                                <label for="subjectName">Subject Name:</label>
                                <input type="text" class="subjectName fill" name="subjectNameSem${semesterCount}Sub${subjectCount}" required>
                                <label for="marks">Marks:</label>
                                <input type="text" class="marks fill" name="marksSem${semesterCount}Sub${subjectCount}" min="0" max="100" required onchange="updateSubjectPoint(this); recalc(); makeRed(this);" oninput="updateSubjectRating(this); updateSubjectPoint(this); recalc(); makeRed(this);"
                                onkeypress="return numbersonly(event)"  onkeyup="validDegree(this,event);" maxlength="3>`;

                            if (getGPA() != 100) {
                                addCode += `<label for="hours" style="visibility:${hoursdisplay}">Hours:</label>
                                <input type="text" class="hours fill" name="hoursSem${semesterCount}Sub${subjectCount}" required min="0" placeholder="0" oninput="updateSubjectPoint(this); recalc(); makeRed(this);" onchange="updateSubjectPoint(this); recalc(); makeRed(this);" 
                                onkeypress="return numbersonly(event)"
                                onkeyup="validHours(this,event);"  maxlength="2">`;
                            }
                            addCode += `
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
                                </select>`;
                            if (getGPA() != 100) {
                                addCode += `
                                <label for="points fill">Points: </label>
                                <input disabled name="pointSem${semesterCount}Sub${subjectCount}" class="points fill" placeholder="0.0">
                                <input type="hidden" name="pointsSem${semesterCount}Sub${subjectCount}" class="point">
                                `;
                            }

                            subjectDiv.innerHTML = addCode;
                            subjectContainer.appendChild(subjectDiv);
                        });
                    });
                },
                preConfirm: () => {
                    const form = document.getElementById("gpaForm2");
                    const subjectNameElements = form.getElementsByClassName("subjectName");
                    const subjectNames = Array.from(form.getElementsByClassName("subjectName")).map((elem) => elem.value);

                    if ($("#gpaForm2 .hours").val() < 0) {
                        Swal.showValidationMessage("Hours cannot be negative.");
                        $("#gpaForm2 .gpaResult").val(0);
                        $("#gpaForm2 .points").val(0);
                        makeRed($("#gpaForm2 .hours").val());
                        return false; // Return false to prevent the modal from closing
                    } else if ($("#gpaForm2 .marks").val() < 0) {
                        Swal.showValidationMessage("Marks cannot be negative.");
                        $("#gpaForm2 .gpaResult").val(0);
                        $("#gpaForm2 .points").val(0);
                        makeRed($("#gpaForm2 .marks").val());
                        return false; // Return false to prevent the modal from closing
                    } else if (!form.checkValidity()) {
                        Swal.showValidationMessage("All fields are required.");
                        return false; // Return false to prevent the modal from closing
                    } else if (hasDuplicateValues(subjectNames)) {
                        Swal.showValidationMessage("Subject names must be unique.");
                        highlightDuplicateSubjectNames(subjectNameElements);
                        return false; // Return false to prevent the modal from closing
                    }

                },
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(gpaForm);
                    const gpaValue = recalc(); // Calculate the GPA value using the recalc() function
                    // Append the GPA value to the FormData object
                    formData.append('gpa', gpaValue);
                    formData.append('addNewGpa', true);
                    $.ajax({
                        url: 'gpabackend.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {

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




<script>
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
                        event.stopPropagation();
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
                    }); //end delete




                    // VIEW GPA
                    newGpaCell.find('.gpacell-edit').val(item._id.$oid).on('click', function(e) {
                        gpaType = item.type;
                        let semesterCount = 0;
                        let gpaFormUpdate = null;

                        let displayHtml = `
                        <form id="gpaForm3">
                        <table class="table table-sm">
                        <tr>
                        <td> <h3>GPA Name: <input type="text" id="gpaName" name="gpaName" value="${item.name}" required></h3></td>
                        </tr>
                        <tr>
                        <td> <h3>Year:  <input type="text" id="gpaYear" name="gpaYear" value="${item.year}"  min="2010" max="2023" onkeypress="return numbersonly(event)"  maxlength="4" required></h3></td>
                        </tr>
                        <tr>
                        <td> <h3>GPA System Type: ${item.type}</h3></td>
                        </tr>
                        <tr>
                        <td>
                            <input type="hidden" value="${item.type}" name="gpaType">
                            <span class="middle"> <b>Previous GPA:</b> <input type="text" value="${item.gpa}" id="prevGPA" name="prevGPA" placeholder="0.0" disabled>
                            <b>New GPA:</b> <input type"text" value="${item.gpa}" class="gpaResult" id="newGPA2" name="newGPA2" placeholder="0.0" disabled></span> 
                        </td>
                        </tr>
                        </table>
                        </form>
                        <button type="button" id="addSemesterBtn2">Add Semester</button>`;
                        Swal.fire({
                            title: 'Update GPA',
                            html: displayHtml,
                            showConfirmButton: true,
                            showCancelButton: true,
                            cancelButtonText: 'Close',
                            confirmButtonText: 'Update GPA',
                            didOpen: () => {
                                gpaFormUpdate = document.getElementById('gpaForm3');
                                const addSemesterBtn2 = document.getElementById('addSemesterBtn2');
                                let semesterContainer = document.createElement('div');
                                semesterContainer.id = 'semesterContainer';
                                Swal.getHtmlContainer().appendChild(semesterContainer);
                                if (item.semesters) {
                                    item.semesters.forEach((semesterData, semesterIndex) => {
                                        semesterCount++;
                                        subjectCount = 0;

                                        // Create a new semester div and populate it
                                        let semesterDiv = document.createElement('div');
                                        semesterDiv.classList.add('semester');
                                        semesterDiv.innerHTML = `
                                     <h3>Semester ${semesterIndex+1}</h3>   
                                     <br><hr>                     
                                     <div class="subjectContainer" id="subjectContainer${semesterIndex+1}"></div>
                                     <button type="button" class="addSubjectBtn" data-semester="${semesterIndex+1}">
                                       Add Subject
                                     </button>                                        
                                    `; //TARGET
                                        semesterContainer.appendChild(semesterDiv);
                                        gpaFormUpdate.appendChild(semesterContainer);
                                        let subjectContainer = document.getElementById(`subjectContainer${semesterIndex+1}`);
                                        hoursdisplay = "visible";
                                        hourstype = "number";



                                        // Populate subjects for this semester
                                        semesterData.forEach((subject, subIndex) => {
                                            subjectCount++;
                                            subjectDiv = document.createElement('div');
                                            subjectDiv.classList.add('subject');
                                            var editCode = '';

                                            editCode = `                                   
                                        <label for="subjectName">Subject Name:</label>                                   
                                        <input type="text"  class="subjectName fill TARGET" name="subjectNameSem${semesterIndex+1}Sub${subIndex + 1}" value="${subject.name}" required>
                                        <label for="marks">Marks:</label>
                                        <input type="text" class="marks fill" name="marksSem${semesterIndex+1}Sub${subIndex + 1}" value="${subject.marks}" min="0" max="100" required onchange="updateSubjectPoint(this); recalc(); makeRed(this)" oninput="updateSubjectRating(this); updateSubjectPoint(this); recalc(); makeRed(this)" onkeypress="return numbersonly(event)" 
                                        onkeyup="validDegree(this,event);" maxlength="3>
                                        `;
                                            if (getGPA() != 100) {
                                                editCode += `
                                        <label for="hours" style="visibility:${hoursdisplay}">Hours:</label>
                                        <input type="text" class="hours fill" name="hoursSem${semesterIndex+1}Sub${subIndex + 1}" value="${subject.hours}" min="0" placeholder="0" oninput="updateSubjectPoint(this); recalc(); makeRed(this)" onchange="updateSubjectPoint(this); recalc(); makeRed(this)"
                                        onkeypress="return numbersonly(event)" onkeyup="validHours(this,event);" maxlength="2">`;
                                            }

                                            editCode += `<label for="grade">Grade:</label>
                                        <select name="gradeSem${semesterIndex+1}Sub${subIndex + 1}" class="grade" required onchange="updateSubjectDegree(this); updateSubjectPoint(this); recalc();">
                                            <option value="">Select Grade</option>
                                            <option value="A+" ${subject.grade === 'A+' ? 'selected' : ''}>A+</option>
                                            <option value="A" ${subject.grade === 'A' ? 'selected' : ''}>A</option>
                                            <option value="B+" ${subject.grade === 'B+' ? 'selected' : ''}>B+</option>
                                            <option value="B" ${subject.grade === 'B' ? 'selected' : ''}>B</option>
                                            <option value="C+" ${subject.grade === 'C+' ? 'selected' : ''}>C+</option>
                                            <option value="C" ${subject.grade === 'C' ? 'selected' : ''}>C</option>
                                            <option value="D+" ${subject.grade === 'D+' ? 'selected' : ''}>D+</option>
                                            <option value="D" ${subject.grade === 'D' ? 'selected' : ''}>D</option>
                                            <option value="F" ${subject.grade === 'F' ? 'selected' : ''}>F</option>
                                        </select>`;

                                            if (getGPA() != 100) {
                                                editCode += `
                                        <label for="points fill" style="visibility:${hoursdisplay}">Points: </label>
                                        <input disabled type="${hourstype}" name="pointSem${semesterIndex+1}Sub${subIndex + 1}" class="points fill" value="${subject.points}" placeholder="0.0">
                                        <input type="hidden" name="pointsSem${semesterIndex+1}Sub${subIndex + 1}" class="point" value="${subject.points}">
                                        `;
                                            }
                                            subjectDiv.innerHTML = editCode;

                                            //HERE
                                            subjectContainer.appendChild(subjectDiv);
                                        });



                                        const addSubjectBtn3 = document.querySelector(`[data-semester="${semesterIndex+1}"]`);
                                        addSubjectBtn3.addEventListener('click', () => {
                                            subjectCount++;
                                            const subjectDiv = document.createElement('div');
                                            subjectDiv.classList.add('subject');
                                            var editAddCode =
                                                `<br>
                                       <label for="subjectName">Subject Name:</label>
                                       <input type="text" class="subjectName fill" name="subjectNameSem${semesterIndex + 1}Sub${subjectCount}" required>
                                       <label for="marks">Marks:</label>
                                       <input type="text" class="marks fill" name="marksSem${semesterIndex + 1}Sub${subjectCount}" min="0" max="100" required onchange="updateSubjectPoint(this); recalc(); makeRed(this);" oninput="updateSubjectRating(this); updateSubjectPoint(this); recalc(); makeRed(this);"
                                       onkeypress="return numbersonly(event)" maxlength="3>`;

                                            if (getGPA() != 100) {
                                                editAddCode += `
                                       <label for="hours" style="visibility:${hoursdisplay}">Hours:</label>
                                       <input type="text" class="hours fill" name="hoursSem${semesterIndex + 1}Sub${subjectCount}" min="0" placeholder="0" oninput="updateSubjectPoint(this); recalc(); makeRed(this);" onchange="updateSubjectPoint(this); recalc(); makeRed(this);" 
                                       onkeypress="return numbersonly(event)" maxlength="2">`;
                                            }
                                            editAddCode += `
                                       <label for="grade">Grade:</label>
                                       <select name="gradeSem${semesterIndex + 1}Sub${subjectCount}" class="grade" required onchange="updateSubjectDegree(this); updateSubjectPoint(this); recalc();">
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
                                       </select>`;

                                            if (getGPA() != 100) {
                                                editAddCode += `
                                       <label for="points fill">Points: </label>
                                       <input disabled name="pointSem${semesterIndex + 1}Sub${subjectCount}" class="points fill" placeholder="0.0">
                                       <input type="hidden" name="pointsSem${semesterIndex + 1}Sub${subjectCount}" class="point">
                                       `;
                                            }
                                            subjectDiv.innerHTML = editAddCode;

                                            subjectContainer.appendChild(subjectDiv);

                                        });

                                    }); //end semesters population loop
                                }


                                addSemesterBtn2.addEventListener('click', () => {
                                    semesterCount++;
                                    let subjectCount = 0;
                                    const semesterDiv = document.createElement('div');
                                    semesterDiv.classList.add('semester');
                                    semesterDiv.innerHTML = `
                                  <h3>Semester ${semesterCount}</h3>                                          
                                  <div class="subjectContainer" id="subjectContainer${semesterCount}"></div><br><br>
                                  <button type="button" class="addSubjectBtn" data-semester="${semesterCount}">
                                    Add Subject
                                  </button>
                                  <br><br>`;
                                    semesterContainer.appendChild(semesterDiv);

                                    const subjectContainer2 = document.getElementById(`subjectContainer${semesterCount}`);
                                    const addSubjectBtn2 = document.querySelector(`[data-semester="${semesterCount}"]`);
                                    addSubjectBtn2.addEventListener('click', () => {
                                        subjectCount++;
                                        const subjectDiv = document.createElement('div');
                                        subjectDiv.classList.add('subject');
                                        var editCode =
                                            `<br>
                                        <label for="subjectName">Subject Name:</label>
                                        <input type="text" class="subjectName fill" name="subjectNameSem${semesterCount}Sub${subjectCount}" required>
                                        <label for="marks">Marks:</label>
                                        <input type="text" class="marks fill" name="marksSem${semesterCount}Sub${subjectCount}" min="0" max="100" required onchange="updateSubjectPoint(this); recalc(); makeRed(this);" oninput="updateSubjectRating(this); updateSubjectPoint(this); recalc(); makeRed(this);"
                                        onkeypress="return numbersonly(event)" maxlength="3>`;

                                        if (getGPA() != 100) {
                                            editCode += `
                                        <label for="hours" style="visibility:${hoursdisplay}">Hours:</label>
                                        <input type="text" class="hours fill" name="hoursSem${semesterCount}Sub${subjectCount}" min="0" placeholder="0" oninput="updateSubjectPoint(this); recalc(); makeRed(this);" onchange="updateSubjectPoint(this); recalc(); makeRed(this);" 
                                        onkeypress="return numbersonly(event)" maxlength="2">`;
                                        }
                                        editCode += `
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
                                        </select>`;

                                        if (getGPA() != 100) {
                                            editCode += `
                                        <label for="points fill">Points: </label>
                                        <input disabled name="pointSem${semesterCount}Sub${subjectCount}" class="points fill" placeholder="0.0">
                                        <input type="hidden" name="pointsSem${semesterCount}Sub${subjectCount}" class="point">
                                        `;
                                        }
                                        subjectDiv.innerHTML = editCode;
                                        subjectContainer2.appendChild(subjectDiv);
                                    });
                                });

                            },
                            preConfirm: () => {
                                form = document.getElementById("gpaForm3");
                                const subjectNameElements = form.getElementsByClassName("subjectName");
                                const subjectNames = Array.from(form.getElementsByClassName("subjectName")).map((elem) => elem.value);
                                
                                if ($("#gpaForm3 .hours").val() < 0) {
                                    Swal.showValidationMessage("Hours cannot be negative.");
                                    $("#gpaForm3 .gpaResult").val(0);
                                    $("#gpaForm3 .points").val(0);
                                    makeRed($("#gpaForm3 .hours").val());
                                    return false; // Return false to prevent the modal from closing
                                } else if ($("#gpaForm3 .marks").val() < 0) {
                                    Swal.showValidationMessage("Marks cannot be negative.");
                                    $("#gpaForm3 .gpaResult").val(0);
                                    $("#gpaForm3 .points").val(0);
                                    makeRed($("#gpaForm3 .marks").val());
                                    return false; // Return false to prevent the modal from closing
                                } else if (!form.checkValidity()) {
                                    Swal.showValidationMessage("All fields are required.");
                                    return false; // Return false to prevent the modal from closing
                                } else if (hasDuplicateValues(subjectNames)) {
                                    Swal.showValidationMessage("Subject names must be unique.");
                                    highlightDuplicateSubjectNames(subjectNameElements);
                                    return false; // Return false to prevent the modal from closing
                                }
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let formData = new FormData(gpaFormUpdate);
                                gpaValue = recalc(); // Calculate the GPA value using the recalc() function
                                console.log("gpaValue", gpaValue)
                                gpaID = item._id.$oid;
                                console.log("id", gpaID)
                                // Append the GPA value to the FormData object
                                formData.append('gpa', gpaValue);
                                formData.append('updateGpa', true);
                                formData.append('id', gpaID);
                                $.ajax({
                                    url: 'gpabackend.php',
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        console.log(response);
                                        Swal.fire({
                                            title: 'GPA Updated',
                                            text: 'GPA has been updated successfully.',
                                            icon: 'success',
                                            showConfirmButton: false,
                                            timer: 1500,
                                        });
                                        location.reload();

                                    },
                                    error: function() {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'An error occurred while updating the GPA.',
                                            icon: 'error',
                                            showConfirmButton: false,
                                            timer: 1500,
                                        });
                                    },
                                });
                            }
                        });

                    });
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