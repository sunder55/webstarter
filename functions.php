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

    // pagination js 
    // <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    // <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.min.js"></script>
    // <link href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.css" rel="stylesheet" />

    wp_enqueue_style('wstr-pagination-css', 'https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.4/pagination.css', array(), true, 'all');
    // wp_enqueue_script('wstr-pagination-js', 'https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.3/pagination.min.js', array('jquery'), time(), true);
    wp_enqueue_script('wstr-pagination', get_template_directory_uri() . '/pagination.js', array('jquery'), time(), true);

    // select 2 js  
    wp_enqueue_style('wstr-select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), true, 'all');

    wp_enqueue_script('wstr-select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), time(), true);



    // Enqueue public media
    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    // // react app 
    // wp_enqueue_script(
    //     'my-react-app',
    //     WP_PLUGIN_DIR.'/react-plugin/my-account/build/static/main.1d965476.js', // Path to your React build JS file
    //     ['wp-element'], // This depends on the WordPress `wp-element` library
    //     filemtime(plugin_dir_path(__FILE__) . 'build/index.js'), // Cache busting
    //     true
    // );
    // wp_enqueue_style(
    //     'my-react-app-style',
    //     WP_PLUGIN_DIR.'/react-plugin/my-account/build/static/main.f855e6bc.css', // Path to your React build CSS file (if any)
    //     [],
    //     filemtime(plugin_dir_path(__FILE__) . 'build/index.css')
    // );

    wp_localize_script('your-script-handle', 'wpApiSettings', array(
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

include(get_stylesheet_directory() . '/includes/wstr_post_type.php');
include(get_stylesheet_directory() . '/includes/wstr_post_meta_boxes.php');
include(get_stylesheet_directory() . '/includes/wstr_api.php');
include(get_stylesheet_directory() . '/includes/wstr_ajax_functions.php');
include(get_stylesheet_directory() . '/includes/wstr_shortcodes.php');
include(get_stylesheet_directory() . '/includes/wstr_filters_hooks.php');
include(get_stylesheet_directory() . '/includes/wstr_functions.php');
include(get_stylesheet_directory() . '/includes/wstr_admin_menu.php');
include(get_stylesheet_directory() . '/includes/wstr_notifications.php');
include(get_stylesheet_directory() . '/includes/wstr_payouts.php');
include(get_stylesheet_directory() . '/includes/wstr_contact_us.php');


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


/**
 * 
 * Ceating an custom table for contact us
 * @return void
 */

add_action('after_setup_theme', 'wstr_create_contact_us_table');
function wstr_create_contact_us_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'contact_us';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id int NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(55) DEFAULT '' NOT NULL,
        phone varchar(20) NOT NULL,
        type text NOT NULL,
        message text NOT NULL,
        time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}




// add_action('wp_footer', function () {


//     $user_details = get_user_by('id',$GLOBALS['user_id']);
//     echo '<pre>';
//     // var_dump($user_details);
//     // var_dump($user_details->data->ID);

//     $data[] = [
//         'id' => $user_details->data->ID ? $user_details->data->ID : '',
//         'user_login' => $user_details->data->user_login ? $user_details->data->user_login : '',
//         'user_email' => $user_details->data->user_email ? $user_details->data->user_email : '',
//         'cap_key' =>$user_details->caps ? $user_details->caps : '',
//         'roles' => $user_details->roles ? $user_details->roles : '',
//     ];
//     // var_dump($data);
//     // $text = 'hello.com';
//     // $apiKey = "sk_6f97cb3d8e487984ffa46daebf483dab8815a02ba7204d8b"; // Replace with your

//     // // The API key for authentication
//     // // $XI_API_KEY = "<xi-api-key>";

//     // // The URL of the API endpoint
//     // $url = "https://api.elevenlabs.io/v1/voices";

//     // // Set up headers for the HTTP request
//     // $headers = [
//     //     "Accept: application/json",
//     //     "xi-api-key: $XI_API_KEY",
//     //     "Content-Type: application/json"
//     // ];

//     // // Initialize cURL session
//     // $ch = curl_init($url);

//     // // Set cURL options
//     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
//     // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set the headers for the request

//     // // Execute the GET request
//     // $response = curl_exec($ch);

//     // // Check if there was an error with the request
//     // if (curl_errno($ch)) {
//     //     echo 'Request Error:' . curl_error($ch);
//     // } else {
//     //     // Parse the JSON response into a PHP associative array
//     //     $data = json_decode($response, true);

//     //     // Loop through the 'voices' array and print 'name' and 'voice_id'
//     //     foreach ($data['voices'] as $voice) {
//     //         echo $voice['name'] . "; " . $voice['voice_id'] . "\n";
//     //     }
//     // }

//     // // Close the cURL session
//     // curl_close($ch);

//     // $curl = curl_init();

//     //    $request_payload = [
//     //     "text" => $text,
//     //     "voice_settings" => [
//     //         "similarity_boost" => 0.5,
//     //         "stability" => 0.5,
//     //         "style" => 0.5,
//     //         "use_speaker_boost" => true
//     //     ]
//     // ];

//     // curl_setopt_array($curl, [
//     //     CURLOPT_URL => "https://api.elevenlabs.io/v1/text-to-speech/onwK4e9ZLuTAKqWW03F9",
//     //     CURLOPT_RETURNTRANSFER => true,
//     //     CURLOPT_ENCODING => "",
//     //     CURLOPT_MAXREDIRS => 10,
//     //     CURLOPT_TIMEOUT => 30,
//     //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     //     CURLOPT_CUSTOMREQUEST => "POST",
//     //       CURLOPT_POSTFIELDS => json_encode($request_payload),
//     //     CURLOPT_HTTPHEADER => [
//     //         "Content-Type: application/json",
//     //         "xi-api-key: " . $apiKey,
//     //     ],
//     // ]);

//     // $response = curl_exec($curl);
//     // $err = curl_error($curl);

//     // curl_close($curl);

//     // if ($err) {
//     //     echo "cURL Error #:" . $err;
//     // } else {
//     //     echo $response;
//     // }


//     // $text = 'hello.com';
//     // $api_url = "https://api.elevenlabs.io/v1/text-to-speech/onwK4e9ZLuTAKqWW03F9"; // Adjust the output format as needed
//     // $request_payload = [
//     //     "text" => $text,
//     //     "voice_settings" => [
//     //         "similarity_boost" => 0.5,
//     //         "stability" => 0.5,
//     //         "style" => 0.5,
//     //         "use_speaker_boost" => true
//     //     ]
//     // ];

//     // $apiKey = "sk_eedc67f1e1f786064f584e497acbe18c37cdb860905ca325"; // Replace with your actual API key

//     // $curl = curl_init();

//     // curl_setopt_array($curl, [
//     //     CURLOPT_URL => $api_url,
//     //     CURLOPT_RETURNTRANSFER => true,
//     //     CURLOPT_ENCODING => "",
//     //     CURLOPT_MAXREDIRS => 10,
//     //     CURLOPT_TIMEOUT => 30,
//     //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     //     CURLOPT_CUSTOMREQUEST => "POST",
//     //     CURLOPT_POSTFIELDS => json_encode($request_payload),
//     //     CURLOPT_HTTPHEADER => [
//     //         "Content-Type: application/json",
//     //         "xi-api-key: " . $apiKey,
//     //     ],
//     // ]);

//     // $response = curl_exec($curl);
//     // $err = curl_error($curl);

//     // curl_close($curl);

//     // // if ($err) {
//     // //     return "Error #:" . $err;
//     // // } else {
//     // //     // var_dump($response);
//     // //     return $response;
//     // // }
//     // var_dump($response);
//     // // Define file name and path
//     // $upload_dir = wp_upload_dir();
//     // $file_name = $text . '.wav';
//     // $file_path = $upload_dir['path'] . '/' . $file_name;
//     // $file_url = $upload_dir['url'] . '/' . $file_name;

//     // // Save audio data to the file
//     // file_put_contents($file_path, $response);

//     // // Display HTML audio player with file path
//     // $audio_player = '<audio controls>';
//     // $audio_player .= '<source src="' . $file_url . '" type="audio/wav">';
//     // $audio_player .= 'Your browser does not support the audio tag.';
//     // $audio_player .= '</audio>';

//     // // var_dump($audio_player);
//     // echo $audio_player;
// });



// register shortcode
// add_shortcode('wstr_register', 'wstr_register');
function wstr_register()
{
    ob_start();
?>
    <form action="#" method="POST" class="wstr_signup">
        <label for="username">Username*</label>
        <input type="text" id="username" name="username" placeholder="Your Username" required>

        <label for="full-name">First Name, Last Name</label>
        <input type="text" id="full-name" name="full_name" placeholder="Enter first and last name" required>

        <label for="email">Email Address*</label>
        <input type="email" id="email" name="email" placeholder="@Email address " required>

        <label for="password">Password*</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <label for="confirm-password">Confirm Password*</label>
        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password " required>

        <div class="checkbox-group">
            <input type="checkbox" id="become-seller" name="become_seller">
            <label for="become-seller">Become a Seller</label>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">I have read and accepted the <a href="#">terms and conditions</a></label>
        </div>

        <button type="submit">Register</button>

        <div class="login-link">
            <p>Already registered? <a href="#">Login</a></p>
        </div>
    </form>
<?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}


include(get_stylesheet_directory() . '/buy-domains.php');

function wstr_buy_domain($attributes)
{
    // Check if the 'type' attribute is set and default to 'new' if it's not
    $type = isset($attributes['type']) ? $attributes['type'] : 'new';


    // Fetch data from the API based on the selected type
    $response = wp_remote_get("https://new-webstarter.codepixelz.tech/wp-json/wstr/v1/domains/?type=new");

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        return '<p>' . esc_html__('Failed to fetch domains: ', 'card-block') . esc_html($error_message) . '</p>';
    }

    $domains = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($domains)) {
        return '<p>' . esc_html__('No domains found.', 'card-block') . '</p>';
    }

    // Initialize output
    ob_start();
?>
    <div class="domain-filters-container">
        <!-- Category Section -->
        <section class="category-section">
            <ul class="categories-list">
                <li>
                    <div class=" reviews_images_lists ws_flex jc_center ai_center">
                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-1.jpeg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-2.jpg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-3.jpeg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-4.jpeg"
                            alt="Client Image">
                        <p>Popular Searches</p>
                    </div>
                </li>
                <li>Trending</li>
                <li>4 Letters</li>
                <li>Retail</li>
                <li>Short Names</li>
                <li>Short Names</li>
                <li>Short Names</li>
                <li>Short Names</li>
                <li>Short Names</li>
            </ul>
        </section>

        <!-- Filters Section -->
        <section class="filters-section">
            <div class="filter-item">
                <label for="sort-by">Sort By:</label>
                <div class="filter-item-aligned filter_item_name">
                    <select id="sort-by" name="sort-by">
                        <option value="z-a">Z-A</option>
                        <option value="a-z">A-Z</option>
                    </select>
                    <select id="domain-type" name="domain-type">
                        <option value="">Any</option>
                        <!-- <option value=".com" <?php //selected($domain_tld, '.com'); 
                                                    ?>><?php _e('.com'); ?></option> -->
                        <option value=".com" <?php //selected($domain_tld, '.com'); 
                                                ?>><?php _e('.com'); ?></option>
                        <option value=".net" <?php //selected($domain_tld, '.net'); 
                                                ?>><?php _e('.net'); ?></option>
                        <option value=".org" <?php //selected($domain_tld, '.org'); 
                                                ?>><?php _e('.org'); ?></option>
                        <option value=".io" <?php //selected($domain_tld, '.io'); 
                                            ?>><?php _e('.io'); ?></option>
                        <option value=".ai" <?php //selected($domain_tld, '.ai'); 
                                            ?>><?php _e('.ai'); ?></option>
                        <option value=".dev" <?php //selected($domain_tld, '.dev'); 
                                                ?>><?php _e('.dev'); ?></option>
                        <option value=".pics" <?php //selected($domain_tld, '.pics'); 
                                                ?>><?php _e('.pics'); ?></option>
                        <option value=".life" <?php //selected($domain_tld, '.life'); 
                                                ?>><?php _e('.life'); ?></option>
                    </select>
                </div>
            </div>

            <div class="filter-item">
                <label for="industry">By Industry:</label>
                <?php
                $industries = get_terms([
                    'taxonomy' => 'domain_industry',
                    'hide_empty' => false,
                ]);

                ?>
                <select id="industry" name="industry[]  " multiple>
                    <?php
                    foreach ($industries as $industry) {
                    ?> <option value="<?php echo $industry->term_id; ?>"><?php echo $industry->name; ?></option>
                    <?php


                    }
                    ?>
                </select>
            </div>

            <div class="filter-item">
                <label for="style">By Style:</label>

                <?php
                $styles = get_terms([
                    'taxonomy' => 'domain_cat',
                    'hide_empty' => false,
                ]);

                ?>
                <select id="style" name="style[]" multiple>
                    <?php
                    foreach ($styles as $style) {
                    ?> <option value="<?php echo $style->term_id; ?>"><?php echo $style->name; ?></option>
                    <?php


                    }
                    ?>
                </select>
            </div>

            <div class="filter-item">
                <label for="price-range-min">Price Range:</label>
                <div class="filter-item-aligned">
                    <select name="price-range-min" id="price-range-min">
                        <option value="">Min</option>
                        <option value="500">$500</option>
                        <option value="1000">$1000</option>
                        <option value="2000">$2000</option>
                        <option value="3000">$3000</option>
                        <option value="4000">$4000</option>
                        <option value="5000">$5000</option>
                        <option value="7500">$7500</option>
                        <option value="10,000">$10,000</option>
                        <option value="25,000">$25,000</option>
                        <option value="50,000">$50,000</option>
                        <option value="100,000">$100,000</option>
                        <option value="250,000">$250,000</option>
                        <option value="500,000">$500,000</option>
                        <option value="750,000">$750,000</option>
                        <option value="1,000,000">$1,000,000</option>
                    </select>

                    <select name="price-range-max" id="price-range-max">
                        <option value="">Max</option>
                        <option value="500">$500</option>
                        <option value="1000">$1000</option>
                        <option value="2000">$2000</option>
                        <option value="3000">$3000</option>
                        <option value="4000">$4000</option>
                        <option value="5000">$5000</option>
                        <option value="7500">$7500</option>
                        <option value="10000">$10,000</option>
                        <option value="25000">$25,000</option>
                        <option value="50000">$50,000</option>
                        <option value="100000">$100,000</option>
                        <option value="250000">$250,000</option>
                        <option value="500000">$500,000</option>
                        <option value="750000">$750,000</option>
                        <option value="1000000">$1,000,000</option>
                    </select>
                </div>
            </div>

            <div class="filter-item">
                <label for="length-slider">Length <i class="fa-solid fa-arrow-right"></i><span id="length-output">
                        50</span>
                    letters </label>
                <input type="range" id="length-slider" name="length-slider" min="0" max="50" value="50"
                    oninput="updateLengthOutput(this.value)">
                <input type="hidden" id="lengthSlider">
            </div>
        </section>
    </div>
    <!-- Reset Button -->
    <div class="reset-filter">
        <button type="button" id="reset-filters" onclick="resetFilters()"><i
                class="fa-solid fa-arrow-rotate-right"></i>Reset Filters</button>
        <?php
        echo do_shortcode('[wstr-domain-count]'); ?>
    </div>
    <!-- cards contaner -->
    <div class="swiper-container ws-container buy_card_lists">
        <div class="ws-cards-container-wrapper ws_cards_xl" id="buy-domain-main">

            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = [
                'posts_per_page' => 20,
                'post_type' => 'domain',
                'paged' => $paged,
                'meta_query' => [
                    [
                        'key' => '_stock_status',
                        'value' => 'outofstock',
                        'compare' => '!='
                    ]
                ],
            ];
            query_posts($args); ?>
            <!-- the loop -->
            <?php if (have_posts()):
                while (have_posts()):
                    the_post();
                    $domain_image = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                    if (!$domain_image) {
                        $domain_image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
                    }
                    $logo = get_post_meta(get_the_ID(), '_logo_image', true);
                    $logo_url = wp_get_attachment_url($logo);
                    $price = get_wstr_price(get_the_ID());
                    $percentage_discount = 0;
                    if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                        // Calculate the discount percentage
                        $percentage_discount = (($regular_price - $sale_price) / $regular_price) * 100;
                        $percentage_discount = round($percentage_discount); // Round to 2 decimal places for readability
                    }
                    // Get the price using custom function (assuming it exists)
                    $domain_price = get_wstr_price(get_the_ID());
                    $currency = get_wstr_currency();
                    // Get DA / PA Ranking
                    $da_pa = get_post_meta(get_the_ID(), '_da_pa', true);
                    $da = $pa = '';
                    if ($da_pa) {
                        $da_pa_split = explode('/', $da_pa);
                        $da = $da_pa_split[0];
                        $pa = $da_pa_split[1];
                    }
                    $term_exist = wstr_check_existing_term(get_the_ID(), 'domain_cat', 'premium-names');
                    // Add to
            ?>
                    <div class="ws-cards-container swiper-slide">
                        <?php echo $term_exist ? '
                <div class="premium_icon">
                    <img decoding="async" src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon">
                </div>' : '';
                        ?>
                        <div class="ws_card_hover_charts ws_flex">
                            <div class="circular-progress page-trust">
                                <div class="progress-text">
                                    <div role="progressbar" aria-valuenow="<?php echo $pa ?: ''; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="--value:<?php echo $pa ?: ''; ?>"></div>
                                </div>
                                <div class="progress-title">
                                    <h6>Page Trust</h6>
                                </div>
                            </div>
                            <div class="circular-progress domain-trust">
                                <div class="progress-text">
                                    <div role="progressbar" aria-valuenow="<?php echo $da ?: ''; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="--value:<?php echo $da ?: ''; ?>"></div>
                                </div>
                                <div class="progress-title">
                                    <h6>Domain Trust</h6>
                                </div>
                            </div>
                        </div>
                        <div class="ws-card-img">
                            <img decoding="async" src="<?php echo $domain_image; ?>" alt="<?php echo get_the_title() ?>">
                        </div>
                        <div class="ws-card-contents ws-flex">
                            <?php echo get_wstr_price_percentage(get_the_ID()); ?>
                            <img decoding="async" src="<?php echo $logo_url ?: $domain_image ?>"
                                alt="<?php echo get_the_title() ?>" title="<?php echo get_the_title() ?>" class="card_logo_img">
                            <span class="ws-card-inner-contents">
                                <h5><a href="<?php echo get_permalink(get_the_ID()) ?: ''; ?> "> <?php echo get_the_title() ?>
                                    </a></h5>
                                <?php echo $price ?: ''; ?>
                                <!-- <div class="ws_card_price_wrapper ws_flex gap_10">
                            <p class="regular_price">£2500</p>
                            <p class="sale_price">£1000</p>
                        </div> -->
                            </span>
                            <div class="ws-card-likes">
                                <h6><span>2k</span><i class="fa-solid fa-heart"></i></h6>
                            </div>
                        </div>
                    </div>
            <?php endwhile;
                // <!-- pagination -->
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('<', 'webstarter'),
                    'next_text' => __('>', 'webstarter'),
                ));
            //  else :
            // <!-- No posts found -->
            endif;
            ?>

        </div>
    </div>
    <style>
        .buy_card_lists {
            margin-top: 30px;
        }

        .domain-filters-container {
            margin: 20px 0;
        }

        .domain-filters-container .categories-list li {
            font-weight: 400;
            background: #fff;
            padding: 15px;
            border-radius: 50px;
            color: #00d9f5;
        }

        .domain-filters-container .categories-list li:first-child p {
            background: #00d9f5;
            padding: 15px;
            border-radius: 50px;
            color: #fff;
            margin-left: -15px;
        }

        .domain-filters-container .categories-list li:first-child {
            background-color: transparent;
            padding: 0;
        }

        .domain-filters-container .categories-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 15px 0;
        }

        .domain-filters-container .categories-list li.active {
            border: 2px solid;
        }

        .category-section {
            margin: 2.5rem 0;
        }

        .categories-list {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }

        .categories-list li {
            display: inline-block;
            margin-right: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .filters-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            width: calc(20% - 15px);
            border-radius: 20px;
            box-shadow: 0px 30px 101px 0px rgba(0, 69, 162, 0.11);
            border: 2px solid rgb(237, 240, 247);
            padding: 20px;
            gap: 10px;
        }

        .filter-item label {
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-item select,
        .filter-item input {
            padding: 8px;
            font-size: 14px;
            border: 2px solid rgb(237, 240, 247);
            border-radius: 10px;
        }

        .filter-item select::placeholder,
        .filter-item input::placeholder {
            color: #00214c;
        }



        .filter-item-aligned {
            display: flex;
            gap: 10px;
        }

        .filter_item_name select:first-child {
            flex: 2;
        }

        .filter_item_name select:last-child {
            flex: 1;
        }

        .filter-item-aligned input {
            width: 50%;
        }

        .reset-filter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }

        .reset-filter p {
            font-size: 14px;
            line-height: 18px;
            font-weight: 500;
            background-color: #fff;
            padding: 15px;
            border-radius: 20px;
            border: 1px solid #edf0f7;

        }

        #reset-filters {
            border: transparent;
            background-color: #fff;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 50px;
            cursor: pointer;
        }

        .buy_card_lists .pagination {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .buy_card_lists .nav-links {
            display: flex;
            gap: 15px;
        }

        .buy_card_lists span.page-numbers:not(.dots) {
            background-color: #f00073;
            color: #fff;
            padding: 4px 12px;
            border-radius: 5px;
        }

        .buy_card_lists a.page-numbers {
            border: 1px solid #edf0f7;
            border-radius: 5px;
            padding: 4px 12px;
            background-color: #fff;
        }

        @media screen and (max-width: 1024px) {
            .filter-item {
                width: calc(50% - 8px);
            }
        }

        @media screen and (max-width: 767px) {
            .filter-item {
                width: 100%;
            }

            .category-section {
                margin: 0rem 0 1rem 0;
            }

            .high_banner_mobile .ws_home_banner::before {
                height: 1500px;
            }
        }
    </style>
    <script>
        function updateLengthOutput(value) {
            document.getElementById('length-output').textContent = value;
            document.getElementById('lengthSlider').value = value;
        }
    </script>
<?php
    return ob_get_clean();
}

