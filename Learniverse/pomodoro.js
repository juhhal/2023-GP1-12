var pomodoro_minutes;
var pomodoro_seconds;
var short_minutes;
var short_seconds;
var long_minutes;
var long_seconds;
var current_minutes;
var current_seconds;
var type = "pomodoro";
var cycle = 0;
var settingOption = 1;
var cycles = 0;


//initialize values
function setPomodoroTimer(p_minute = 25, p_second = 0) {
  pomodoro_minutes = p_minute;
  pomodoro_seconds = p_second;
}
function setShortTimer(s_minute = 5, s_second = 0) {
  short_minutes = s_minute;
  short_seconds = s_second;
}
function setLongTimer(l_minute = 10, l_second = 0) {
  long_minutes = l_minute;
  long_seconds = l_second;
}
function setCurrent() {
  if (type == "pomodoro") {
    current_minutes = pomodoro_minutes;
    current_seconds = pomodoro_seconds;
  } else if(type == 'short'){
    current_minutes = short_minutes;
    current_seconds = short_seconds;
  } else {
    current_minutes = long_minutes;
    current_seconds = long_seconds;
  }
}

//Timer
function startTimer() {

    //start timer
    if (document.getElementById("play-icon").textContent == "start") {
        checkCurrent();
        document.getElementById("pomodoro-timer").textContent =String(current_minutes).padStart(2, "0") + ":" + String(current_seconds).padStart(2, "0");
        document.getElementById("play-icon").textContent = "Pause";


        timer = setInterval(function () {
        if (current_seconds === 0) {
            //timer finish, change timer type



            if (current_minutes === 0) {

              if(type === 'pomodoro')
                cycles++;

              if (cycle == 0) {
                type = 'short';
                cycle++;
            } else if (cycle == 2) {
                type = 'long';
                cycle++;
            } else if (cycle == 3) {
                type = 'pomodoro';
                cycle = 0; // Reset for the next full cycle
            }
            else{cycle++; type='pomodoro'}
            
            //session finishing sound
            var ring = new Audio('mp3/bell.mp3');
            updateCycle();
            ring.volume = 0.5;
            ring.play();
            checkType();
            setCurrent();
            return;
          }
            current_minutes--;
            current_seconds = 59;
        } else {
            current_seconds--;
        }

        document.getElementById("pomodoro-timer").textContent =String(current_minutes).padStart(2, "0") + ":" + String(current_seconds).padStart(2, "0");
        }, 1000);
    } 
    //pause timer
    else {
        clearInterval(timer);
        document.getElementById("play-icon").textContent = "start";
    }
}

//display pressed timer type
function whatTimer() {
  // Get the checked radio button with the name 'timerType'
  var checkedRadio = document.querySelector('input[name="timerType"]:checked');

  if (checkedRadio) {
    // Define all labels
    var labels = {
        "pomodoro": document.getElementById("pomodoro"),
        "short": document.getElementById("short"),
        "long": document.getElementById("long")
      };
  
      // Reset background and font color for all labels
      for (var key in labels) {
        if (labels.hasOwnProperty(key)) {
          labels[key].style.backgroundColor = "";
          labels[key].style.color = "white";
        }
      }
  
      // Get the label corresponding to the checked radio button
      var selectedLabel = labels[checkedRadio.value];
  
      // Apply white background and black font color to the selected label
      if (selectedLabel) {
        selectedLabel.style.backgroundColor = "#fff";
        selectedLabel.style.color = "black";
      }

    // If a radio button is checked, display its time
    if(checkedRadio.value == 'pomodoro' && type != 'pomodoro'){
        document.getElementById("pomodoro-timer").textContent =String(pomodoro_minutes).padStart(2, "0") + ":" + String(pomodoro_seconds).padStart(2, "0");
        clearInterval(timer);
        document.getElementById("play-icon").textContent = "start";}

    else if(checkedRadio.value == 'short' && type != 'short'){
        document.getElementById("pomodoro-timer").textContent =String(short_minutes).padStart(2, "0") + ":" + String(short_seconds).padStart(2, "0");
        document.getElementById("short").style.backgroundColor = "#fff";
        clearInterval(timer);
        document.getElementById("play-icon").textContent = "start";  }  
    
    else if(checkedRadio.value == 'long' && type != 'long'){
        document.getElementById("pomodoro-timer").textContent =String(long_minutes).padStart(2, "0") + ":" + String(long_seconds).padStart(2, "0");
        document.getElementById("long").style.backgroundColor = "#fff";
        clearInterval(timer);
        document.getElementById("play-icon").textContent = "start";}
    
    else{
        document.getElementById("pomodoro-timer").textContent =String(current_minutes).padStart(2, "0") + ":" + String(current_seconds).padStart(2, "0");
    }
  } 
}

