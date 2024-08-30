<?php

/**
 * Functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package webstarter
 * @since 1.0.0
 */

/**
 * Enqueue the CSS files.
 *
 * @since 1.0.0
 *
 * @return void
 */

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
 * For removing block editor from domain post type
 */

add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    // Use your post type key instead of 'product'
    if ($post_type === 'domain') return false;
    return $current_status;
}


/*
 * This action hook allows to add a new empty column
 */
add_filter('manage_domain_posts_columns', 'misha_featured_image_column');
function misha_featured_image_column($column_array)
{

    // I want to add my column at the beginning, so I use array_slice()
    // in other cases $column_array['featured_image'] = 'Featured Image' will be enough
    $column_array = array_slice($column_array, 0, 1, true)
        + array('featured_image' => 'Featured Image') // our new column for featured images
        + array_slice($column_array, 1, NULL, true);

    return $column_array;
}

/*
 * This hook will fill our column with data
 */
add_action('manage_posts_custom_column', 'misha_render_the_column', 10, 2);
function misha_render_the_column($column_name, $post_id)
{

    if ($column_name == 'featured_image') {

        // if there is no featured image for this post, print the placeholder
        if (has_post_thumbnail($post_id)) {

            $thumb_id = get_post_thumbnail_id($post_id);
           
            echo '<img data-id="' . $thumb_id . '" src="' . wp_get_attachment_url($thumb_id) . '" style="width:40px; height:40px;" />';
        } 
         else {

            // // data-id should be "-1" I will explain below
            // echo '<img data-id="-1" src="' . get_stylesheet_directory_uri() . '/assets/image/wstr-placeholder.webp" />';
        }
    }
}
