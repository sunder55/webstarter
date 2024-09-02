<?php

/**
 * Classes used for creating meta boxes to the custom post type.
 */

/**
 * For creating meta boxes to the domain post type
 */
class wstr_domain_meta_boxes
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_domain_meta_boxes'));
        add_action('save_post', array($this, 'save_domain_meta_boxes'));
    }

    public function add_domain_meta_boxes()
    {
        add_meta_box(
            'domain_fields',
            __('Domain Fields', 'webstarter'),
            array($this, 'render_domain_fields_meta_box'),
            'domain', // The custom post type slug
            'normal',
            'default'
        );

        add_meta_box(
            'domain_data',
            __('Domain Data', 'webstarter'),
            array($this, 'render_domain_data_fields_meta_box'),
            'domain', // The custom post type slug
            'side',
            'core'
        );
    }

    public function render_domain_fields_meta_box($post)
    {
        // Add nonce for security and authentication
        wp_nonce_field('domain_fields_nonce_action', 'domain_fields_nonce');

        // Retrieve existing value from the database if available
        $highlight_title = get_post_meta($post->ID, '_highlight_title', true);
        $highlight_content = get_post_meta($post->ID, '_highlight_content', true);
        $age = get_post_meta($post->ID, '_age', true);
        $length = get_post_meta($post->ID, '_length', true);
        $da_pa = get_post_meta($post->ID, '_da_pa', true);
        $seo_rating = get_post_meta($post->ID, '_seo_rating', true);

        $pronounce_audio_id = get_post_meta($post->ID, '_pronounce_audio', true);
        $pronounce_audio_url = wp_get_attachment_url($pronounce_audio_id);

        $logo_image_id = get_post_meta($post->ID, '_logo_image', true);
        $logo_image_url = wp_get_attachment_url($logo_image_id);

        // Retrieve TLD selection if available
        $domain_tld = get_post_meta($post->ID, '_tld', true);
?>
        <div class="domainFields widefat">
            <div class="domainHighlightTitle">
                <label><?php _e('Highlight Title'); ?></label>
                <input type="text" name="highlight_title" class="widefat" value="<?php echo esc_attr($highlight_title); ?>">
            </div>
            <div class="domainHighlightContent">
                <label><?php _e('Highlight Content'); ?></label>
                <textarea name="highlight_content" class="widefat"><?php echo esc_textarea($highlight_content); ?></textarea>
            </div>
            <div class="domainAge">
                <label><?php _e('Age'); ?></label>
                <input type="text" name="age" id="domainAge" class="widefat" value="<?php echo esc_attr($age); ?>">
            </div>
            <div class="domainLength">
                <label class="wstr-mandatory"><?php _e('Length'); ?></label>
                <input type="number" name="length" id="domainLength" class="widefat" value="<?php echo esc_attr($length); ?>">
            </div>
            <div id="domainTld">
                <label><?php _e('TLD'); ?></label>
                <select name="tld" class="widefat">
                    <option value=".com" <?php selected($domain_tld, '.com'); ?>><?php _e('.com'); ?></option>
                    <option value=".net" <?php selected($domain_tld, '.net'); ?>><?php _e('.net'); ?></option>
                    <option value=".org" <?php selected($domain_tld, '.org'); ?>><?php _e('.org'); ?></option>
                    <option value=".io" <?php selected($domain_tld, '.io'); ?>><?php _e('.io'); ?></option>
                    <option value=".ai" <?php selected($domain_tld, '.ai'); ?>><?php _e('.ai'); ?></option>
                    <option value=".dev" <?php selected($domain_tld, '.dev'); ?>><?php _e('.dev'); ?></option>
                    <option value=".pics" <?php selected($domain_tld, '.pics'); ?>><?php _e('.pics'); ?></option>
                    <option value=".life" <?php selected($domain_tld, '.life'); ?>><?php _e('.life'); ?></option>
                </select>
            </div>
            <div class="domainDaPa">
                <label><?php _e('DA / PA Ranking (optional)'); ?></label>
                <input type="text" name="da_pa" class="widefat" id="domainDaPa" value="<?php echo esc_attr($da_pa); ?>">
            </div>
            <div class="domainSeo">
                <label><?php _e('SEO Rating (optional)'); ?></label>
                <input type="number" name="seo_rating" min="1" max="5" class="widefat" value="<?php echo esc_attr($seo_rating); ?>">
                <div class="wstr-error-msg"></div>
            </div>
            <div class="domainPronounce">
                <label><?php _e('How to Pronounce'); ?></label>
                <input type="hidden" id="pronounce_audio_url" name="pronounce_audio" value="<?php echo esc_attr($pronounce_audio_id); ?>">
                <button type="button" class="button" id="upload_pronounce_audio"><?php _e('Add File'); ?></button>
                <button type="button" class="button remove-button" id="remove_pronounce_audio"><?php _e('Remove File'); ?></button>
                <p class="description"><?php echo $pronounce_audio_url ? '<audio controls src="' . esc_url($pronounce_audio_url) . '"></audio>' : __('No file selected'); ?></p>
            </div>

            <div class="domainLogo">
                <label><?php _e('Logo/Document'); ?></label>
                <input type="hidden" id="logo_image_url" name="logo_image" value="<?php echo esc_attr($logo_image_id); ?>">
                <button type="button" class="button" id="upload_logo_image"><?php _e('Add Image'); ?></button>
                <button type="button" class="button remove-button" id="remove_logo_image"><?php _e('Remove Image'); ?></button>
                <p class="description"><?php echo $logo_image_url ? '<img src="' . esc_url($logo_image_url) . '" style="max-width: 150px; height: auto;" />' : __('No image selected'); ?></p>
            </div>

        </div>
    <?php
    }

    public function render_domain_data_fields_meta_box($post)
    {
        wp_nonce_field('domain_fields_nonce_action', 'domain_fields_nonce');
        // Retrieve existing values from the database if available
        $regular_price = get_post_meta($post->ID, '_regular_price', true);
        $sale_price = get_post_meta($post->ID, '_sale_price', true);
        $stock_status = get_post_meta($post->ID, '_stock_status', true);
        $enable_offers = get_post_meta($post->ID, '_enable_offers', true);

    ?>
        <div class="domainDataFields">
            <div class="domainPrice">
                <div class="domainRegularPrice">
                    <div class="wstr-error-msg"></div>
                    <label><?php _e('Regular price ($)', 'webstarter'); ?></label>
                    <input type="text" name="regular_price" class="widefat" value="<?php echo esc_attr($regular_price); ?>">
                </div>
                <div class="domainSalePrice">
                    <label><?php _e('Sale price ($)', 'webstarter'); ?></label>
                    <input type="text" name="sale_price" class="widefat" value="<?php echo esc_attr($sale_price); ?>">
                </div>
            </div>
            <div class="domainStatus">
                <label><?php _e('Stock status', 'webstarter'); ?></label>
                <select name="stock_status" class="widefat">
                    <option value="instock" <?php selected($stock_status, 'instock'); ?>><?php _e('In Stock', 'webstarter'); ?></option>
                    <option value="outofstock" <?php selected($stock_status, 'outofstock'); ?>><?php _e('Out of Stock', 'webstarter'); ?></option>
                </select>
            </div>
            <div class="domainOffers">
                <label><?php _e('Enable Offers', 'webstarter'); ?></label>
                <input type="checkbox" name="enable_offers" class="widefat" <?php checked($enable_offers, 'yes'); ?>>
                <span class="wstr-help-tip" tabindex="0" aria-label="Enable this option to enable the 'Make Offer' buttons and form display in the shop."></span>
            </div>
        </div>
<?php
    }

    public function save_domain_meta_boxes($post_id)
    {
        // Verify nonce
        if (!isset($_POST['domain_fields_nonce']) || !wp_verify_nonce($_POST['domain_fields_nonce'], 'domain_fields_nonce_action')) {
            return;
        }

        // Check if this is an autosave and return if it is
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Retrieve fields
        $regular_price = isset($_POST['regular_price']) ? sanitize_text_field($_POST['regular_price']) : '';
        $sale_price = isset($_POST['sale_price']) ? sanitize_text_field($_POST['sale_price']) : '';
        $seo_rating = isset($_POST['seo_rating']) ? sanitize_text_field($_POST['seo_rating']) : '';

        // Validate sale price is not greater than regular price
        if (!empty($regular_price) && !empty($sale_price) && floatval($sale_price) > floatval($regular_price)) {
            // Set an error message
            $sale_price = '';
            // Redirect to avoid resubmission on refresh
        }

        // Save/Update custom fields
        $fields = [
            'highlight_title',
            'highlight_content',
            'age',
            'length',
            'da_pa',
            'seo_rating',
            'pronounce_audio',
            'logo_image',
            'regular_price',
            // 'sale_price',
            'stock_status',
            'enable_offers',
            'tld' // Added TLD field
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                // Handle sanitization based on field type
                if ($field === 'enable_offers') {
                    $value = isset($_POST[$field]) ? 'yes' : 'no';
                } elseif ($field === 'logo_image') {
                    $value = intval($_POST[$field]);
                } else {
                    $value = sanitize_text_field($_POST[$field]);
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        update_post_meta($post_id, '_sale_price', $sale_price);
    }
}

new wstr_domain_meta_boxes();
