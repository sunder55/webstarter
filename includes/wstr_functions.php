<?php

/**
 * function for returning price 
 * @param mixed $domain_id required
 * @return mixed
 */
function get_wstr_price($domain_id)
{
    if (!$domain_id) {
        return 0;
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $currency = $_SESSION['currency'] ?? '';
    $currency_rates = get_option('wstr_currency_rates', []);
    $currency_rate = $currency_rates[$currency] ?? 1;
    $regular_price = (float) get_post_meta($domain_id, '_regular_price', true);
    $sale_price = (float) get_post_meta($domain_id, '_sale_price', true);

    if ($currency && $currency != 'USD') {
        $currency_rate = wstr_truncate_number((float) $currency_rate);
        //  $currency_rate;
        $price = $sale_price > 0 ? $sale_price * $currency_rate : $regular_price * $currency_rate;
    } else {
        $currency = 'USD';
        $price = $sale_price > 0 ? $sale_price : $regular_price;
    }
    // $price_html = '<div class="wstr-price_html">
    // <span class="wstr-currency">' . get_wstr_currency_symbol($currency) . '</span>
    // <span class="wstr-price">' . wstr_truncate_number($price) . '<span> </div>';

    if ($sale_price) {
        $price_html = '<div class="ws_card_price_wrapper ws_flex gap_10"><p class="regular_price">' . get_wstr_currency() . '' . get_wstr_regular_price($domain_id) . '</p><p class="sale_price">' . get_wstr_currency() . '' . get_wstr_sale_price($domain_id) . '</p></div>';
    } else {
        $price_html = '<div class="single_domain_price ws_card_price_wrapper ws_flex gap_10"><p class="sale_price">' . get_wstr_currency() . '' . get_wstr_regular_price($domain_id) . '</p></div>';
    }
    return $price_html;
}

function get_wstr_price_value($domain_id)
{
    if (!$domain_id) {
        return 0;
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $currency = $_SESSION['currency'] ?? '';
    $currency_rates = get_option('wstr_currency_rates', []);
    $currency_rate = $currency_rates[$currency] ?? 1;
    $regular_price = (float) get_post_meta($domain_id, '_regular_price', true);
    $sale_price = (float) get_post_meta($domain_id, '_sale_price', true);

    if ($currency && $currency != 'USD') {
        $currency_rate = wstr_truncate_number((float) $currency_rate);
        //  $currency_rate;
        $price = $sale_price > 0 ? $sale_price * $currency_rate : $regular_price * $currency_rate;
    } else {
        $currency = 'USD';
        $price = $sale_price > 0 ? $sale_price : $regular_price;
    }
    // $price_html = '<div class="wstr-price_html">
    // <span class="wstr-currency">' . get_wstr_currency_symbol($currency) . '</span>
    // <span class="wstr-price">' . wstr_truncate_number($price) . '<span> </div>';
    return $price;
}

/**
 * Function for getting regular price of domain
 * @param mixed $domain_id
 * @return mixed
 */
function get_wstr_regular_price($domain_id)
{
    $currency = $_SESSION['currency'] ?? '';
    $regular_price = (float) get_post_meta($domain_id, '_regular_price', true);
    $currency_rates = get_option('wstr_currency_rates', []);
    $currency_rate = $currency_rates[$currency] ?? 1;
    if ($currency && $currency != 'USD') {
        $currency_rate = wstr_truncate_number((float) $currency_rate);

        // Calculate the prices in the specified currency
        $regular_price = $regular_price > 0 ? $regular_price * $currency_rate : 0;
    }
    return wstr_truncate_number($regular_price);
}

/**
 * Function for getting regular price of domain
 * @param mixed $domain_id
 * @return mixed
 */
function get_wstr_sale_price($domain_id)
{
    $currency = $_SESSION['currency'] ?? '';
    $sale_price = (float) get_post_meta($domain_id, '_sale_price', true);
    $currency_rates = get_option('wstr_currency_rates', []);
    $currency_rate = $currency_rates[$currency] ?? 1;
    if ($currency && $currency != 'USD') {
        $currency_rate = wstr_truncate_number((float) $currency_rate);

        // Calculate the prices in the specified currency
        $sale_price = $sale_price > 0 ? $sale_price * $currency_rate : 0;
    }
    return wstr_truncate_number($sale_price);
}

/**
 * Function for getting currecy value according to the currency selected
 * @return mixed
 */
function wstr_get_updated_price($price)
{
    $currency = $_SESSION['currency'] ?? '';
    $currency_rates = get_option('wstr_currency_rates', []);
    $currency_rate = $currency_rates[$currency] ?? 1;
    if ($currency && $currency != 'USD') {
        $currency_rate = wstr_truncate_number((float) $currency_rate);

        // Calculate the prices in the specified currency
        $price = $price > 0 ? $price * $currency_rate : 0;
    }
    return wstr_truncate_number($price);
}

/**
 * Function for percetage of price differnce
 * @param mixed $domain_id ID of the domain
 * @return mixed
 */
function get_wstr_price_percentage($domain_id)
{
    $regular_price = get_wstr_regular_price($domain_id);
    $sale_price = get_wstr_sale_price($domain_id);

    $percentage_discount = 0;

    if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
        // Calculate the discount percentage
        $percentage_discount = (($regular_price - $sale_price) / $regular_price) * 100;
        $percentage_discount = round($percentage_discount); // Round to 2 decimal places for readability  
    }

    $output = ' <div class="ws_discount_percent">' . $percentage_discount . '%</div>';

    if ($percentage_discount > 0) {
        return $output;
    }
}

/**
 * Function for getting currency symbol
 * @return string
 */
function get_wstr_currency()
{

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $currency = $_SESSION['currency'] ? $_SESSION['currency'] : 'USD';
    return get_wstr_currency_symbol($currency);
}


/**
 * Fuction for getting currency symbol 
 * @param mixed $string ex: $string = 'USD'
 * @return string
 */
function get_wstr_currency_symbol($string, $for_api = false)
{
    // $locale = 'en-US'; //browser or user locale
    // $fmt = new NumberFormatter($locale . "@currency=$string", NumberFormatter::CURRENCY);
    // $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    // header("Content-Type: text/html; charset=UTF-8;");
    // return $symbol;

    $locale = 'en-US'; //browser or user locale
    $fmt = new NumberFormatter($locale . "@currency=$string", NumberFormatter::CURRENCY);
    $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

    if ($for_api) {
        // Convert Unicode character to escaped representation
        $escaped_symbol = json_encode($symbol);
        // Remove quotes around the symbol
        $escaped_symbol = substr($escaped_symbol, 1, -1);
        return $escaped_symbol;
    } else {
        header("Content-Type: text/html; charset=UTF-8;");
        return $symbol;
    }
}

/**
 * Fuction for checking if product is on sale
 * @param mixed $domain_id 
 * @return bool
 */
function wstr_on_sale($domain_id)
{
    if ($domain_id) {
        $context = false;
        $sale_price = get_post_meta($domain_id, '_sale_price', true);
        if ($sale_price) {
            $context = true;
        }
    }
    return $context;
}


/**
 * Truncate a number to a specified number of decimal places without rounding.
 * 
 * This function will truncate a number to the given precision without rounding
 * and will handle both positive and negative numbers.
 *
 * @param float|int $number The number to be truncated.
 * @param int $precision The number of decimal places to keep. Default is 3.
 * @return float|int The truncated number.
 */
function wstr_truncate_number($number, $precision = 2)
{

    // Zero causes issues, and no need to truncate
    if (0 == (int) $number) {
        return $number;
    }

    // Determine if the number is negative
    $negative = $number < 0 ? -1 : 1;

    // Cast the number to positive to solve rounding
    $number = abs($number);

    // Calculate precision number for dividing / multiplying
    $precisionFactor = pow(10, $precision);

    // Run the math, re-applying the negative value to ensure
    // returns correctly negative / positive
    return floor($number * $precisionFactor) / $precisionFactor * $negative;
}

/**
 * Function to check if a term exists in a taxonomy for a specific domain
 * @param mixed $domain_id
 * @param mixed $taxonomy
 * @param mixed $term_slug
 * @return bool
 */
function wstr_check_existing_term($domain_id, $taxonomy, $term_slug)
{
    // Get terms associated with the post (domain_id)
    $terms = wp_get_post_terms($domain_id, $taxonomy);

    if (!is_wp_error($terms) && !empty($terms)) {
        $term_exists = false;

        // Loop through terms to see if the specific term exists
        foreach ($terms as $term) {
            if ($term->slug == $term_slug || $term->name == $term_slug || $term->term_id == $term_slug) {
                $term_exists = true;
                break;
            }
        }

        if ($term_exists) {
            // echo "The term '$term_slug' exists for post ID: $domain_id.";
            return true;
        } else {
            // echo "The term '$term_slug' does not exist for post ID: $domain_id.";
            return false;
        }
    } else {
        // echo "No terms found for post ID: $domain_id in the '$taxonomy' taxonomy.";
        return false;
    }
}

/**
 * Function for getting favourite count for specfic domain
 * @param mixed $domain_id
 * @return int|string
 */
function wstr_get_favourite_count($domain_id)
{
    $favourite_count = get_post_meta($domain_id, '_favourite_count', true);
    $favourite_count = (int) $favourite_count; // Ensure it's an integer

    // Check if the count is above 1000 and format it
    if ($favourite_count >= 1000) {
        // Format the number to display with "K" (rounded to 1 decimal place)
        $favourite_count = round($favourite_count / 1000, 1) . 'K';
    }

    return $favourite_count;
}

/**
 * Function for getting order details by order id
 * @param  int order_id
 * @return  mixed
 */

function wstr_get_order_details($order_id)
{
    $order_status = get_post_meta($order_id, '_order_status', true);
    $order_total = get_post_meta($order_id, '_order_total', true);
    $order_subtotal = get_post_meta($order_id, '_order_subtotal', true);

    $order_date = get_post_meta($order_id, '_date_created', true);
    $customer_id = get_post_meta($order_id, '_customer', true);
    $transfer_to = get_post_meta($order_id, '_transfer_to', true);

    $billing_first_name = get_post_meta($order_id, '_billing_first_name', true);
    $billing_last_name = get_post_meta($order_id, '_billing_last_name', true);
    $billing_company = get_post_meta($order_id, '_billing_company', true);
    $billing_address_1 = get_post_meta($order_id, '_billing_address_1', true);
    $billing_address_2 = get_post_meta($order_id, '_billing_address_2', true);
    $billing_city = get_post_meta($order_id, '_billing_city', true);
    $billing_postcode = get_post_meta($order_id, '_billing_postcode', true);
    $billing_country = get_post_meta($order_id, '_billing_country', true);
    $billing_state = get_post_meta($order_id, '_billing_state', true);
    $billing_email = get_post_meta($order_id, '_billing_email', true);
    $billing_phone = get_post_meta($order_id, '_billing_phone', true);

    $shipping_first_name = get_post_meta($order_id, '_shipping_first_name', true);
    $shipping_last_name = get_post_meta($order_id, '_shipping_last_name', true);
    $shipping_company = get_post_meta($order_id, '_shipping_company', true);
    $shipping_address_1 = get_post_meta($order_id, '_shipping_address_1', true);
    $shipping_address_2 = get_post_meta($order_id, '_shipping_address_2', true);
    $shipping_city = get_post_meta($order_id, '_shipping_city', true);
    $shipping_postcode = get_post_meta($order_id, '_shipping_postcode', true);
    $shipping_country = get_post_meta($order_id, '_shipping_country', true);
    $shipping_state = get_post_meta($order_id, '_shipping_state', true);
    $shipping_email = get_post_meta($order_id, '_shipping_email', true);
    $shipping_phone = get_post_meta($order_id, '_shipping_phone', true);

    $payment_method = get_post_meta($order_id, '_payment_method', true);
    $transaction_id = get_post_meta($order_id, '_transaction_id', true);

    $doamin_ids = get_post_meta($order_id, '_domain_ids', true);

    $data = [
        'order_status' => $order_status,
        'order_total' => $order_total,
        'order_subtotal' => $order_subtotal,
        'order_date' => $order_date,
        'customer_id' => $customer_id,
        'transfer_to' => $transfer_to,
        'payment_method' => $payment_method,
        'transaction_id' => $transaction_id,
        'doamin_ids' => $doamin_ids,
        'billing_details' => [
            'billing_first_name' => $billing_first_name,
            'billing_last_name' => $billing_last_name,
            'billing_company' => $billing_company,
            'billing_address_1' => $billing_address_1,
            'billing_address_2' => $billing_address_2,
            'billing_city' => $billing_city,
            'billing_postcode' => $billing_postcode,
            'billing_country' => $billing_country,
            'billing_state' => $billing_state,
            'billing_email' => $billing_email,
            'billing_phone' => $billing_phone,
        ],
        'shipping_details' => [
            'shipping_first_name' => $shipping_first_name,
            'shipping_last_name' => $shipping_last_name,
            'shipping_company' => $shipping_company,
            'shipping_address_1' => $shipping_address_1,
            'shipping_address_2' => $shipping_address_2,
            'shipping_city' => $shipping_city,
            'shipping_postcode' => $shipping_postcode,
            'shipping_country' => $shipping_country,
            'shipping_state' => $shipping_state,
            'shipping_email' => $shipping_email,
            'shipping_phone' => $shipping_phone,
        ],
    ];
    return $data;
}

/**
 * Function for getting currency rates
 */

// function wstr_get_currency_rates($currency)
// {
//     $access_key = 'cur_live_RFDFd4STzeV5MnBBE3MFokvZmnaKEWpfAB1wT1iP';
//     $response = wp_remote_get('https://api.currencyapi.com/v3/latest?apikey=' . $access_key . '&currencies=' . $currency);

//     if (is_wp_error($response)) {
//         // Handle the error
//         $error_message = $response->get_error_message();
//         echo "Something went wrong: $error_message";
//     } else {
//         $body = wp_remote_retrieve_body($response);
//         $data = json_decode($body, true);

//         if (isset($data['data'])) {
//             var_dump($data['data'][$currency]['value']);
//         } else {
//             error_log('Failed to update currency rate .');
//         }
//     }
// }
