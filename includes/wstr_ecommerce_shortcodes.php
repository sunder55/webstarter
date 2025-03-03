<?php
class wstr_ecommerce_shortcodes
{
    private $stripe;

    public function __construct()
    {
        add_shortcode('wstr_cart_page', [$this, 'wstr_cart_page']);
        add_shortcode('wstr_checkout_page', [$this, 'wstr_checkout_page']);
        add_shortcode('wstr_payment_success_page', [$this, 'wstr_payment_success_page']);

        require_once get_template_directory() . '/stripe-php/init.php';
        $this->stripe = new \Stripe\StripeClient('sk_test_51QVNzYITeSzB5TIRk5zVO4pUTw0QwwJWGGeQKH7TG4m7gtUm7zCFvZDYFEXw1hdJ1aLQHb5cqmHsfPeBwFSb6dD100pxog7rqg');
    }


    public function wstr_cart_page()
    {
        // Retrieve cart items
        $cart_items = wstr_retrieve_cart_items();
        $pricing_plan_id = isset($_GET['pricing_plan']) ? intval($_GET['pricing_plan']) : 0;
        $override_curr_cart = isset($_GET['override_cart']) ? sanitize_text_field($_GET['override_cart']) : '';

        $cart_has_installment = false;

        // Check if cart has an installment product
        foreach ($cart_items as $cart_item) {
            if (isset($cart_item['payment_option']) && $cart_item['payment_option'] === 'installment') {
                $cart_has_installment = true;
                break;
            }
        }

        if ($pricing_plan_id > 0) {
            $product = get_post($pricing_plan_id);
            if ($product) {
                $plan_title = $product->post_title;
                $eligible_plans = ['Pro Plan', 'Premium Plan', 'Plus Plan'];
                if ($cart_has_installment && $override_curr_cart == '') {
                    echo '<div class="confirm-pricing-plan-modal plan_modal_overlay">
                    <div class="confirm-pricing-plan-modal-content plan_modal">
                        <h5 class="ws_text_center">
                            Hmmm... it looks like you already have an installment payment domain in your cart. 
                            Please confirm if you want to proceed with purchasing our ' . esc_html($product->post_title) . ',
                            or continue with your existing domain in the cart.
                        </h5>
                        <div class="confirm-pricing-plan-btns">
                            <a href="/cart?pricing_plan=' . esc_attr($product->ID) . '&override_cart=yes" class="btn btn-primary">Continue with pricing plan</a>
                            <a href="/cart" class="btn btn-secondary">Continue shopping</a>
                        </div>
                    </div>
                </div>';
                    echo '
                    <script>
                        jQuery(document).ready(function () {
                            var cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.replaceState({}, document.title, cleanUrl);
                        });
                    </script>
                    ';
                } elseif (($override_curr_cart === 'yes' && in_array($plan_title, $eligible_plans)) || empty($_SESSION['cart'])) {
                    unset($_SESSION['cart']);
                    $_SESSION['cart'][$product->ID] = ['full', '1'];
                    echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.replaceState({}, document.title, cleanUrl);
                        });
                    </script>
                    ';
                } else if (!$cart_has_installment && in_array($plan_title, $eligible_plans)) {
                    $_SESSION['cart'][$product->ID] = ['full', '1'];
                    echo '
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.replaceState({}, document.title, cleanUrl);
                        });
                    </script>
                    ';
                }
            }
        }




        ob_start();

        $cart_items = wstr_retrieve_cart_items();
        if (empty($cart_items)) {
            echo '<h3 class="cart-empty-msg error_msg"> <i class="fa-solid fa-circle-info"></i>  Your cart is empty. Please add some items to your cart.</h3>';
            return ob_get_clean();
        }

