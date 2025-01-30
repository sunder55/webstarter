<?php

class Wstr_payouts
{
    function __construct()
    {

        add_action('init', [$this, 'wstr_payouts_table']);

        // add_action('init', array($this, 'wstr_payouts'));
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
            echo 'Data inserted successfully';
        } else {
            echo 'Error';
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
    }

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
        $results = $wpdb->get_results("SELECT amount, currency FROM $table_name WHERE seller_id = $user_id and type='commission' GROUP BY currency");
        $total_in_usd = 0;
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

            return new WP_REST_Response(['total_commission' => round($total_in_usd)], 200);
        } else {
            return new WP_Error('query_failed', 'Query failed', array('status' => 500));
        }
    }
}

global $wstr_payouts;
$wstr_payouts = new Wstr_payouts();
