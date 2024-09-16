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
    $price_html = '<div class="wstr-price_html">
    <span class="wstr-currency">' . get_wstr_currency_symbol($currency) . '</span>
    <span class="wstr-price">' . wstr_truncate_number($price) . '<span> </div>';
    return $price_html;
}

/**
 * Function for getting currency symbol
 * @return void
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
 * Function for getting regular price of domain
 * @param mixed $domain_id
 * @return void
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
 * @return void
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
        $sale_price = $sale_price > 0 ? $sale_price * $sale_price : 0;
    }
    return wstr_truncate_number($sale_price);
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

/**
 * Function to check if a term exists in a taxonomy for a specific domain
 * @param mixed $domain_id
 * @param mixed $taxonomy
 * @param mixed $term_slug
 * @return bool
 */
function wstr_check_existing_term($domain_id,$taxonomy, $term_slug)
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
 * Function for home page premium domains via REST API
 */
function wstr_premium_domains_api($request)
{

    $params = $request->get_params();
    if (isset($params['type']) && $params['type'] === 'premium') {
        $query_args = array(
            'posts_per_page' => 8,
            'post_type' => 'domain',
            // 'orderby' => 'rand',
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

                $logo = get_post_meta($premium_domain, '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);

                $sale_price = get_post_meta($premium_domain, '_sale_price', true);
                $regular_price = get_post_meta($premium_domain, '_regular_price', true);
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $currency = $_SESSION['currency'] ?? '';

                $regular_price = get_wstr_regular_price($premium_domain);
                $sale_price = get_wstr_sale_price($premium_domain);

                $percentage_discount = 0;

                if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                    // Calculate the discount percentage
                    $percentage_discount = (($regular_price - $sale_price) / $regular_price) * 100;
                    $percentage_discount = round($percentage_discount, 2); // Round to 2 decimal places for readability  
                }
                // Get the price using custom function (assuming it exists)
                $domain_price = get_wstr_price($premium_domain);
                $currency = get_wstr_currency();
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
                    'id' => $premium_domain,
                    'title' => $domain_title,
                    'permalink' => $domain_permalink,
                    'featured_image' => $domain_image,
                    'logo' => $logo_url,
                    'price' => $domain_price,
                    'da' => $da,
                    'pa' => $pa,
                    'currency' => $currency,
                    'sale_price' => $sale_price,
                    'regular_price' => $regular_price,
                    'precentage_discount' => $percentage_discount,
                );
            }
        }

        // Return the data in JSON format
        return new WP_REST_Response($premium_domains_data, 200);
    } else if (isset($params['type']) && $params['type'] === 'new') {
        $query_args = array(
            'posts_per_page' => 8,
            'post_type' => 'domain',
            // 'orderby' => 'rand', //rand
            'order' => 'DESC',
            'fields' => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            ),
        );

        $domains = get_posts($query_args);

        // Prepare data to return as JSON
        $domains_data = array();

        if ($domains) {
            foreach ($domains as $domain) {
                // Get the basic domain details
                $domain_title = get_the_title($domain);
                $domain_permalink = get_permalink($domain);
                $domain_image = get_the_post_thumbnail_url($domain, 'medium_large');

                if (!$domain_image) {
                    $domain_image = get_stylesheet_directory_uri() . '/assets/images/Frame-1.png';
                }

                $logo = get_post_meta($domain, '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);


                $regular_price = get_wstr_regular_price($domain);
                $sale_price = get_wstr_sale_price($domain);

                $percentage_discount = 0;

                if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                    // Calculate the discount percentage
                    $percentage_discount = (($regular_price - $sale_price) / $regular_price) * 100;
                    $percentage_discount = round($percentage_discount, 2); // Round to 2 decimal places for readability  
                }
                // Get the price using custom function (assuming it exists)
                $domain_price = get_wstr_price($domain);
                $currency = get_wstr_currency();
                // Get DA / PA Ranking
                $da_pa = get_post_meta($domain, '_da_pa', true);
                $da = $pa = '';
                if ($da_pa) {
                    $da_pa_split = explode('/', $da_pa);
                    $da = $da_pa_split[0];
                    $pa = $da_pa_split[1];
                }

                $term_exist = wstr_check_existing_term($domain, 'domain_cat','premium-names');

                // Add to the response array
                $domains_data[] = array(
                    'id' => $domain,
                    'title' => $domain_title,
                    'permalink' => $domain_permalink,
                    'featured_image' => $domain_image,
                    'logo' => $logo_url,
                    'price' => $domain_price,
                    'da' => $da,
                    'pa' => $pa,
                    'currency' => $currency,
                    'sale_price' => $sale_price,
                    'regular_price' => $regular_price,
                    'precentage_discount' => $percentage_discount,
                    'term_exist' => $term_exist,
                );
            }
        }

        // Return the data in JSON format
        return new WP_REST_Response($domains_data, 200);
    }
}
