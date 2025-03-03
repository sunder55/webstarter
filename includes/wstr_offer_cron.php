<?php
require_once '/var/www/html/wp-load.php';

function wstr_declined_offer_expired()
{
    global $wpdb;

    $current_date = date('Y-m-d');
    var_dump($current_date);

    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT offer_id FROM {$wpdb->prefix}offers WHERE offer_expiry_date < %s AND status != %s", $current_date, 'declined')
    );
    if ($results) {
        foreach ($results as $offers) {
            $offer_id = $offers->offer_id;
            $data = ['status' => 'declined'];
            $format = ['%s'];
            $where = ['offer_id' => (int) $offer_id];
            $where_format = ['%d'];
            $wpdb->update("{$wpdb->prefix}offers", $data, $where, $format, $where_format);
        }
    }
}
wstr_declined_offer_expired();
