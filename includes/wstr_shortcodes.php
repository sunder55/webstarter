<?php
class wstr_shortcodes
{

    public function __construct()
    {
        add_shortcode('wstr_banner_reviews', array($this, 'wstr_banner_reviews_function'));
        add_shortcode('wstr-multicurrency', array($this, 'wstr_multicurrency'));
        add_shortcode('wstr-browse-industry', array($this, 'wstr_browse_industry'));
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
            </select><?php
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
                            $term_id = $industry->term_id;

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
                            );

                            $domains_query = new WP_Query($args_domains);

                            if ($domains_query->have_posts()) {
                                echo '<ul>';
                                while ($domains_query->have_posts()) {
                                    $domains_query->the_post();

            ?>
                    <?php
                                }
                                echo '</ul>';
                            }
                    ?>
                    <div class="ws-industry_details">
                        <?php
                        ?>
                        <a href="<?php echo $domains_list_page . '?industry=' . $industry->slug ?>"><?php echo $industry->name; ?></a>
                        <!-- <a href="<?php //echo $domains_list_page . '?industry=' . $industry->slug 
                                        ?>"><?php //echo $industry->name; 
                                                                                                            ?></a> -->
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
            }
            new wstr_shortcodes();
