<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    #CSoverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #CSoverlay-content {
        background-color: white;
        padding: 1px 20px 20px;
        border-radius: 5px;
        max-width: 60%;
        text-align: center;
    }

    #cs-head {
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        height: 30%;
        /* border: solid red ; */
    }

    #cs-bottom {
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        height: 30%;
    }

    #cs-head img {
        width: 100%;
        height: 5%;
    }

    #cs-bottom img {
        width: 100%;
        height: 3%;
    }

    #complaint-form textarea {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 70%;
        max-width: 90%;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        padding: 1%;
        clear: both;
        border-radius: 5px;
    }

    #complaint-form textarea:focus {
        outline: none;
        /* Remove default focus outline */
        box-shadow: 0 0 5px 1px rgba(0, 0, 255, 0.2);
        /* Apply a blue box shadow when focused */
    }

    #cs-submit {
        background-color: #fdae9b;
        /* float: right; */
        margin-top: 5%;
        padding: 0.5rem 2rem;
        height: fit-content;
        border-radius: 10px;
        border: none;
        /* Remove borders */
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        /* Soft shadow for depth */
        transition: background-color 0.3s, box-shadow 0.3s;
        /* Smooth transition for hover effects */
    }

    #cs-submit:hover {
        cursor: pointer;
        background-color: #ec947e;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.13);
    }

    #confirmationDIV {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        line-height: 2rem;
        font-size: 1.2rem;
        margin-bottom: 5%;
    }
</style>

<script>
    function customerSupport() {
        const overlay = document.getElementById("CSoverlay");
        if (overlay !== null) {
            if (overlay.style.display != "none")
                overlay.remove();
            else
                overlay.style.display = "flex";

        } else {
            const overlay = document.createElement('div');
            overlay.id = 'CSoverlay';

            const overlayContent = document.createElement('div');
            overlayContent.id = 'CSoverlay-content';

            const head = document.createElement('div');
            head.id = 'cs-head';
            const image = document.createElement('img');
            image.src = 'images/upper-clouds.png';
            head.appendChild(image);
            overlayContent.appendChild(head);

            const title = document.createElement('h2');
            title.textContent = 'Customer Support';
            overlayContent.appendChild(title);

            const complaintForm = document.createElement('form');
            complaintForm.id = 'complaint-form';

            const problemText = document.createElement('p');
            problemText.textContent = 'We are here to Help. Tell us about your problem:';
            complaintForm.appendChild(problemText);

            const complaintTextarea = document.createElement('textarea');
            complaintTextarea.rows = 8;
            complaintForm.appendChild(complaintTextarea);

            const submitButton = document.createElement('button');
            submitButton.id = "cs-submit";
            submitButton.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';
            complaintForm.appendChild(submitButton);

            overlayContent.appendChild(complaintForm);
            overlay.appendChild(overlayContent);
            document.body.appendChild(overlay);

            const bottom = document.createElement('div');
            bottom.id = 'cs-bottom';
            overlayContent.appendChild(bottom);

            overlay.style.display = 'flex';
            complaintTextarea.focus();

            // Add event listener to hide the overlay when clicked outside
            document.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    overlay.style.display = 'none';
                }
            });

            // Add event listener for submitting the form
            submitButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior

                const complaint = complaintTextarea.value.trim(); // Get the value of the textarea and trim any leading/trailing whitespace

                // Remove any existing error messages
                const existingErrorMessage = complaintForm.querySelector('.error-message');
                if (existingErrorMessage !== null) {
                    existingErrorMessage.remove();
                }

                if (complaint === '') {
                    // Show an error message if the textarea is empty
                    const errorMessage = document.createElement('p');
                    errorMessage.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="color: #ff5252;"></i> Please write your complaint in the textbox.';
                    errorMessage.classList.add('error-message'); // Add a class to style the error message if needed
                    complaintForm.insertBefore(errorMessage, submitButton);
                } else {
                    // send the complaint
                    $.ajax({
                        url: 'processComplaint.php',
                        method: 'post',
                        data: {
                            CS_complaint: complaint
                        },
                        success: function(response) {
                            if (response == '1') {
                                complaintForm.style.display = "none";
                                const confirmationDIV = document.createElement('div');
                                confirmationDIV.id = 'confirmationDIV';
                                confirmationDIV.innerHTML = '<p>We appreciate your Feedback! <br>Our team will address it as soon as possible.</p>';
                                const image = document.createElement('img');
                                image.src = 'images/bottom-clouds.png';
                                bottom.appendChild(image);
                                overlayContent.style.paddingBottom = "1px";
                                overlayContent.insertBefore(confirmationDIV, bottom); // Append the confirmation message to the document
                            } else {
                                console.log(response);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("AJAX Error:", error);
                            // Display or handle the error as needed
                        }
                    });
                }
            });
        }
    }
</script>