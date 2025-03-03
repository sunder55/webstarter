<?php
require_once '/var/www/html/wp-load.php';
function wstr_pricing_cron()
{

    $args = [
        'post_type' => 'domain',
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sale_price_dates_to',
                'compare' => 'EXISTS',
            ],
            [
                'key' => '_sale_price_dates_to',
                'value' => '',
                'compare' => '!=',
            ],
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!=',
            ],
        ],
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $domain_id) {

            $sale_end_date = get_post_meta($domain_id, '_sale_price_dates_to', true);
            $current_date = date('Y-m-d');

            if ($sale_end_date && $current_date > $sale_end_date) {
                delete_post_meta($domain_id, '_sale_price');
                delete_post_meta($domain_id, '_sale_price_dates_from');
                delete_post_meta($domain_id, '_sale_price_dates_to');
            }
        }
    }
}

wstr_pricing_cron();
