<?php

/**
 * function for returning price 
 * @param mixed $domain_id required
 * @return mixed
 */
function wstr_get_price($domain_id)
{
    if ($domain_id) {
        $regular_price = get_post_meta($domain_id, '_regular_pice', true);
        $sale_price = get_post_meta($domain_id, '_sale_price', true);

        $price = 0;
        if ($sale_price) {
            $price = $sale_price;
        } else {
            $price = $regular_price;
        }
        return $price;
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