add_shortcode('wstr-buy-domain', 'wstr_buy_domain');


// add_action('wp_footer', 'get_domain_estimation');
// add_shortcode('get-order', 'wstr_get_product_of_seller_by_order_id');

// add_action('wp_footer', 'wstr_get_order_by_seller');

function wstr_get_seller_products_by_order_and_seller_id()
{
    $order_id =  5305;
    $seller_id = 6;
    ob_start();
    $order_products = get_post_meta($order_id, '_ordered_products', true);

    // var_dump($order_products);
    $product_id = [];
    foreach ($order_products as $order_product) {
        // var_dump($order_product);
        if (in_array($seller_id, $order_product)) {
            $product_id[] = $order_product['product_id'];
        }
    }
    var_dump($product_id);
}





// add_action('wp_footer', function () {
//     $apiKey = "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";

//     // API endpoint
//     $url = "https://api.openai.com/v1/chat/completions";

//     // Prepare the request data
//     $data = [
//         "model" => "o1", // Replace with the model you want to use
//         "prompt" => "Write a PHP script to fetch data from an API.",
//         // "max_tokens" => 100, // Adjust based on your needs
//         // "temperature" => 0.7
//     ];

//     // Convert the data to JSON
//     $jsonData = json_encode($data);

//     // Initialize cURL
//     $ch = curl_init($url);