//Reset sessions
function reset() {
  clearInterval(timer);
  current_minutes = pomodoro_minutes;
  current_seconds = pomodoro_seconds;
  type='pomodoro';
  checkType();
  cycle=0;
  cycles=0;
  updateCycle();
  document.getElementById("pomodoro-timer").textContent =String(pomodoro_minutes).padStart(2, "0") + ":" + String(pomodoro_seconds).padStart(2, "0");

}

//reset all settings

function ResetAll(){
  //reset timer settings
  setPomodoroTimer(25,0);
  setLongTimer(10,0);
  setShortTimer(5,0);
  type='pomodoro';
  setCurrent();
  checkType();
  document.getElementById("pomodoro-timer").textContent =String(pomodoro_minutes).padStart(2, "0") + ":" + String(pomodoro_seconds).padStart(2, "0");
  cycle=0;
  cycles=0;
  updateCycle();
  //reset background picture
  document.body.style.backgroundImage='url("images/none.png")';

  //reset audio settings
  document.getElementById("audio").pause();
  document.getElementById("audio").src = '';
  document.getElementById("timerSoundVolume").value = 0.5;

}

//
function checkType(){
    var labels = {
        "pomodoro": document.getElementById("pomodoro"),
        "short": document.getElementById("short"),
        "long": document.getElementById("long")
    };
  
    // Reset background and font color for all labels and uncheck all radio buttons
    for (var key in labels) {
      if (labels.hasOwnProperty(key)) {
          labels[key].style.backgroundColor = "";
          labels[key].style.color = "white";
          labels[key].querySelector('.form-check-input').checked = false;
          console.log("Reset styles for:", key); // Debugging statement
      }
  }

  // Check the radio button of the selected type and update styles
  var selectedLabel = labels[type];
  if (selectedLabel) {
      selectedLabel.style.backgroundColor = "#fff";
      selectedLabel.style.color = "black";
      selectedLabel.querySelector('.form-check-input').checked = true;
      console.log("Updated styles for:", type); // Debugging statement
  } else {
      console.log("Selected label not found for type:", type); // Debugging statement
  }
}

function checkCurrent(){
  // Get the checked radio button with the name 'timerType'
  var checkedRadio = document.querySelector('input[name="timerType"]:checked');

  if (checkedRadio) {
    // Define all labels
    var labels = {
        "pomodoro": document.getElementById("pomodoro"),
        "short": document.getElementById("short"),
        "long": document.getElementById("long")
      };
  
      // Reset background and font color for all labels
      for (var key in labels) {
        if (labels.hasOwnProperty(key)) {
          labels[key].style.backgroundColor = "";
          labels[key].style.color = "white";
        }
      }
  
      // Get the label corresponding to the checked radio button
      var selectedLabel = labels[checkedRadio.value];
      // Apply white background and black font color to the selected label
      if (selectedLabel) {
        selectedLabel.style.backgroundColor = "#fff";
        selectedLabel.style.color = "black";
      }

    // If a radio button is checked, display its time
    if(checkedRadio.value == 'pomodoro' && type != 'pomodoro'){
        type = 'pomodoro';
        setCurrent();
        cycle = 0;
      }

    else if(checkedRadio.value == 'short' && type != 'short'){
      type = 'short';
      setCurrent();
    cycle = 1;}  
    
    else if(checkedRadio.value == 'long' && type != 'long'){
      type = 'long';
      setCurrent();
    cycle = 3;}
    
    else{
        document.getElementById("pomodoro-timer").textContent =String(current_minutes).padStart(2, "0") + ":" + String(current_seconds).padStart(2, "0");
    }
  } 

}

