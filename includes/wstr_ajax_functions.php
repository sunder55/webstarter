<?php
class Wstr_ajax_functions
{
    function __construct()
    {
        add_action('wp_ajax_get_users', array($this, 'get_users'));
        add_action('wp_ajax_get_domains_list', array($this, 'get_domains_list'));
        add_action('wp_ajax_get_domain_details', array($this, 'get_domain_details'));
        add_action('wp_ajax_remove_domain_from_order', array($this, 'remove_domain_from_order'));
        add_action('wp_ajax_add_domain_order_notes', array($this, 'add_domain_order_notes'));
        add_action('wp_ajax_delete_domain_order_note', array($this, 'delete_domain_order_note'));

        add_action('wp_ajax_set_currency_session', array($this, 'set_currency_session'));
        add_action('wp_ajax_nopriv_set_currency_session', array($this, 'set_currency_session'));

        add_action('wp_ajax_wstr_favourite', array($this, 'wstr_favourite'));

        add_action('wp_ajax_wstr_domain_filter', [$this, 'wstr_domain_filter']);
        add_action('wp_ajax_nopriv_wstr_domain_filter', [$this, 'wstr_domain_filter']);

        add_action('wp_ajax_wstr_resend_otp', array($this, 'wstr_resend_otp'));
        add_action('wp_ajax_nopriv_wstr_resend_otp', array($this, 'wstr_resend_otp'));

        add_action('wp_ajax_wstr_make_offer', array($this, 'wstr_make_offer'));
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
                'search' => '*' . esc_attr($search_term) . '*',
                'search_columns' => array('user_login', 'user_email', 'display_name'),
            ));

            $users = $user_query->get_results();

            // Prepare the response data
            $response = array();
            foreach ($users as $user) {
                $response[] = array(
                    'id' => $user->ID,
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
                'post_type' => 'domain',
                's' => $search_term,
                'posts_per_page' => -1, // Adjust this if you want to limit the number of results
                'fields' => 'ids', // Only retrieve post IDs
                'meta_query' => [              // Meta query conditions
                    [
                        'key' => '_stock_status',    // Meta key for stock status
                        'value' => 'outofstock',       // Exclude posts with 'outofstock' status
                        'compare' => '!=',               // Not equal to 'outofstock'
                    ]
                ],
            ));

            $domains = $domain_query->get_posts();

            // Prepare the response data
            $response = array();
            foreach ($domains as $domain_id) {
                $response[] = array(
                    'id' => $domain_id,
                    'name' => get_the_title($domain_id),
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
                                $regular_price = get_post_meta($domain_post->ID, '_regular_price', true);
                                $sale_price = get_post_meta($domain_post->ID, '_sale_price', true);
                                $sale_end_date = get_post_meta($domain_post->ID, '_sale_price_dates_to', true);
                                $price = '';
                                $current_date = date('Y-m-d');

                                if ($sale_price) {
                                    if ($sale_end_date && $sale_end_date >= $current_date) {
                                        $price = $sale_price;
                                    } else if ($sale_end_date && $sale_end_date <= $current_date) {
                                        $price = $regular_price;
                                    } else {
                                        $price = $sale_price;
                                    }
                                } else {
                                    $price = $regular_price;
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
                $regular_price = get_post_meta($domain_post->ID, '_regular_price', true);
                $sale_price = get_post_meta($domain_post->ID, '_sale_price', true);
                $sale_end_date = get_post_meta($domain_post->ID, '_sale_price_dates_to', true);
                $current_date = date('Y-m-d');
                $price = '';
                if ($sale_price) {
                    if ($sale_end_date && $sale_end_date >= $current_date) {
                        $price = $sale_price;
                    } else if ($sale_end_date && $sale_end_date <= $current_date) {
                        $price = $regular_price;
                    } else {
                        $price = $sale_price;
                    }
                } else {
                    $price = $regular_price;
                }

                // Prepare the response data
                $response = array(
                    'id' => $domain_post->ID,
                    'name' => $domain_post->post_title,
                    'image' => $image_url ? $image_url : '', // Use empty string if no image
                    'amount' => $price ? $price : '0.00', // Use '0.00' if no amount is set
                    'order_id' => $order_id ? $order_id : '',
                    'subtotal' => number_format($subtotal, 2), // Add subtotal to response
                    'total' => number_format($total, 2), // Add total to response
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
                'id' => $domain_id,
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($total, 2),
                'message' => 'Domain removed successfully.',
            ));
        }
    }


    /**
     * Function for adding order notes to the order_notes table
     * @return never
     */
    public function add_domain_order_notes()
    {
        global $wpdb;
        $order_id = sanitize_text_field($_POST['order_id']);
        $order_note_type = sanitize_text_field($_POST['order_note_type']);
        $order_note = sanitize_text_field($_POST['order_note']);

        $table = $wpdb->prefix . 'order_notes';

        // Prepare the data for insertion
        $data = array(
            'order_id' => $order_id,
            'note' => $order_note,
            'note_type' => $order_note_type,
            'note_date' => current_time('mysql') // Current date and time
        );

        // Specify the data types for each field
        $format = array(
            '%d',   // order_id (integer)
            '%s',   // note (string)
            '%s',   // note_type (string)
            '%s'    // note_date (string, MySQL format)
        );

        // Insert data into the database
        $wpdb->insert($table, $data, $format);

        // Optionally, you can check if the insert was successful
        if ($wpdb->insert_id) {
            wp_send_json_success(array(
                'id' => $wpdb->insert_id,
                'note' => $order_note,
                'note_date' => date('F j, Y \a\t g:i a', strtotime(current_time('mysql')))
            ));
        } else {
            wp_send_json_error('Failed to add order note.');
        }
        die();
    }

    /**
     * Function for deleting order note from custom table -> order_notes
     * @return never
     */
    public function delete_domain_order_note()
    {
        global $wpdb;

        $note_id = intval($_POST['note_id']);
        $table = $wpdb->prefix . 'order_notes';

        $deleted = $wpdb->delete($table, array('id' => $note_id), array('%d'));
        if ($deleted) {
            wp_send_json_success('Note deleted successfully.');
        } else {
            wp_send_json_error('Failed to delete note.');
        }

        die();
    }

    /**
     * Function for add currency value to the session
     */

    public function set_currency_session()
    {
        $currency = sanitize_text_field($_POST['currency']);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['currency'] = $currency;

        // Return the session value in response
        $get_session_value = isset($_SESSION['currency']) ? $_SESSION['currency'] : '';
        wp_send_json_success($get_session_value);
        wp_die();
    }

    /**
     *  Function for favourite section
     */
    public function wstr_favourite()
    {
        $domain_id = sanitize_text_field($_POST['domain_id']);

        $favourite_data = get_user_meta(get_current_user_id(), '_favourite', true);

        // Ensure $favourite_data is an array
        if (!is_array($favourite_data)) {
            $favourite_data = [];
        }

        // Get the current favorite count from post meta
        $favourite_count = get_post_meta($domain_id, '_favourite_count', true);
        $favourite_count = (int) $favourite_count; // Ensure it's an integer

        // Check if the domain ID already exists
        if (($key = array_search($domain_id, $favourite_data)) !== false) {
            // Remove domain ID if it exists
            unset($favourite_data[$key]);

            // Decrease the favorite count if the domain is removed
            $favourite_count = max(0, $favourite_count - 1); // Prevent negative count
            $count = 'deduct';
        } else {
            // Add domain ID if it doesn't exist
            $favourite_data[] = $domain_id;

            // Increase the favorite count if the domain is added
            $favourite_count++;
            $count = 'add';
        }
        // Update the favorite count in post meta
        update_post_meta($domain_id, '_favourite_count', $favourite_count);
        update_user_meta(get_current_user_id(), '_favourite', $favourite_data);
        wp_send_json_success(array(
            'count' => $count,
        ));
        wp_die();
    }


    public function wstr_domain_filter()
    {
        // $indurstry = array_map("strip_tags", $_POST['industry']);
        $industry = !empty($_POST['industry']) ? array_map('sanitize_text_field', $_POST['industry']) : [];
        $styles = !empty($_POST['style']) ? array_map('sanitize_text_field', $_POST['style']) : [];
        $tld = !empty($_POST['tld']) ? sanitize_text_field($_POST['tld']) : '';
        $length = !empty($_POST['length']) ? sanitize_text_field($_POST['length']) : '';
        $sortBy = !empty($_POST['sortBy']) ? sanitize_text_field($_POST['sortBy']) : '';
        $min_price = !empty($_POST['min_price']) ? sanitize_text_field($_POST['min_price']) : '';
        $max_price = !empty($_POST['max_price']) ? sanitize_text_field($_POST['max_price']) : '';
        // $sort_by_price = !empty($_POST['sort_by_price']) ? sanitize_text_field($_POST['sort_by_price']) : '';
        $sort_by_list = !empty($_POST['sort_by_list']) ? sanitize_text_field($_POST['sort_by_list']) : '';
        $targeted_id = !empty($_POST['targeted_id']) ? sanitize_text_field($_POST['targeted_id']) : '';
        $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1; // Add paged parameter

        // Update search count for each style term
        if (!empty($styles) && is_array($styles)) {
            foreach ($styles as $style) {
                $new_count = 1;
                $previous_count = get_term_meta($style, '_search_count', true);
                if ($previous_count) {
                    $new_count = $previous_count + 1;
                }
                update_term_meta($style, '_search_count', $new_count);
            }
        }

        // Update search count for each industry term
        if (!empty($industry) && is_array($industry)) {
            foreach ($industry as $ind) {
                $new_count = 1;
                $previous_count = get_term_meta($ind, '_search_count', true);
                if ($previous_count) {
                    $new_count = $previous_count + 1;
                }
                update_term_meta($ind, '_search_count', $new_count);
            }
        }

        // Build the initial query arguments
        $args = [
            'post_type' => 'domain',
            'post_status' => 'publish',
            'posts_per_page' => -1, // Fetch all posts initially
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                ]
            ]
        ];

        // Initialize the tax_query array
        $tax_query = [];

        if (!empty($industry) && is_array($industry)) {
            // Add a separate subquery for each industry term with an 'AND' relation
            $industry_subqueries = [];
            foreach ($industry as $ind) {
                $industry_subqueries[] = [
                    'taxonomy' => 'domain_industry',
                    'field' => 'term_id',
                    'terms' => $ind,
                    'operator' => 'IN',
                ];
            }
            $tax_query[] = [
                'relation' => 'AND',
                $industry_subqueries,
            ];
        }

        if (!empty($styles) && is_array($styles)) {
            // Add a separate subquery for each style term with an 'AND' relation
            $style_subqueries = [];
            foreach ($styles as $style) {
                $style_subqueries[] = [
                    'taxonomy' => 'domain_cat',
                    'field' => 'term_id',
                    'terms' => $style,
                    'operator' => 'IN',
                ];
            }
            $tax_query[] = [
                'relation' => 'AND',
                $style_subqueries,
            ];
        }

        // If there are tax queries, add them to args with 'relation' set to AND
        if (!empty($tax_query)) {
            $args['tax_query'] = [
                'relation' => 'AND',
                $tax_query
            ];
        }
        // // // Add meta query for _tld if it's set
        if (!empty($tld)) {
            $args['meta_query'][] = [
                'key' => '_tld',
                'value' => $tld,
                'compare' => '='
            ];
        }

        // Add meta query for _length if it's set
        if (!empty($length)) {
            $args['meta_query'][] = [
                'key' => '_length',
                'value' => $length,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ];
        }
        ;

        // if (!empty($min_price)) {
        //     $args['meta_query'][] = [
        //         'relation' => 'OR',
        //         // Check if sale price exists and is greater than or equal to min_price
        //         [
        //             'key' => '_sale_price',
        //             'value' => $min_price,
        //             'compare' => '>=',
        //             'type' => 'NUMERIC'
        //         ],
        //         // If sale price doesn't exist, check regular price
        //         [
        //             'relation' => 'AND',
        //             [
        //                 'key' => '_sale_price',
        //                 'compare' => 'NOT EXISTS'
        //             ],
        //             [
        //                 'key' => '_regular_price',
        //                 'value' => $min_price,
        //                 'compare' => '>=',
        //                 'type' => 'NUMERIC'
        //             ]
        //         ]
        //     ];
        // }

        // if (!empty($max_price)) {
        //     // $current_date = current_time('Y-m-d');
        //     $args['meta_query'][] = [
        //         'relation' => 'OR',
        //         // Check if sale price exists and is less than or equal to max_price
        //         [
        //             'key' => '_sale_price',
        //             'value' => $max_price,
        //             'compare' => '<=',
        //             'type' => 'NUMERIC'
        //         ],
        //         // If sale price doesn't exist, check regular price
        //         [
        //             'relation' => 'AND',
        //             [
        //                 'key' => '_sale_price',
        //                 'compare' => 'NOT EXISTS'
        //             ],
        //             [
        //                 'key' => '_regular_price',
        //                 'value' => $max_price,
        //                 'compare' => '<=',
        //                 'type' => 'NUMERIC'
        //             ]
        //         ]
        //     ];
        // }
        // if (!empty($sort_by_list)) {

        //     if ($sort_by_list === 'high') {
        //         $args['orderby'] = 'meta_value_num';
        //         $args['meta_key'] = 'ws_product_view_count';
        //         $args['order'] = 'DESC';
        //     } else if ($sort_by_list === 'new') {
        //         $args['orderby'] = 'date';
        //         $args['order'] = 'DESC';
        //     }
        //     // else if($sort_by_list == 'recommended'){
        //     //     $args['orderby'] = 'meta_value_num';
        //     //     $args['meta_key'] = 'ws_product_view_count';
        //     //     $args['order'] = 'DESC';
        //     // }
        // }
        if (!empty($sortBy)) {
            if ($sortBy === 'a-z') {
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
            } else if ($sortBy === 'z-a') {
                $args['orderby'] = 'title';
                $args['order'] = 'DESC';
            } else if ($sortBy === 'high') {
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'ws_product_view_count';
                $args['order'] = 'DESC';
            } else if ($sortBy === 'new') {
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
            }
            // $args['orderby'] = 'title';
            // $args['order'] = ($sortBy === 'a-z') ? 'ASC' : 'DESC';
            // if ($sort_by_list === 'high') {
            //     $args['orderby'] = 'meta_value_num';
            //     $args['meta_key'] = 'ws_product_view_count';
            //     $args['order'] = 'DESC';
            // } else if ($sort_by_list === 'new') {
            //     $args['orderby'] = 'date';
            //     $args['order'] = 'DESC';
            // }
        }

        // Query all posts matching the criteria
        $all_query = new WP_Query($args);

        $response = '';

        if ($all_query->have_posts()) {
            $posts = $all_query->posts;
            $filtered_posts = [];

            foreach ($posts as $post) {
                $sale_price = (float) get_post_meta($post->ID, '_sale_price', true);
                $regular_price = (float) get_post_meta($post->ID, '_regular_price', true);
                $price = $sale_price ? $sale_price : $regular_price;

                // Apply min price filter
                if (!empty($min_price) && $price < $min_price) {
                    continue;
                }

                // Apply max price filter
                if (!empty($max_price) && $price > $max_price) {
                    continue;
                }

                $filtered_posts[] = $post;
            }

            if (!empty($_POST['sortBy']) && ($_POST['sortBy'] === 'low-to-high' || $_POST['sortBy'] === 'high-to-low')) {
                $sort_by_price = $_POST['sortBy'];
                usort($filtered_posts, function ($a, $b) use ($sort_by_price) {
                    $sale_price_a = (float) get_post_meta($a->ID, '_sale_price', true);
                    $regular_price_a = (float) get_post_meta($a->ID, '_regular_price', true);
                    $price_a = $sale_price_a ? $sale_price_a : $regular_price_a;

                    $sale_price_b = (float) get_post_meta($b->ID, '_sale_price', true);
                    $regular_price_b = (float) get_post_meta($b->ID, '_regular_price', true);
                    $price_b = $sale_price_b ? $sale_price_b : $regular_price_b;

                    if ($sort_by_price == 'low-to-high') {
                        return $price_a - $price_b;
                    } else if ($sort_by_price == 'high-to-low') {
                        return $price_b - $price_a;
                    }
                });
            }

            // Apply pagination to the sorted posts
            $posts_per_page = 20;
            $offset = ($paged - 1) * $posts_per_page;
            $paginated_posts = array_slice($filtered_posts, $offset, $posts_per_page);

            // Generate the response for each paginated post
            foreach ($paginated_posts as $post) {
                // var_dump($post);
                setup_postdata($post);

                $domain_image = get_the_post_thumbnail_url($post->ID, 'medium_large');
                if (!$domain_image) {
                    $domain_image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
                }

                $logo = get_post_meta($post->ID, '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);

                $price = get_wstr_price($post->ID);
                $sale_price = (float) get_post_meta($post->ID, '_sale_price', true);
                $regular_price = (float) get_post_meta($post->ID, '_regular_price', true);

                $percentage_discount = 0;
                if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                    $percentage_discount = round((($regular_price - $sale_price) / $regular_price) * 100);
                }

                $da_pa = get_post_meta($post->ID, '_da_pa', true);
                $da = $pa = '';
                if ($da_pa) {
                    $da_pa_split = explode('/', $da_pa);
                    $da = $da_pa_split[0];
                    $pa = $da_pa_split[1];
                }

                $term_exist = wstr_check_existing_term($post->ID, 'domain_cat', 'premium-names');

                $response .= '<div class="ws-cards-container swiper-slide">';
                $response .= $term_exist ? '<div class="premium_icon"><img decoding="async" src="/wp-content/plugins/cpm-card-block/images/diamond.png" alt="Diamond Icon"></div>' : '';
                $response .= '<div class="ws_card_hover_charts ws_flex">';
                $response .= '<div class="circular-progress page-trust">';
                $response .= '<div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($pa) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($pa) . '"></div></div>';
                $response .= '<div class="progress-title"><h6>Page Trust</h6></div></div>';
                $response .= '<div class="circular-progress domain-trust"><div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($da) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($da) . '"></div></div>';
                $response .= '<div class="progress-title"><h6>Domain Trust</h6></div></div></div>';
                $response .= '<div class="ws-card-img"><img decoding="async" src="' . esc_url($domain_image) . '" alt="' . esc_attr($post->post_title) . '"></div>';
                $response .= '<div class="ws-card-contents ws-flex">';
                $response .= get_wstr_price_percentage($post->ID);
                $response .= '<img decoding="async" src="' . esc_url($logo_url ?: $domain_image) . '" alt="' . esc_attr($post->post_title) . '" title="' . esc_attr($post->post_title) . '" class="card_logo_img">';
                $response .= '<span class="ws-card-inner-contents"><h5><a href="' . esc_url(get_permalink($post->ID)) . '"> ' . esc_html($post->post_title) . '</a></h5>';
                $response .= $price ?: '';
                $response .= '</span>  <div class="ws-card-likes" id="' . $post->ID . '">
                                <h6>
                                    <span>' . wstr_get_favourite_count($post->ID) . '</span>
                                    <i class="fa-solid fa-heart"></i>
                                </h6>
                            </div></div></div>';

                $response .= '</div>';
            }

            wp_reset_postdata();

            // Generate pagination
            $total_pages = ceil(count($filtered_posts) / $posts_per_page);
            $pagination = paginate_links([
                'base' => '%_%',
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => $total_pages,
                'prev_text' => __('<', 'webstarter'),
                'next_text' => __('>', 'webstarter'),
                'type' => 'array',
            ]);

            if ($pagination) {
                $response .= '<div class="pagination">';
                foreach ($pagination as $page_link) {
                    $response .= '<span>' . $page_link . '</span>';
                }
                $response .= '</div>';
            }
        } else {
            $response .= '<p>' . __('No domain found.', 'webstarter') . '</p>';
        }

        wp_send_json_success($response);
        wp_die();
    }

    /** 
     * function for resending   OTP
     */
    public function wstr_resend_otp()
    {
        $user_id = (int) sanitize_text_field($_POST['userId']);
        $user = get_userdata($user_id);
        if ($user_id) {

            $otp = '';
            $length = 6;
            for ($i = 0; $i < $length; $i++) {
                $otp .= random_int(0, 9);
            }

            set_transient('custom_otp_' . $user->ID, $otp, 600); // Store OTP for 5 minutes

            // $to = $user->user_email;
            // $subject = "Otp Code";
            // $txt = 'Your opt code is ' . $otp;
            // $headers = "From: webstarter.com" . "\r\n" .
            //     "CC: somebodyelse@example.com";
            // $mail = wp_mail($to, $subject, $txt, $headers);
            $to = $user->user_email;
            $subject = "Your OTP Code for Verification";
            $otpFormatted = '<div style="font-family: Arial, sans-serif; text-align: center; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;">
                    <h2 style="color: #4CAF50; margin-bottom: 20px;">Your OTP Code</h2>
                    <p style="font-size: 18px; margin: 10px 0; color: #333;">Please use the following OTP to complete your verification:</p>
                    <p style="font-size: 28px; font-weight: bold; color: #333; margin: 20px 0;">' . $otp . '</p>
                    <p style="font-size: 14px; color: #777;">If you did not request this, please ignore this email.</p>
                </div>';

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: webstarter.com <contact@webstarter.com>',
            );

            $mail = wp_mail($to, $subject, $otpFormatted, $headers);
            // $mail = false;
            if ($mail) {
                $response = [
                    'type' => 'success',
                    'message' => 'OTP sent successfully'
                ];
                wp_send_json_success($response, 200);
            } else {
                $response = [
                    'type' => 'failed',
                    'message' => 'Unable to send an email. Please try again later'
                ];
                wp_send_json_success($response, 200);
            }
        } else {
            $response = [
                'type' => 'failed',
                'message' => 'Missing user id. Please try login again.'
            ];
            wp_send_json_success($response, 200);
            // wp_send_json('Missing user id. Please try login again.', 500);
        }
    }

    public function wstr_make_offer()
    {

        $domain_id = sanitize_text_field($_POST['domain_id']);
        $offer_price = sanitize_text_field($_POST['offer_amount']);
        $buyer_id = get_current_user_id();
        $author_id = get_post_field('post_author', $domain_id);

        if (!$buyer_id) {
            wp_send_json_error('Please login to make an offer');
        }

        if ($author_id == $buyer_id) {
            wp_send_json_error('You cannot make an offer on your own domain');
        }

        $domain_status = get_post_meta($domain_id, '_stock_status', true);
        if ($domain_status == 'outofstock') {
            wp_send_json_error('Sorry, domain is already taken.');
        }

        $currency = isset($_SESSION['currency']) ? get_wstr_currency_symbol($_SESSION['currency']) : '$';

        $date = new DateTime(); // Y-m-d
        $date->add(new DateInterval('P30D'));
        $expiry_date = $date->format('Y-m-d');


        global $wpdb; //removed $name and $description there is no need to assign them to a global variable
        $table_name = $wpdb->prefix . "offers"; //try not using Uppercase letters or blank spaces when naming db tables

        $insert_offer = $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO `$table_name` (`domain_id`, `offer_amount`, `buyer_id`, `seller_id`, `status`,`offer_expiry_date`,`currency`) 
                 VALUES (%d, %s, %d, %d, %s, %s, %s)",
                $domain_id,
                $offer_price,
                $buyer_id,
                $author_id,
                'pending',
                $expiry_date,
                $currency

            )
        );
        if ($insert_offer) {
            $offer_id = $wpdb->insert_id; // Get the ID of the last inserted row
            global $notifcations;
            $test = $notifcations->wstr_notification_handler($buyer_id, $author_id, 'offer', $offer_id);
            wp_send_json_success('Offer sent successfully');
        } else {
            wp_send_json_error('Unable to send offer. Please try again later');
        }
        die();
    }
}

new Wstr_ajax_functions();
