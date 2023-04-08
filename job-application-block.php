<?php
/**
 * Plugin name: Job Application Block
 * Plugin description: Custom Gutenberg Block that contins a table of emploeey that application for job and an application form
 * Author name: Saeed Ghourbanian ;)
 */

function sgh_enqueue_job_application_block_js_file()
{
    wp_enqueue_script('job-application-block', plugins_url('job-application-block.js', __FILE__), array('wp-blocks','wp-i18n','wp-editor'), true, true);
}

add_action('enqueue_block_editor_assets', 'sgh_enqueue_job_application_block_js_file');


function sgh_enqueue_frontend_scripts()
{
    wp_enqueue_script('job-application-block-front', plugins_url('frontend.js', __FILE__), array( 'jquery' ), true, false);

    wp_localize_script('job-application-block-front', 'job_application_frontend_vars', array(

            'ajax_url' => admin_url('admin-ajax.php'),
             'nonce' => wp_create_nonce('job-nonce')
        ));
    wp_enqueue_style('style', plugins_url('style.css', __FILE__), array(), true);
}
add_action('wp_enqueue_scripts', 'sgh_enqueue_frontend_scripts');





function sgh_saveData_callback()
{
    // Check the nonce
    $nonce = isset($_POST['nonce_controller']) ? sanitize_text_field($_POST['nonce_controller']) : '';
    if (! wp_verify_nonce($nonce, 'job-nonce')) {
        // Nonce is not valid; return an error response
        $response = array(
            'success' => false,
            'message' => 'Security check failed',

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

    $jobTitleName=get_post($jobTitle)->post_title;
    // Data is valid; process the request and return a success response
    $post_id = wp_insert_post(array(
        'post_title' => $jobTitleName . ' - ' . $firstName . ' ' . $lastName,
        'post_type' => 'job_applications',
        'post_status' => 'publish',
        'meta_input' => array(
            'job_title_name'=>$jobTitleName,
            'first_name'=>$firstName,
            'last_name'=>$lastName,
            'entry_date' => $entryDate,
            'job_title_id'=>$jobTitle
        ),
    ));


    if ($post_id) {
        $term_ids = get_post_meta($jobTitle, '_job_title_skills', true);
        $skills = '';

        if (empty($term_ids) || !is_array($term_ids)) {
            $skills = 'No Skills Needed, just be patient ;)';
        } else {
            foreach ($term_ids as $term_id) {
                $term_obj = get_term(sanitize_text_field($term_id), 'job_title_skills');

                if (!is_wp_error($term_obj) && !empty($term_obj->name)) {
                    $skills .= sanitize_text_field($term_obj->name) . '<br>';
                }
            }
        }

        $response = array(
            'success' => true,
            'jobSkills' => $skills,
            'message' => 'Data saved successfully',
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error saving data',
        );
    }


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
        'show_in_rest' => true
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

    //'hide_empty' => false Retrieve all terms, including those that have no associated posts
    $terms = get_terms('job_title_skills', array('hide_empty' => false));

    if (empty($terms)) {
        echo '<label>There are no terms defined in this taxonomy. <a href="' . admin_url('edit-tags.php?taxonomy=job_title_skills&post_type=job_title') . '">Create one</a></label>';
    } else {
        echo '<label for="job_title_skills">Select Skills: <br> (Hold Crtl Key for multiple selecr)</label><br/>';
        echo '<select name="job_title_skills[]" multiple>';

        if (empty($skills)) {
            foreach ($terms as $term) {
                echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
            }
        } else {
            foreach ($terms as $term) {
                $selected = in_array($term->term_id, $skills) ? 'selected' : '';
                echo '<option value="' . $term->term_id . '" ' . $selected . '>' . $term->name . '</option>';
            }
        }

        echo '</select>';
    }
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



// register custom post type job_applications
// for security reason it has to not show in Rest

function sgh_register_custom_post_type_job_applications()
{
    $args = array(
            'labels' => array(
                'name' => __('Job Applications'),
                'singular_name' => __('Job Application'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-groups',
            'show_in_rest' => false
        );
    register_post_type('job_applications', $args);
}
add_action('init', 'sgh_register_custom_post_type_job_applications');




add_action('wp_ajax_nopriv_get_job_applications', 'prefix_get_job_applications');
add_action('wp_ajax_get_job_applications', 'prefix_get_job_applications');

function prefix_get_job_applications()
{
    // Verify the nonce
    // check_ajax_referer('prefix_get_job_applications_nonce', 'security');

    // Prepare the query arguments
    $args = array(
        'post_type' => 'job_applications',
        'posts_per_page' => -1, // Get all posts
    );

    // Run the query
    $query = new WP_Query($args);

    // Get the posts
    $posts = $query->get_posts();

    // Prepare the response data
    $data = array();
    foreach ($posts as $post) {
        $job_title_id = get_post_meta($post->ID, 'job_title_id', true);
        $terms = get_post_meta($job_title_id, '_job_title_skills', true);

        $skills = '';
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                // $skills.=$term;
                $skills .= get_term($term, 'job_title_skills')->name . '<br>';
            }
        }

        $data[] = array(
            'job_title_name'=>get_post_meta($post->ID, 'job_title_name', true),
            'first_name'=>get_post_meta($post->ID, 'first_name', true),
            'last_name'=>get_post_meta($post->ID, 'last_name', true),
            'entry_date' => get_post_meta($post->ID, 'entry_date', true),
            'job_title_id'=>$job_title_id,
            'skills'=>$skills
        );
    }

    // Return the response
    wp_send_json_success($data);
    wp_die();
}
