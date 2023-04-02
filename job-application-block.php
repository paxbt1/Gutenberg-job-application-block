<?php
/**
 * Plugin name: Job Application Block
 * Plugin description: Custom Gutenberg Block that contins a table of emploeey that application for job and an application form
 * Author name: Saeed Ghourbanian ;)
 */

function sgh_enqueue_job_application_block_js_file()
{
    wp_enqueue_script('job-application-block', plugins_url('job-application-block.js', __FILE__), array('wp-blocks','wp-i18n','wp-editor'), true, false);

    wp_localize_script('job-application-block', 'job_application_block_vars', array(
            'nonce' => wp_create_nonce('jab-nonce')
        ));
}

add_action('enqueue_block_editor_assets', 'sgh_enqueue_job_application_block_js_file');


function sgh_enqueue_frontend_scripts()
{
    wp_enqueue_script('job-application-block-front', plugins_url('frontend.js', __FILE__), array( 'jquery' ), true, false);

    wp_localize_script('job-application-block-front', 'job_application_frontend_vars', array(

            'ajax_url' => admin_url('admin-ajax.php')
        ));
}
add_action('wp_enqueue_scripts', 'sgh_enqueue_frontend_scripts');




function sgh_saveData_callback($data)
{
    // Check the nonce
    $nonce = isset($_POST['nonce_controller']) ? sanitize_text_field($_POST['nonce_controller']) : '';
    if (! wp_verify_nonce($nonce, 'jab-nonce')) {
        // Nonce is not valid; return an error response
        $response = array(
            'success' => false,
            'message' => 'Security check failed'.$nonce,

        );
        wp_send_json($response);
        return;
    }

    // Sanitize and validate the input data
    $jobTitle = isset($_POST['jobTitle']) ? sanitize_text_field($_POST['jobTitle']) : '';
    $firstName = isset($_POST['firstName']) ? sanitize_text_field($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? sanitize_text_field($_POST['lastName']) : '';
    $entryDate = isset($_POST['entryDate']) ? sanitize_text_field($_POST['entryDate']) : '';

    // Validate the entryDate field
    $date_parts = explode('-', $entryDate);
    if (count($date_parts) !== 3 || ! checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
        // Date is not valid; return an error response
        $response = array(
            'success' => false,
            'message' => 'Invalid date format'
        );
        wp_send_json($response);
        return;
    }

    // Data is valid; process the request and return a success response
    $response = array(
        'success' => true,
        'message' => $entryDate
    );
    wp_send_json($response);
}

add_action('wp_ajax_nopriv_saveData', 'sgh_saveData_callback');
add_action('wp_ajax_saveData', 'sgh_saveData_callback');
