<?php

add_action('admin_enqueue_scripts', 'wstr_enqueue_admin_scripts');
function wstr_enqueue_admin_scripts()
{
    // Enqueue admin CSS
    wp_enqueue_style('wstr-admin-css', get_template_directory_uri() . '/assets/admin/css/wstr_style.css', array(), true, 'all');

    // Enqueue admin JS
    wp_enqueue_script('wstr-admin-js', get_template_directory_uri() . '/assets/admin/js/wstr_script.js', array('jquery'), time(), true);

    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }
}

add_action('wp_enqueue_scripts', 'wstr_enqueue_scripts');
function wstr_enqueue_scripts()
{
    // Enqueue public CSS
    wp_enqueue_style('wstr-admin-css', get_template_directory_uri() . '/assets/public/css/wstr_style.css', array(), true, 'all');

    // Enqueue public JS
    wp_enqueue_script('wstr-admin-js', get_template_directory_uri() . '/assets/public/js/wstr_script.js', array('jquery'), time(), true);

    // Enqueue public media
    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }
}



include(get_stylesheet_directory() . '/includes/wstr_post_type.php');
include(get_stylesheet_directory() . '/includes/wstr_post_meta_boxes.php');


/**
 * 
 */

add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    // Use your post type key instead of 'product'
    if ($post_type === 'domain') return false;
    return $current_status;
}