//display settings
function setting(){
  if (document.getElementById("modal-dialog").style.visibility === 'hidden') {
    document.getElementById("modal-dialog").style.visibility = 'visible';
    show(1);
  }else{
    document.getElementById("modal-dialog").style.visibility = 'hidden';

  }
    // document.getElementById("modal-dialog").style.visibility = 'visible';
}

//change settings view
function show(num) {
  var activeTabClass = 'actives';
  
  // Remove the 'actives' class from all tab buttons
  var tabs = document.querySelectorAll('.nav-link');
  tabs.forEach(function(tab) {
    tab.classList.remove(activeTabClass);
  });
  
  // Hide all content panels
  var panels = document.querySelectorAll('.tab-pane');
  panels.forEach(function(panel) {
    panel.classList.remove(activeTabClass);
  });

  // Add the 'actives' class to the clicked tab button and corresponding content panel
  switch(num) {
    case 1:
      document.getElementById("settModal-general-tab").classList.add(activeTabClass);
      document.getElementById("settModal-general").classList.add(activeTabClass);
      settingOption = 1;
      break;
    case 2:
      document.getElementById("settModal-timers-tab").classList.add(activeTabClass);
      document.getElementById("settModal-timers").classList.add(activeTabClass);
      
      document.getElementById("pomBreakLength").value = String(pomodoro_minutes).padStart(2, "0");
      document.getElementById("shortBreakLength").value = String(short_minutes).padStart(2, "0");
      document.getElementById("longBreakLength").value = String(long_minutes).padStart(2, "0");

      settingOption = 2;
      break;
    case 3:
      document.getElementById("settModal-sounds-tab").classList.add(activeTabClass);
      document.getElementById("settModal-sounds").classList.add(activeTabClass);
      settingOption = 3;
      break;
  }
}


function changeSetting(x=0){
  //if I'm modifying general settings
  if(settingOption==1){
    if(document.getElementById("themeSelect").value!='none'){
    document.body.style.backgroundImage='url('+document.getElementById("themeSelect").value+')';
  }
    else{
      document.body.style.backgroundImage='url("images/none.png")';

    }
  
    //if I'm modifying pomodoro timer settings
  }else if(settingOption==2){
    setPomodoroTimer(document.getElementById("pomBreakLength").value,0);
    setLongTimer(document.getElementById("longBreakLength").value,0);
    setShortTimer(document.getElementById("shortBreakLength").value,0);
    reset();

  //if I'm modifying audio settings
  }else{
    document.getElementById("audio").pause();
    if(document.getElementById("backgroundSound").value=='none'){
      document.getElementById("audio").src = '';
    }
    else{
      document.getElementById("audio").src = 'mp3/'+document.getElementById("backgroundSound").value+'.mp3';
      document.getElementById("audio").play();

    }

  }
  if(x==1)
  setting();

}

function changeAudio(amount){
  var audioobject = document.getElementsByTagName("audio")[0];
  audioobject.volume = amount;
}

function updateCycle(){
  document.getElementById("cycle").textContent = 'Cycle: '+cycles;
}

