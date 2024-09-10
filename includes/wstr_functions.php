<?php

/**
 * function for returning price 
 * @param mixed $domain_id required
 * @return mixed
 */
function wstr_get_price($domain_id)
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
    $price_html = '<div class="wstr-price_html">
    <span class="wstr-currency">' . get_wstr_currency_symbol($currency) . '</span>
    <span class="wstr-price">' . wstr_truncate_number($price) . '<span> </div>';
    return $price_html;
}

/**
 * Fuction for getting currency symbol 
 * @param mixed $string ex: $string = 'USD'
 * @return void
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
 * Fuction for check if product is on sale
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
    if (0 == (int)$number) {
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



add_shortcode('test', 'test');
function test()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['currency'] = '';

    $price = wstr_get_price(5036);
    echo ($price);
    // $curArr = Zend_Locale::getTranslationList('CurrencySymbol');
    // echo $curArr['GBP'];
    echo  get_wstr_currency_symbol('JPY');
}




/**
 * Function for home page premium domains via REST API
 */
function wstr_premium_domains_api()
{
    $query_args = array(
        'posts_per_page' => 8,
        'post_type' => 'domain',
        'orderby' => 'rand',
        'order' => 'DESC',
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!='
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'domain_cat',
                'field' => 'term_id',
                'terms' => 57, // Example category ID
            ),
        ),
    );

    $premium_domains = get_posts($query_args);

    // Prepare data to return as JSON
    $premium_domains_data = array();

    if ($premium_domains) {
        foreach ($premium_domains as $premium_domain) {
            // Get the basic domain details
            $domain_title = get_the_title($premium_domain);
            $domain_permalink = get_permalink($premium_domain);
            $domain_image = get_the_post_thumbnail_url($premium_domain, 'medium_large');

            if (!$domain_image) {
                $domain_image = get_stylesheet_directory_uri() . '/assets/images/Frame-1.png';
            }

            // Get the price using custom function (assuming it exists)
            $domain_price = wstr_get_price($premium_domain);

            $currency = get_wstr_currency_symbol('JPY');

            // Get DA / PA Ranking
            $da_pa = get_post_meta($premium_domain, '_da_pa', true);
            $da = $pa = '';
            if ($da_pa) {
                $da_pa_split = explode('/', $da_pa);
                $da = $da_pa_split[0];
                $pa = $da_pa_split[1];
            }

            // Add to the response array
            $premium_domains_data[] = array(
                'title' => $domain_title,
                'permalink' => $domain_permalink,
                'image' => $domain_image,
                'price' => $domain_price,
                'da' => $da,
                'pa' => $pa,
                'currency' => $currency
            );
        }
    }

    // Return the data in JSON format
    return new WP_REST_Response($premium_domains_data, 200);
}
