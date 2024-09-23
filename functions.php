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

    wp_enqueue_script('wstr-js', get_template_directory_uri() . '/script.js', array('jquery'), time(), true);

    // localize ajax
    wp_localize_script('wstr-admin-js', 'cpmAjax', array('ajax_url' => admin_url('admin-ajax.php')));

    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    // select 2 js  
    wp_enqueue_style('wstr-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), true, 'all');

    wp_enqueue_script('wstr-select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), time(), true);

    // font awesome
    wp_enqueue_style('wstr-font-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', array(), true, 'all');
    wp_enqueue_script('wstr-font-js', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js', array('jquery'), time(), true);
}

add_action('wp_enqueue_scripts', 'wstr_enqueue_scripts');
function wstr_enqueue_scripts()
{
    // Enqueue public CSS
    wp_enqueue_style('wstr-public-css', get_template_directory_uri() . '/assets/public/css/wstr_style.css', array(), true, 'all');

    wp_enqueue_style('wstr-public-mobile-css', get_template_directory_uri() . '/assets/public/css/wstr_style_mobile.css', array(), true, 'all');

    wp_enqueue_style('wstr-public-card-block-css', get_template_directory_uri() . '/assets/public/css/wstr_card_block_style.css', array(), true, 'all');

    // Enqueue public JS
    wp_enqueue_script('wstr-public-js', get_template_directory_uri() . '/assets/public/js/wstr_script.js', array('jquery'), time(), true);

    wp_enqueue_script('wstr-js', get_template_directory_uri() . '/script.js', array('jquery'), time(), true);

    //Localize ajax
    wp_localize_script('wstr-public-js', 'cpmAjax', array('ajax_url' => admin_url('admin-ajax.php')));

    // select 2 js  
    wp_enqueue_style('wstr-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), true, 'all');

    wp_enqueue_script('wstr-select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), time(), true);

    // Enqueue public media
    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }
}

include(get_stylesheet_directory() . '/includes/wstr_post_type.php');
include(get_stylesheet_directory() . '/includes/wstr_post_meta_boxes.php');
include(get_stylesheet_directory() . '/includes/wstr_api.php');
include(get_stylesheet_directory() . '/includes/wstr_ajax_functions.php');
include(get_stylesheet_directory() . '/includes/wstr_shortcodes.php');
include(get_stylesheet_directory() . '/includes/wstr_filters_hooks.php');
include(get_stylesheet_directory() . '/includes/wstr_functions.php');
include(get_stylesheet_directory() . '/includes/wstr_admin_menu.php');
include(get_stylesheet_directory() . '/includes/wstr_api_functions.php');


// font awesome
function enqueue_font_awesome()
{
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// megamenu block

function enqueue_webstarter_mega_menu_assets()
{
    wp_enqueue_script(
        'mega-menu-block-editor',
        get_template_directory_uri() . '/blocks/mega-menu/index.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor'),
        filemtime(get_template_directory() . '/blocks/mega-menu/index.js'),
        true
    );

    wp_enqueue_style(
        'mega-menu-block-style',
        get_template_directory_uri() . '/blocks/mega-menu/style.css',
        array(),
        filemtime(get_template_directory() . '/blocks/mega-menu/style.css')
    );

    wp_enqueue_style(
        'mega-menu-block-editor-style',
        get_template_directory_uri() . '/blocks/mega-menu/editor.css',
        array(),
        filemtime(get_template_directory() . '/blocks/mega-menu/editor.css')
    );
}
add_action('enqueue_block_assets', 'enqueue_webstarter_mega_menu_assets');


/**
 * 
 * Ceating an custom table for order notes
 * @return void
 */
function create_order_notes_table_on_theme_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'order_notes';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT(20) UNSIGNED NOT NULL,
            note TEXT NOT NULL,
            note_type TEXT NOT NULL,
            note_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (order_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('after_setup_theme', 'create_order_notes_table_on_theme_activation');


// shortcode for single domain
add_shortcode('single_domain', 'single_domain');
function single_domain()
{
    ob_start();
    ?>
    <div class="single-container ws-container">
        <?php
        // echo do_shortcode('[wstr-multicurrency]');
        // Start the Loop.
        while (have_posts()):
            the_post();

            // Get the featured image URL
            $featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

            // Get custom fields or post meta
            $logo = get_post_meta(get_the_ID(), 'logo', true);
            $title = get_the_title();
            $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
            $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);
            $domain_length = get_post_meta(get_the_ID(), '_length', true);
            $domain_age = get_post_meta(get_the_ID(), '_age', true);
            $da_pa = get_post_meta(get_the_ID(), '_da_pa', true);
            $highlights_title = get_post_meta(get_the_ID(), '_highlight_title', true);
            $highlights_content = get_post_meta(get_the_ID(), '_highlight_content', true);
            $currency = get_wstr_currency();
            // $discount_percent = esc_html($domain['precentage_discount']);
            // $term_exist = isset($domain['term_exist']) ? (bool) $domain['term_exist'] : true; // Default to true if not set
    

            // $currency = esc_html($domain['currency']);
    
            $da = $pa = '';
            if ($da_pa) {
                $da_pa_split = explode('/', $da_pa);
                $da = $da_pa_split[0];
                $pa = $da_pa_split[1];
            }
            $post_count = (int) get_post_meta(get_the_ID(), 'ws_product_view_count', true);
            $new_post_count = $post_count + 1;

            update_post_meta(get_the_ID(), 'ws_product_view_count', $new_post_count);

            ?>
            <div class="single_domain_details ws_flex fd_mob_col ">
                <!-- Featured Image -->
                <div class="featured-image p_relative">
                    <?php if ($featured_image): ?>
                        <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>">
                    <?php endif; ?>
                    <div class="single_featured_image_footer ws_flex">
                        <span class="domain_online ws_flex gap_10 ai_center online">
                            <i class="fa-solid fa-comments"></i>
                            <!-- <p>Online</p> -->
                            <i class="fa-solid fa-circle"></i>
                        </span>
                        <a href="#">
                            <p> <?php echo esc_html('Message'); ?></p>
                        </a>
                    </div><?php
                    // if ($term_exist) {
                    $output .= '<div class="premium_icon"><img src="/wp-content/plugins/card-block/images/diamond.png"
                        alt="Diamond Icon" /></div>';
                    // } ?>
                    <div class="ws_flex ai_center single_domain_meta_search">
                        <div class="single_domain_search">
                            <form>
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </form>
                        </div>
                        <div class="ws-card-likes">
                            <h6><span>2k</span><i class="fa-solid fa-heart"></i></h6>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="domain-details">
                    <div class="ws_flex gap_20 ai_center p_relative">
                        <div><?php
                        // if ((int) $discount_percent > 0) {
                        //     $output .= '<div class="ws_discount_percent"> -' . $discount_percent . '%</div>';
                        // }
                        if ($logo): ?>
                                <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($title); ?>" class="logo">
                            <?php elseif ($featured_image): ?>
                                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>"
                                    class="logo">
                            <?php endif; ?>
                        </div>

                        <?php
                        //  if (isset($discount_percent) && (int) $discount_percent > 0) {
                        echo get_wstr_price_percentage(get_the_ID());
                        // }
                        ?>
                        <div>
                            <h2 class="fw-600"><?php echo esc_html($title); ?></h2>
                            <div class="single_domain_price ws_flex gap_10 ai_center">
                                <?php
                                if (!empty($regular_price)) { ?>
                                    <p class="regular_price"><?php
                                    echo get_wstr_currency();
                                    echo get_wstr_regular_price(get_the_ID()); ?></p><?php
                                }
                                if (!empty($sale_price)) { ?>
                                    <p class="sale_price"><?php
                                    echo get_wstr_currency();
                                    echo get_wstr_sale_price(get_the_ID()); ?></p><?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="single_domain_short_desc">
                        <?php
                        the_excerpt();
                        ?>
                    </div>
                    <div class="wstr_payments">
                        <h6>PAYMENT OPTIONS</h6>
                    </div>
                    <?php
                    // the_content();
                    ?>


                </div>
            </div>
            <div class="single_domain_reviews_information ws_flex fd_mob_col">
                <div class="single_domain_tabs_container">
                    <ul class="tabs">
                        <li class="tab current" data-tab="tab-1">Domain</li>
                        <li class="tab" data-tab="tab-2">Seller Reviews</li>
                    </ul>

                    <div id="tab-1" class="tab-content current">
                        <h2 class="fw-600 margin_v_35">Domain Information</h2>
                        <div class="single_domain_progress_wrapper br_15">
                            <h2 class="fw-600"><?php echo esc_html($title); ?></h2>
                            <div class="ws_flex gap_20">
                                <div class="circular-progress page-trust">
                                    <div class="progress-text">
                                        <div role="progressbar" aria-valuenow="<?php echo esc_html($pa); ?>" aria-valuemin="0"
                                            aria-valuemax="100" style="--value:<?php echo esc_html($pa); ?>">
                                            <p>of 100</p>
                                        </div>
                                    </div>
                                    <div class="progress-title">
                                        <h6>PAGE TRUST</h6>
                                    </div>
                                </div>

                                <div class="circular-progress domain-trust">
                                    <div class="progress-text">
                                        <div role="progressbar" aria-valuenow="<?php echo esc_html($da); ?>" aria-valuemin="0"
                                            aria-valuemax="100" style="--value:<?php echo esc_html($da); ?>">
                                            <p>of 100</p>
                                        </div>
                                    </div>
                                    <div class="progress-title">
                                        <h6>DOMAIN TRUST</h6>

                                    </div>
                                </div>
                                <div class="circular-progress domain-trust">
                                    <div class="progress-text">
                                        <div role="progressbar" aria-valuenow="<?php echo esc_html($domain_length); ?>"
                                            aria-valuemin="0" aria-valuemax="100"
                                            style="--value:<?php echo esc_html($domain_length); ?>">
                                            <p>Letters</p>
                                        </div>
                                    </div>
                                    <div class="progress-title">
                                        <h6>DOMAIN LENGTH</h6>

                                    </div>
                                </div>
                                <div class="circular-progress domain-trust">
                                    <div class="progress-text">
                                        <div role="progressbar" aria-valuenow="<?php echo esc_html($domain_age); ?>"
                                            aria-valuemin="0" aria-valuemax="100"
                                            style="--value:<?php echo esc_html($domain_age); ?>">
                                            <p>Years</p>
                                        </div>
                                    </div>
                                    <div class="progress-title">
                                        <h6>DOMAIN AGE</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="single_domain_info">
                            <?php
                            the_content();
                            ?>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-content">
                        <h2>Tab Two</h2>
                        <!-- Your content here -->

                    </div>
                </div>

                <div class="single_domain_highlights">
                    <h2 class="fw-600"><?php echo ($highlights_title) ?></h2>
                    <div class="single_domain_highlights_cards">
                        <div class="single_domain_highlights_card">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-1.png"
                                alt="Feature Image">
                            <h5>Fast and Secure Transfer</h5>
                        </div>
                        <div class="single_domain_highlights_card">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-1.png"
                                alt="Feature Image">
                            <h5>Fast and Secure Transfer</h5>
                        </div>
                        <div class="single_domain_highlights_card">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-1.png"
                                alt="Feature Image">
                            <h5>Fast and Secure Transfer</h5>
                        </div>
                        <div class="single_domain_highlights_card">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-1.png"
                                alt="Feature Image">
                            <h5>Fast and Secure Transfer</h5>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        endwhile; // End the Loop.
        ?>
    </div>
    <?php

    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}