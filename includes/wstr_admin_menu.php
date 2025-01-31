<?php

/**
 * For creating menu setting on wordpress backend
 */
class Wstr_admin_menu
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'wstr_menu'));
    }

    /** Step 1. */
    function wstr_menu()
    {
        add_menu_page('Webstarter Menu', 'Webstarter Menu', 'manage_options', 'wstr-menu', array($this, 'wstr_menu_options'));
        add_menu_page('Contact Us', 'Contact Us', 'manage_options', 'contact-us', array($this, 'wstr_contact_us'), 'dashicons-testimonial');
    }

    /** Step 3. */
    function wstr_menu_options()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (isset($_POST['currencies'])) {
            $currencies = array_map('sanitize_text_field', $_POST['currencies']);
            update_option('wstr_currency_codes', $currencies);
        }
?>
        <div class="">
            <button class="" onclick="openCity('wstrCurrency')">Muti Currency</button>
            <button class="" onclick="openCity('Paris')">Paris</button>
            <button class="" onclick="openCity('Tokyo')">Tokyo</button>
        </div>
        <div id="wstrCurrency" class="wstr-menu">
            <form method="post">
                <h4>Multi Currency Settings</h4>
                <div id="selectCurrency">
                    <label for="currency">Select Currency</label>
                    <select id="currencyList" name="currencies[]" multiple="multiple">

                        <?php
                        $saved_currencies = get_option('wstr_currency_codes', []);
                        $currencies_list = [
                            "USD" => "US dollar",
                            "EUR" => "Euro",
                            "JPY" => "Japanese yen",
                            "GBP" => "Pound sterling",
                            "AED" => "United Arab Emirates dirham",
                            "AFN" => "Afghan afghani",
                            "ALL" => "Albanian lek",
                            "AMD" => "Armenian dram",
                            "ANG" => "Netherlands Antillean guilder",
                            "AOA" => "Angolan kwanza",
                            "ARS" => "Argentine peso",
                            "AUD" => "Australian dollar",
                            "AWG" => "Aruban florin",
                            "AZN" => "Azerbaijani manat",
                            "BAM" => "Bosnia and Herzegovina convertible mark",
                            "BBD" => "Barbadian dollar",
                            "BDT" => "Bangladeshi taka",
                            "BGN" => "Bulgarian lev",
                            "BHD" => "Bahraini dinar",
                            "BIF" => "Burundian franc",
                            "BMD" => "Bermudian dollar",
                            "BND" => "Brunei dollar",
                            "BOB" => "Bolivian boliviano",
                            "BRL" => "Brazilian real",
                            "BSD" => "Bahamian dollar",
                            "BTN" => "Bhutanese ngultrum",
                            "BWP" => "Botswana pula",
                            "BYN" => "Belarusian ruble",
                            "BZD" => "Belize dollar",
                            "CAD" => "Canadian dollar",
                            "CDF" => "Congolese franc",
                            "CHF" => "Swiss franc",
                            "CLP" => "Chilean peso",
                            "CNY" => "Chinese yuan",
                            "COP" => "Colombian peso",
                            "CRC" => "Costa Rican colón",
                            "CUP" => "Cuban peso",
                            "CVE" => "Cape Verdean escudo",
                            "CZK" => "Czech koruna",
                            "DJF" => "Djiboutian franc",
                            "DKK" => "Danish krone",
                            "DOP" => "Dominican peso",
                            "DZD" => "Algerian dinar",
                            "EGP" => "Egyptian pound",
                            "ERN" => "Eritrean nakfa",
                            "ETB" => "Ethiopian birr",
                            "FJD" => "Fijian dollar",
                            "FKP" => "Falkland Islands pound",
                            "FOK" => "Faroese króna",
                            "GEL" => "Georgian lari",
                            "GGP" => "Guernsey pound",
                            "GHS" => "Ghanaian cedi",
                            "GIP" => "Gibraltar pound",
                            "GMD" => "Gambian dalasi",
                            "GNF" => "Guinean franc",
                            "GTQ" => "Guatemalan quetzal",
                            "GYD" => "Guyanese dollar",
                            "HKD" => "Hong Kong dollar",
                            "HNL" => "Honduran lempira",
                            "HRK" => "Croatian kuna",
                            "HTG" => "Haitian gourde",
                            "HUF" => "Hungarian forint",
                            "IDR" => "Indonesian rupiah",
                            "ILS" => "Israeli new shekel",
                            "IMP" => "Isle of Man pound",
                            "INR" => "Indian rupee",
                            "IQD" => "Iraqi dinar",
                            "IRR" => "Iranian rial",
                            "ISK" => "Icelandic króna",
                            "JEP" => "Jersey pound",
                            "JMD" => "Jamaican dollar",
                            "JOD" => "Jordanian dinar",
                            "KES" => "Kenyan shilling",
                            "KGS" => "Kyrgyzstani som",
                            "KHR" => "Cambodian riel",
                            "KID" => "Kiribati dollar",
                            "KMF" => "Comorian franc",
                            "KRW" => "South Korean won",
                            "KWD" => "Kuwaiti dinar",
                            "KYD" => "Cayman Islands dollar",
                            "KZT" => "Kazakhstani tenge",
                            "LAK" => "Lao kip",
                            "LBP" => "Lebanese pound",
                            "LKR" => "Sri Lankan rupee",
                            "LRD" => "Liberian dollar",
                            "LSL" => "Lesotho loti",
                            "LYD" => "Libyan dinar",
                            "MAD" => "Moroccan dirham",
                            "MDL" => "Moldovan leu",
                            "MGA" => "Malagasy ariary",
                            "MKD" => "Macedonian denar",
                            "MMK" => "Burmese kyat",
                            "MNT" => "Mongolian tögrög",
                            "MOP" => "Macanese pataca",
                            "MRU" => "Mauritanian ouguiya",
                            "MUR" => "Mauritian rupee",
                            "MVR" => "Maldivian rufiyaa",
                            "MWK" => "Malawian kwacha",
                            "MXN" => "Mexican peso",
                            "MYR" => "Malaysian ringgit",
                            "MZN" => "Mozambican metical",
                            "NAD" => "Namibian dollar",
                            "NGN" => "Nigerian naira",
                            "NIO" => "Nicaraguan córdoba",
                            "NOK" => "Norwegian krone",
                            "NPR" => "Nepalese rupee",
                            "NZD" => "New Zealand dollar",
                            "OMR" => "Omani rial",
                            "PAB" => "Panamanian balboa",
                            "PEN" => "Peruvian sol",
                            "PGK" => "Papua New Guinean kina",
                            "PHP" => "Philippine peso",
                            "PKR" => "Pakistani rupee",
                            "PLN" => "Polish złoty",
                            "PYG" => "Paraguayan guaraní",
                            "QAR" => "Qatari riyal",
                            "RON" => "Romanian leu",
                            "RSD" => "Serbian dinar",
                            "RUB" => "Russian ruble",
                            "RWF" => "Rwandan franc",
                            "SAR" => "Saudi riyal",
                            "SBD" => "Solomon Islands dollar",
                            "SCR" => "Seychellois rupee",
                            "SDG" => "Sudanese pound",
                            "SEK" => "Swedish krona",
                            "SGD" => "Singapore dollar",
                            "SHP" => "Saint Helena pound",
                            "SLL" => "Sierra Leonean leone",
                            "SOS" => "Somali shilling",
                            "SRD" => "Surinamese dollar",
                            "SSP" => "South Sudanese pound",
                            "STN" => "São Tomé and Príncipe dobra",
                            "SYP" => "Syrian pound",
                            "SZL" => "Eswatini lilangeni",
                            "THB" => "Thai baht",
                            "TJS" => "Tajikistani somoni",
                            "TMT" => "Turkmenistan manat",
                            "TND" => "Tunisian dinar",
                            "TOP" => "Tongan paʻanga",
                            "TRY" => "Turkish lira",
                            "TTD" => "Trinidad and Tobago dollar",
                            "TVD" => "Tuvaluan dollar",
                            "TZS" => "Tanzanian shilling",
                            "UAH" => "Ukrainian hryvnia",
                            "UGX" => "Ugandan shilling",
                            "UYU" => "Uruguayan peso",
                            "UZS" => "Uzbekistani som",
                            "VES" => "Venezuelan bolívar",
                            "VND" => "Vietnamese đồng",
                            "VUV" => "Vanuatu vatu",
                            "WST" => "Samoan tālā",
                            "XAF" => "Central African CFA franc",
                            "XCD" => "East Caribbean dollar",
                            "XOF" => "West African CFA franc",
                            "XPF" => "CFP franc",
                            "YER" => "Yemeni rial",
                            "ZAR" => "South African rand",
                            "ZMW" => "Zambian kwacha",
                            "ZWL" => "Zimbabwean dollar"
                        ];

                        foreach ($currencies_list as $value => $label) {
                            // Check if the current option is in the saved currencies array
                            $selected = in_array($value, $saved_currencies) ? 'selected="selected"' : '';
                            echo "<option value='{$value}' {$selected} label='{$label}'>{$value}</option>";
                        }
                        ?>
                    </select>
                    <?php
                    if ($saved_currencies) {
                        $access_key = 'cur_live_RFDFd4STzeV5MnBBE3MFokvZmnaKEWpfAB1wT1iP';
                        // Build the symbols query for the API request
                        $symbols = implode(',', $saved_currencies);

                        $response = wp_remote_get('https://api.currencyapi.com/v3/latest?apikey=' . $access_key . '&currencies=' . $symbols);

                        if (is_wp_error($response)) {
                            // Handle the error
                            $error_message = $response->get_error_message();
                            echo "Something went wrong: $error_message";
                        } else {
                            $body = wp_remote_retrieve_body($response);
                            $data = json_decode($body, true);

                            if (isset($data['data'])) {
                                // Prepare an array to store the exchange rates
                                $currency_rates = [];

                                // Loop through each currency and get the rate
                                foreach ($saved_currencies as $currency) {
                                    if (isset($data['data'][$currency])) {
                                        $currency_rates[$currency] = $data['data'][$currency]['value'];
                                    }
                                }

                                // Save the updated rates to the options table
                                update_option('wstr_currency_rates', $currency_rates);
                                error_log('Currency rates updated successfully.');
                            } else {
                                error_log('Failed to retrieve currency data.');
                            }
                        }
                    }
                    ?>
                </div>
                <input type="submit" value="Add Currency">
            </form>
        </div>

        <div id="Paris" class="wstr-menu" style="display:none">
            <h2>Paris</h2>
            <p>Paris is the capital of France.</p>
        </div>

        <div id="Tokyo" class="wstr-menu" style="display:none">
            <h2>Tokyo</h2>
            <p>Tokyo is the capital of Japan.</p>
        </div>


    <?php


    }

    /**
     * callback function for contact us
     */

    function wstr_contact_us()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;

        // Define the table name
        $contact_us = $wpdb->prefix . 'contact_us';

        // Set the number of items per page
        $items_per_page = 10;

        // Get the current page from the URL, default to 1 if not set
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

        // Calculate the offset for the query
        $offset = ($current_page - 1) * $items_per_page;

        // Get the total number of rows
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $contact_us");

        // Calculate the total number of pages
        $total_pages = ceil($total_items / $items_per_page);

        // Fetch the data for the current page
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $contact_us ORDER BY ID DESC LIMIT %d OFFSET %d",
                $items_per_page,
                $offset
            )
        );
    ?>
        <div class="main_contact_us">
            <h3>Contact Us</h3>
            <table border="1" class="widefat" id="contact-us-form">
                <thead>
                    <th>S.N</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Date</th>
                </thead>
                <tbody>
                    <?php
                    $i = $offset + 1; // Adjust the numbering based on the page
                    foreach ($result as $contact_details) {
                        $type = $contact_details->type;
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo esc_html($contact_details->name); ?></td>
                            <td><?php echo esc_html($contact_details->email); ?></td>
                            <td><?php echo esc_html($contact_details->phone); ?></td>
                            <td>
                                <?php
                                switch ($type) {
                                    case 'domain':
                                        echo 'Domain Inquiry';
                                        break;
                                    case 'technical':
                                        echo 'Technical Support';
                                        break;
                                    case 'general':
                                        echo 'General Questions';
                                        break;
                                    case 'billing':
                                        echo 'Billing & Payments';
                                        break;
                                    default:
                                        echo 'Others';
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html($contact_details->message); ?></td>
                            <td><?php echo esc_html($contact_details->time); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-contact-us">
                <?php
                $base_url = admin_url('admin.php?page=contact-us'); // Replace with your actual page slug
                if ($total_pages > 1) {
                    for ($page = 1; $page <= $total_pages; $page++) {
                        if ($page == $current_page) {
                            echo '<strong>' . $page . '</strong> ';
                        } else {
                            echo '<a href="' . esc_url(add_query_arg('paged', $page, $base_url)) . '">' . $page . '</a> ';
                        }
                    }
                }
                ?>
            </div>
        </div>
    <?php
    }
}

