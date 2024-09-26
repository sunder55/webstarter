<?php
class wstr_shortcodes
{
    public function __construct()
    {
        add_shortcode('wstr_banner_reviews', array($this, 'wstr_banner_reviews_function'));
        add_shortcode('wstr-multicurrency', array($this, 'wstr_multicurrency'));
        add_shortcode('wstr-browse-industry', array($this, 'wstr_browse_industry'));
        add_shortcode('wstr-popular-category', array($this, 'wstr_popular_category'));
        add_shortcode('wstr-domain-count', array($this, 'wstr_domain_count'));
        add_shortcode('wstr-footer-average-cost', array($this, 'wstr_footer_average_cost_calculation'));
        add_shortcode('wstr-you-may-like', array($this, 'wstr_you_may_like'));
        add_shortcode('wstr_estimation', array($this, 'wstr_estimation'));
        add_shortcode('wstr-single-domain', array($this, 'wstr_single_domain_page'));
        add_shortcode('wstr-similar-industry-name', [$this, 'wstr_similar_industry_name']);
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
                        <span>1,500+ </span>clients trust WebStarter. <a href="#">Join them today!</a>
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
     * @return void
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
        $domains_list_page = get_page_link(get_option('ws_domain_list_page')); // getting product page link
        if (!$domains_list_page) {
            $domains_list_page = get_home_url() . '/domain-list/';
        }
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

                        $term_image_id = get_term_meta($industry->term_id, 'taxonomy-image-id', true);

                        if ($term_image_id) {
                            $term_image_url = wp_get_attachment_url($term_image_id);
                        ?>
                            <img src="<?php echo $term_image_url ? $term_image_url : '' ?>">
                        <?php
                        }
                        ?>

                        <a href="<?php echo $domains_list_page . '?industry=' . $industry->slug ?>"><?php echo $industry->name; ?></a>
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
        $total_prices = 0;
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

        $output = '<p class="get_average_price">' . get_wstr_currency() . '' . wstr_truncate_number($average_price) . ' </p>';

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
                <input type="text" name="domain" id="domain" placeholder="Enter your domain" class="w_100" />
                <button type="submit" value="Estimate">Estmate
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
            'post__not_in'   => array($post->ID),
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
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/love-emoji.png" alt="You May Like">
                    You May Like
                </h4>
                <a href="#">All Domains</a>
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
                            <div class="ws-card-likes">
                                <h6>
                                    <span>2k</span>
                                    <i class="fa-solid fa-heart"></i>
                                </h6>
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
                shuffle($category);
                $category_name = $category[0]->name;
                $category_id = $category[0]->term_id;
                $cat_image_id = get_term_meta($category_id, 'taxonomy-image-id', true);
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

            ?>
                <div class="single_domain_details ws_flex fd_mob_col ">
                    <!-- Featured Image -->
                    <div class="featured-image p_relative img_producto_container" data-scale="1.6">

                        <img src="<?php echo $featured_image ? esc_url($featured_image) : get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png' ?>" alt="<?php echo esc_attr($title); ?>"
                            class="img_producto">

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
                                <img src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon" />
                            </div> <?php
                                }
                                    ?>
                        <div class="ws_flex ai_center single_domain_meta_search">
                            <div class="single_domain_search">

                                <i class="fa-solid fa-magnifying-glass" id="enlarge-icon"></i>

                            </div>
                            <div class="ws-card-likes">
                                <h6><span>2k</span><i class="fa-solid fa-heart"></i></h6>
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
                                    <img src="<?php echo $logo ? $logo : $featured_image ?>"
                                        alt="<?php echo $title; ?> ">
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
                                    if (!empty($regular_price)) { ?>
                                        <p class="regular_price">
                                            <?php
                                            echo get_wstr_currency();
                                            echo get_wstr_regular_price(get_the_ID());
                                            ?>
                                        </p>
                                    <?php
                                    }
                                    if (!empty($sale_price)) { ?>
                                        <p class="sale_price">
                                            <?php
                                            echo get_wstr_currency();
                                            echo get_wstr_sale_price(get_the_ID());
                                            ?>
                                        </p>
                                    <?php
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
                            <div class="single_domain_features ws_trending_cards">
                                <h2>What You Get</h2>
                                <div class="ws_flex gap_10 fd_mob_col">
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/domain-name.png"
                                            alt="Feature Image">
                                        <div class="ws-card-inner-contents">
                                            <h5 class="fw-600"><?php echo $title ?></h5>
                                            <div class="ws_card_price_wrapper ws_flex gap_10">
                                                <p>Domain Name</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/svg-file-icon.png"
                                            alt="SVG Icon">
                                        <div class="ws-card-inner-contents">
                                            <h5 class="fw-600">SVG File & Copyright</h5>
                                            <div class="ws_card_price_wrapper ws_flex gap_10">
                                                <p class="">Logo Design</p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="similar-industry-names ws-card-contents ws_flex single_domain_feature">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/technical-support.png"
                                            alt="SVG Icon">
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
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-2.png"
                                    alt="Feature Image">
                                <h5>Free Technical Support</h5>
                            </div>
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/highlight-3.png"
                                    alt="Feature Image">
                                <h5>Professionally Crafter Logo</h5>
                            </div>
                            <div class="single_domain_highlights_card">
                                <img src="<?php echo $cat_image_url ?> ">
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
        $category_id  = $atts['category_id'];
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
}
new wstr_shortcodes();
