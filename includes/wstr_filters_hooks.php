<?php

/**
 * For removing block editor from domain post type
 */

add_filter('use_block_editor_for_post_type', 'wstr_disable_gutenberg', 10, 2);
function wstr_disable_gutenberg($current_status, $post_type)
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

        // 'read' => true,
        'edit_domains' => true,
        'delete_domains' => true,
        'publish_domains' => true,
        // 'upload_files' => true,
        'edit_others_domains' => true,
        'delete_others_domains' => false,
        // Add any other capabilities you need
    ));
}

// adding seller capablities for allowing edit and delete to their own domains
function add_seller_domain_capabilities()
{
    // Get the role

    $roles = array('seller');

    // Loop through each role and assign capabilities
    foreach ($roles as $the_role) {

        $role = get_role($the_role);

        if ($role) {
            // Capabilities for managing their own posts
            $role->add_cap('read'); // Basic read capability
            $role->add_cap('edit_posts'); // Edit their own posts
            $role->add_cap('delete_posts'); // Delete their own posts
            $role->add_cap('publish_posts'); // Publish their own posts

            // Additional capabilities to edit and delete published posts
            $role->add_cap('edit_published_posts'); // Edit their own published posts
            $role->add_cap('delete_published_posts'); // Delete their own published posts

            // Prevent capabilities that allow managing others' posts
            $role->remove_cap('edit_others_posts');
            $role->remove_cap('delete_others_posts');
            // Optionally remove private post capabilities if needed
            $role->remove_cap('edit_private_posts');
            $role->remove_cap('delete_private_posts');
        }
    }
}

add_action('init', 'add_seller_domain_capabilities');



/**
 * Starting sestion
 */
add_action('init', 'wstr_start_session');
function wstr_start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $GLOBALS['user_id'] = get_current_user_id();
}

/**
 * Filter for the solution of extra tag adding to the shortcode
 */
add_filter('register_block_type_args', function ($settings, $name) {
    if ($name === 'core/shortcode') {
        $settings['render_callback'] = function ($attributes, $content) {
            return $content;
        };
    }
    return $settings;
}, 10, 2);


/**
 * Preventing default wp login
 */
add_action('init', 'wstr_prevent_wp_login');
function wstr_prevent_wp_login()
{
    if (!is_user_logged_in()) {
        if ($_GET['reason'] == 'checkout') {
            $_SESSION['redirect'] = 'checkout';
        }
    }

    global $pagenow;

    $allowed_actions = ['logout', 'lostpassword', 'rp', 'resetpass', 'postpass']; // allowing action 

    if ($pagenow == 'wp-login.php' && (!isset($_GET['action']) || !in_array($_GET['action'], $allowed_actions))) {
        $page = get_home_url() . '/my-account';

        wp_redirect($page);
    }
}


add_action('init', 'wstr_redirect_after_login', 999);
function wstr_redirect_after_login()
{
    if (is_user_logged_in()) {
        if (isset($_SESSION['redirect']) && !empty($_SESSION['redirect']) && $_SESSION['redirect'] == 'checkout') {
            $redirect = $_SESSION['redirect'];
            unset($_SESSION['redirect']);
            wp_redirect(home_url('/' . $redirect));
            exit;
        }
    }
}

/**
 * passing login error codes as parameter
 */
add_filter('login_errors', function ($error) {
    global $errors;
    $err_codes = $errors->get_error_codes();
    wp_redirect('/my-account?reason=' . $err_codes[0]);
    return $error;
});

// for getting number of post in rest api
add_filter('rest_domain_collection_params', function ($params, $post_type) {
    if (isset($params['per_page'])) {
        $params['per_page']['maximum'] = 9999; //edit it as you want
    }

    return $params;
}, 10, 2);


// for getting number of post in rest api
add_filter('rest_domain_order_collection_params', function ($params, $post_type) {
    if (isset($params['per_page'])) {
        $params['per_page']['maximum'] = 999; //edit it as you want
    }

    return $params;
}, 10, 2);


function allow_rest_access_for_admins($result)
{
    // if (is_user_logged_in() && current_user_can('manage_options')) {
    if (is_user_logged_in()) {
        return true;
    }
    return $result;
}
add_filter('rest_authentication_errors', 'allow_rest_access_for_admins');


add_action('rest_api_init', 'register_custom_meta_fields');
function register_custom_meta_fields()
{
    register_post_meta('domain', '_enable_offers', array(
        'single' => true,
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'auth_callback' => '__return_true'
    ));

    register_post_meta('domain', '_regular_price', array(
        'single' => true,
        'type' => 'string',
        'default' => '',
        'show_in_rest' => true,
        'auth_callback' => '__return_true'
    ));
}

function rest_api_permissions($result)
{
    $current_user = wp_get_current_user();
    if ($current_user->has_cap('seller')) {
        $result['read'] = true;
        $result['create_posts'] = true;
    }
    return $result;
}
add_filter('rest_domain_collection_params', 'rest_api_permissions');

/**
 * redirect /domain to the /buy page
 */

function wstr_redirect_domain_page()
{
    // Check if the current request is for the /domain page
    if (is_user_logged_in()) {
        if (trim($_SERVER['REQUEST_URI'], '/') === 'domain') {
            wp_redirect(home_url('/buy'));
            exit;
        }
        if (trim($_SERVER['REQUEST_URI'], '/') === 'login') {
            wp_redirect(home_url('/my-account'));
            exit;
        }
        // if (is_user_logged_in()) {
        if (trim($_SERVER['REQUEST_URI'], '/') === 'register') {
            wp_redirect(home_url('/my-account'));
            exit;
        }
    }
    // }
}
add_action('template_redirect', 'wstr_redirect_domain_page');


function filter_media_comment_status($open, $post_id)
{
    $post = get_post($post_id);
    if ($post->post_type == 'attachment') {
        return false;
    }
    return $open;
}
add_filter('comments_open', 'filter_media_comment_status', 10, 2);

/**
 * Avoiding accest to the admin dashboard for non admin
 * @return void
 */
function redirect_non_admin_users()
{
    if (is_admin() && !defined('DOING_AJAX')) {
        $user_id = get_current_user_id();
        $user_meta = get_userdata($user_id);
        $user_roles = $user_meta ? $user_meta->roles : [];

        if (!in_array('administrator', $user_roles)) {
            wp_redirect(site_url('/my-account/'));
            exit;
        }
    }
}
add_action('init', 'redirect_non_admin_users');

/**
 * Hiding dashboard header for non admin users
 * @return void
 */
function hide_admin_bar_for_non_admins()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('init', 'hide_admin_bar_for_non_admins');


// Adds the new domains column to the users dashboard.
function add_discord_column_to_users_dashboard($columns)
{
    // Unset columns you don't want
    unset($columns['posts']); // Removes the "Posts" column

    // Add the custom "Domains" column
    $columns['domains'] = esc_html__('Domains', 'text_domain');

    return $columns;
}
add_filter('manage_users_columns', 'add_discord_column_to_users_dashboard');

// Populate domains on users dashboard column.
function populate_users_dashboard_discord_column($output, $column_name, $user_id)
{
    if ('domains' === $column_name) {
        // Get the total number of domains (custom post type) assigned to the user.
        $domain_count = new WP_Query([
            'post_type'      => 'domain',
            'author'         => $user_id,
            'posts_per_page' => -1, // Get all posts
            'fields'         => 'ids', // Fetch only IDs for efficiency
        ]);

        $output = $domain_count->post_count; // Get the count of posts
    }

    return $output;
}
add_filter('manage_users_custom_column',  'populate_users_dashboard_discord_column', 10, 3);
