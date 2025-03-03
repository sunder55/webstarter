<?php

class Wstr_payouts
{
    function __construct()
    {

        add_action('after_setup_theme', [$this, 'wstr_payouts_table']);
        add_action('rest_api_init', array($this, 'wstr_payouts_rest_api_endpoint'));
    }

    public function wstr_payouts_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_id mediumint(9) NOT NULL,
            seller_id mediumint(9) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(10) NOT NULL,
            domain_id mediumint(9) NOT NULL,
            type varchar(20) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Function for adding data to wstr_payouts table
     * @param int $order_id 
     * @param  $seller_id 
     * @param float $amount 
     * @param string $type 
     * @param string $status
     * @return mixed
     */
    public function wstr_payouts($order_id, $seller_id, $amount, $currency, $domain_id, $type, $status)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $result = $wpdb->insert(
            $table_name,
            array(
                'order_id' => $order_id,
                'seller_id' => $seller_id,
                'amount' => $amount,
                'currency' => $currency,
                'domain_id' => $domain_id,
                'type' => $type,
                'status' => $status
            )
        );

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    public function wstr_payouts_rest_api_endpoint()
    {
        register_rest_route('wstr/v1', '/payouts/(?P<user_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'wstr_get_payouts'),
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ));
        register_rest_route('wstr/v1', '/commissions/(?P<user_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'wstr_get_commissions'),
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ));
        register_rest_route('wstr/v1', '/total-commission/(?P<user_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'wstr_get_total_commissions'),
            'permission_callback' => function () {
                return get_current_user_id();
            }
        ));
        register_rest_route('wstr/v1', '/request-payout/', array(
            'methods' => 'POST',
            'callback' => array($this, 'wstr_request_payout'),
            'permission_callback' => function () {
                return get_current_user_id();
            }
        ));
    }

    //for getting payouts
    public function wstr_get_payouts($request)
    {

        $user_id = (int) $request->get_param('user_id');

        $current_user = get_current_user_id();
        if ($current_user !== $user_id) {
            return new WP_Error('unauthorized', 'Unauthorized', array('status' => 401));
        }

        if (!$user_id) {
            return new WP_Error('user_not_found', 'Sorry, user not found', array('status' => 404));
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE seller_id = $user_id and type='payout' ORDER BY created_at DESC");

        $new_array = [];
        if ($results !== null) {
            foreach ($results as $result) {
                $domain_id = $result->domain_id;

                $image  = '';
                $domain_image = get_the_post_thumbnail_url($domain_id, 'medium_large');
                $logo = get_post_meta($domain_id, '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);
                if ($domain_image) {
                    $image = $domain_image;
                } else if (!$domain_image && $logo_url) {
                    $image = $logo_url;
                } else if (!$domain_image && !$logo_url) {
                    $image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
                }
                $domain = get_post($domain_id);
                $domain_title =  $domain->post_title;
                $domain_link = get_permalink($domain_id);

                $date = $result->created_at;
                $formattedDate = date("d M, Y", strtotime($date));

                $new_array[] = [
                    'id' => $result->id,
                    'order_id' => $result->order_id,
                    'seller_id' => $result->seller_id,
                    'amount' => $result->amount,
                    'currency' => get_wstr_currency_symbol($result->currency),
                    'domain_id' => $result->domain_id,
                    'type' => $result->type,
                    'status' => $result->status,
                    'created_at' => $formattedDate,
                    'domain_image' => $image,
                    'domain_title' => $domain_title,
                    'domain_link' => $domain_link
                ];
            }
            return new WP_REST_Response($new_array, 200);
        } else {
            return new WP_Error('query_failed', 'Query failed', array('status' => 500));
        }
    }

    // for getting list of the commissions
    public function wstr_get_commissions($request)
    {
        $user_id = (int) $request->get_param('user_id');

        $current_user = get_current_user_id();
        if ($current_user !== $user_id) {
            return new WP_Error('unauthorized', 'Unauthorized', array('status' => 401));
        }

        if (!$user_id) {
            return new WP_Error('user_not_found', 'Sorry, user not found', array('status' => 404));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $results = $wpdb->get_results("SELECT * FROM $table_name WHERE seller_id = $user_id and type='commission' ORDER BY created_at DESC");

        $new_array = [];
        if ($results !== null) {
            foreach ($results as $result) {

                $domain_id = $result->domain_id;

                $image  = '';
                $domain_image = get_the_post_thumbnail_url($domain_id, 'medium_large');
                $logo = get_post_meta($domain_id, '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);
                if ($domain_image) {
                    $image = $domain_image;
                } else if (!$domain_image && $logo_url) {
                    $image = $logo_url;
                } else if (!$domain_image && !$logo_url) {
                    $image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
                }

                $domain = get_post($domain_id);
                $domain_title =  $domain->post_title;
                $domain_link = get_permalink($domain_id);

                $date = $result->created_at;
                $formattedDate = date("d M, Y", strtotime($date));

                $new_array[] = [
                    'id' => $result->id,
                    'order_id' => $result->order_id,
                    'seller_id' => $result->seller_id,
                    'amount' => $result->amount,
                    'currency' => get_wstr_currency_symbol($result->currency),
                    'domain_id' => $result->domain_id,
                    'type' => $result->type,
                    'status' => $result->status,
                    'created_at' => $formattedDate,
                    'domain_image' => $image,
                    'domain_title' => $domain_title,
                    'domain_link' => $domain_link
                ];
            }
            return new WP_REST_Response($new_array, 200);
        } else {
            return new WP_Error('query_failed', 'Query failed', array('status' => 500));
        }
    }

    // for getting total amount of commissions
    public function wstr_get_total_commissions($request)
    {
        $user_id = (int) $request->get_param('user_id');

        $current_user = get_current_user_id();
        if ($current_user !== $user_id) {
            return new WP_Error('unauthorized', 'Unauthorized', array('status' => 401));
        }

        if (!$user_id) {
            return new WP_Error('user_not_found', 'Sorry, user not found', array('status' => 404));
        }

        $currency_value =  get_option('wstr_currency_rates', []);

        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $results = $wpdb->get_results("SELECT amount, currency FROM $table_name WHERE seller_id = $user_id and type='commission'");
        $payout_completeds = $wpdb->get_results("SELECT amount FROM $table_name WHERE seller_id = $user_id and type='payout' and status='paid'");
        $payout_pendings = $wpdb->get_results("SELECT amount FROM $table_name WHERE seller_id = $user_id and type='payout' and status='pending' OR status='in-progress'");
        $total_in_usd = 0;
        // for dispalying total commission
        $completed_amount = 0;
        if ($payout_completeds) {
            foreach ($payout_completeds as $completed) {
                $completed_amount += floatval($completed->amount);
            }
        }

        $pending_amount = 0;
        if ($payout_pendings) {
            foreach ($payout_pendings as $pending) {
                $pending_amount += floatval($pending->amount);
            }
        }

        if ($results) {
            foreach ($results as $result) {
                $amount = floatval($result->amount);
                $currency = $result->currency;
                // Convert to USD if not already in USD
                if ($currency !== 'USD' && isset($currency_value[$currency])) {

                    $conversion_rate = floatval($currency_value[$currency]);
                    $amount_in_usd = $amount / $conversion_rate; // Convert to USD
                } else {
                    $amount_in_usd = $amount;
                }

                $total_in_usd += $amount_in_usd;
            }
            $total_commission  = $total_in_usd - $completed_amount;
            $withdrawable_amount = $total_commission - $pending_amount;

            $data = [
                'total_commission' => round($total_commission),
                'withdrawable_amount' => round($withdrawable_amount)
            ];
            return new WP_REST_Response($data, 200);
        } else {
            return new WP_Error('query_failed', 'Query failed', array('status' => 500));
        }
    }

    //for requesting payout
    public function wstr_request_payout($request)
    {
        $params = $request->get_json_params();
        $user_id = sanitize_text_field($params['user_id']);

        $current_user = get_current_user_id();
        if ($current_user != $user_id) {
            return new WP_Error('unauthorized', 'User Unauthorized', array('status' => 401));
        }

        if (!$user_id) {
            return new WP_Error('user_not_found', 'Sorry, user not found', array('status' => 404));
        }

        $amount = sanitize_text_field($params['amount']);

        if ($amount) {
            $result = $this->wstr_payouts(0, $user_id, $amount, 'USD', 0, 'payout', 'pending');

            if ($result) {
                return new WP_REST_Response(['message' => 'Payout request submitted successfully'], 200);
            } else {
                return new WP_Error('query_failed', 'Query failed', array('status' => 500));
            }
        } else {
            return new WP_Error('amount_required', 'Amount is required', array('status' => 400));
        }
    }
}

global $wstr_payouts;
$wstr_payouts = new Wstr_payouts();


class Wstr_payouts_dashboard
{
    function __construct()
    {

        add_action('admin_menu', array($this, 'payout_menu'));
        add_action('wp_ajax_wstr_update_payout_status', array($this, 'wstr_update_payout_status'));
    }
    public function payout_menu()
    {
        add_menu_page('Payouts', 'Payouts', 'manage_options', 'wstr-payouts', array($this, 'payouts_dashboard'), 'dashicons-pinterest');
    }

    public function payouts_dashboard()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;

        // Define the table name
        $table_name = $wpdb->prefix . 'wstr_payouts';

        // Set the number of items per page
        $items_per_page = 5;

        // Get the current page from the URL, default to 1 if not set
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

        // Calculate the offset for the query
        $offset = ($current_page - 1) * $items_per_page;

        // Get the total number of rows
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE type='status'");

        // Calculate the total number of pages
        $total_pages = ceil($total_items / $items_per_page);

        $selected_status = $_GET['status'] ?? '';
        $user = (int) ($_GET['user'] ?? 0);

        // Build the query with filters
        $query = "SELECT * FROM $table_name WHERE type='payout'";
        $query_params = [];

        if ($selected_status) {
            $query .= " AND status = %s";
            $query_params[] = $selected_status;
        }

        if ($user) {
            $query .= " AND seller_id = %d";
            $query_params[] = $user;
        }

        $query .= " ORDER BY ID DESC LIMIT %d OFFSET %d";
        $query_params[] = $items_per_page;
        $query_params[] = $offset;

        // Fetch the data for the current page
        $results = $wpdb->get_results($wpdb->prepare($query, ...$query_params));

?>
        <div class="main_payouts">
            <h3>Payouts</h3>
            <!-- // filter sections starts  -->

            <div class="payout_filter">
                <form class="payout_filter_form">
                    <div>
                        <?php wp_dropdown_users(array('name' => 'user_filter', 'option_none_value' => '', 'show_option_none' => 'select user', 'selected' => $user ?: 0)); ?>
                    </div>
                    <div>
                        <select id="status_filter">
                            <option value="">select status</option>
                            <option value='pending' <?php echo $selected_status == 'pending' ? "selected" : "" ?>>Pending</option>
                            <option value='paid' <?php echo $selected_status == 'paid' ? "selected" : "" ?>>Paid</option>
                            <option value='in-progress' <?php echo $selected_status == 'in-progress' ? "selected" : "" ?>>In Progress</option>
                            <option value='cancelled' <?php echo $selected_status == 'cancelled' ? "selected" : "" ?>>Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <!-- // filter sections ends  -->

            <table border="1" class="widefat" id="contact-main_payouts">
                <thead>
                    <th>S.N</th>
                    <th>Seller</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Date</th>

                </thead>
                <tbody>
                    <?php
                    $i = $offset + 1; // Adjust the numbering based on the page
                    foreach ($results as $payout) {
                        $status = $payout->status;
                        $seller_id = $payout->seller_id;
                        $payment_method = get_user_meta($seller_id, '_preferred_payment_method', true);

                        $user_details = get_user_by('id', $seller_id);

                        $user_image_id = (int) get_user_meta($seller_id, 'ws_profile_pic', true);
                        $user_image = '';
                        if ($user_image_id) {
                            $user_image =  wp_get_attachment_url($user_image_id);
                        } else {
                            $user_image = get_avatar_url($seller_id);
                        }
                        // var_dump($user_details);
                        $full_name = $user_details->first_name . ' ' . $user_details->last_name;

                        $date = $payout->created_at ?: '0000/00/00';
                        $formattedDate = date("d M, Y", strtotime($date));
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><img style="width: 30px;" src=<?php echo $user_image; ?>>
                                <h4><?php echo esc_html($full_name); ?><span>(<?php echo $seller_id; ?>)</span></h4>
                            </td>
                            <td><?php echo '$' . esc_html($payout->amount); ?></td>

                            <td class="payout-status-main-<?php echo $payout->id ?>">
                                <!-- <div class="success-<?php //echo $payout->id 
                                                            ?>"></div> -->
                                <div class="error-<?php echo $payout->id ?>"></div>
                                <div class="loading-<?php echo $payout->id ?>"></div>
                                <select <?php echo $status == 'paid' || $status == 'cancelled' ? 'disabled' : '' ?> class="payouts-status" id=<?php echo $payout->id; ?>>
                                    <option value='pending' <?php echo $status == 'pending' ? "selected" : "" ?>>Pending</option>
                                    <option value='paid' <?php echo $status == 'paid' ? "selected" : "" ?>>Paid</option>
                                    <option value='in-progress' <?php echo $status == 'in-progress' ? "selected" : "" ?>>In Progress</option>
                                    <option value='cancelled' <?php echo $status == 'cancelled' ? "selected" : "" ?>>Cancelled</option>
                                </select>
                            </td>
                            <td><?php
                                switch ($payment_method) {
                                    case 'paypal':
                                        echo 'Paypal';
                                        break;
                                    case 'crypto':
                                        echo 'Crypto Wallet';
                                        break;
                                    case 'bank':
                                        echo 'Bank Transfer';
                                        break;
                                    default:
                                        echo 'Paypal';
                                        break;
                                }

                                ?></td>
                            <td><?php echo esc_html($formattedDate); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-payouts-us">
                <?php
                $base_url = admin_url('admin.php?page=wstr-payouts'); // Replace with your actual page slug
                if ($total_pages > 1) {
                    for ($page = 1; $page <= $total_pages; $page++) {
                        if ($page == $current_page) {
                            echo '<strong>' . $page . '</strong> ';
                        } else {
                            echo '<a href="' . esc_url(add_query_arg('paged', $page, $base_url)) . '">' . $page . '</a> ';
                        }
                    }
                }
                ?>
            </div>

            <?php if (!$results) {
                echo '<div>No payouts yet!</div>';
            }
            ?>
        </div>
<?php

    }

    public function wstr_update_payout_status()
    {
        $status = sanitize_text_field($_POST['status']);
        $payout_id = (int) sanitize_text_field($_POST['payout_id']);
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_payouts';
        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table_name SET status = %s WHERE type = %s AND id = %d",
                $status,
                "payout",
                $payout_id
            )
        );

        if ($result) {
            $data = [
                'message' => 'Status updated.',
                'payout_status' => $status
            ];
            wp_send_json_success($data);
        } else {
            wp_send_json_error('Update failed.');
        }
        wp_die();
    }
}
new Wstr_payouts_dashboard();
