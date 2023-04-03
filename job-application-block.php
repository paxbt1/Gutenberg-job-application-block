<?php
/**
 * Plugin name: Job Application Block
 * Plugin description: Custom Gutenberg Block that contins a table of emploeey that application for job and an application form
 * Author name: Saeed Ghourbanian ;)
 */



function get_job_titles()
{
    $job_titles = array();

    // Arguments for get_posts function
    $args = array(
        'post_type' => 'job_title',
        'posts_per_page' => -1,
    );

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $job_titles[$post->post_title] = $post->ID;
    }

    return $job_titles;
}

function sgh_enqueue_job_application_block_js_file()
{
    wp_enqueue_script('job-application-block', plugins_url('job-application-block.js', __FILE__), array('wp-blocks','wp-i18n','wp-editor'), false, false);

    wp_localize_script('job-application-block', 'job_application_block_vars', array(
            'nonce' => wp_create_nonce('jab-nonce'),
            'options' => get_job_titles(),

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



// Define custom post type
function create_job_title_post_type()
{
    $args = array(
        'labels' => array(
            'name' => 'Job Titles',
            'singular_name' => 'Job Title'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-businessman',
        'show_in_rest' => true,
    );
    register_post_type('job_title', $args);
}
add_action('init', 'create_job_title_post_type');

// Define custom taxonomy for skills
function create_job_title_skills_taxonomy()
{
    register_taxonomy(
        'job_title_skills',
        'job_title',
        array(
            'label' => 'Skills',
            'rewrite' => array('slug' => 'job_title_skills'),
            'hierarchical' => false
        )
    );
}
add_action('init', 'create_job_title_skills_taxonomy');

// Add custom fields for skills to Job Titles post type
function add_job_title_skills_field()
{
    add_meta_box(
        'job_title_skills_field',
        'Skills',
        'render_job_title_skills_field',
        'job_title',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_job_title_skills_field');

// Render custom fields for skills in Job Titles post type
function render_job_title_skills_field($post)
{
    $skills = get_post_meta($post->ID, '_job_title_skills', true);
    $taxonomy = 'job_title_skills';
    $terms = get_terms($taxonomy, array('hide_empty' => false));

    echo '<label for="job_title_skills">Select Skills: <br> (Hold Crtl Key for multiple selecr)</label><br/>';
    echo '<select name="job_title_skills[]" multiple>';

    foreach ($terms as $term) {
        $selected = in_array($term->term_id, $skills) ? 'selected' : '';
        echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . '</option>';
    }

    echo '</select>';
}

// Save custom fields for skills value for Job Titles as post meta
function save_job_title_skills_field($post_id)
{
    if (isset($_POST['job_title_skills'])) {
        $skills = $_POST['job_title_skills'];
        update_post_meta($post_id, '_job_title_skills', $skills);
    }
}
add_action('save_post_job_title', 'save_job_title_skills_field');
