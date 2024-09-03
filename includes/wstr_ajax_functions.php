<?php
class Wstr_ajax_functions
{
    function __construct()
    {
        add_action('wp_ajax_get_users', array($this, 'get_users'));
        add_action('wp_ajax_get_domains_list', array($this, 'get_domains_list'));
        add_action('wp_ajax_get_domain_details', array($this, 'get_domain_details'));
        add_action('wp_ajax_remove_domain_from_order', array($this, 'remove_domain_from_order'));
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
            $order_id = sanitize_text_field($_POST['order_id']);

            $domain_post = get_post($domain_id);
            if ($domain_post && $domain_post->post_type === 'domain') {

                if ($order_id) {
                    $saved_domains = get_post_meta($order_id, '_domain_ids', true);
                    $saved_domains = is_array($saved_domains) ? $saved_domains : array();

                    // Add the new domain ID to the array
                    if (!in_array($domain_id, $saved_domains)) {
                        $saved_domains[] = $domain_id;
                        update_post_meta($order_id, '_domain_ids', $saved_domains);

                        // Recalculate subtotal and total
                        $subtotal = 0;
                        foreach ($saved_domains as $domain) {
                            $domain_post = get_post($domain);
                            if ($domain_post && $domain_post->post_type === 'domain') {
                                $price = get_post_meta($domain_post->ID, '_sale_price', true);
                                if (!$price) {
                                    $price = get_post_meta($domain_post->ID, '_regular_price', true);
                                }
                                $subtotal += (float) $price;
                            }
                        }

                        // Optionally, calculate the total if different from subtotal
                        $total = $subtotal; // Adjust if you have additional calculations

                        // Update order meta with subtotal and total
                        update_post_meta($order_id, '_order_subtotal', $subtotal);
                        update_post_meta($order_id, '_order_total', $total);
                    }
                }
                $image_url = get_the_post_thumbnail_url($domain_post->ID, 'full');
                // Get the amount 
                $price = get_post_meta($domain_post->ID, '_sale_price', true);
                if (!$price) {
                    $price = get_post_meta($domain_post->ID, '_regular_price', true);
                }

                // Prepare the response data
                $response = array(
                    'id'    => $domain_post->ID,
                    'name'  => $domain_post->post_title,
                    'image' => $image_url ? $image_url : '', // Use empty string if no image
                    'amount' => $price ? $price : '0.00', // Use '0.00' if no amount is set
                    'order_id' => $order_id ? $order_id : '',
                    'subtotal' => number_format($subtotal, 2), // Add subtotal to response
                    'total'    => number_format($total, 2), // Add total to response
                );

                // Send the response in JSON format
                wp_send_json($response);
            }
        }
        wp_die();
    }

    /**
     * Function for removing domains from order meta
     */
    public function remove_domain_from_order()
    {

        if (isset($_POST['domain_id'])) {
            $domain_id = sanitize_text_field($_POST['domain_id']);
            $order_id = sanitize_text_field($_POST['order_id']);

            // Get the current saved domains
            $saved_domains = get_post_meta($order_id, '_domain_ids', true);
            $saved_domains = is_array($saved_domains) ? $saved_domains : array();

            // // Check if the domain ID exists in the array and remove it
            if (($key = array_search($domain_id, $saved_domains)) !== false) {
                unset($saved_domains[$key]);

                // Re-index the array to ensure no gaps in the array keys
                $saved_domains = array_values($saved_domains);

                // Update the post meta with the new array
                update_post_meta($order_id, '_domain_ids', $saved_domains);
        
                // Recalculate subtotal and total
                $subtotal = 0;
                foreach ($saved_domains as $domain) {
                    $domain_post = get_post($domain);
                    if ($domain_post && $domain_post->post_type === 'domain') {
                        $price = get_post_meta($domain_post->ID, '_sale_price', true);
                        if (!$price) {
                            $price = get_post_meta($domain_post->ID, '_regular_price', true);
                        }
                        $subtotal += (float) $price;
                    }

                }

                $total = $subtotal; // Adjust if needed
                update_post_meta($order_id, '_order_subtotal', $subtotal);
                update_post_meta($order_id, '_order_total', $total);
            }

            wp_send_json_success(array(
                'id'       => $domain_id,
                'subtotal' => number_format($subtotal, 2),
                'total'    => number_format($total, 2),
                'message'  => 'Domain removed successfully.',
            ));
        }
    }
}

new Wstr_ajax_functions();