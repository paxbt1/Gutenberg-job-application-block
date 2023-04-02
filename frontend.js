jQuery(document).ready(function ($) {
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
        let jobTitle = $("#jobTitle").val();
        let firstName = $("#firstName").val();
        let lastName = $("#lastName").val();
        let entryDate = $("#entryDate").val();
        let nonce_controller = $("#nonce-controller").val();

        $.ajax({
        type: "POST",
        url: job_application_frontend_vars.ajax_url,
        data: {
            action: "saveData",
            jobTitle: jobTitle,
            firstName: firstName,
            lastName: lastName,
            entryDate: entryDate,
            nonce_controller: nonce_controller,
        },
        success: function (response) {
            // alert("Form submitted successfully!");
            console.log(response);
            $("#easyApply")[0].reset(); // clear the form inputs
        },
        error: function () {
            alert("Form submission failed.");
        },
        });
    }
    });

});
