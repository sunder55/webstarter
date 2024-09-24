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
                            'date_query'     => array(
                                'after' => array(
                                    'year'  => date("Y"),
                                    'month' => date("m"),
                                    'day'   => date("d") - 17,
                                ),
                            ),
                        );

                        $domains_query = new WP_Query($args_domains);

                        if ($domains_query->have_posts()) {

                        ?>
                            <span>New</span>
                        <?php
                        }

                        $term_image_id =  get_term_meta($industry->term_id, 'taxonomy-image-id', true);

                        if ($term_image_id) {
                            $term_image_url  =  wp_get_attachment_url($term_image_id);
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
            'post_type'      => 'domain',
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

        if ($count > 120) {
            echo '120+';
        } else {
            echo $count;
        }

        return ob_get_clean();
    }

    public function wstr_footer_average_cost_calculation()
    {
        ob_start();
        $total_prices = 0;
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'domain',
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
        <h4>Become a seller</h4>
        <p>Verify your Domain Value Estimation!</p>

    <?php
    }


    public function wstr_you_may_like($atts)
    {
        global $post;
        ob_start();
        $args = shortcode_atts(array(
            'count' => 12,
        ), $atts);

        $query_args = array(
            'posts_per_page' => $args['count'],
            'post_type'      => 'domain',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_key'       => 'ws_product_view_count',
            // 'post__not_in'   => array($post->ID),
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
            <h3>You May Like</h3>
            <a href="#">All Domains</a>
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
                <div class="you-may-like-details">
                    <img src="<?php echo $featured_image ? $featured_image : get_home_url() . '/assets/images/alternate-domain.png' ?>">
                </div>

                <div class="ws-cards-container">
                    <div class="ws_card_hover_charts ws_flex">
                        <div class="circular-progress page-trust">
                            <div class="progress-text">
                                <div role="progressbar" aria-valuenow="<?php echo (int) $pa; ?>" aria-valuemin="0" aria-valuemax="100" style="--value: <?php echo (int) $pa; ?>"></div>
                            </div>
                            <div class="progress-title">
                                <h6>Page Trust</h6>
                            </div>
                        </div>
                        <div class="circular-progress domain-trust">
                            <div class="progress-text">
                                <div role="progressbar" aria-valuenow="<?php echo (int) $da; ?> " aria-valuemin="0" aria-valuemax="100" style="--value:<?php echo (int) $da; ?> "></div>
                            </div>
                            <div class="progress-title">
                                <h6>Domain Trust</h6>
                            </div>
                        </div>
                    </div>
                    <div class="ws-card-img">
                        <img decoding="async" src="<?php echo $featured_image ? $featured_image : get_home_url() . '/assets/images/alternate-domain.png' ?>" alt="<?php echo get_the_title($domain->ID); ?>">
                    </div>
                    <div class="ws-card-contents ws-flex">
                        <img decoding="async" src="<?php echo $logo_image ? $logo_image : $featured_image ?>" alt="zanabism.com" title="<?php echo get_the_title($domain->ID); ?>" class="card_logo_img">
                        <span class="ws-card-inner-contents">
                            <h5>
                                <a href="https://new-webstarter.codepixelz.tech/domain/zanabism/"><?php echo get_the_title($domain->ID); ?></a>
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
<?php
        return ob_get_clean();
    }
}
new wstr_shortcodes();