//update database when confirm or reset all
$(document).ready(function() {
  //save settings when confirmed
  $('#saveUserSettings').click(function(e) {
      // Prevent form submission
      e.preventDefault();

      // Get the form data
      var theme = $('#themeSelect').val();
      console.log(theme);
      var pomodoroLength = $('#pomBreakLength').val();
      var shortLength = $('#shortBreakLength').val();
      var longLength = $('#longBreakLength').val();

      // Send AJAX request to the server
      $.ajax({
          url: 'userPomodoroPreference.php',
          type: 'POST',
          data: {
              theme: theme,
              pomodoroLength: pomodoroLength,
              shortLength: shortLength,
              longLength: longLength

            },
          dataType: 'json',
          success: function(response) {
              if (response.message === "success") {
                  // Redirect to the workspace page
              } else {
                  // Display failure message
                  $('#response').text(response.message);
                  
              }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log('AJAX error:', textStatus); // Log the error status
            console.log('AJAX error thrown:', errorThrown); // Log the error thrown
            console.log('AJAX detailed error:', jqXHR.responseText); // Log the full error response
            $('#response').text('An error occurred: ' + textStatus);
        }
      });
  });
  //save settings when close setting box
  $('#close-btn').click(function(e) {
    // Prevent form submission
    e.preventDefault();

    // Get the form data
    var theme = $('#themeSelect').val();
    var pomodoroLength = $('#pomBreakLength').val();
    var shortLength = $('#shortBreakLength').val();
    var longLength = $('#longBreakLength').val();
    var sound = $('#backgroundSound').val();
    var vol = $('#timerSoundVolume').val();
    // Send AJAX request to the server
    $.ajax({
        url: 'userPomodoroPreference.php',
        type: 'POST',
        data: {
            theme: theme,
            pomodoroLength: pomodoroLength,
            shortLength: shortLength,
            longLength: longLength

          },
        dataType: 'json',
        success: function(response) {
            if (response.message === "success") {
                // Redirect to the workspace page
                
            } else {
                // Display failure message
                $('#response').text(response.message);
                
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log('AJAX error:', textStatus); // Log the error status
          console.log('AJAX error thrown:', errorThrown); // Log the error thrown
          console.log('AJAX detailed error:', jqXHR.responseText); // Log the full error response
          $('#response').text('An error occurred: ' + textStatus);
      }
    });
});
  //reset all settings
  $('#resetUserSettings').click(function(e) {
    // Prevent form submission
    e.preventDefault();

    // Set the values and trigger the change event
    $('#themeSelect').val("none").trigger('change');
    $('#pomBreakLength').val(25).trigger('change');
    $('#shortBreakLength').val(5).trigger('change');
    $('#longBreakLength').val(10).trigger('change');
    $('#backgroundSound').val('none').trigger('change');
    $('#timerSoundVolume').val(0.5).trigger('change');
    console.log($('#themeSelect').val());
    // Retrieve the values separately
    var theme = $('#themeSelect').val();
    var pomodoroLength = $('#pomBreakLength').val();
    var shortLength = $('#shortBreakLength').val();
    var longLength = $('#longBreakLength').val();

      // Send AJAX request to the server
      $.ajax({
          url: 'userPomodoroPreference.php',
          type: 'POST',
          data: {
              theme: theme,
              pomodoroLength: pomodoroLength,
              shortLength: shortLength,
              longLength: longLength
            },
          dataType: 'json',
          success: function(response) {
              if (response.message === "success") {
                  // Redirect to the workspace page
                  
              } else {
                  // Display failure message
                  $('#response').text(response.message);
                  alert('in');
              }
          }
      });
  });

  $('#full').click(function() {
    var elem = document.documentElement; // Get the root element of the document, adjust if you want another element to go fullscreen
    var header = $('header')[0]; // Using jQuery to select the header element
    var pomodoroContainer = $('#pomodoro-container')[0]; // Using jQuery to select the pomodoro container

    if (!document.fullscreenElement) { // Check if the document is not already in fullscreen mode
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) { // Safari
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) { // IE11
            elem.msRequestFullscreen();
        }
        // No need to adjust visibility here, since the fullscreenchange event will handle it
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) { // Safari
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE11
            document.msExitFullscreen();
        }
        // No need to adjust visibility here, since the fullscreenchange event will handle it
    }
});
});

// Keep the fullscreen change event listener as it was
document.addEventListener('fullscreenchange', function () {
  var header = $('header')[0]; // Using jQuery to select the header element
  var pomodoroContainer = $('#pomodoro-container')[0]; // Using jQuery to select the pomodoro container
  
  // Check if the document is in fullscreen mode
  if (document.fullscreenElement) {
      console.log('Entering fullscreen mode');
      header.style.visibility = 'hidden';
      pomodoroContainer.style.marginTop = '5%';
      footer.style.visibility = 'hidden';
      document.getElementById("sidebar-tongue").style.visibility = 'hidden';


  } else {
      console.log('Exiting fullscreen mode');
      header.style.visibility = 'visible';
      pomodoroContainer.style.marginTop = '0%';
      footer.style.visibility = 'visible';
      document.getElementById("sidebar-tongue").style.visibility = 'visible';


  }
});


