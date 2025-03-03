<?php
class wstr_shortcodes
{
    private $stripe;

    public function __construct()
    {
        add_shortcode('wstr_banner_reviews', array($this, 'wstr_banner_reviews_function'));
        add_shortcode('wstr_google_reviews', array($this, 'wstr_google_reviews_function'));
        add_shortcode('wstr-multicurrency', array($this, 'wstr_multicurrency'));
        add_shortcode('wstr-browse-industry', array($this, 'wstr_browse_industry'));
        add_shortcode('wstr-popular-category', array($this, 'wstr_popular_category'));
        add_shortcode('wstr-domain-count', array($this, 'wstr_domain_count'));
        add_shortcode('wstr-footer-average-cost', array($this, 'wstr_footer_average_cost_calculation'));
        add_shortcode('wstr-you-may-like', array($this, 'wstr_you_may_like'));
        add_shortcode('wstr_estimation', array($this, 'wstr_estimation'));
        add_shortcode('wstr-single-domain', array($this, 'wstr_single_domain_page'));
        add_shortcode('wstr-similar-industry-name', [$this, 'wstr_similar_industry_name']);
        add_shortcode('wstr-buy-domain', [$this, 'wstr_buy_domain']);
        add_shortcode('wstr-login', [$this, 'wstr_login']);
        add_shortcode('wstr_register', [$this, 'wstr_register']);
        add_shortcode('wstr-faq', [$this, 'wstr_faq']);
        // add_shortcode('contact_form', [$this, 'wstr_contact_form']);
        // add_shortcode('contact_forms', [$this, 'wstr_contact_form']);
    }

    public function wstr_banner_reviews_function()
    {
        ob_start();
?>
        <!-- reviews banner -->
        <div class="banner-reviews ws_min_container ws_flex gap_20 jc_center margin_v_30 fd_mob_col">
            <div class=" reviews_images_lists ws_flex jc_center ai_center">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-1.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-2.jpg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-3.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-4.jpeg" alt="Client Image" />

                <i class="fa-solid fa-circle-plus"></i>
            </div>
            <div class="reviews-contents ws_text_center ">
                <div class="reviews-total ws_flex">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <p>4.9 Excellent</p>
                </div>
                <div>
                    <p>
                        <span>1,500+ </span>clients trust WebStarter. <a href="/my-account/?register=true"
                            class="hide_logged_in">Join them today!</a>
                    </p>
                </div>
            </div>
        </div>
    <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }


    // shortcode for about us Google review section
    public function wstr_google_reviews_function()
    {
        ob_start();
    ?>
        <!-- reviews banner -->
        <div class="banner-reviews ws_flex gap_20 jc_center margin_v_30 fd_mob_col wstr_google_review_sc">
            <div class=" reviews_images_lists ws_flex jc_center ai_center">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-1.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-2.jpg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-3.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-4.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/google.svg" alt="Client Image" />

            </div>
            <div class="reviews-contents ">
                <div class="ws_flex">
                    <p>4.9</p>
                    <div class="reviews-total ws_flex">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <span>(314)</span>
                </div>
                <div>
                    <p>
                        <span>1,500+ </span>clients trust WebStarter. <a href="javascript:void(0)">Review us on
                            Google</a>
                    </p>
                </div>
            </div>
        </div>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    /**
     * Shortcode for getting currecy symbol for frontend
     * @return mixed
     */
    function wstr_multicurrency()
    {
        ob_start();
        // Start the session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Get the selected currency from session, default to 'USD' if not set
        $selected_currency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'USD';

        // Get the list of currency codes from the options table
        $currency_codes = get_option('wstr_currency_codes');
        if ($currency_codes) {
            // Remove 'USD' from the list if it exists
            if (($key = array_search('USD', $currency_codes)) !== false) {
                unset($currency_codes[$key]);
            }

            // Output the select box
        ?>
            <select id="wstr-mulitcurrency">
                <!-- USD option -->
                <option value="USD" <?php selected($selected_currency, 'USD'); ?>>$</option>
                <?php
                // Loop through the remaining currency codes and add them as options
                ?>
                <?php
                foreach ($currency_codes as $currency_code) {
                    // Assuming get_wstr_currency_symbol() fetches the appropriate symbol for each currency code
                    $currency_symbol = get_wstr_currency_symbol($currency_code);
                ?>
                    <option value="<?php echo esc_attr($currency_code); ?>" <?php selected($selected_currency, $currency_code); ?>>
                        <?php echo esc_html($currency_symbol); ?>
                    </option>
                <?php
                }
                ?>
            </select>
        <?php
        }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * function for home page browse industry
     */
    public function wstr_browse_industry()
    {

        ob_start();

        $args = array(
            'hide_empty' => false,
            'number' => 17,
            'taxonomy' => 'domain_industry',
        );

        $industries = get_terms($args);
        // $domains_list_page = get_page_link(get_option('ws_domain_list_page')); // getting product page link

        $domains_list_page = get_home_url() . '/buy/';

        ?>
        <div class="ws-industry-wrapper">
            <?php
            if ($industries) {

                foreach ($industries as $industry) {

            ?>
                    <div class="ws-industry_details">
                        <?php
                        // Query domains for each industry (term)
                        $args_domains = array(
                            'post_type' => 'domain', // Assuming 'domain' is your custom post type
                            'posts_per_page' => -1, // Fetch all domains for this industry
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'domain_industry',
                                    'field' => 'slug',
                                    'terms' => $industry->slug, // Get domains for the current term (industry)
                                ),
                            ),
                            'date_query' => array(
                                'after' => array(
                                    'year' => date("Y"),
                                    'month' => date("m"),
                                    'day' => date("d") - 17,
                                ),
                            ),
                        );

                        $domains_query = new WP_Query($args_domains);

                        if ($domains_query->have_posts()) {

                        ?>
                            <span>New</span>
                        <?php
                        }

                        $term_image_id = get_term_meta($industry->term_id, 'taxonomy_image_id', true);

                        if ($term_image_id) {
                            $term_image_url = wp_get_attachment_url($term_image_id);
                        ?>
                            <img src="<?php echo $term_image_url ? $term_image_url : '' ?>">
                        <?php
                        }
                        ?>

                        <a
                            href="<?php echo $domains_list_page . '?industry=' . $industry->term_id ?>"><?php echo $industry->name; ?></a>
                    </div>
                <?php
                }
                ?>
                <div class="ws-industry_details">

                    <a href="<?php echo $domains_list_page; ?>">Browse All</a>
                </div>
            <?php

            }
            ?>

        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Shortcode for getting no of domain of each popular category
     * @param mixed $args
     * @return bool|string
     */
    public function wstr_popular_category($args)
    {
        ob_start();
        $term_id = $args['term_id'];
        $term_details = get_term($term_id);
        echo $term_details->count;
        return ob_get_clean();
    }

    /**
     * Shortcode for getting total of domains
     */
    public function wstr_domain_count()
    {
        ob_start();
        $query_args = array(
            'posts_per_page' => -1,
            'post_type' => 'domain',
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            )
        );
        $domain_products = get_posts($query_args);
        $count = count($domain_products);

        // if ($count > 1200) {
        //     echo '<p class="get_average_price">120+ </p>';
        // } else {
        echo '<p class="get_average_price">' . $count . '</p>';
        // }

        return ob_get_clean();
    }