//     // Set cURL options
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, [
//         "Content-Type: application/json",
//         "Authorization: Bearer $apiKey"
//     ]);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

//     // Execute the request
//     $response = curl_exec($ch);

//     // Check for errors
//     if (curl_errno($ch)) {
//         echo "cURL error: " . curl_error($ch);
//     } else {
//         // Decode and handle the response
//         $responseData = json_decode($response, true);
//         echo "<pre>";
//         print_r($responseData);
//         echo "</pre>";
//     }

//     // Close cURL
//     curl_close($ch);
// });



function add_openai_script_to_footer()
{
    // sk - proj - MAduAuRg723hsXQcqoDsfe76frFF1j24V6o - d2UHwnHCbmVe - EoIVmVZwGXzFzXZvkIvqS3WdMT3BlbkFJTDz0CCEK3_6xFw9m0wOWSshM1gzV9aPWufH7udtT - Z43jOpQkC7fYKDUYbb0NfNMrAG1Xd2SwA
?>
    <script>
        jQuery(document).ready(function() {
            fetchOpenAIResponse();
        });
        async function fetchOpenAIResponse() {
            // const apiKey = " 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"; // Replace with your OpenAI API key
            const endpoint = "https://api.openai.com/v1/completions";

            const data = {
                model: "o1-mini",
                prompt: "Write a short motivational quote.",
                max_tokens: 50
            };

            try {
                const response = await fetch(endpoint, {
                    method: "POST",
                    headers: {
                        "Authorization": `Bearer ${apiKey}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log(result.choices[0].text); // Log the response to the console
                document.getElementById('openai-response').innerText = result.choices[0].text;
            } catch (error) {
                console.error("Error fetching OpenAI response:", error);
            }
        }

        // Trigger the function on page load
        document.addEventListener("DOMContentLoaded", fetchOpenAIResponse);
    </script>

    <div id="openai-response" style="margin-top: 20px; font-weight: bold; font-size: 1.2em;"></div>
<?php
}
// add_action('wp_footer', 'add_openai_script_to_footer');


// add_action('wp_footer', 'wstr_seller_order_details');


/**
 * CRON FUNCTIONS STARTS============================================
 */

// add_action('wp_footer', 'wstr_pricing_cron');
function wstr_pricing_cron()
{

    $args = [
        'post_type' => 'domain',
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sale_price_dates_to',
                'compare' => 'EXISTS',
            ],
            [
                'key' => '_sale_price_dates_to',
                'value' => '',
                'compare' => '!=',
            ],
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!=',
            ],
        ],
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $domain_id) {

            $sale_end_date = get_post_meta($domain_id, '_sale_price_dates_to', true);
            $current_date = date('Y-m-d');

            if ($sale_end_date && $current_date > $sale_end_date) {
                delete_post_meta($domain_id, '_sale_price');
                delete_post_meta($domain_id, '_sale_price_dates_from');
                delete_post_meta($domain_id, '_sale_price_dates_to');
            }
        }
    }
}

// add_action('wp_footer', 'wstr_declined_offer_expired');
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

/**
 * CRON FUNCTIONS ENDS============================================
 */


// add_action('wp_footer', 'commissions');
function commissions()
{

    $current_user = get_current_user_id();


    $currency_value =  get_option('wstr_currency_rates', []);

    global $wpdb;
    $table_name = $wpdb->prefix . 'wstr_payouts';
    $results = $wpdb->get_results("SELECT amount, currency FROM $table_name WHERE seller_id = $current_user and type='commission'");
    $payout_completeds = $wpdb->get_results("SELECT amount FROM $table_name WHERE seller_id = $current_user and type='payout' and status='paid'");
    $payout_pendings = $wpdb->get_results("SELECT amount FROM $table_name WHERE seller_id = $current_user and type='payout' and status='pending' OR status='in-progress'");
    $total_commission = 0;
    // for dispalying total commission
    $completed_amount = 0;
    $paid_count = count($payout_completeds) ?: 0;
    if ($payout_completeds) {
        foreach ($payout_completeds as $completed) {
            $completed_amount += floatval($completed->amount);
        }
    }

    $pending_amount = 0;
    if ($payout_pendings) {
        foreach ($payout_pendings as $pending) {
            $pending_amount += floatval($pending->amount);
        }
    }
    if ($results) {
        foreach ($results as $result) {
            $amount = floatval($result->amount);

            $currency = $result->currency;
            // Convert to USD if not already in USD
            if ($currency !== 'USD' && isset($currency_value[$currency])) {

                $conversion_rate = floatval($currency_value[$currency]);
                $amount_in_usd = $amount / $conversion_rate; // Convert to USD
            } else {
                $amount_in_usd = $amount;
            }

            $total_commission += $amount_in_usd;
        }

        $total_pending  = $total_commission - $completed_amount;
        $withdrawable_amount = $total_pending - $pending_amount;

        $pending_percentage = ($total_commission > 0) ? ($total_pending / $total_commission) * 100 : 0;
        var_dump($pending_percentage);
        $paid_percentage = ($total_commission > 0) ? ($completed_amount / $total_commission) * 100 : 0;

        $data = [
            $total_commission => round($total_commission),
            'pending_amount' => round($total_pending),
            'pending_percentage' => round($pending_percentage),
            'paid_percentage' => round($paid_percentage),
            'paid_amount' => round($completed_amount),
            // 'paid_count' => $paid_count,
        ];
        echo '<pre>';
        var_dump($data);
    }
}


function textToSpeech($text)
{
    $men_voice = 'pNInz6obpgDQGcFmaJgB';
    $lady_voice = '21m00Tcm4TlvDq8ikWAM';
    $lady_voice = 'EXAVITQu4vr4xnSDxMaL';
    $apiKey = "sk_2e46d158130a6d2aeb20e265006a80c653e008839f070710"; // Replace with your actual API key

    $voices = [
        'lady' => $lady_voice,
        'men' => $men_voice
    ];

    $attachment_urls = [];

    foreach ($voices as $voice_name => $voice_id) {
        $api_url = "https://api.elevenlabs.io/v1/text-to-speech/{$voice_id}"; // Adjust the output format as needed
        $request_payload = [
            "text" => $text,
            "voice_settings" => [
                "similarity_boost" => 0.5,
                "stability" => 0.5,
                "style" => 0.5,
                "use_speaker_boost" => true
            ]
        ];

        // Increase the sound for lady voice
        if ($voice_name == 'lady') {
            $request_payload["voice_settings"]["similarity_boost"] = 0.5;
            $request_payload["voice_settings"]["stability"] = 0.5;
            $request_payload["voice_settings"]["style"] = 0.5;
            $request_payload["voice_settings"]["use_speaker_boost"] = true;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($request_payload),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "xi-api-key: " . $apiKey,
            ],
        ]);

        $response = curl_exec($curl);
        return $response;
        $err = curl_error($curl);

        curl_close($curl);

        // if ($err) {
        //     return "Error #:" . $err;
        // }

        // Define file name and path
        $upload_dir = wp_upload_dir();
        $file_name = $text . '-' . $voice_name . '.wav';
        $file_path = $upload_dir['path'] . '/' . $file_name;
        $file_url = $upload_dir['url'] . '/' . $file_name;

        // Save audio data to the file
        file_put_contents($file_path, $response);

        // Insert the file into the WordPress media library
        $attachment = array(
            'guid' => $file_url,
            'post_mime_type' => 'audio/wav',
            'post_title' => sanitize_file_name($file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attachment_id = wp_insert_attachment($attachment, $file_path);
        if ($attachment_id) {
            // Generate attachment metadata and update the attachment
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
            $attachment_url = wp_get_attachment_url($attachment_id);
            // Display HTML audio player with file path
            $attachment_urls[] = [
                'id' => $attachment_id,
                'url' => $attachment_url
            ];
        }
    }

    return $attachment_urls;
}


// add_action('wp_footer', function () {
//     var_dump(textToSpeech("see.com"));
// });
