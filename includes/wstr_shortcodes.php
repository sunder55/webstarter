<?php
class wstr_shortcodes
{

    public function __construct()
    {
        add_shortcode('wstr-new-domains', array($this, 'wstr_new_domains'));
        add_shortcode('wstr-home-premium', array($this, 'wstr_home_premium'));
        add_shortcode('wstr_banner_reviews', array($this, 'wstr_banner_reviews_function'));
    }

    /**
     * function for home page premium domains 
     */
    public function wstr_home_premium()
    {
        ob_start();
        $query_args = array(
            'posts_per_page' => 8,
            'post_type' => 'domain',
            'orderby' => 'rand', //rand
            'order' => 'DESC',
            'fields' => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'domain_cat', // Replace with your custom taxonomy name if necessary.
                    'field' => 'term_id',
                    // 'terms' => 52, // Category ID to check (52 in this case).
                    'terms' => 57,
                ),
            ),

        );

        $premium_domains = get_posts($query_args);

?>
        <div class="premium-product-wrapper">
            <?php
            if ($premium_domains) {
                foreach ($premium_domains as $premium_domain) {
                    // $product = wc_get_product($premium_domain);

            ?>
                    <div class="premium-details">
                        <a href="<?php echo get_permalink($premium_domain) ?>">
                            <?php
                            $product_title = get_the_title($premium_domain);

                            $product_image = get_the_post_thumbnail_url($premium_domain, 'medium_large');
                            if (!$product_image) {
                                $product_image = get_stylesheet_directory_uri() . '/assets/images/Frame-1.png';
                            }

                            ?>
                            <div class="premium-top-wrapper">
                                <div class="ws-preminum-image">
                                    <img src="<?php echo $product_image; ?>" alt="<?php echo $product_title; ?>">
                                </div>
                                <div class="premium-content">

                                    <?php //if ($product->is_on_sale()) {
                                    echo get_wstr_price($premium_domain);
                                    // var_dump (wstr_on_sale($premium_domain));
                                    ?>
                                    <span><?php //esc_html_e('Sale!', 'woocommerce')
                                            ?></span>
                                    <?php //}
                                    ?>
                                    <span><?php echo $product_title;
                                            ?></span>
                                    <span> <?php //echo $product->get_price_html(); 
                                            ?></span>
                                </div>
                            </div>
                            <?php
                            $da_pa = get_post_meta($premium_domain, '_da_pa', true);
                            if($da_pa){
                            $da_pa_split = explode('/',$da_pa);
                            $da = $da_pa_split[0];
                            $pa = $da_pa_split[1];
                            }
                            ?>
                            <div class="premium-btn-wrapper">
                                <h3>Energy & Environment</h3>
                                <h4>DA / PA Ranking:
                                    <span>
                                        <?php

                                        if ($da_pa) {
                                            echo $da_pa;
                                        }
                                        //echo esc_html( get_field('da_pa_ranking') , $premium_domain); 
                                        ?>
                                    </span>
                                </h4>

                                </span>
                                </h4>
                            </div>

                        </a>
                    </div>

            <?php

                }
            }
            ?>

        </div>
    <?php

        return ob_get_clean();
    }

    public function wstr_new_domains()
    {
        ob_start();
        $query_args = array(
            'posts_per_page' => 8,
            'post_type' => 'domain',
            'orderby' => 'rand', //rand
            'order' => 'DESC',
            'fields' => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                )
            ),
            // 'tax_query' => array(
            //     array(
            //         'taxonomy' => 'product_cat', // Replace with your custom taxonomy name if necessary.
            //         'field' => 'term_id',
            //         'terms' => 52, // Category ID to check (52 in this case).
            //     ),
            // ),

        );
        $premium_domains = get_posts($query_args);
    ?>
        <div class="ws-premium-product-wrapper">
            <?php
            if ($premium_domains) {
                foreach ($premium_domains as $premium_domain) {
                    // $product = wc_get_product($premium_domain);
            ?>
                    <div class="ws-premium-details ws-new-details">
                        <a href="<?php echo get_permalink($premium_domain) ?>">
                            <?php
                            $product_title = get_the_title($premium_domain);

                            $product_image = get_the_post_thumbnail_url($premium_domain, 'medium_large');
                            if (!$product_image) {
                                $product_image = get_stylesheet_directory_uri() . '/assets/images/Frame-1.png';
                            }

                            ?>

                            <div class="ws-preminum-image">
                                <img src="<?php echo $product_image; ?>" alt="<?php echo $product_title; ?>">
                            </div>
                            <div class="ws-premium-content">
                                <?php //if ($product->is_on_sale()) {
                                ?>
                                <span><?php //esc_html_e('Sale!', 'woocommerce')
                                        ?></span>
                                <?php //}
                                ?>
                                <span><?php echo $product_title; ?></span>
                                <span> <?php echo get_wstr_price($premium_domain);
                                        //echo $product->get_price_html(); 
                                        ?></span>
                            </div>
                        </a>
                    </div>
            <?php

                }
            }
            ?>

        </div>
    <?php

        return ob_get_clean();
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
}
new wstr_shortcodes();
