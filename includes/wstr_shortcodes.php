<?php
// shortcode for banner reviews
add_shortcode('wstr_banner_reviews', 'wstr_banner_reviews_function');
function wstr_banner_reviews_function()
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

