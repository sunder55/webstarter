<?php
class Wstr_ajax_functions
{
    function __construct()
    {
        add_action('wp_ajax_get_users', array($this, 'get_users'));
        add_action('wp_ajax_get_domains_list', array($this, 'get_domains_list'));
        add_action('wp_ajax_get_domain_details', array($this, 'get_domain_details'));
    }

    /**
     * 
     * Function for getting user on select option lists in backend order page 
     * @return void
     */
    public function get_users()
    {
        if (isset($_POST['search'])) {
            $search_term = sanitize_text_field($_POST['search']);

            // Query for users
            $user_query = new WP_User_Query(array(
                'search'         => '*' . esc_attr($search_term) . '*',
                'search_columns' => array('user_login', 'user_email', 'display_name'),
            ));

            $users = $user_query->get_results();

            // Prepare the response data
            $response = array();
            foreach ($users as $user) {
                $response[] = array(
                    'id'    => $user->ID,
                    'username' => $user->user_login,
                    'email' => $user->user_email,
                );
            }

            // Send the response in JSON format
            wp_send_json($response);
        }
        wp_die();
    }

    /**
     * 
     * Function for getting domains on select option lists in backend order page
     * @return void
     */
    public function get_domains_list()
    {
        if (isset($_POST['search'])) {
            $search_term = sanitize_text_field($_POST['search']);

            // Query for domains
            $domain_query = new WP_Query(array(
                'post_type'      => 'domain',
                's'              => $search_term,
                'posts_per_page' => -1, // Adjust this if you want to limit the number of results
                'fields'         => 'ids', // Only retrieve post IDs
            ));

            $domains = $domain_query->get_posts();

            // Prepare the response data
            $response = array();
            foreach ($domains as $domain_id) {
                $response[] = array(
                    'id'    => $domain_id,
                    'name'  => get_the_title($domain_id),
                );
            }

            // Send the response in JSON format
            wp_send_json($response);
        }
        wp_die();
    }

    public function get_domain_details()
    {
        if (isset($_POST['domain_id'])) {
            $domain_id = sanitize_text_field($_POST['domain_id']);
            // Get the domain post
            $domain_post = get_post($domain_id);
            if ($domain_post && $domain_post->post_type === 'domain') {
                // Prepare the response data
                $response = array(
                    'id'   => $domain_post->ID,
                    'name' => $domain_post->post_title,
                );

                // Send the response in JSON format
                wp_send_json($response);
            }
        }
        wp_die();
    }
}

new Wstr_ajax_functions();
