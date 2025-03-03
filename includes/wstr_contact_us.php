<?php
class wstr_contact_us
{
    private function __construct()
    {
        add_shortcode('contact_form', [$this, 'wstr_contact_form']);
        add_action('init', [$this, 'wstr_handle_contact_form_submission']);
    }

    public static function init()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
    }

    public function wstr_handle_contact_form_submission()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['contact_us_submit'])) {
            return;
        }

        $error = '';
        $success = '';

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
                    // $secret = get_option(option: 'recaptcha_secret_key');
                    // $recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';

                    // if (empty($recaptcha_response)) {
                    //     $error = "Please complete the reCAPTCHA.";
                    // } else {
                    //     $verify_url = "https://www.google.com/recaptcha/api/siteverify";
                    //     $response = wp_remote_post($verify_url, [
                    //         'body' => [
                    //             'secret' => $secret,
                    //             'response' => $recaptcha_response,
                    //             'remoteip' => $_SERVER['REMOTE_ADDR']
                    //         ]
                    //     ]);

                    //     if (is_wp_error($response)) {
                    //         $error = "Unable to verify reCAPTCHA. Please try again.";
                    //     } else {
                    // $response_data = json_decode(wp_remote_retrieve_body($response));

                    // if (!$response_data->success) {
                    //     $error = "reCAPTCHA verification failed. Please try again.";
                    // } else {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'contact_us';
                    $name = sanitize_text_field($_POST['first_name']) . ' ' . sanitize_text_field($_POST['last_name']);
                    $email = sanitize_email($_POST['email']);

                    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(substr(strrchr($email, "@"), 1), "MX")) {
                        $error = "Invalid email address.";
                    } else {
                        if ($email !== 'ericjonesmyemail@gmail.com') {
                            $phone = sanitize_text_field($_POST['phone']);
                            $type = sanitize_text_field($_POST['inquiry_type']);
                            $message = sanitize_textarea_field($_POST['message']);
                            $time = current_time('mysql');

                            // Spam keywords check
                            $spam_keywords = ['casino', 'loan', 'viagra', 'click here', 'www.', 'buy now', 'limited time offer', 'urgent:', 'quick cash'];
                            foreach ($spam_keywords as $keyword) {
                                if (stripos($message, $keyword) !== false) {
                                    $error = "Message contains suspicious content.";
                                    break;
                                }
                            }

                            // Phone number validation
                            if (!$error && (strlen($phone) > 15 || strlen($phone) < 7)) {
                                $error = "Phone number is not valid.";
                            }

                            if (!$error) {
                                // Rate limiting increment
                                set_transient($transient_key, ($attempts ?: 0) + 1, DAY_IN_SECONDS);

                                // Secure SQL Insert with Prepared Statements
                                $insert = $wpdb->query(
                                    $wpdb->prepare(
                                        "INSERT INTO $table_name (name, email, phone, type, message, time) VALUES (%s, %s, %s, %s, %s, %s)",
                                        $name,
                                        $email,
                                        $phone,
                                        $type,
                                        $message,
                                        $time
                                    )
                                );

                                if (!$insert) {
                                    $error = "Something went wrong. Please try again later.";
                                } else {
                                    $this->wstr_send_contact_email($name, $email, $phone, $type, $message, $time);
                                    $success = "Your message has been sent successfully.";
                                }
                            }
                        }
                        // }
                        //     }
                        // }
                    }
                }
            }
        }

        // Store messages in a session variable to display after redirect
        set_transient('wstr_contact_form_message', ['error' => $error, 'success' => $success], 60);

        // Redirect to prevent duplicate submissions
        wp_safe_redirect(esc_url($_SERVER['REQUEST_URI']));
        exit;
    }
    public function wstr_send_contact_email($name, $email, $phone, $type, $message, $time)
    {
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
        $admin_email = 'losiwe8210@jarars.com';

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
    }


    public function wstr_contact_form()
    {
        ob_start();

        // Retrieve messages from transient storage
        $messages = get_transient('wstr_contact_form_message');
        delete_transient('wstr_contact_form_message'); // Clear message after displaying
        $error = $messages['error'] ?? '';
        $success = $messages['success'] ?? '';

?>
        <div class="contact-form-wrapper">
            <h2>Get In Touch</h2>
            <p class="sub-title">Have any questions? Don't hesitate to contact us!</p>
            <p class="small-subtitle"><sup>"*" indicates required fields</sup></p>
            <div class="error_msg"><?php echo $error ?: '' ?> </div>
            <div class="success_msg"><?php echo $success ?: '' ?> </div>
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
                // $site = get_option('recaptcha_site_key');
                ?>
                <!-- <div class="form-group captcha">
                    <div class="g-recaptcha" data-sitekey="<?php //echo $site ?: '' 
                                                            ?>"></div>
                </div> -->
                <?php wp_nonce_field('contact_form_nonce_action', 'contact_form_nonce'); ?>
                <button type="submit" class="hover-white" name="contact_us_submit">Submit</button>
            </form>
        </div>


<?php

        return ob_get_clean();
    }
}

wstr_contact_us::init();