?>
        <!-- Display a payment form -->
        <div class="shopping-cart cart_page_wrapper_main">
            <p class="coupon_error_msg tab_w_100">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/coupon_error_info.png" alt="Info Image">
                Please enter a valid coupon code.
            </p>
            <div id="cart-page-wrappers" class="ws_flex fd_tab_col">
                <?php
                $total_price = 0;
                $currency_symbol = '';

                if (!empty($cart_items)) { ?>
                    <div class="cart_carts_lists">
                        <div class="cart_shopping_cart">
                            <h2>Shopping Cart</h2>
                            <?php
                            $installment_duration = 1;
                            $actual_price = 0;
                            foreach ($cart_items as $item) {
                                $installment_duration_str = '';
                                $total_price += $item['price'];
                                $currency_symbol = $item['currency'];

                                if ($item['payment_option'] === 'installment') {
                                    $installment_duration_str = '/' . $item['installment_duration'] . ' months';
                                    $installment_duration = (int) $item['installment_duration'];
                                    $actual_price = number_format(($total_price / $installment_duration), 2, '.', '');
                                }
                                $actual_price = number_format(($total_price / $installment_duration), 2, '.', '');
                            ?>
                                <div class="cart_shopping_inner_content_outline"
                                    data-product-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES); ?>">
                                    <div class="cart_shopping_inner_content_wrapper">
                                        <div class="cart_shopping_cart_content fd_mob_col">
                                            <div class="cart_shopping_cart_content_left">
                                                <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>"
                                                    alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>">
                                            </div>
                                            <div class="cart_shopping_cart_content_right">
                                                <span>
                                                    <h4>Payment Plan</h4>
                                                    <p>Payment:
                                                        <?php echo htmlspecialchars($item['payment_option'], ENT_QUOTES) . $installment_duration_str; ?>
                                                    </p>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="cart_shopping_cart_footer ws_flex fd_mob_col">
                                            <div class="ws_flex gap_20">
                                                <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>"
                                                    class='hide_mobile'
                                                    alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>" />
                                                <div class="ws-card-inner-contents">
                                                    <div class="ws_card_price_wrapper">
                                                        <p>Product</p>
                                                        <h4><?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?></h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <p>Price</p>
                                                <h5><?php echo htmlspecialchars($item['currency'] . $item['price'], ENT_QUOTES); ?>
                                                </h5>
                                            </div>
                                            <div>
                                                <p>Subtotal</p>
                                                <h4><?php echo htmlspecialchars($item['currency'] . number_format(($item['price'] / $installment_duration), 2, '.', ''), ENT_QUOTES); ?>
                                                </h4>
                                            </div>
                                        </div>
                                        <span class="dashicons dashicons-no-alt remove_cart_item_cart_page"
                                            data-product-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES); ?>"></span>
                                    </div>
                                </div>
                            <?php
                            } ?>
                        </div>
                    </div>
                    <div class="cart_order_sumamry">
                        <h2>Order Summary</h2>
                        <div class="cart_order_summary_first">
                            <span class="ws_flex">
                                <p>Subtotal</p>
                                <p><?php echo htmlspecialchars($item['currency'] . number_format(($total_price / $installment_duration), 2, '.', ''), ENT_QUOTES); ?>
                                </p>
                            </span>
                            <span class="ws_flex">
                                <p>Discount</p>
                                <p>
                                    <?php
                                    $discount_amount = 0;
                                    if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                                        $discount_percent = (float) $_SESSION['coupon_code_discount_percentage'];
                                        $discount_amount = $actual_price * ($discount_percent / 100);
                                    }
                                    echo htmlspecialchars($currency_symbol . $discount_amount, ENT_QUOTES);
                                    ?>
                                </p>
                            </span>
                        </div>
                        <div class="cart_order_summary_total">
                            <span class="ws_flex">
                                <h4>Total Due Today</h4>
                                <h4>
                                    <?php
                                    if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                                        $discount_percent = $_SESSION['coupon_code_discount_percentage'];
                                        $actual_price = $actual_price - ($actual_price * ($discount_percent / 100));
                                    }
                                    echo htmlspecialchars($currency_symbol . $actual_price, ENT_QUOTES);
                                    ?>
                                </h4>
                            </span>

                            <?php if ($installment_duration > 1) { ?>
                                <span class="ws_flex">
                                    <p>
                                        Future Payments<span class="dashicons dashicons-info margin_y_0 ml_10"></span>

                                    </p>
                                    <p>Remaining:
                                        <?php echo htmlspecialchars($item['currency'] . number_format(($total_price - ($total_price / $installment_duration)), 2, '.', ''), ENT_QUOTES); ?>
                                    </p>
                                </span>
                                <table class="future_payment_table"
                                    style="border-collapse: collapse; width: 100%; border: 1px solid #edf0f7;">
                                    <tr style="background-color: #f9f9f9;">
                                        <th
                                            style="border: 1px solid #edf0f7; padding: 10px; text-align: left; font-weight:500; background-color:#f6f8fe;">
                                            Date
                                        </th>
                                        <th
                                            style="border: 1px solid #edf0f7; padding: 10px; text-align: left; font-weight:500;background-color:#f6f8fe;">
                                            Amount
                                        </th>
                                    </tr>
                                    <?php
                                    for ($i = 1; $i <= ($installment_duration - 1); $i++) {
                                        $date = (new DateTime())->modify("+$i month");
                                        echo "<tr>
                <td style='border: 1px solid #edf0f7; padding: 10px;'>" . $date->format('Y/m/d') . "</td>
                <td style='border: 1px solid #edf0f7; padding: 10px;'>" . htmlspecialchars($item['currency'] . number_format(($total_price / $installment_duration), 2, '.', ''), ENT_QUOTES) . "</td>
              </tr>";
                                    }
                                    ?>
                                    <td style="padding: 10px;" colspan='2'> Excludes Tax</td>
                                </table>
                                <br>

                            <?php } ?>
                        </div>
                        <div class="cart_order_summary_footer">
                            <?php
                            $ask_coupon_str = 'Have a coupon?';
                            if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                                $ask_coupon_str = 'Use another coupon instead?';
                                echo '<p class="fw-600">
                                    <i>Coupon applied: ' . $_SESSION['coupon_code'] . '</i>
                                    <small class="remove_coupon">(Remove)</small>
                                </p>';
                            }
                            ?>

                            <p class="fw-600"><?php echo $ask_coupon_str; ?> <span><a href="javascript:void(0)"
                                        class="show_coupon_form">Click
                                        here</a></span></p>
                            <form class="p_relative coupon_form">
                                <input type="text" class="coupon_code" placeholder="Enter coupon code" />
                                <input type="submit" class="apply_coupon" value="Apply">
                            </form>
                            <a href="/checkout" class="proceed_checkout">Proceed to checkout<span
                                    class="dashicons dashicons-arrow-right-alt"></span></a>
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
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/MASTERCARD.png"
                                    alt="MASTERCARD">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/americanexpress.png"
                                    alt="American Express">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/DISCOVER.png" alt="DISCOVER">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/WIRE.png" alt="WIRE">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ESCROW.png" alt="ESCROW">
                            </div>
                        </div>
                    </div>
                <?php
                } ?>
            </div> <!-- Close #cart-page-wrapper -->
        </div> <!-- Close .shopping-cart -->
    <?php
        return ob_get_clean();
    }


    // checkout form shortcode===================================================================
    public function wstr_checkout_page()
    {
        if (!is_user_logged_in()) {
            wp_safe_redirect(home_url('/my-account?reason=checkout'));
        }
        //pricing_plan=5912&override_cart=yes
        $pricing_plan_id = (isset($_GET['pricing_plan']) && !empty($_GET['pricing_plan'])) ? intval($_GET['pricing_plan']) : 0;
        $override_curr_cart = (isset($_GET['override_cart']) && !empty($_GET['override_cart'])) ? sanitize_text_field($_GET['override_cart']) : '';
        // var_dump('1111');
        if ($pricing_plan_id != 0) {
            // var_dump('222');
            $product = get_post($pricing_plan_id);

            if ($product) {
                $plan_title = $product->post_title;
                if (($plan_title == 'Pro Plan' || $plan_title == 'Premium Plan' || $plan_title == 'Plus Plan') && $override_curr_cart === 'yes') {
                    unset($_SESSION['cart']);
                    if (is_user_logged_in()) {
                        update_user_meta(get_current_user_id(), 'user_cart', [$product->ID => ['installment', '1']]);
                    }
                    $_SESSION['cart'] = [
                        $product->ID => ['installment', '1']
                    ];
                }

                if (!empty($_SESSION['cart']) && $override_curr_cart == '') {
                    echo '
                                <div class="confirm-pricing-plan-modal">
                                    <div class="confirm-pricing-plan-modal-content">
                                        <h2> 
                                            Hmmm... seems like you already have items in your cart. 
                                            Please confirm if you want to proceed with the purchase of our ' . $product->post_title . ' plan.
                                            or continue shopping with the existing items in your cart.
                                        </h2>
                                        <div class="confirm-pricing-plan-btns"></div>
                                            <a href="/checkout?pricing_plan=' . $product->ID . '&override_cart=yes" class="btn btn-primary">Continue with pricing plan</a>
                                            <a href="/checkout" class="btn btn-secondary">Continue with shopping</a>
                                        </div>
                                    </div>
                                </div>
                            ';
                }
            }
        }
        ob_start();
        $cart_items = wstr_retrieve_cart_items();
        if (empty($cart_items)) {
            echo '<h3 class="cart-empty-msg error_msg"> <i class="fa-solid fa-circle-info"></i>  Your cart is empty. Please add some items to your cart.</h3>';
            return ob_get_clean();
        }

        if (is_user_logged_in()) {
            if (get_current_user_id()) {
                $user_meta = get_user_meta(get_current_user_id(), 'billing_details', true);
                if (is_array($user_meta) && !empty($user_meta)) {
                    $user_meta = maybe_unserialize($user_meta);

                    $first_name = $user_meta['first_name'] ?: '';
                    $last_name = $user_meta['last_name'] ?: '';
                    $email = $user_meta['email'] ?: '';
                    $phone_country = $user_meta['phone_country'] ?: '';
                    $phone = $user_meta['phone'] ?: '';
                    $city = $user_meta['city'] ?: '';
                    $state = $user_meta['state'] ?: 'Al';
                    $zip = $user_meta['zip'] ?: '';
                    $apt = $user_meta['apt'] ?: '';
                    $notes = $user_meta['notes'] ?: '';
                    $country = $user_meta['notes'] ?: '';
                }
            }
        } else {
            $first_name = '';
            $last_name = '';
            $email = '';
            $phone_country = '';
            $phone = '';
            $city = '';
            $state = 'Al';
            $zip = '';
            $apt = '';
            $notes = '';
            $country = 'US';
        }
    ?>
        <p class="coupon_error_msg tab_w_100">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/coupon_error_info.png" alt="Info Image">
            Please enter a valid coupon code.
        </p>
        <div id="checkout-page-wrappers" class="ws_flex fd_tab_col">

            <div class="cart_carts_lists checkout_details">
                <div class="cart_shopping_cart">
                    <form id="payment-form" class="checkout_details_form">
                        <div class="form-container">
                            <div class="checkout_billing_detail_title fd_mob_col">
                                <h2>Billing Details</h2>
                                <?php if (!is_user_logged_in()) { ?>
                                    <p>If you already have an account please <a href="/register">sign in here</a></p>
                                <?php } ?>
                            </div>

                            <div class="form-group">
                                <label for="first_name">First Name <span class='form_required'>*</span></label>
                                <input type="text" id="billing_first_name" name="billing_first_name"
                                    value="<?php echo esc_attr($first_name); ?>">
                                <span class="billing-err billing-err-first_name"></span>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name <span class='form_required'>*</span></label>
                                <input type="text" id="billing_last_name" name="billing_last_name"
                                    value="<?php echo esc_attr($last_name); ?>">
                                <span class="billing-err billing-err-last_name"></span>
                            </div>

                            <div class="form-group form_group_phone">
                                <input type="hidden" id="billing_saved_country_phone_code"
                                    value="<?php echo esc_attr($phone_country); ?>">
                                <label for="phone">Phone <span class="form_required">*</span></label>
                                <div class="p_relative">
                                    <input type="tel" id="billing_phone" name="billing_phone"
                                        value="<?php echo esc_attr($phone); ?>" placeholder="Enter phone number">
                                    <div class="additional-dropdown-helper-wrapper">
                                        <img class="additional-dropdown-helper"
                                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/chevron-down.svg"
                                            alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address <span class='form_required'>*</span></label>
                                <input type="email" id="billing_email" name="billing_email"
                                    value="<?php echo esc_attr($email); ?>">
                                <span class="billing-err billing-err-email"></span>
                            </div>

                            <div class="form-group">
                                <label for="city">City <span class='form_required'>*</span></label>
                                <input type="text" id="billing_city" name="billing_city" value="<?php echo esc_attr($city); ?>">
                                <span class="billing-err billing-err-city"></span>
                            </div>

                            <div class="form-group">
                                <label for="apt">Apt/Suite (optional)</label>
                                <input type="text" id="billing_apt" name="billing_apt" value="<?php echo esc_attr($apt); ?>"
                                    placeholder="Apartment, suite, unit, etc">
                                <span class="billing-err billing-err-apt"></span>
                            </div>

                            <div class="three-in-row">
                                <div class="form-group">
                                    <label for="state">State <span class='form_required'>*</span></label>
                                    <select id="billing_state" name="billing_state">
                                        <option value="AL" <?php selected('AL', $state); ?>>Alaska</option>
                                        <option value="CA" <?php selected('CA', $state); ?>>California</option>
                                        <option value="NY" <?php selected('NY', $state); ?>>New York</option>
                                        <option value="TX" <?php selected('TX', $state); ?>>Texas</option>
                                    </select>
                                    <span class="billing-err billing-err-state"></span>
                                </div>

                                <div class="form-group">
                                    <label for="zip">ZIP Code <span class='form_required'>*</span></label>
                                    <input type="text" id="billing_zip" name="billing_zip"
                                        value="<?php echo esc_attr($zip); ?>">
                                    <span class="billing-err billing-err-zip"></span>
                                </div>

                                <div class="form-group">
                                    <label for="country">Country/Region <span class='form_required'>*</span></label>
                                    <select id="billing_country" name="billing_country">
                                        <option value="US" <?php selected('US', $country); ?>>USA</option>
                                        <option value="CA" <?php selected('CA', $country); ?>>Canada</option>
                                        <option value="UK" <?php selected('UK', $country); ?>>United Kingdom</option>
                                    </select>
                                    <span class="billing-err billing-err-country"></span>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="order_notes">Order Notes (optional)</label>
                                <textarea id="billing_order_notes" name="billing_order_notes"
                                    rows="6"><?php echo esc_textarea($notes); ?></textarea>
                                <span class="billing-err billing-err-order_notes"></span>
                            </div>
                        </div>
                        <div class="checkout_payment_method">
                            <h2>Payment Method</h2>
                            <div class="wstr_stripe_payment">
                                <div class="wstr_checkout_accordion">
                                    <div class="wstr_checkout_accordion-item">
                                        <div class="wstr_checkout_accordion-header" data-target="#credit-card">
                                            <input type="checkbox" class="wstr_checkout_accordion-checkbox"
                                                id="credit-card-checkbox">
                                            <label for="credit-card-checkbox">Credit Card</label>
                                        </div>
                                        <div class="wstr_checkout_accordion-content" id="credit-card">
                                            <div class="spinner-circle circle-bounce hidden">
                                                <div class="double-bounce1"></div>
                                                <div class="double-bounce2"></div>
                                            </div>
                                            <div id="payment-element">
                                                <!--Stripe.js injects the Payment Element-->
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    // $bank_name = '';
                                    // $bank_account_name = '';
                                    // $bank_account_number = '';
                                    // if (is_user_logged_in()) {
                                    //     $bank_name = get_user_meta(get_current_user_id(), '_bank_name', true);
                                    //     $bank_account_name = get_user_meta(get_current_user_id(), '_bank_account_name', true);
                                    //     $bank_account_number = get_user_meta(get_current_user_id(), '_bank_account_number', true);
                                    // }
                                    $cart = $_SESSION['cart'];
                                    $hasSubscription = false;
                                    foreach ($cart as $item) {
                                        if ($item[0] === 'installment') {
                                            $hasSubscription = true;
                                        }
                                    }
                                    ?>
                                    <?php if (!$hasSubscription) { ?>
                                        <div class="wstr_checkout_accordion-item">
                                            <div class="wstr_checkout_accordion-header" data-target="#bank-details">
                                                <input type="checkbox" class="wstr_checkout_accordion-checkbox"
                                                    id="bank-details-checkbox">
                                                <label for="bank-details-checkbox">Bank Transfer</label>
                                            </div>
                                            <div class="wstr_checkout_accordion-content" id="bank-details">
                                                <!-- <div class="form-group">
                                                    <label for="bank_name">Bank Name <span class='form_required'>*</span></label>
                                                    <input type="text" id="bank_name" name="bank_name"
                                                        value="<?php //echo esc_attr($bank_name); 
                                                                ?>">
                                                    <span class="billing-err bank-err-bank_name"></span>
                                                    <br>

                                                    <label for="account_name">Account Name <span
                                                            class='form_required'>*</span></label>
                                                    <input type="text" id="bank_account_name" name="bank_account_name"
                                                        value="<?php //echo esc_attr($bank_account_name); 
                                                                ?>">
                                                    <span class="billing-err bank-err-bank_account_name"></span>
                                                    <br>

                                                    <label for="account_number">Account Number <span
                                                            class='form_required'>*</span></label>
                                                    <input type="text" id="bank_account_number" name="bank_account_number"
                                                        value="<?php //echo esc_attr($bank_account_number); 
                                                                ?>">
                                                    <span class="billing-err bank-err-bank_account_number"></span>
                                                </div> -->
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="checkout_payment_footer fd_mob_col">
                                    <div class="checkout_save_account">
                                        <label for="save_account">
                                            <input type="checkbox" name="save_account" id="save_account" />
                                            Securely Save to Account
                                        </label>
                                    </div>

                                    <div class="cart_order_payment_methods checkout_accepted_payments">
                                        <p>Accepted Payments</p>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/visa.png"
                                            alt="VISA">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/MASTERCARD.png"
                                            alt="MASTERCARD">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/americanexpress.png"
                                            alt="American Express">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/DISCOVER.png"
                                            alt="DISCOVER">

                                    </div>
                                </div>
                                <h6>
                                    <i class="fa-solid fa-lock"></i>
                                    This site is protected by reCAPTCHA and the Google <a href="/privacy-policy"
                                        target="_blank">Privacy
                                        Policy</a> and <a href="/terms-conditions" target="_blank">Terms of Service</a> apply.
                                </h6>
                            </div>
                            <div class="check_terms_payment">
                                <label for="terms">
                                    <input type="checkbox" name="terms" id="terms" />
                                    I have read and agree to the website
                                    <a href="/terms-conditions/" target="_blank">terms and conditions</a>
                                    <span class="check_required">*</span>
                                </label>
                            </div>
                            <button id="submit" style="display: none;">
                                <div class="spinner-circle-submit circle-bounce hidden">
                                    <div class="double-bounce1"></div>
                                    <div class="double-bounce2"></div>
                                </div>

                                <span id="button-text">Place Order</span>
                                <i class="fa-solid fa-arrow-right-long"></i>
                            </button>
                            <div id="payment-message" class="hidden"></div>

                    </form>
                    <!-- <div id="dpm-annotation">
                        <p class="ws_text_center">
                            Payment methods are dynamically displayed based on customer location, order amount, and
                            currency.&nbsp;
                            <a href="#" target="_blank" rel="noopener noreferrer" id="dpm-integration-checker">Preview
                                payment
                                methods by transaction</a>
                        </p>
                    </div> -->
                </div>

            </div>
        </div>
        <div class="cart_order_sumamry checkout_order_summary">
            <h2>Your Order</h2>
            <?php
            $total_price = 0;
            $currency_symbol = '';
            $installment_duration = 1;
            $actual_price = 0;
            foreach ($cart_items as $item) {
                $installment_duration_str = '';
                if ($item['payment_option'] === 'installment') {
                    $installment_duration = $item['installment_duration'];
                    $installment_duration_str = '/' . $item['installment_duration'] . ' months';
                    $final_item_price = number_format((float) ($item['price'] / (int) $installment_duration), 2, '.', '');
                    $actual_price = $final_item_price;
                } else {
                    $final_item_price = number_format((float) ($item['price']), 2, '.', '');
                    $actual_price += $final_item_price;
                }
                $currency_symbol = $item['currency'];

            ?>
                <div class="checkout_order_product_outline">
                    <div class="cart_shopping_cart_content fd_mob_col checkout_order_detail">
                        <div class="cart_shopping_cart_content_left">
                            <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>"
                                alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?>">
                        </div>
                        <div class="cart_shopping_cart_content_right checkout_order_detail_content">
                            <span>
                                <h4>Payment Plan</h4>
                                <p>Payment:
                                    <?php echo htmlspecialchars($item['payment_option'], ENT_QUOTES) . $installment_duration_str; ?>
                                </p>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="cart_order_summary_first">
                    <span class="ws_flex">
                        <h4><?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?></h4>
                        <p><?php echo htmlspecialchars($currency_symbol . $final_item_price, ENT_QUOTES); ?></p>
                    </span>
                </div>


            <?php } ?>


            <div class="cart_order_summary_first">
                <span class="ws_flex">
                    <p>Subtotal</p>
                    <p><?php echo htmlspecialchars($currency_symbol . number_format($actual_price, 2, '.', ''), ENT_QUOTES); ?>
                    </p>
                </span>
                <span class="ws_flex checkout_discount_amount">
                    <p>Discount</p>
                    <p>
                        <?php
                        $discount_amount = 0;
                        if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                            $discount_percent = (float) $_SESSION['coupon_code_discount_percentage'];
                            $discount_amount = $actual_price * ($discount_percent / 100);
                        }
                        echo htmlspecialchars($currency_symbol . $discount_amount, ENT_QUOTES);
                        ?>
                    </p>
                </span>
            </div>
            <div class="cart_order_summary_total">
                <span class="ws_flex">
                    <h4>Total Due Today</h4>
                    <h4>
                        <?php
                        if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                            $discount_percent = $_SESSION['coupon_code_discount_percentage'];
                            $actual_price = $actual_price - ($actual_price * ($discount_percent / 100));
                        }
                        echo htmlspecialchars($currency_symbol . number_format($actual_price, 2, '.', ''), ENT_QUOTES);
                        ?>
                    </h4>
                </span>
                <?php
                if ($installment_duration > 1) { ?>

                    <span class="ws_flex">
                        <p>
                            Future Payments<span class="dashicons dashicons-info margin_y_0 ml_10"></span>

                        </p>
                        <p>Remaining:
                            <?php echo htmlspecialchars($currency_symbol . number_format(($actual_price * ($installment_duration - 1)), 2, '.', ''), ENT_QUOTES); ?>
                        </p>
                    </span>
                    <table class="future_payment_table" style="border-collapse: collapse; width: 100%; border: 1px solid #edf0f7;">
                        <tr style="background-color: #f9f9f9;">
                            <th
                                style="border: 1px solid #edf0f7; padding: 10px; text-align: left; font-weight:500; background-color:#f6f8fe;">
                                Date
                            </th>
                            <th
                                style="border: 1px solid #edf0f7; padding: 10px; text-align: left; font-weight:500;background-color:#f6f8fe;">
                                Amount
                            </th>
                        </tr>
                        <?php
                        for ($i = 1; $i <= ($installment_duration - 1); $i++) {
                            $date = (new DateTime())->modify("+$i month");
                            echo "<tr>
                <td style='border: 1px solid #edf0f7; padding: 10px;'>" . $date->format('Y/m/d') . PHP_EOL . "</td>
                <td style='border: 1px solid #edf0f7; padding: 10px;'>" . htmlspecialchars($currency_symbol . number_format($actual_price, 2, '.', ''), ENT_QUOTES) . "</td>
              </tr>";
                        }
                        ?>
                        <td style="padding: 10px;" colspan='2'> Excludes Tax</td>
                    </table>
                <?php } ?>
            </div>
            <div class="cart_order_summary_footer">
                <?php
                $ask_coupon_str = 'Have a coupon?';
                if (isset($_SESSION['coupon_code']) && !empty($_SESSION['coupon_code'])) {
                    $ask_coupon_str = 'Use another coupon instead?';
                    echo '<p class="fw-600">
                        <i>Coupon applied: ' . $_SESSION['coupon_code'] . '</i>
                        <small class="remove_coupon">(Remove)</small>
                    </p>';
                }
                ?>
                <p class="fw-600"><?php echo $ask_coupon_str; ?> <span><a href="javascript:void(0)"
                            class="show_coupon_form">Click
                            here</a></span></p>
                <form class="p_relative coupon_form">
                    <input type="text" class="coupon_code" placeholder="Enter coupon code" />
                    <input type="submit" class="apply_coupon" value="Apply">
                </form>

            </div>
        </div>
        </div>
        </div>
    <?php
        return ob_get_clean();
    }


    private function handle_payment_success()
    {
        $output = [];

        try {
            // Retrieve the payment/subscription status from URL parameters
            $payment_intent_id = isset($_GET['payment_intent']) ? sanitize_text_field($_GET['payment_intent']) : null;

            if ($payment_intent_id) {
                // Handle one-time payment
                $payment_intent = $this->stripe->paymentIntents->retrieve($payment_intent_id);

                if ($payment_intent->status === 'succeeded') {

                    $is_subscription = (isset($payment_intent['description']) && $payment_intent['description'] === 'Subscription creation') ? true : false;
                    $metadata = $payment_intent->metadata;
                    if (!$is_subscription) {
                        $products_count = isset($metadata['products_count']) ? (int) $metadata['products_count'] : 0;
                        $checkout_currency_symbol = '';
                        for ($i = 0; $i < $products_count; $i++) {
                            $prefix = "product_{$i}_";
                            $checkout_currency = $metadata[$prefix . 'currency_checkout'];
                            $checkout_currency_symbol = $metadata[$prefix . 'currency_symbol_checkout'];
                        }

                        $output = [
                            'status' => 'success',
                            'type' => 'payment',
                            'message' => 'Your payment was successfull!',
                            'amount' => $metadata['total_amount_checkout_currency'],
                            'currency' => $checkout_currency,
                            'currency_symbol' => $checkout_currency_symbol
                        ];
                    } else {

                        $output = [
                            'status' => 'success',
                            'metadata' => $metadata,
                            'type' => 'subscription',
                            'message' => 'Your installment payment was successfull!',
                            'amount' => $metadata['total_amount_checkout_currency'],
                            'currency' => $metadata['product_currency_checkout'],
                            'currency_symbol' => $metadata['product_currency_symbol_checkout'],
                            'interval' => (int) $metadata['total_installments'] - 1,
                        ];
                    }

                    unset($_SESSION['cart']);
                } else {
                    $output = [
                        'status' => 'error',
                        'message' => 'Payment was not successful. Please try again.'
                    ];
                }
            }
        } catch (Exception $e) {
            $output = [
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return $output;
    }

    public function wstr_payment_success_page()
    {
        ob_start();
        $payment_result = [];
        if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
            $order_id = intval($_GET['order_id']);
            $order = get_post($order_id);
            $order_type = '';
            if ($order) {
                $order_type = get_post_meta($order_id, '_order_type', true);
                $total_installments = '';
                if ($order_type === 'installment') {
                    $order_type = 'subscription';
                    $total_installments = get_post_meta($order_id, '_total_installments', true);
                    $total_installments = $total_installments == 'indefinite' ? 'indefinite' : ((int) $total_installments - 1);
                }

                $order_status = get_post_meta($order_id, '_order_status', true);
                $message = '';
                if ($order_status === 'success') {
                    $message = 'Your installment payment was successfull!';
                } else if ($order_status === 'pending') {
                    $message = 'Your order has been registered successfully !';
                }

                $payment_result = [
                    'status' => $order_status,
                    'metadata' => [],
                    'type' => $order_type,
                    'message' => $message,
                    'amount' => get_post_meta($order_id, '_order_total', true),
                    'currency' => get_post_meta($order_id, '_currency', true),
                    'currency_symbol' => get_post_meta($order_id, '_currency_symbol', true),
                    'interval' => $total_installments
                ];

                unset($_SESSION['cart']);
            }
        } else if (isset($_GET['payment_intent']) && !empty($_GET['payment_intent'])) {
            $payment_result = $this->handle_payment_success();
        }

    ?>
        <div class="payment-success-container">
            <?php if ($payment_result['status'] === 'success' || $payment_result['status'] === 'pending') { ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <h1>Thank You!</h1>
                    <p><?php echo esc_html($payment_result['message']); ?></p>

                    <div class="payment-details">
                        <h2>Payment Details</h2>
                        <h2>Amount: <?php echo esc_html($payment_result['currency_symbol']); ?>
                            <?php echo esc_html($payment_result['amount']); ?>
                        </h2>
                        <?php if ($payment_result['type'] === 'subscription' && $payment_result['status'] === 'success') { ?>
                            <p>You will be charged the above mentioned amount MONTHLY for the next
                                <?php echo esc_html($payment_result['interval']); ?> months
                            </p>
                        <?php } else if ($payment_result['type'] === 'subscription' && $payment_result['status'] === 'pending') { ?>
                            <p>You will be contacted soon about your subscription.</p>
                        <?php } ?>
                    </div>

                    <div class="next-steps">
                        <h3>Next Steps</h3>

                        <?php
                        if ($order_status === 'success') {
                            $next_step_msg = 'You will receive a confirmation email shortly.';
                        } else if ($order_status === 'pending') {
                            $next_step_msg = 'You will be contacted soon by our admin.';
                        }
                        ?>

                        <p><?php echo esc_html($next_step_msg); ?></p>

                        <?php if ($payment_result['type'] === 'subscription' && $payment_result['status'] === 'success') { ?>
                            <p>You can manage your subscription from your account dashboard.</p>
                        <?php } ?>
                    </div>

                    <div class="action-buttons">
                        <a href="<?php echo esc_url(home_url('/my-account')); ?>" class="button">Go to My Account</a>
                        <a href="<?php echo esc_url(home_url('/buy')); ?>" class="button secondary">Continue Shopping</a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <h1>Oops!</h1>
                    <p><?php echo esc_html($payment_result['message']); ?></p>

                    <div class="action-buttons">
                        <a href="<?php echo esc_url(home_url('/faq')); ?>" class="button secondary">FAQ</a>
                    </div>
                </div>
            <?php } ?>
        </div>

<?php

        return ob_get_clean();
    }
}
new wstr_ecommerce_shortcodes();
