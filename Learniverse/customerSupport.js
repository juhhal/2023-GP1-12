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
            image.src = '../images/upper-clouds.png';
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
                    overlay.remove();
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
                                image.src = '../images/bottom-clouds.png';
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