new Wstr_admin_menu();


/**
 * Class for getting user bank details to the user Edit page
 */
class Wstr_get_user_detials
{
    function __construct()
    {
        // add_action('admin_menu', array($this, 'extra_user_profile_fields'));
        if (is_admin()) {
            add_action('show_user_profile', array($this, 'get_user_wallet_banking'));
            add_action('edit_user_profile', array($this, 'get_user_wallet_banking'));
        }
    }



    public function get_user_wallet_banking($user)
    { ?>

        <?php
        $bank_name =  get_user_meta($user->ID, '_bank_name', true);
        $account_number =  get_user_meta($user->ID, '_bank_account_number', true);
        $account_name =  get_user_meta($user->ID, '_bank_account_name', true);
        $bank_state =  get_user_meta($user->ID, '_bank_state', true);
        $bank_city =  get_user_meta($user->ID, '_bank_city', true);
        $bank_swift_code =  get_user_meta($user->ID, '_bank_swift_code', true);
        ?>
        <h3><?php _e("Bank Information", "blank"); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="address"><?php _e("Bank Name"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $bank_name ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
            <tr>
                <th><label for="city"><?php _e("Account Number"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $account_number ?: ''; ?>" class="regular-text" readonly="readonly" />
                    <!-- <br /> -->
                    <!-- <span class="description"><?php //_e("Please enter your city."); 
                                                    ?></span> -->
                </td>
            </tr>
            <tr>
                <th><label for="accountname"><?php _e("Account Name"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $account_name ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
            <tr>
                <th><label for="bankstate"><?php _e("Bank State"); ?></label></th>

                <td>
                    <input type="text" value="<?php echo $bank_state ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
            <tr>
                <th><label for="bankcity"><?php _e("Bank City"); ?></label></th>

                <td>
                    <input type="text" value="<?php echo $bank_city ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
            <tr>
                <th><label for="swiftcode"><?php _e("Bank Swift Code"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $bank_swift_code ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
        </table>

        <?php
        $paypal_email = get_user_meta($user->ID, '_paypal_email', true);
        ?>
        <h3><?php _e("Paypal Information", "blank"); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="paypalemail"><?php _e("Paypal Email"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $paypal_email ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
        </table>

        <?php
        $crypto_wallet_id = get_user_meta($user->ID, '_crypto_wallet_id', true);
        ?>
        <h3><?php _e("Crypto Information", "blank"); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="walletid"><?php _e("Wallet ID"); ?></label></th>
                <td>
                    <input type="text" value="<?php echo $crypto_wallet_id ?: ''; ?>" class="regular-text" readonly="readonly" />
                </td>
            </tr>
        </table>
<?php }
}


new Wstr_get_user_detials();
