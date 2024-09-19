<?php
get_header();
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
            <div class="featured-image">
                <?php if ($featured_image): ?>
                    <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($title); ?>">
                <?php endif; ?>
                <div class="single_featured_image_footer ws_flex">
                    <span class="domain_online ws_flex gap_10 ai_center online">
                        <i class="fa-solid fa-comments"></i>
                        <p>Online</p>
                        <i class="fa-solid fa-circle"></i>
                    </span>
                    <a href="#">
                        <p>Message</p>
                    </a>
                </div><?php
                if ($term_exist) {
                    $output .= '<div class="premium_icon"><img src="/wp-content/plugins/card-block/images/diamond.png"
                        alt="Diamond Icon" /></div>';
                } ?>
            </div>

            <!-- Details Section -->
            <div class="domain-details">
                <div class="ws_flex gap_20 ai_center">
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
                    <?php echo get_wstr_price_percentage(get_the_ID()); ?>
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
get_footer();
?>