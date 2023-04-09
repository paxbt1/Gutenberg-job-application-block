<?php
/**
 * Plugin name: Job Application Block
 * Plugin description: Custom Gutenberg Block that contains a table of employees that applied for a job and an application form.
 * Author name: Saeed Ghourbanian ;)
 */

class Job_Application_Block_Plugin
{
    private $prefix='jabp';

    public function __construct()
    {
        add_action('enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ));
        add_action('wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ));
        add_action('init', array( $this, 'create_job_title_post_type'));
        add_action('init', array( $this, 'create_job_title_skills_taxonomy'));
        add_action('init', array( $this, 'create_job_applications_post_type'));
        add_action('save_post_job_title', array( $this,'save_job_title_skills_field'));
        add_action('add_meta_boxes', array( $this,'add_job_title_skills_field'));

        add_action('wp_ajax_nopriv_'.$this->prefix.'_save_job_applications', array( $this, 'save_job_applications'));
        add_action('wp_ajax_'.$this->prefix.'_save_job_applications', array( $this, 'save_job_applications'));
        add_action('wp_ajax_nopriv_' . $this->prefix . '_get_job_applications', array($this, 'get_job_applications'));
        add_action('wp_ajax_' . $this->prefix . '_get_job_applications', array($this, 'get_job_applications'));
        add_action('wp_ajax_nopriv_' . $this->prefix . '_get_job_titles', array($this, 'get_job_titles'));
        add_action('wp_ajax_' . $this->prefix . '_get_job_titles', array($this, 'get_job_titles'));

        add_action('wp_ajax_' . $this->prefix . '_get_job_applications_filter', array($this, 'get_job_applications_filter'));
        add_action('wp_ajax_nopriv_' . $this->prefix . '_get_job_applications_filter', array($this, 'get_job_applications_filter'));
        

    }

    public function enqueue_editor_assets()
    {
        wp_enqueue_script('job-application-block', plugins_url('job-application-block.js', __FILE__), array( 'wp-blocks', 'wp-i18n', 'wp-editor' ), true, true);
    }

    public function enqueue_frontend_assets()
    {
        wp_enqueue_script('job-application-block-front', plugins_url('frontend.js', __FILE__), array( 'jquery' ), true, false);
        wp_localize_script(
            'job-application-block-front',
            'job_application_frontend_vars',
            array(

                'ajax_url' => admin_url('admin-ajax.php'),
                'get_job_titles_nonce' => wp_create_nonce('get_job_titles_nonce'),
                'get_job_applications_nonce' => wp_create_nonce('get_job_applications_nonce'),
                'save_job_applications_nonce' => wp_create_nonce('save_job_applications_nonce'),
                'get_job_applications_filter_nonce' => wp_create_nonce('get_job_applications_filter_nonce'),
                'prefix'=>$this->prefix.'_'
            )
        );
        wp_enqueue_style('style', plugins_url('style.css', __FILE__), array(), true);
    }

    public function save_job_applications()
    {
        // Check the nonce
        if (! check_ajax_referer('save_job_applications_nonce', 'security')) {
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


// Define custom post type
public function create_job_title_post_type()
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



// Define custom taxonomy for skills
public function create_job_title_skills_taxonomy()
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



// Add custom fields for skills to Job Titles post type
public function add_job_title_skills_field()
{
    add_meta_box(
        'job_title_skills_field',
        'Skills',
        array($this,'render_job_title_skills_field'),
        'job_title',
        'normal',
        'high'
    );
}


// Render custom fields for skills in Job Titles post type
public function render_job_title_skills_field($post)
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
public function save_job_title_skills_field($post_id)
{
    if (isset($_POST['job_title_skills'])) {
        $skills = $_POST['job_title_skills'];
        update_post_meta($post_id, '_job_title_skills', $skills);
    }
}




// register custom post type job_applications
// for security reason it has to not show in Rest

public function create_job_applications_post_type()
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



public function get_job_applications()
{
    // Verify the nonce
    check_ajax_referer('get_job_applications_nonce', 'security');

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

public function get_job_titles()
{
    check_ajax_referer('get_job_titles_nonce', 'security');
    

    $args = array(
        'post_type' => 'job_title',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $query = new \WP_Query($args);
    $posts = $query->get_posts();

    $options = '<option value="">Select a job title</option>';

    foreach ($posts as $post) {
        $options .= '<option value="' . esc_attr($post->ID) . '">' . esc_html($post->post_title) . '</option>';
    }

    wp_send_json_success($options);
    wp_die();
}




public function get_job_applications_filter()
{
    check_ajax_referer('get_job_applications_filter_nonce', 'security');

    $jobTitleId = $_POST['job_title_id'];
    $args = array(
      'post_type' => 'job_applications',
      'meta_query' => array(
        array(
          'key' => 'job_title_id',
          'value' => $jobTitleId,
          'compare' => '=',
        ),
      ),
    );
    $query = new WP_Query($args);
    $posts = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post = array(
              'id' => get_the_ID(),
              'title' => get_the_title(),
              // add any other post data you need here
            );
            array_push($posts, $post);
        }
    }
    wp_reset_postdata();
    echo json_encode($posts);
    wp_die();
}

}

new Job_Application_Block_Plugin();