    public function wstr_footer_average_cost_calculation()
    {
        ob_start();
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'domain',
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            )
        );
        $domains = get_posts($args);
        if ($domains) {
            $price = 0;
            foreach ($domains as $domain) {
                $price += get_wstr_price_value($domain->ID);
            }
        }
        $average_price = (float) $price / count($domains);

        $output = '<p class="get_average_price">' . get_wstr_currency() . '' . round($average_price) . ' </p>';

        echo $output;
        return ob_get_clean();
    }

    public function wstr_estimation()
    {
    ?>
        <div class="wstr_estimate_domain_wrapper ws_home_banner" id="wstr_domain_estimate">
            <div class="reviews_images_lists ws_flex jc_center ai_center">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-1.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-2.jpg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-3.jpeg" alt="Client Image" />
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-4.jpeg" alt="Client Image" />
            </div>
            <h4 class="ws_text_center">Become a seller</h4>
            <p class="ws_text_center">Verify your Domain Value Estimation!</p>
            <div class="wp-block-search p_relative">
                <form method='post' action="<?php echo get_home_url() . '/sell' ?>">
                    <input type="text" name="domain" id="domain" placeholder="Enter your domain" class="w_100" />
                    <button type="submit" value="Estimate">Estmate </button>

                </form>
            </div>
        </div>
    <?php
    }

    /**
     * shortcode for you may like section in single page
     * @param mixed $atts
     * @return mixed
     */
    public function wstr_you_may_like($atts)
    {
        global $post;
        ob_start();
        $args = shortcode_atts(array(
            'count' => 12,
        ), $atts);

        $query_args = array(
            'posts_per_page' => $args['count'],
            'post_type' => 'domain',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'meta_key' => 'ws_product_view_count',
            'post__not_in' => array($post->ID),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            )
        );

    ?>
        <div class="you-may-like-main">
            <div class="you_may_like_heading_wrapper ws_flex">
                <h4 class="ws_flex ai_center gap_5">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/youmaylike.svg" alt="You May Like">
                    You May Like
                </h4>
                <a href="/buy" class="button_hover_light">All Domains</a>
            </div>
            <div class="ws-cards-container-wrapper ws_cards_xl you_may_like_card_wrapper margin_v_35">
                <?php
                $domains = get_posts($query_args);
                foreach ($domains as $domain) {
                    $domain_id = $domain->ID;
                    $featured_image = get_the_post_thumbnail_url($domain->ID);
                    $logo_image_id = get_post_meta($domain->ID, '_logo_image', true);
                    $logo_image = wp_get_attachment_url($logo_image_id);

                    $da_pa = get_post_meta($domain->ID, '_da_pa', true);
                    $da = $pa = '';
                    if ($da_pa) {
                        $da_pa_split = explode('/', $da_pa);
                        $da = $da_pa_split[0];
                        $pa = $da_pa_split[1];
                    }

                ?>


                    <div class="ws-cards-container">
                        <div class="ws_card_hover_charts ws_flex">
                            <div class="circular-progress page-trust">
                                <div class="progress-text">
                                    <div role="progressbar" aria-valuenow="<?php echo (int) $pa; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="--value: <?php echo (int) $pa; ?>"></div>
                                </div>
                                <div class="progress-title">
                                    <h6>Page Trust</h6>
                                </div>
                            </div>
                            <div class="circular-progress domain-trust">
                                <div class="progress-text">
                                    <div role="progressbar" aria-valuenow="<?php echo (int) $da; ?> " aria-valuemin="0"
                                        aria-valuemax="100" style="--value:<?php echo (int) $da; ?> "></div>
                                </div>
                                <div class="progress-title">
                                    <h6>Domain Trust</h6>
                                </div>
                            </div>
                        </div>
                        <div class="ws-card-img">
                            <img decoding="async"
                                src="<?php echo $featured_image ? $featured_image : get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png' ?>"
                                alt="<?php echo get_the_title($domain->ID); ?>">
                        </div>
                        <div class="ws-card-contents ws-flex">
                            <img decoding="async" src="<?php echo $logo_image ? $logo_image : $featured_image ?>" alt="zanabism.com"
                                title="<?php echo get_the_title($domain->ID); ?>" class="card_logo_img">
                            <span class="ws-card-inner-contents">
                                <h5>
                                    <a
                                        href="<?php echo get_permalink($domain->ID); ?>"><?php echo get_the_title($domain->ID); ?></a>
                                </h5>

                                <?php echo get_wstr_price($domain->ID); ?>
                            </span>
                            <?php
                            $favourite_disable = '';
                            if (!is_user_logged_in()) {
                                $favourite_disable = 'disable-favourite';
                            }
                            ?>
                            <div class="ws-card-likes <?php echo $favourite_disable ?> " id="<?php echo $domain->ID; ?>">
                                <h6><span><?php echo wstr_get_favourite_count($domain->ID); ?></span><i
                                        class="fa-solid fa-heart"></i></h6>
                            </div>
                        </div>
                    </div>
                <?php
                }

                // $domains_list_page = get_page_link(get_option('ws_domain_list_page'));
                ?>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Shortocode for single domain page
     * @return bool|string
     */
    public function wstr_single_domain_page()
    {
        ob_start();
    ?>
        <div class="single-container ws-container">
            <?php
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
                $category = get_the_terms(get_the_ID(), 'domain_cat');
                if ($category) {
                    shuffle($category);
                }
                $category_name = $category[0]->name;
                $category_id = $category[0]->term_id;
                $cat_image_id = get_term_meta($category_id, 'taxonomy_image_id', true);
                $cat_image_url = wp_get_attachment_url($cat_image_id);
                $tags = get_the_terms(get_the_ID(), 'domain_tag');
                $term_exist = wstr_check_existing_term(get_the_ID(), 'domain_cat', 'premium-names');

                $da = $pa = '';
                if ($da_pa) {
                    $da_pa_split = explode('/', $da_pa);
                    $da = $da_pa_split[0];
                    $pa = $da_pa_split[1];
                }
                $post_count = (int) get_post_meta(get_the_ID(), 'ws_product_view_count', true);
                $new_post_count = $post_count + 1;

                update_post_meta(get_the_ID(), 'ws_product_view_count', $new_post_count);

                $offer = get_post_meta(get_the_ID(), '_enable_offers', true);
                $lease_to_own = get_post_meta(get_the_ID(), '_lease_to_own', true);

            ?>
                <div class="single_domain_details ws_flex fd_mob_col fd_tab_col ">
                    <!-- Featured Image -->
                    <div class="featured-image p_relative img_producto_container" data-scale="1.6">
                        <div class="p_relative">
                            <img src="<?php echo $featured_image ? esc_url($featured_image) : get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png' ?>"
                                alt="<?php echo esc_attr($title); ?>" class="img_producto">
                        </div>
                        <div class="single_featured_image_footer ws_flex">
                            <span class="domain_online ws_flex gap_10 ai_center online">
                                <i class="fa-solid fa-comments"></i>
                                <p>Online
                                    <i class="fa-solid fa-circle"></i>
                                </p>
                            </span>
                            <a href="#">
                                <p>Message</p>
                            </a>
                        </div><?php
                                if ($term_exist) {
                                ?>
                            <div class="premium_icon">
                                <img src="/wp-content/plugins/cpm-card-block/images/premium.svg" alt="Diamond Icon" />
                            </div> <?php
                                }
                                    ?>
                        <div class="ws_flex ai_center single_domain_meta_search">
                            <div class="single_domain_search">

                                <i class="fa-solid fa-magnifying-glass" id="enlarge-icon"></i>

                            </div>
                            <?php
                            $favourite_disable = '';
                            if (!is_user_logged_in()) {
                                $favourite_disable = 'disable-favourite';
                            }
                            ?>
                            <div class="ws-card-likes <?php echo $favourite_disable ?> " id="<?php echo get_the_ID(); ?>">
                                <h6><span><?php echo wstr_get_favourite_count(get_the_ID()); ?></span><i
                                        class="fa-solid fa-heart"></i></h6>
                            </div>
                        </div>
                    </div>
                    <div id="imageModal" class="modal">
                        <!-- Modal content -->
                        <span class="close">&times;</span>
                        <img id="modalImage" src="" alt="Full-Width Image" />
                    </div>

                    <!-- Details Section -->
                    <div class="domain-details">
                        <div class="ws_flex gap_20 ai_center p_relative">
                            <div>
                                <?php if (!$logo && !$featured_image) {
                                ?>
                                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png' ?>"
                                        alt="<?php echo $title ?>">
                                <?php
                                } else {

                                ?>
                                    <img src="<?php echo $logo ? $logo : $featured_image ?>" alt="<?php echo $title; ?> ">
                                <?php }
                                ?>

                            </div>

                            <?php
                            $discount_percent = get_wstr_price_percentage(get_the_ID());
                            echo $discount_percent;
                            ?>
                            <div>
                                <h2 class="fw-600"><?php echo esc_html($title); ?></h2>
                                <div class="single_domain_price ws_flex gap_10 ai_center">
                                    <?php
                                    echo get_wstr_price(get_the_ID());
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="single_domain_short_desc">
                            <?php
                            the_excerpt();
                            ?>
                        </div>
                        <?php
                        $stock_status = get_post_meta(get_the_ID(), '_stock_status', true);
                        $product_is_in_stock = $stock_status == 'instock' ? true : false;

                        if ($product_is_in_stock) { ?>
                            <div class="wstr_payments">
                                <h6>PAYMENT OPTIONS</h6>

                                <!-- Payment options -->
                                <div class="payments_options_option">
                                    <div class="payment_form_group active">
                                        <input type="radio" name="payment_option" class="payment-option" value="full" id="full_payment"
                                            checked>
                                        <label for="full_payment">Pay Now</label>
                                    </div>

                                    <?php if ($lease_to_own) {
                                    ?>

                                        <div class="payment_form_group installlmentPayment">
                                            <div>
                                                <input type="radio" name="payment_option" class="payment-option" value="installment"
                                                    id="installment_payment">
                                                <label for="installment_payment">Lease-to-Own</label>
                                            </div>

                                        </div>
                                        <div id="installment_duration_options" style="display: none;">
                                            <div class="form-group">
                                                <div>
                                                    <input type="radio" name="installment_duration" class="installment_duration" value="3"
                                                        id="three_months" checked>
                                                    <label for="three_months">3 months</label>
                                                </div>
                                                <div class="wstr_payments_lease_value">
                                                    <?php
                                                    echo
                                                    get_wstr_currency() . number_format((get_wstr_regular_price(get_the_ID()) / 3), 2, '.', ',');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <input type="radio" name="installment_duration" class="installment_duration" value="6"
                                                        id="six_months">
                                                    <label for="six_months">6 months</label>
                                                </div>
                                                <div class="wstr_payments_lease_value">
                                                    <?php

                                                    echo
                                                    get_wstr_currency() . number_format((get_wstr_regular_price(get_the_ID()) / 6), 2, '.', ',');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <input type="radio" name="installment_duration" class="installment_duration" value="12"
                                                        id="twelve_months">
                                                    <label for="twelve_months">12 months</label>
                                                </div>
                                                <div class="wstr_payments_lease_value">
                                                    <?php

                                                    echo
                                                    get_wstr_currency() . number_format((get_wstr_regular_price(get_the_ID()) / 12), 2, '.', ',');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <input type="radio" name="installment_duration" class="installment_duration" value="24"
                                                        id="twenty_four_months">
                                                    <label for="twenty_four_months">24 months</label>
                                                </div>
                                                <div class="wstr_payments_lease_value">
                                                    <?php
                                                    // Calculate the price divided by 24 months
                                                    echo get_wstr_currency() . number_format((get_wstr_regular_price(get_the_ID()) / 24), 2, '.', ',');
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    if ($offer == 'yes') {
                                    ?>

                                        <div class="payment_form_group make_offer">
                                            <div>
                                                <input type="radio" name="payment_option" class="payment-option" value="full"
                                                    id="make_offer">
                                                <label for="make_offer">Make an Offer</label>
                                            </div>
                                        </div>
                                        <div class="make_offer_textbox">

                                            <form class="make_offer_form" action="#" method="POST">

                                                <div class="make_offer_loading"></div>
                                                <div class=" make_offer_error error_msg">
                                                    <?php
                                                    $not_logged_in = false;
                                                    if (!is_user_logged_in()) {
                                                        echo 'Please login to make an offer.';
                                                        $not_logged_in = true;
                                                    }
                                                    ?>
                                                </div>
                                                <div class="make_offer_success success_msg"></div>
                                                <input type="number" id="offer_amount" class="make_offer_amount"
                                                    placeholder="Enter your offer" required>
                                                <!-- <textarea name="offer_amount" id="offer_amount" class="make_offer_amount"
                                                placeholder="Enter your offer"></textarea> -->
                                                <input type="hidden" class="make_offer_domain_id" value="<?php echo get_the_ID(); ?>" />
                                                <input type="hidden" class="offer_user_id" value="<?php echo get_current_user_id(); ?>" />
                                                <input type="submit" value="Make Offer" />
                                            </form>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                </div>
                                <?php

                                $is_curr_product_in_cart = false;
                                if (isset($_SESSION['cart'])) {
                                    $all_product_ids = array_keys($_SESSION['cart']);
                                    $is_curr_product_in_cart = in_array(get_the_ID(), $all_product_ids);
                                }

                                echo '<button class="add-to-cart-btn" data-product-id="' . get_the_ID() . '" 
                                style="display: ' . ($is_curr_product_in_cart ? 'none' : 'block') . '">ADD TO CART  <i class="fa-solid fa-arrow-right-long"></i></button>';
                                echo '<button class="remove-from-cart-btn" data-product-id="' . get_the_ID() . '" 
                                style="display: ' . ($is_curr_product_in_cart ? 'block' : 'none') . '">REMOVE FROM CART</button>';
                                ?>
                            </div>
                            <div class="cart_payment_secured">
                                <span>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ssl.png" alt="SSL Image">
                                    <h6>SSL Secure payment</h6>
                                </span>
                                <span>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/guarantee.png"
                                        alt="Guarantee Image">
                                    <h6>Buyer guarantee</h6>
                                </span>
                            </div>
                            <div class="cart_order_payment_methods">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/visa.png" alt="VISA">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/MASTERCARD.png" alt="MASTERCARD">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/americanexpress.png"
                                    alt="American Express">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/DISCOVER.png" alt="DISCOVER">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/WIRE.png" alt="WIRE">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ESCROW.png" alt="ESCROW">
                            </div>
                        <?php
                        } else {
                            echo "<h5 class='mt_10'>Domain has been sold and is no longer available</h5>";
                        }
                        ?>
                    </div>
                </div>
                <div class="single_domain_reviews_information ws_flex fd_mob_col fd_tab_col">
                    <div class="single_domain_tabs_container tab_w_100">
                        <!-- <ul class="tabs">
                            <li class="tab current" data-tab="tab-1">Domain</li>
                            <li class="tab" data-tab="tab-2">Seller Reviews</li>
                        </ul> -->

                        <div id="tab-1" class="tab-content current">
                            <h2 class="fw-600 margin_v_35 mt_center">Domain Information</h2>
                            <div class="single_domain_progress_wrapper br_15">
                                <h4 class="fw-600"><?php echo esc_html($title); ?></h4>
                                <div class="ws_flex gap_20">
                                    <div class="circular-progress page-trust">
                                        <div class="progress-text">
                                            <div role="progressbar" aria-valuenow="<?php echo (int) esc_html($pa); ?>"
                                                aria-valuemin="0" aria-valuemax="100"
                                                style="--value:<?php echo (int) esc_html($pa); ?>">
                                                <p>of 100</p>
                                            </div>
                                        </div>
                                        <div class="progress-title">
                                            <h6>PAGE TRUST</h6>
                                        </div>
                                    </div>

                                    <div class="circular-progress domain-trust">
                                        <div class="progress-text">
                                            <div role="progressbar" aria-valuenow="<?php echo (int) esc_html($da); ?>"
                                                aria-valuemin="0" aria-valuemax="100"
                                                style="--value:<?php echo (int) esc_html($da); ?>">
                                                <p>of 100</p>
                                            </div>
                                        </div>
                                        <div class="progress-title">
                                            <h6>DOMAIN TRUST</h6>

                                        </div>
                                    </div>
                                    <div class="circular-progress domain-trust domain-length">
                                        <div class="progress-text">
                                            <div role="progressbar" aria-valuenow="<?php echo (int) esc_html($domain_length); ?>"
                                                aria-valuemin="0" aria-valuemax="100"
                                                style="--value:<?php echo (int) esc_html($domain_length); ?>">
                                                <p>Letters</p>
                                            </div>
                                        </div>
                                        <div class="progress-title">
                                            <h6>DOMAIN LENGTH</h6>

                                        </div>
                                    </div>
                                    <div class="circular-progress domain-trust domain-age">
                                        <div class="progress-text">
                                            <div role="progressbar" aria-valuenow="<?php echo (int) esc_html($domain_age); ?>"
                                                aria-valuemin="0" aria-valuemax="100"
                                                style="--value:<?php echo (int) esc_html($domain_age); ?>">
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
                            <div class="single_domain_features ws_trending_cards section_gap_mobile">
                                <h2>What You Get</h2>
                                <div class="ws_flex gap_10 fd_mob_col">
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <!-- <img src="<?php // echo get_stylesheet_directory_uri(); 
                                                        ?>/assets/images/domain-name.png" -->
                                        <div class="single_domain_svg_wrapper">
                                            <svg xmlns="http://www.w3.org/2000/svg" id="solid" viewBox="0 0 32 31.95">
                                                <path
                                                    d="M6,21.01H.82c-.53-1.59-.82-3.27-.82-5.04,0-2.2.45-4.3,1.26-6.22h4.94c-.37,1.93-.57,4.03-.57,6.22,0,1.75.13,3.45.37,5.04Z" />
                                                <path
                                                    d="M6.32,22.79c.65,3.09,1.75,5.77,3.15,7.8-3.5-1.56-6.32-4.34-7.95-7.8h4.8,0Z" />
                                                <path
                                                    d="M15.11,22.79v9.16c-.75-.04-1.49-.13-2.22-.27-2.09-1.65-3.79-4.88-4.73-8.89h6.95Z" />
                                                <path
                                                    d="M15.11,9.75v11.26h-7.3c-.26-1.6-.4-3.29-.4-5.04,0-2.18.23-4.29.63-6.22h7.08Z" />
                                                <path
                                                    d="M9.47,1.36c-1.22,1.77-2.2,4.02-2.87,6.61H2.15c1.67-2.9,4.24-5.23,7.32-6.61Z" />
                                                <path
                                                    d="M15.11,0v7.98h-6.64c.97-3.46,2.55-6.22,4.42-7.7.72-.14,1.47-.24,2.22-.27Z" />
                                                <path
                                                    d="M23.53,7.98h-6.64V0c.75.04,1.49.13,2.22.27,1.87,1.48,3.45,4.24,4.42,7.7Z" />
                                                <path
                                                    d="M16.89,22.79h6.95c-.94,4.01-2.64,7.24-4.73,8.89-.72.14-1.47.24-2.22.27v-9.16Z" />
                                                <path
                                                    d="M24.59,15.98c0,1.74-.14,3.44-.4,5.04h-7.3v-11.26h7.08c.4,1.93.63,4.04.63,6.22h0Z" />
                                                <path
                                                    d="M25.4,7.98c-.66-2.6-1.65-4.85-2.87-6.61,3.08,1.39,5.65,3.71,7.32,6.61h-4.46Z" />
                                                <path
                                                    d="M25.68,22.79h4.8c-1.64,3.46-4.46,6.23-7.95,7.8,1.4-2.03,2.5-4.71,3.15-7.8h0Z" />
                                                <path
                                                    d="M32,15.98c0,1.77-.28,3.45-.82,5.04h-5.18c.24-1.59.37-3.28.37-5.04,0-2.19-.2-4.29-.57-6.22h4.94c.81,1.92,1.26,4.02,1.26,6.22h0Z" />
                                            </svg>
                                        </div>
                                        <div class="ws-card-inner-contents">
                                            <h5 class="fw-600"><?php echo $title ?></h5>
                                            <div class="ws_card_price_wrapper ws_flex gap_10">
                                                <p>Domain Name</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <!-- <img src="<?php // echo get_stylesheet_directory_uri(); 
                                                        ?>/assets/images/svg-file-icon.png"
                                            alt="SVG Icon"> -->
                                        <div class="single_domain_svg_wrapper">
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 506 512">
                                                <path
                                                    d="M85,60c0-11.03,8.97-20,20-20h174.89v71.11c0,33.09,26.91,60,60,60h71v118.89h40v-147.14L308.76,0H105c-33.09,0-60,26.91-60,60v230h40V60ZM319.89,111.11v-43.21l62.89,63.21h-42.89c-11.03,0-20-8.97-20-20ZM506,401v20c0,50.05-40.71,90.8-90.82,91h-.37c-50.11-.2-90.82-40.95-90.82-91s40.8-91,90.95-91c12.87,0,25.32,2.64,37.01,7.86l-16.3,36.53c-6.53-2.91-13.5-4.39-20.71-4.39-28.09,0-50.95,22.88-50.95,51s22.86,50.9,51,51c21.02-.07,39.1-12.85,46.9-31h-42.9v-40h87ZM145,456.5c0,30.6-24.9,55.5-55.5,55.5H0v-40h89.5c8.55,0,15.5-6.95,15.5-15.5s-6.95-15.5-15.5-15.5h-34c-30.6,0-55.5-24.9-55.5-55.5s24.9-55.5,55.5-55.5h68.5v40H55.5c-8.55,0-15.5,6.95-15.5,15.5s6.95,15.5,15.5,15.5h34c30.6,0,55.5,24.9,55.5,55.5ZM270.62,330h42.32l-62.79,182h-30.45l-63.7-182h42.38l36.38,103.95,35.86-103.95Z" />
                                            </svg>
                                        </div>
                                        <div class="ws-card-inner-contents">
                                            <h5 class="fw-600">SVG File & Copyright</h5>
                                            <div class="ws_card_price_wrapper ws_flex gap_10">
                                                <p class="">Logo Design</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <!-- <img src="<?php // echo get_stylesheet_directory_uri(); 
                                                        ?>/assets/images/technical-support.png"
                                            alt="SVG Icon"> -->
                                        <div class="single_domain_svg_wrapper">
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode"
                                                viewBox="0 0 385.5 385.5">
                                                <path
                                                    d="M228.55,385.5h-71.6c-6.64,0-12.22-5.01-12.93-11.61l-4.55-42.4c-2.41-.93-4.8-1.92-7.15-2.96l-33.2,26.77c-5.17,4.17-12.66,3.77-17.35-.93l-50.63-50.63c-4.7-4.7-5.1-12.18-.93-17.35l26.77-33.2c-1.05-2.36-2.04-4.74-2.96-7.15l-42.4-4.55c-6.61-.71-11.61-6.28-11.61-12.93v-71.6c0-6.64,5.01-12.22,11.61-12.93l42.4-4.55c.93-2.41,1.91-4.8,2.96-7.15l-26.77-33.2c-4.17-5.17-3.77-12.66.93-17.35l50.63-50.63c4.7-4.7,12.18-5.1,17.35-.93l33.2,26.77c2.36-1.05,4.74-2.04,7.15-2.96l4.55-42.4c.71-6.61,6.28-11.61,12.93-11.61h71.6c6.64,0,12.22,5.01,12.93,11.61l4.55,42.4c2.41.93,4.8,1.92,7.15,2.96l33.2-26.77c5.17-4.17,12.65-3.77,17.35.93l50.63,50.63c4.7,4.7,5.1,12.18.93,17.35l-26.77,33.2c1.05,2.36,2.04,4.74,2.96,7.15l42.4,4.55c6.61.71,11.61,6.28,11.61,12.93v71.6c0,6.64-5.01,12.22-11.61,12.93l-42.4,4.55c-.93,2.41-1.92,4.8-2.96,7.15l26.77,33.2c4.17,5.17,3.77,12.66-.93,17.35l-50.63,50.63c-4.7,4.7-12.18,5.1-17.35.93l-33.2-26.77c-2.36,1.05-4.74,2.04-7.15,2.96l-4.55,42.4c-.71,6.61-6.28,11.61-12.93,11.61ZM168.63,359.5h48.24l4.18-38.94c.55-5.12,4.07-9.44,8.98-11,6.51-2.08,12.89-4.72,18.95-7.86,4.58-2.37,10.12-1.81,14.13,1.43l30.5,24.59,34.11-34.11-24.59-30.5c-3.23-4.01-3.79-9.56-1.43-14.13,3.14-6.06,5.78-12.44,7.86-18.95,1.56-4.91,5.88-8.43,11-8.98l38.94-4.18v-48.24l-38.94-4.18c-5.12-.55-9.44-4.07-11-8.98-2.08-6.51-4.72-12.89-7.86-18.95-2.37-4.58-1.81-10.12,1.43-14.13l24.59-30.49-34.11-34.11-30.5,24.59c-4.01,3.23-9.56,3.79-14.13,1.43-6.06-3.14-12.44-5.78-18.95-7.86-4.91-1.56-8.43-5.88-8.98-11l-4.18-38.94h-48.24l-4.18,38.94c-.55,5.12-4.07,9.44-8.98,11-6.51,2.08-12.89,4.72-18.96,7.86-4.58,2.37-10.12,1.81-14.13-1.43l-30.49-24.59-34.11,34.11,24.59,30.49c3.23,4.01,3.79,9.56,1.43,14.13-3.14,6.06-5.78,12.44-7.86,18.96-1.56,4.91-5.88,8.43-11,8.98l-38.94,4.18v48.24l38.94,4.18c5.12.55,9.44,4.07,11,8.98,2.08,6.51,4.72,12.89,7.86,18.95,2.37,4.58,1.81,10.12-1.43,14.13l-24.59,30.5,34.11,34.11,30.5-24.59c4.01-3.23,9.56-3.79,14.13-1.43,6.06,3.14,12.44,5.78,18.95,7.86,4.91,1.56,8.43,5.88,8.98,11l4.18,38.94ZM372.5,228.55h0,0Z" />
                                                <path
                                                    d="M192.75,266.73c-40.79,0-73.98-33.19-73.98-73.98s33.19-73.98,73.98-73.98,73.98,33.19,73.98,73.98-33.19,73.98-73.98,73.98ZM192.75,144.77c-26.46,0-47.98,21.52-47.98,47.98s21.52,47.98,47.98,47.98,47.98-21.52,47.98-47.98-21.52-47.98-47.98-47.98Z" />
                                            </svg>
                                        </div>
                                        <div class="ws-card-inner-contents">
                                            <h5 class="fw-600">Technical Support</h5>
                                            <div class="ws_card_price_wrapper ws_flex gap_10">
                                                <p>Free Technical Support</p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="domain-tag">
                                <h2>Related tags</h2>
                                <ul class="related_tag_list_wrapper ws_flex gap_10">
                                    <?php
                                    foreach ($tags as $tag) {
                                    ?>
                                        <li>
                                            <?php
                                            $tag_name = $tag->name;
                                            echo $tag_name;
                                            ?>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <!-- <div id="tab-2" class="tab-content">
                            <h2 class="margin_v_35">Seller Reviews Contents</h2>
                             content here
                        </div> -->
                    </div>

                    <div class="single_domain_highlights tab_w_100">
                        <h2 class="fw-600"><?php echo ($highlights_title) ?></h2>
                        <div class="single_domain_highlights_cards">
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/transfert.svg"
                                    alt="Feature Image">
                                <h5>Fast and Secure Transfer</h5>
                            </div>
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/support.svg"
                                    alt="Feature Image">
                                <h5>Free Technical Support</h5>
                            </div>
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/crafted.svg"
                                    alt="Feature Image">
                                <h5>Professionally Crafted Logo</h5>
                            </div>
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo $cat_image_url ? $cat_image_url : get_stylesheet_directory_uri() . '/assets/images/support.svg'; ?> "
                                    alt="<?php echo $category_name ?>">
                                <h5><?php echo $category_name ?></h5>
                            </div>
                        </div>
                        <?php
                        echo do_shortcode('[wstr_estimation]');
                        echo do_shortcode('[wstr-similar-industry-name category_id =' . $category_id . ']')
                        ?>
                    </div>
                </div>

                <div class="single_domain_you_may_like">
                    <?php
                    echo do_shortcode('[wstr-you-may-like]');
                    ?>
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

    /**
     * Function for getting similar category domain
     * @param mixed $atts
     * @return bool|string
     */
    public function wstr_similar_industry_name($atts)
    {
        ob_start();
        $category_id = $atts['category_id'];
        $similar_domain_args = array(
            'post_type' => 'domain',
            'fields' => 'ids',
            'posts_per_page' => 3,
            'orderby' => 'rand',
            'tax_query' => array(
                array(
                    'taxonomy' => 'domain_cat',
                    'field' => 'term_id',
                    'terms' => $category_id,
                    'operator' => 'IN',
                )
            )
        );
    ?>
        <div class="similar-industry-names-main ws_trending_cards margin_v_35">
            <h5>Similar Industry Names</h5>
            <?php
            $similar_domain_ids = get_posts($similar_domain_args);
            foreach ($similar_domain_ids as $similar_domain_id) {
                $similar_domain_title = get_the_title($similar_domain_id);
                $featured_image_id = get_post_thumbnail_id($similar_domain_id);
                $featured_image_url = wp_get_attachment_url($featured_image_id);
                $logo_image_id = get_post_meta($similar_domain_id, '_logo_image', true);
                $logo_image_url = wp_get_attachment_url($logo_image_id);
                $permalink = get_permalink($similar_domain_id);
            ?>
                <a href="<?php echo $permalink ?>">
                    <div class="similar-industry-names ws-card-contents ws_flex">

                        <?php if (!$logo_image_url && !$featured_image_url) {
                        ?>
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png' ?>"
                                alt="<?php echo $similar_domain_title ?>">
                        <?php
                        } else {

                        ?>
                            <img src="<?php echo $logo_image_url ? $logo_image_url : $featured_image_url ?>"
                                alt="<?php echo $similar_domain_title; ?> ">
                        <?php }
                        ?>
                        <div class="ws-card-inner-contents">
                            <h5><?php echo $similar_domain_title ?></h5>
                            <?php echo get_wstr_price($similar_domain_id); ?>
                        </div>
                    </div>
                </a>
            <?php
            }
            ?>
        </div>
    <?php
        return ob_get_clean();
    }

    public function wstr_buy_domain()
    {
        ob_start();
        $get_style = $_GET['style'];
        $get_industry = $_GET['industry'];
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
                    <?php
                    // Get top industry terms by _search_count
                    $top_industries = get_terms([
                        'taxonomy' => 'domain_industry',
                        'meta_key' => '_search_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'number' => 10, // Limit to top 5 terms
                        'hide_empty' => false,
                    ]);

                    // Get top style terms by _search_count
                    $top_styles = get_terms([
                        'taxonomy' => 'domain_cat',
                        'meta_key' => '_search_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                        'number' => 10, // Limit to top 5 terms
                        'hide_empty' => false,
                    ]);

                    // Merge industries and styles into a single array
                    $all_terms = array_merge($top_industries, $top_styles);

                    // Sort all terms by _search_count in descending order
                    usort($all_terms, function ($a, $b) {
                        $count_a = get_term_meta($a->term_id, '_search_count', true);
                        $count_b = get_term_meta($b->term_id, '_search_count', true);
                        return $count_b - $count_a; // Descending order
                    });

                    // Display the top 5 terms
                    foreach (array_slice($all_terms, 0, 10) as $term) {
                        $count = get_term_meta($term->term_id, '_search_count', true);
                        echo "<li class='popular-searched-item' id='$term->term_id' data-taxonomy='{$term->taxonomy}'>";
                        echo "{$term->name}";
                        echo "</li>";
                    }

                    ?>
                </ul>
            </section>

            <!-- Filters Section -->
            <section class="filters-section">
                <div class="filter-item">
                    <label for="sort-by">Sort By:</label>
                    <div class="filter-item-aligned filter_item_name">
                        <select id="sort-by" name="sort-by">
                            <option value="">Any</option>
                            <option value="a-z">A-Z</option>
                            <option value="z-a">Z-A</option>
                            <option value="high">Most Viewed</option>
                            <option value="new">Newest Added</option>
                            <option value="low-to-high">Price: Low to High</option>
                            <option value="high-to-low">Price: High to Low</option>
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

                <!-- Price (Low to High, High to Low) -->
                <!-- <div class="filter-item">
                    <label for="sort-by">Sort By Price:</label>
                    <select id="sort-by-price" name="sort-by-price">
                        <option value="">Any</option>
                        <option value="low-to-high">Low to High</option>
                        <option value="high-to-low">High to Low</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="sort-by">Popular</label>
                    <select id="sort-by-list" name="sort-by-list">
                        <option value="">Any</option>
                        <option value="high">Most Viewed</option> -->
                <!-- <option value="new">Newest Added</option> -->
                <!-- <option value="recommended">Recommended</option> -->
                <!-- </select>
                </div> -->

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
                            if ($get_industry) {
                                $selected = $get_industry == $industry->term_id ? 'selected = "selected"' : '';
                            }
                        ?>
                            <option value="<?php echo $industry->term_id; ?>" <?php echo $selected ?>>
                                <?php echo $industry->name; ?>
                            </option>
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
                            if ($get_style) {
                                $selected = $get_style == $style->term_id ? 'selected = "selected"' : '';
                            }
                        ?>
                            <option value="<?php echo $style->term_id; ?>" <?php echo $selected ?>><?php echo $style->name; ?>
                            </option>
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
                            <option value="10000">$10,000</option>
                            <option value="25000">$25,000</option>
                            <option value="50000">$50,000</option>
                            <option value="100000">$100,000</option>
                            <option value="250000">$250,000</option>
                            <option value="500000">$500,000</option>
                            <option value="750000">$750,000</option>
                            <option value="1000000">$1,000,000</option>
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
                    <label for="length-slider">Length <i class="fa-solid fa-chevron-left"></i><span id="length-output">
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
            <a href="<?php echo get_home_url() . '/buy'; ?>" id="reset-filters"><i
                    class="fa-solid fa-arrow-rotate-right"></i>Reset Filter</a>
            <!-- <button type="button" id="reset-filters" onclick="resetFilters()"><i
                    class="fa-solid fa-arrow-rotate-right"></i>Reset Filters</button> -->
            <?php
            echo do_shortcode('[wstr-domain-count]'); ?>
        </div>
        <!-- cards contaner -->
        <div class="ws-container buy_card_lists">
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

                if ($get_industry) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'domain_industry',
                            'field' => 'term_id',
                            'terms' => sanitize_text_field($get_industry),
                        ),
                    );
                }
                if ($get_style) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'domain_cat',
                            'field' => 'term_id',
                            'terms' => sanitize_text_field($get_style),
                        ),
                    );
                }
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
                    <img decoding="async" src="/wp-content/plugins/cpm-card-block/images/premium.svg" alt="Diamond Icon">
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
                                <img decoding="async" src="<?php echo $logo_url ?: $domain_image ?>" alt="<?php echo get_the_title() ?>"
                                    title="<?php echo get_the_title() ?>" class="card_logo_img">
                                <span class="ws-card-inner-contents">
                                    <h5><a href="<?php echo get_permalink(get_the_ID()) ?: ''; ?> "> <?php echo get_the_title() ?>
                                        </a></h5>
                                    <?php echo $price ?: ''; ?>

                                </span>
                                <?php
                                $favourite_disable = '';
                                if (!is_user_logged_in()) {
                                    $favourite_disable = 'disable-favourite';
                                }
                                ?>
                                <div class="ws-card-likes <?php echo $favourite_disable ?> " id="<?php echo get_the_ID(); ?>">
                                    <h6><span><?php echo wstr_get_favourite_count(get_the_ID()); ?></span><i
                                            class="fa-solid fa-heart"></i></h6>
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
                else:
                    echo 'No domain found.';
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
                font-weight: 500;
                background: #fff;
                padding: 10px 20px;
                border-radius: 50px;
                color: #001f3e;
                font-size: 12px;
            }

            .domain-filters-container .categories-list li:first-child p {
                background: #001f3e;
                padding: 10px 20px;
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
                gap: 15px 10px;
            }

            .domain-filters-container .categories-list li.active {
                border: 2px solid;
            }

            .category-section {
                margin: 1.5rem 0 2.5rem;
            }

            .categories-list {
                list-style-type: none;
                padding: 0;
                text-align: center;
            }

            .categories-list li {
                display: inline-block;
                font-weight: bold;
                cursor: pointer;
                margin-right: 0;
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
                border: 2px solid #fafafa77;
                padding: 20px;
                gap: 10px;
            }

            .filter-item label {
                margin-bottom: 5px;
                font-weight: 500;
                font-size: 1rem;
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
                border-radius: 50px;
                border: 1px solid #edf0f7;
                aspect-ratio: 1;
                align-content: center;
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
                    height: 1100px;
                }

                .domain-filters-container .categories-list {
                    justify-content: center;
                }

                .buy_card_lists .pagination {
                    margin-bottom: 20px;
                }

                .domain-filters-container .categories-list li:nth-child(n+7) {
                    display: none;
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

    public function wstr_login()
    {
        ob_start();
        if (is_user_logged_in()) {
            return;
        }
        if (isset($_POST['register_user'])) {
            // Process the registration
            $username = sanitize_user($_POST['username']);
            $email = sanitize_email($_POST['email']);
            $password = sanitize_text_field($_POST['password']);
            $confirm_password = sanitize_text_field($_POST['confirm_password']);
            // $full_name = sanitize_text_field($_POST['full_name']);
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $become_seller = isset($_POST['become_seller']) ? true : false;

            $errors = [];


            // Validate required fields
            if (empty($username)) {
                $errors[] = 'Username is required.';
            }

            if (empty($email)) {
                $errors[] = 'Email is required.';
            } elseif (!is_email($email)) {
                $errors[] = 'Invalid email address.';
            }

            if (empty($password) || empty($confirm_password)) {
                $errors[] = 'Password and confirm password are required.';
            } elseif ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            }

            // Check if username or email already exists
            if (username_exists($username)) {
                $errors[] = 'Username already exists.';
            }

            if (email_exists($email)) {
                $errors[] = 'Email already exists.';
            }

            // If no errors, proceed with user registration
            if (empty($errors)) {
                $user_data = [
                    'user_login' => $username,
                    'user_email' => $email,
                    'user_pass' => $password,
                    'first_name' => $first_name, // First name
                    'last_name' => $last_name, // Last name if available
                ];

                $user_id = wp_insert_user($user_data);

                if (!is_wp_error($user_id)) {
                    // Optionally, assign the user role as seller if checkbox is checked
                    if ($become_seller) {
                        $user = new WP_User($user_id);
                        $user->set_role('seller');
                    } else {
                        $user = new WP_User($user_id);
                        $user->set_role('buyer');
                    }

                    // Redirect after successful registration
                    if (isset($_SESSION['redirect']) && !empty($_SESSION['redirect']) && $_SESSION['redirect'] == 'checkout') {
                        wp_redirect(home_url('/my-account?new_user=yes&reason=checkout'));
                    } else {
                        wp_redirect(home_url('/my-account?new_user=yes'));
                    }
                    exit;
                } else {
                    $errors[] = $user_id->get_error_message(); // Display any WP error
                }
            }
        }


        $register = $_GET['register'];
        if ($register == true) {

        ?>
            <div class="register-page-wrapper">
                <div class="login-form-details">
                    <div>
                        <h2 class="m-0">Create an account</h2>
                        <div class="col-lg-12 mb-4 login-redirect-to-register">
                            <p>Enter your credentials to access your account.<span class="fw-600">Already registered?</span>
                                <span><a href="<?php echo get_home_url() . '/my-account/' ?>">Login
                                    </a></span>
                            </p>
                        </div>
                        <?php
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo '<p id="error-msg">' . esc_html($error) . '</p>';
                            }
                        }
                        ?>
                        <p id="error-msg"></p>
                        <form action="#" method="POST" class="wstr_signup" id="wstr_signup">
                            <div class="reg_form_group buyer_check selected">
                                <div class="radio-group checkbox-group">
                                    <!-- Single Radio Button for Buyer or Seller -->
                                    <input type="radio" id="become-buyer" name="become_buyer" value="buyer">
                                    <label for="become-buyer">
                                        <div class="checked_reg">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div class="unchecked_reg">
                                            <i class="fa-regular fa-circle"></i>
                                        </div>
                                        <div class="reg_img_group">
                                            <img decoding="async"
                                                src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/join_buyer.svg"
                                                alt="Buyer Image">
                                        </div>
                                        <h5>Join as a Buyer</h5>
                                        <p>Find the perfect domain name</p>
                                    </label>
                                </div>
                            </div>

                            <div class="reg_form_group seller_check">
                                <div class="radio-group checkbox-group">
                                    <!-- Single Radio Button for Seller -->
                                    <input type="radio" id="become-seller" name="become_seller" value="seller">
                                    <label for="become-seller">
                                        <div class="checked_reg">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <div class="unchecked_reg">
                                            <i class="fa-regular fa-circle"></i>
                                        </div>
                                        <div class="seller_free_logo">
                                            <span>Free Logo</span>
                                        </div>
                                        <div class="reg_img_group">
                                            <img decoding="async"
                                                src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/join_seller.svg"
                                                alt="Seller Image">
                                        </div>
                                        <h5>Join as a Seller</h5>
                                        <p>Showcase and sell your domains</p>
                                    </label>
                                </div>
                            </div>
                            <div class="reg_form_group">
                                <label for="username">Username<span class="required">*</span></label>
                                <input type="text" id="username" name="username" placeholder="Your Username" required>
                            </div>
                            <div class="reg_form_group">
                                <label for="email">Email Address<span class="required">*</span></label>
                                <input type="email" id="email" class="register-email" name="email" required>
                            </div>
                            <!-- <label for="full-name">First Name, Last Name</label>
                            <input type="text" id="full-name" name="full_name" placeholder="Enter first and last name" required> -->
                            <div class="reg_form_group">
                                <label for="first-name">First Name<span class="required">*</span> </label>
                                <input type="text" id="first-name" name="first_name" required>
                            </div>
                            <div class="reg_form_group">
                                <label for="last-name">Last Name<span class="required">*</span></label>
                                <input type="text" id="last-name" name="last_name" required>
                            </div>
                            <div class="reg_form_group p_relative">
                                <label for="password">Password<span class="required">*</span></label>
                                <input type="password" id="password" name="password" class="wstr_password" required>
                                <span class="toggle-password">
                                    <img src="<?php echo get_theme_file_uri('/assets/images/pass-cross.svg'); ?>"
                                        class="pass-eye toggle-password-icon" alt="Show password" />
                                    <img src="<?php echo get_theme_file_uri('/assets/images/pass.svg'); ?>"
                                        class="pass-cross toggle-password-icon-cross" alt="Hide password" style="display: none;" />
                                </span>
                            </div>
                            <div class="reg_form_group p_relative">
                                <label for="confirm-password">Confirm Password<span class="required">*</span></label>
                                <input type="password" id="confirm-password" name="confirm_password" class="wstr_password" required>
                                <span class="toggle-password">
                                    <img src="<?php echo get_theme_file_uri('/assets/images/pass-cross.svg'); ?>"
                                        class="pass-eye toggle-password-icon" alt="Show password" />
                                    <img src="<?php echo get_theme_file_uri('/assets/images/pass.svg'); ?>"
                                        class="pass-cross toggle-password-icon-cross" alt="Hide password" style="display: none;" />
                                </span>
                            </div>

                            <div class="checkbox-group">
                                <!-- <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I have read and accepted the <a href="/terms-conditions/">terms and
                                        conditions<span class="required">*</span></a></label> -->
                                <p class="login-remember register_terms_check">
                                    <input type="checkbox" name="rememberme" id="rememberme" value="forever" />
                                    <label for="rememberme"><?php _e(''); ?></label>
                                    <span>I have read and accepted the <a href="/terms-conditions/">terms and
                                            conditions<span class="required">*</span></a></label></span>
                                </p>
                            </div>

                            <button type="submit" name="register_user">Create My Account <i
                                    class="fa-solid fa-arrow-right-long"></i></button>


                        </form>
                        <!-- <div class="terms-popup-overlay"></div>
                        <div id="terms-popup" class="terms-popup">
                            <div class="popup-content">
                                <div class="terms_popup_header">
                                    <div class="terms_popup_header_figure">
                                        <img decoding="async"
                                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/terms_header.svg"
                                            alt="Webstarter Logo">
                                    </div>
                                    <div class="terms_popup_header_title">
                                        <h2>Terms & Conditions</h2>
                                    </div>
                                </div>
                                <button id="accept-btn">Accept</button>
                                <button id="decline-btn">Decline</button>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="login-img register_page_right ws_text_center hide_tablet">
                    <div class="register_right_header_wrapper">
                        <h1>Buy and Sell Premium <span>Domains</span> Names Today !</h1>
                        <?php echo do_shortcode('[wstr_google_reviews]'); ?>
                    </div>

                    <div class="ws-testimonial-container ws_testimonial_container_register">
                        <!-- Testimonial Card 1 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main green">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/jungl-logo.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/jungl-icon-1.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial Card 2 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main purple">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/safetofly@3x.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/safetofly-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial Card 3 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main cyan">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/toulkin-image.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/toulkin-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial Card 4 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main orange">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/tummyhug-logo.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/tummyhug-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial Card 5 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main black">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/toxictreats-logo.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/toxictreats-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial Card 6 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main deep-blue">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/chartys-logo.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/chartys-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial Card 7 -->
                        <div class="ws-card">
                            <div class="ws-card-content">
                                <div class="ws-testimonial-main new-green">
                                    <div class="ws-company-logo">
                                        <img src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/WPCODETEAM-logo.png"
                                            alt="Jungl Logo">
                                    </div>
                                    <p class="ws-testimonial">"WebStarter lives up to its mission. They're truly empowering
                                        individuals like me to
                                        thrive in the digital world. I'm grateful for their platform."</p>
                                </div>
                                <div class="ws-testimonial-footer">
                                    <div class="ws-user-info">
                                        <img
                                            src="http://new-webstarter.codepixelz.tech/wp-content/uploads/2024/10/WPCODETEAM-icon.png">
                                        <div>
                                            <h4>Charlotte Mitchell</h4>
                                            <p>jungl.com</p>
                                        </div>
                                    </div>
                                    <div class="ws-rating"><span>5.0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <ul class="nav-arrows">
                        <li class="prev"><i class="fa-solid fa-angle-left"></i></li>
                        <li class="next"><i class="fa-solid fa-angle-right"></i></li>
                    </ul> -->
                    <!-- <img src="<?php // echo get_stylesheet_directory_uri(); 
                                    ?>/assets/images/login-right.png" alt="login Image"> -->
                </div>
            </div>

        <?php
        } else {
            // Check if the user is not logged in
            if (is_user_logged_in()) {
                // Redirect them to the wp-admin login page
                // $user_id = get_current_user_id();
                // // $author_url = get_author_posts_url($user_id);
                // wp_redirect(get_home_url());
                // exit;
                return;
            }
        ?>
            <div class="login-page-wrapper">
                <div class="user-details login-form-details forms_container wstr_login_column" id="login-form">
                    <div>
                        <?php
                        if (isset($_GET['new_user']) && $_GET['new_user'] == 'yes') {
                            echo ' <span class=" sg_success_msg d-flex gap-10 mb-2"><i class="bi bi-exclamation-circle  " ></i> User has been successfully created. Please login.
         </span>';
                        }
                        if (isset($_GET['reason'])) {
                            $login_err_msg = '';
                            switch ($_GET['reason']) {
                                case 'invalid_username':
                                    $login_err_msg = 'Invalid username';
                                    break;

                                case 'empty_password':
                                    $login_err_msg = 'Password is empty';
                                    break;

                                case 'empty_username':
                                    $login_err_msg = 'Username is Empty';
                                    break;

                                case 'incorrect_password':
                                    $login_err_msg = 'Incorrect Password';
                                    break;

                                case 'checkout':
                                    $login_err_msg = 'You need to login first to checkout';
                                    break;
                            }

                            echo '<span class="text-danger fw-bold">' . $login_err_msg . '</span>';
                        }
                        ?>
                        <?php
                        if (!isset($_GET['step']) && $_GET['step'] !== 'otp') { ?>
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/HAND.png" alt="login Image">
                            <h1 class="m-0">Welcome to Webstarter</h1>
                            <div class="col-lg-12 mb-4 login-redirect-to-register login_subtitle">
                                <p>Enter your credentials to access your account.</span>
                                </p>
                            </div>

                        <?php
                        };
                        // ============================= custom login and otp section starts
                        // Check if this is the OTP step
                        if (isset($_GET['step']) && $_GET['step'] === 'otp' && isset($_SESSION['pending_user_id'])) {
                            // Display OTP verification form
                        ?>
                            <div class="otp_verify_wrapper">


                                <?php
                                echo '<span class="text-danger fw-bold">' . $_GET['otp_reason'] . '</span>';
                                ?>
                                <div class="otp-verification-form">
                                    <h2>Verify OTP</h2>
                                    <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                                        <p>
                                            <label for="otp_code"><?php _e('Enter OTP'); ?></label>
                                            <!-- <input type="text" name="otp_code" id="otp_code" required /> -->

                                        <div id="otp_inputs" class="inputs" name="otp_inputs">
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input1"
                                                required />
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input2"
                                                required />
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input3"
                                                required />
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input4"
                                                required />
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input5"
                                                required />
                                            <input class="input" type="text" inputmode="numeric" maxlength="1" name="otp_input6"
                                                required />
                                        </div>

                                        </p>
                                        <p>
                                            <button type="submit" name="verify_otp_submit"><?php _e('Verify OTP'); ?></button>
                                        </p>
                                    </form>
                                    <p id="resend_error"></p>
                                    <p id="resend_success"></p>
                                    <p>
                                        Didn't get an OTP code?
                                        <button class="resend_otp" id="<?php echo $_SESSION['pending_user_id'] ?>">Resend OTP</button>
                                    </p>
                                </div>
                            </div>
                        <?php
                        } else {
                            // Display username and password login form
                        ?>
                            <div class="login_form_wrapper">

                                <form method="post" id="loginform" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                                    <p>
                                        <label for="username"><?php _e('Username or email address');
                                                                ?><span class='required'>*</span></label>
                                        <input type="text" name="log" id="sg-username" tabindex="1" required />
                                    </p>
                                    <!-- <p class="p_relative">
                                        <label for="password"><?php _e('Password');
                                                                ?><span class='required'>*</span></label>
                                        <a href="<?php //echo esc_url(wp_lostpassword_url()) 
                                                    ?>" class="login-forgot-password">
                                            <span class="text-center mt-4">Forgot password?</span>
                                        </a>
                                        <input type="password" name="pwd" id="sg-password" required />

                                    </p> -->
                                    <p class="p_relative">
                                        <label for="password"><?php _e('Password'); ?><span class='required'>*</span></label>
                                        <a href="<?php echo esc_url(wp_lostpassword_url()) ?>" class="login-forgot-password">
                                            <span class="text-center mt-4">Forgot password?</span>
                                        </a>
                                        <input type="password" name="pwd" id="sg-password" class="wstr_password" tabindex="2"
                                            required />
                                        <span class="toggle-password">
                                            <img src="<?php echo get_theme_file_uri('/assets/images/pass-cross.svg'); ?>"
                                                id="toggle-password-icon" class="pass-eye" alt="Show password" />
                                            <img src="<?php echo get_theme_file_uri('/assets/images/pass.svg'); ?>"
                                                id="toggle-password-icon-cross" class="pass-cross" alt="Hide password"
                                                style="display: none;" />
                                        </span>
                                    </p>
                                    <p class="login-remember">
                                        <input type="checkbox" name="rememberme" id="rememberme" value="forever" tabindex="3" />
                                        <label for="rememberme"><?php _e(''); ?></label>
                                        <span>Remember me</span>
                                    </p>

                                    <p>
                                        <input type="submit" id="sg-submit" name="custom_login_submit" class="button button-primary"
                                            tabindex="4" value="Login" />
                                </form>
                                <div class="col-lg-12 mb-4 login-redirect-to-register">
                                    <p class="ws_text_center">Not registered yet?<span><a
                                                href="<?php echo home_url('/my-account?register=true') ?>"> Create
                                                account
                                            </a></span>
                                    </p>
                                </div>

                            </div>
                        <?php
                        }
                        // ============================= custom login and otp section ends 




                        // echo wp_login_form(
                        //     array(
                        //         'redirect' => esc_url($_SERVER['REQUEST_URI']),
                        //         'form_id' => 'loginform',
                        //         'label_username' => 'Username or email address',
                        //         'label_password' => 'Password',
                        //         //  'label_username' => __('Username', 'stat-genius'),
                        //         //  'label_password' => __('Password', 'stat-genius'),
                        //         'label_remember' => __('Remember Me', 'stat-genius'),
                        //         'label_log_in' => __('Login', 'stat-genius'),
                        //         'id_username' => 'sg-username',
                        //         'id_password' => 'sg-password',
                        //         'id_remember' => 'sg-rememberme',
                        //         'id_submit' => 'sg-submit',
                        //         'remember' => true,
                        //         'value_username' => '',
                        //         'value_remember' => false,
                        //         'before' => '',
                        //         'after' => '<p><input type="checkbox" id="show-password"> ' . __('Show Password', 'stat-genius') . '</p>'

                        //     )
                        // );

                        if (!isset($_GET['step']) && $_GET['step'] !== 'otp') {
                            // 
                        ?>

                            <!-- <a href="<?php // echo esc_url(wp_lostpassword_url()) 
                                            ?>" class="login-forgot-password">
                                <p class="text-center mt-4">Forgot password?</p>
                            </a> -->
                        <?php }
                        ?>
                    </div>
                </div>
                <div class="login-img">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/login-right.png" alt="login Image">
                </div>
            </div>

            </h4> <?php
                }
                return ob_get_clean();
            }


            public function wstr_faq($args)
            {
                ob_start();
                $category = $args['category']; // Get the category argument
                $faq_args = [
                    'posts_per_page' => -1,
                    'post_type' => 'faq',
                    'tax_query' => [
                        [
                            'taxonomy' => 'faq_cat', // FAQ category taxonomy
                            'field' => 'slug',
                            'terms' => $category,
                        ],
                    ],
                ];

                $query = new WP_Query($faq_args);
                    ?>
        <div class="wstr-faq-accordion">

            <?php
                if ($query->have_posts()):
                    while ($query->have_posts()):
                        $query->the_post(); ?>
                    <div class="wstr-faq-accordion-item">
                        <div class="wstr-faq-accordion-header">
                            <h5><?php the_title(); ?></h5>
                            <span class="wstr-faq-icon">
                                <i class="fa-solid fa-plus"></i>
                            </span>
                        </div>
                        <div class="wstr-faq-accordion-content">
                            <p>
                                <?php the_content(); ?>
                            </p>
                        </div>
                    </div>
            <?php endwhile;
                else:
                    echo '<p>No FAQs found.</p>';
                endif;
            ?>
        </div>
    <?php
                wp_reset_postdata();

                return ob_get_clean(); // Return the output buffer
            }

            /**
             * Contact us form shortcode
             */


            public function wstr_contact_form()
            {

                $error = '';
                $success = '';
                if (isset($_POST['contact_us_submit'])) {
                    // Rate limiting
                    $transient_key = 'contact_form_rate_limit_' . wp_hash($_SERVER['REMOTE_ADDR']);
                    $attempts = get_transient($transient_key);

                    if ($attempts && $attempts > 5) {
                        $error = "Too many attempts. Please wait 24 hours.";
                    } else {
                        // Enhanced honeypot
                        $submission_time = time();
                        $time_diff = $submission_time - (int) $_POST['timestamp'];

                        if (!empty($_POST['honeypot']) || $time_diff < 2) {
                            $error = "Suspicious activity detected.";
                        } else {
                            // Verify nonce
                            if (!isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'contact_form_nonce_action')) {
                                $error = "Security check failed. Please try again.";
                            } else {
                                // Verify reCAPTCHA
                                $secret = get_option('recaptcha_secret_key');
                                $recaptcha_secret_key = $secret;
                                $recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';


                                if (empty($recaptcha_response)) {
                                    $error = "Please complete the reCAPTCHA.";
                                } else {
                                    // Verify reCAPTCHA with Google's API
                                    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
                                    $response = wp_remote_post($verify_url, array(
                                        'body' => array(
                                            'secret' => $recaptcha_secret_key,
                                            'response' => $recaptcha_response,
                                            'remoteip' => $_SERVER['REMOTE_ADDR']
                                        )
                                    ));

                                    if (is_wp_error($response)) {
                                        $error = "Unable to verify reCAPTCHA. Please try again.";
                                    } else {
                                        $response_data = json_decode(wp_remote_retrieve_body($response));

                                        if (!$response_data->success) {
                                            $error = "reCAPTCHA verification failed. Please try again.";
                                        } else {
                                            global $wpdb;
                                            $table_name = $wpdb->prefix . 'contact_us';
                                            $name = sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']);
                                            $email = sanitize_email($_POST['email']);
                                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $error = "Invalid email address.";
                                            } else {
                                                // Check DNS validity
                                                if (!checkdnsrr(substr(strrchr($email, "@"), 1), "MX")) {
                                                    $error = "Invalid email addressss.";
                                                } else {
                                                    if ($email != 'ericjonesmyemail@gmail.com') {
                                                        $phone = sanitize_text_field($_POST['phone']);
                                                        $type = sanitize_text_field($_POST['inquiry_type']);
                                                        $message = sanitize_textarea_field($_POST['message']);

                                                        // Check for spam keywords
                                                        $spam_keywords = [
                                                            'casino',
                                                            'loan',
                                                            'viagra',
                                                            'click here',
                                                            'www.',
                                                            'buy now',
                                                            'limited time offer',
                                                            'urgent:',
                                                            'quick cash'
                                                        ];
                                                        $message_error = '';
                                                        foreach ($spam_keywords as $keyword) {
                                                            if (stripos($message, $keyword) !== false) {
                                                                $message_error = "Message contains suspicious content.";
                                                                break;
                                                            }
                                                        }
                                                        $time = current_time('mysql');
                                                        if ($message_error) {
                                                            $error = $message_error;
                                                        } else {
                                                            if (strlen($phone) > 15 || strlen($phone) < 7) {
                                                                $error = "Phone number is not valid.";
                                                            } else {
                                                                // Rate limiting increment
                                                                set_transient($transient_key, ($attempts ?: 0) + 1, DAY_IN_SECONDS);
                                                                $data = [
                                                                    'name' => esc_sql($name),
                                                                    'email' => esc_sql($email),
                                                                    'phone' => esc_sql($phone),
                                                                    'type' => esc_sql($type),
                                                                    'message' => esc_sql($message),
                                                                    'time' => esc_sql($time)
                                                                ];
                                                                $format = ['%s', '%s', '%s', '%s', '%s', '%s'];
                                                                $insert = $wpdb->insert($table_name, $data, $format);
                                                                if (!$insert) {
                                                                    $error = "Something went wrong. Please try again later.";
                                                                } else {
                                                                    $email_header = get_option('email_header', '');
                                                                    $email_footer = get_option('email_footer', '');
                                                                    $type_msg = '';
                                                                    if ($type == 'domain') {
                                                                        $type_msg = 'Domain Inquiry';
                                                                    } else if ($type == 'technical') {
                                                                        $type_msg = 'Technical Support';
                                                                    } else if ($type == 'general') {
                                                                        $type_msg = 'General Questions';
                                                                    } else if ($type == 'billing') {
                                                                        $type_msg = 'Billing & Payments';
                                                                    } else {
                                                                        $type_msg = 'Others';
                                                                    }
                                                                    // $admin_email = get_option('admin_email');
                                                                    $admin_email = 'digecak700@prorsd.com';

                                                                    $email_body = '
                            <div class="wstr_email_template_main">
                                <div class="wstr_email_template_wrapper" style="font-family: \'Poppins\', serif;">
                                    ' . $email_header . '
                                    <h3 style="text-align: center; color: #333; margin-top:30px" >New Contact Us Submission</h3>
                                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 16px; color: #555;">
                                        <tr>
                                            <th style="text-align: left; padding: 8px; background: #f4f4f4; border: 1px solid #ddd;">Field</th>
                                            <th style="text-align: left; padding: 8px; background: #f4f4f4; border: 1px solid #ddd;">Details</th>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Name</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($name) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Email</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($email) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Phone</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($phone) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Type</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($type_msg) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Message</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . nl2br(htmlspecialchars($message)) . '</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">Time</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($time) . '</td>
                                        </tr>
                                    </table>
                                    <p style="text-align: center; margin: 20px 0; color: #888;">Thank you for using our service!</p>
                                    ' . $email_footer . '
                                </div>
                            </div>';
                                                                    // Construct the email template
                                                                    wp_mail($admin_email, 'New Contact Us Submission', $email_body, ['Content-Type: text/html; charset=UTF-8']);


                                                                    $success = "Your message has been sent successfully.";
                                                                    wp_safe_redirect(add_query_arg('success', '1', esc_url($_SERVER['REQUEST_URI'])));
                                                                    exit;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                ob_start();
                $success = $_GET['success'];

    ?>
        <div class="contact-form-wrapper">
            <h2>Get In Touch</h2>
            <p class="sub-title">Have any questions? Don't hesitate to contact us!</p>
            <p class="small-subtitle"><sup>"*" indicates required fields</sup></p>
            <div class="error_msg"><?php echo $error ?: '' ?> </div>
            <div class="success_msg"><?php echo $success == 1  ? 'Your message has been sent successfully.' : '' ?> </div>
            <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="POST">
                <?php wp_nonce_field('my_delete_action'); ?>
                <div class="form-group select-group">
                    <input type="radio" id="general" name="inquiry_type" value="general" checked>
                    <label for="general" class="select-option">
                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/general-question-icon.png"
                            alt="Icon">
                        <div class="option-title">General Questions</div>
                    </label>

                    <input type="radio" id="domain" name="inquiry_type" value="domain">
                    <label for="domain" class="select-option">
                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/domain-inquiry-icon.png"
                            alt="Icon">
                        <div class="option-title">Domain Inquiry</div>
                    </label>

                    <input type="radio" id="billing" name="inquiry_type" value="billing">
                    <label for="billing" class="select-option">
                        <img decoding="async" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/billing-icon.png"
                            alt="Icon">
                        <div class="option-title">Billing & Payments</div>
                    </label>

                    <input type="radio" id="technical" name="inquiry_type" value="technical">
                    <label for="technical" class="select-option">
                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/technical-support-icon.png"
                            alt="Icon">
                        <div class="option-title">Technical Support</div>
                    </label>
                </div>
                <div class="form-group">
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First Name">
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last Name">
                    </div>
                </div>

                <div class="form-group">
                    <div>
                        <label for="phone">Your Phone</label>
                        <input type="tel" id="phone" name="phone" placeholder="Phone">
                    </div>
                    <div>
                        <label for="email">Your Email <sup>*</sup></label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div style="display:none;">
                    <label for="honeypot">Leave this field empty</label>
                    <input type="text" id="honeypot" name="honeypot">
                    <input type="hidden" id="timestamp" name="timestamp" value="<?php echo time(); ?>">
                </div>

                <div class="form-group">
                    <div>
                        <label for="message">Message <sup>*</sup></label>
                        <textarea id="message" name="message" placeholder="I would like to hear about.." required></textarea>
                    </div>
                </div>
                <?php
                $site = get_option('recaptcha_site_key');
                ?>
                <div class="form-group captcha">
                    <div class="g-recaptcha" data-sitekey="<?php echo $site ?: '' ?>"></div>
                </div>
                <?php wp_nonce_field('contact_form_nonce_action', 'contact_form_nonce'); ?>
                <button type="submit" class="hover-white" name="contact_us_submit">Submit</button>
            </form>
        </div>



<?php
                return ob_get_clean();
            }
        }
        new wstr_shortcodes();


        // add_action('wp_footer', function () {
        //     global $wpdb;
        //     $sql = $wpdb->query('TRUNCATE table wp_contact_us');

        //     // $site = get_option('recaptcha_site_key');
        //     // $secret = get_option('recaptcha_secret_key');
        //     // var_dump($site, $secret);
        // });
