jQuery(document).ready(function ($) {
    $("#nonce-controller").val(job_application_frontend_vars.nonce)
    $("#easyApply").submit(function (event) {
    event.preventDefault(); // prevent the default form submission behavior

    let formInputs = $(this).find("input ,select");
    let isValid = true;

    formInputs.each(function() {
        let input = $(this);
        // Check if input is empty
        if (input.val() === "" || (input.is('select') && input.val() === input.find('option:first').val())) {
        input.css("border-color", "red");
        isValid = false;
        } else {
        input.css("border-color", "");
        }
    });

    // Check if all fields are valid before making the AJAX request
    if (isValid) {
        let jobTitleId = $("#jobTitle").val();
        let jobTitleName=$("#jobTitle").find('option:selected').text();
        let firstName = $("#firstName").val();
        let lastName = $("#lastName").val();
        let entryDate = $("#entryDate").val();
        let nonce_controller = $("#nonce-controller").val();

        $.ajax({
            type: "POST",
            url: job_application_frontend_vars.ajax_url,
            data: {
                action: "saveData",
                jobTitle: jobTitleId,
                firstName: firstName,
                lastName: lastName,
                entryDate: entryDate,
                nonce_controller: nonce_controller,
            },
            success: function (response) {
                // alert("Form submitted successfully!");
                console.log(response);

                // Check if the response was successful
                if (response.success) {
                    // Get the job skills from the response data
                    let jobSkills = response.jobSkills;

                    // Build the HTML table row with the job application data
                    let newRow = '<tr><td>' + jobTitleName + '</td><td>' + firstName + '</td><td>' + lastName + '</td><td>' + entryDate + '</td><td>' + jobSkills + '</td></tr>';

                    // Append the new row to the table body
                    console.log(newRow);
                    $('.job-applications-table tbody').append(newRow);

                    // Clear the form inputs
                    $("#easyApply")[0].reset();
                } else {
                    // Handle the error response
                    alert(response.message);
                }
            },
            error: function () {
                alert("Form submission failed.");
            },
        });

    }
    });

    $.ajax({
        url: job_application_frontend_vars.ajax_url,
        type: 'POST',
        data: {
            action: 'get_job_applications'
        },
        success: function(response) {
            if (response.success) {
            const tableBody = $('.job-applications-table tbody');
            tableBody.empty(); // Clear the current table rows

            $.each(response.data, function(i, job_application) {
                const row = '<tr>' +
                        '<td>' + job_application.job_title_name + '</td>' +
                        '<td>' + job_application.first_name + '</td>' +
                        '<td>' + job_application.last_name + '</td>' +
                        '<td>' + job_application.entry_date + '</td>' +
                        '<td>' + job_application.skills + '</td>' +
                        '</tr>';
                tableBody.append(row);
            });
            } else {
            alert(response.message);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            alert('Error: ' + errorThrown);
        }
    });


});
