jQuery(document).ready(function ($) {


    //submit form send data with nonce to server
    $("#easyApply").submit(function (event) {
    event.preventDefault(); // prevent the default form submission behavior

    let formInputs = $(this).find("input ,select");
    let isValid = true;

        
        job_application_frontend_vars.get_job_titles_nonce
    formInputs.each(function() {
        let input = $(this);
        // Check if input is empty
        if (input.val() === "" || (input.is('select') && input.val() === input.find('option:first').val())) {
        input.css("border-color", "#f44336");
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

        $.ajax({
            type: "POST",
            url: job_application_frontend_vars.ajax_url,
            data: {
                action: job_application_frontend_vars.prefix+"save_job_applications",
                security:job_application_frontend_vars.save_job_applications_nonce,
                jobTitle: jobTitleId,
                firstName: firstName,
                lastName: lastName,
                entryDate: entryDate,
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

//get all entries and fill table with it   
    $('table.job-applications-table tbody').addClass("spinner");
    $.ajax({
        url: job_application_frontend_vars.ajax_url,
        type: 'POST',
        data: {
            action: job_application_frontend_vars.prefix+'get_job_applications',
            security:job_application_frontend_vars.get_job_applications_nonce
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
        },
         complete: function() {
         $('table.job-applications-table tbody').removeClass("spinner");
        }
        
    });

    //get All jobtitles and fill Select with it
    $.ajax({
        url: job_application_frontend_vars.ajax_url,
        type: 'POST',
        data: {
            action: job_application_frontend_vars.prefix+'get_job_titles',
            security: job_application_frontend_vars.get_job_titles_nonce
        },
        success: function(response) {
            if (response.success) {
                
                $('#jobTitle').html(response.data);
                $("#jobTitleFilterSelect").html(response.data);
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            alert('Error: ' + errorThrown);
        },
        complete: function() {
            // Code to run after request is complete
        }
    });

    
    $('#jobTitleFilterSelect').on('change', function () {
        console.log($(this).val());
        var jobTitleId = $(this).val();
        $.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: job_application_frontend_vars.ajax_url,
        data: {
            action: job_application_frontend_vars.prefix + 'get_job_applications_filter',
             security: job_application_frontend_vars.get_job_applications_filter_nonce,
            job_title_id: jobTitleId
        },
            success: function (response) {
                console.log(response);
            // handle the response here
        }
        });
    });
    



});
