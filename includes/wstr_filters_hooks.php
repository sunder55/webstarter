<?php

/**
 * For removing block editor from domain post type
 */

add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    // Use your post type key instead of 'product'
    if ($post_type === 'domain')
        return false;
    return $current_status;
}


/*
 * For adding featured image column to the domain list in backend
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

/**
 * Display the featured image in the custom column of the posts list table.
 *
 * Hooked to the 'manage_posts_custom_column' action to customize the display
 * of columns in the WordPress admin post list.
 *
 * @param string $column_name The name of the column being rendered.
 * @param int    $post_id     The ID of the current post.
 */
add_action('manage_posts_custom_column', 'misha_render_the_column', 10, 2);
function misha_render_the_column($column_name, $post_id)
{

    if ($column_name == 'featured_image') {

        // if there is no featured image for this post, print the placeholder
        if (has_post_thumbnail($post_id)) {

            $thumb_id = get_post_thumbnail_id($post_id);

            echo '<img data-id="' . $thumb_id . '" src="' . wp_get_attachment_url($thumb_id) . '" style="width:40px; height:40px;" />';
        } else {

            // // data-id should be "-1" I will explain below
            // echo '<img data-id="-1" src="' . get_stylesheet_directory_uri() . '/assets/image/wstr-placeholder.webp" />';
        }
    }
}

/**
 * Function for adding custom use role
 */
add_action('init', 'wstr_add_custom_user_roles');
function wstr_add_custom_user_roles()
{
    // Add Buyer role
    add_role('buyer', __('Buyer', 'webstarter'), array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
        // Add any other capabilities 
    ));

    // Add Seller role
    add_role('seller', __('Seller', 'webstarter'), array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'publish_posts' => true,
        'upload_files' => true,
        'edit_others_posts' => false,
        'delete_others_posts' => false,
        // Add any other capabilities you need
    ));
}
