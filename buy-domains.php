<?php
add_shortcode('buy-domain', 'buy_domain');
function buy_domain()
{
    ob_start();
?>
    <!-- n the buy page, the sort by: can we include the following also:
Price (Low to High, High to Low)
Popularity (Most Viewed)
Newest (Newest Added)
Alphabetically (We Already Have)
DA/PA (Can we have this, or is it big headache?)
Recommended (Our Picks) -->
    <div class="domain-filters-container">
        <form id="domain-form">
            <!-- Category Section -->
            <section class="category-section">
                <ul class="categories-list">
                    <li>Trending</li>
                    <li>4 Letters</li>
                    <li>Retail</li>
                    <li>Short Names</li>
                    <li>Short Names</li>
                    <li>Short Names</li>
                    <li>Short Names</li>
                    <li>Short Names</li>
                </ul>
            </section>

            <!-- Filters Section -->
            <section class="filters-section">
                <div class="filter-item">
                    <label for="sort-by">Sort By:</label>
                    <div class="filter-item-aligned filter_item_name">
                        <select id="sort-by" name="sort-by">
                            <option value="">Any</option>
                            <option value="z-a">Z-A</option>
                            <option value="a-z">A-Z</option>
                        </select>
                        <select id="domain-type" name="domain-type">
                            <option value="">Any</option>
                            <!-- <option value=".com" <?php //selected($domain_tld, '.com'); 
                                                        ?>><?php _e('.com'); ?></option> -->
                            <option value=".com" <?php //selected($domain_tld, '.com'); 
                                                    ?>><?php _e('.com'); ?></option>
                            <option value=".net" <?php //selected($domain_tld, '.net'); 
                                                    ?>><?php _e('.net'); ?></option>
                            <option value=".org" <?php //selected($domain_tld, '.org'); 
                                                    ?>><?php _e('.org'); ?></option>
                            <option value=".io" <?php //selected($domain_tld, '.io'); 
                                                ?>><?php _e('.io'); ?></option>
                            <option value=".ai" <?php //selected($domain_tld, '.ai'); 
                                                ?>><?php _e('.ai'); ?></option>
                            <option value=".dev" <?php //selected($domain_tld, '.dev'); 
                                                    ?>><?php _e('.dev'); ?></option>
                            <option value=".pics" <?php //selected($domain_tld, '.pics'); 
                                                    ?>><?php _e('.pics'); ?></option>
                            <option value=".life" <?php //selected($domain_tld, '.life'); 
                                                    ?>><?php _e('.life'); ?></option>
                        </select>
                    </div>
                </div>
                <!-- Price (Low to High, High to Low) -->
                <div class="filter-item">
                    <label for="sort-by">Sort By Price:</label>
                    <select id="sort-by-price" name="sort-by-price">
                        <option value="">Any</option>
                        <option value="low-to-high">Low to High</option>
                        <option value="high-to-low">High to Low</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="sort-by">Popular</label>
                    <select id="sort-by-list" name="sort-by-list">
                        <option value="">Any</option>
                        <option value="high">Most Viewed</option>
                        <option value="new">Newest Added</option>
                        <option value="recommended">Recommended</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="industry">By Industry:</label>
                    <?php
                    $industries = get_terms([
                        'taxonomy' => 'domain_industry',
                        'hide_empty' => false,
                    ]);

                    ?>
                    <select id="industry" name="industry[]  " multiple>
                        <?php
                        foreach ($industries as $industry) {
                        ?> <option value="<?php echo $industry->term_id; ?>"><?php echo $industry->name; ?></option>
                        <?php


                        }
                        ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="style">By Style:</label>

                    <?php
                    $styles = get_terms([
                        'taxonomy' => 'domain_cat',
                        'hide_empty' => false,
                    ]);

                    ?>
                    <select id="style" name="style[]" multiple>
                        <?php
                        foreach ($styles as $style) {
                        ?> <option value="<?php echo $style->term_id; ?>"><?php echo $style->name; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="price-range-min">Price Range:</label>
                    <div class="filter-item-aligned">
                        <select name="price-range-min" id="price-range-min">
                            <option value="">Min</option>
                            <option value="500">$500</option>
                            <option value="1000">$1000</option>
                            <option value="2000">$2000</option>
                            <option value="3000">$3000</option>
                            <option value="4000">$4000</option>
                            <option value="5000">$5000</option>
                            <option value="7500">$7500</option>
                            <option value="10,000">$10,000</option>
                            <option value="25,000">$25,000</option>
                            <option value="50,000">$50,000</option>
                            <option value="100,000">$100,000</option>
                            <option value="250,000">$250,000</option>
                            <option value="500,000">$500,000</option>
                            <option value="750,000">$750,000</option>
                            <option value="1,000,000">$1,000,000</option>
                        </select>

                        <select name="price-range-max" id="price-range-max">
                            <option value="">Max</option>
                            <option value="500">$500</option>
                            <option value="1000">$1000</option>
                            <option value="2000">$2000</option>
                            <option value="3000">$3000</option>
                            <option value="4000">$4000</option>
                            <option value="5000">$5000</option>
                            <option value="7500">$7500</option>
                            <option value="10000">$10,000</option>
                            <option value="25000">$25,000</option>
                            <option value="50000">$50,000</option>
                            <option value="100000">$100,000</option>
                            <option value="250000">$250,000</option>
                            <option value="500000">$500,000</option>
                            <option value="750000">$750,000</option>
                            <option value="1000000">$1,000,000</option>
                        </select>
                    </div>
                </div>

                <div class="filter-item">
                    <label for="length-slider">Length <i class="fa-solid fa-arrow-right"></i><span id="length-output">
                            50</span>
                        letters </label>
                    <input type="range" id="length-slider" name="length-slider" min="0" max="50" value="50"
                        oninput="updateLengthOutput(this.value)">
                    <input type="hidden" id="lengthSlider">
                </div>

            </section>
            <div class="reset-filter">
                <a href="<?php echo get_home_url() . '/buy'; ?>" id="reset-filters"><i
                        class="fa-solid fa-arrow-rotate-right"></i>Reset Filter</a>
                <!-- <button type="button" id="reset-filters" onclick="resetFilters()"><i
                        class="fa-solid fa-arrow-rotate-right"></i>Reset Filters</button> -->
            </div>
        </form>
    </div>

    <style>
        .domain-filters-container {
            margin: 20px 0;
        }

        .domain-filters-container .categories-list li {
            font-weight: 400;
            background: #fff;
            padding: 10px;
            border-radius: 20px;
            color: #00d9f5;
        }

        .category-section {
            margin-bottom: 2rem;
        }

        .categories-list {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }

        .categories-list li {
            display: inline-block;
            margin-right: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .filters-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            width: calc(20% - 15px);
            border-radius: 20px;
            box-shadow: 0px 30px 101px 0px rgba(0, 69, 162, 0.11);
            border: 2px solid rgb(237, 240, 247);
            padding: 20px;
            gap: 10px;
        }

        .filter-item label {
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-item select,
        .filter-item input {
            padding: 8px;
            font-size: 14px;
            border: 2px solid rgb(237, 240, 247);
            border-radius: 10px;
        }

        .filter-item select::placeholder,
        .filter-item input::placeholder {
            color: #00214c;
        }



        .filter-item-aligned {
            display: flex;
            gap: 10px;
        }

        .filter_item_name select:first-child {
            flex: 2;
        }

        .filter_item_name select:last-child {
            flex: 1;
        }

        .filter-item-aligned input {
            width: 50%;
        }

        #reset-filters {
            border: transparent;
            background-color: #fff;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 50px;
            margin-top: 20px;
        }
    </style>
    <script>
        function updateLengthOutput(value) {
            document.getElementById('length-output').textContent = value;
            document.getElementById('lengthSlider').value = value;
        }
    </script>

    <div style="display:flex;flex-wrap:wrap; gap:20px" id="buy-domain-main">
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = [
            'posts_per_page' => 20,
            'post_type' => 'domain',
            'paged' => $paged,
            'meta_query' => [
                [
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                    'compare' => '!='
                ]
            ],

        ];
        query_posts($args); ?>
        <!-- the loop -->
        <?php if (have_posts()):
            while (have_posts()):
                the_post();
                $domain_image = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');

                if (!$domain_image) {
                    $domain_image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
                }

                $logo = get_post_meta(get_the_ID(), '_logo_image', true);
                $logo_url = wp_get_attachment_url($logo);

                $price = get_wstr_price(get_the_ID());
                $percentage_discount = 0;

                if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                    // Calculate the discount percentage
                    $percentage_discount = (($regular_price - $sale_price) / $regular_price) * 100;
                    $percentage_discount = round($percentage_discount); // Round to 2 decimal places for readability  
                }
                // Get the price using custom function (assuming it exists)
                $domain_price = get_wstr_price(get_the_ID());
                $currency = get_wstr_currency();
                // Get DA / PA Ranking
                $da_pa = get_post_meta(get_the_ID(), '_da_pa', true);
                $da = $pa = '';
                if ($da_pa) {
                    $da_pa_split = explode('/', $da_pa);
                    $da = $da_pa_split[0];
                    $pa = $da_pa_split[1];
                }

                $term_exist = wstr_check_existing_term(get_the_ID(), 'domain_cat', 'premium-names');
                // Add to 
        ?>

                <div class="ws-cards-container swiper-slide">
                    <?php echo $term_exist ? '
                <div class="premium_icon">
                    <img decoding="async" src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon">
                </div>' : '';
                    ?>
                    <div class="ws_card_hover_charts ws_flex">
                        <div class="circular-progress page-trust">
                            <div class="progress-text">
                                <div role="progressbar" aria-valuenow="<?php echo $pa ?: ''; ?>" aria-valuemin="0" aria-valuemax="100" style="--value:<?php echo $pa ?: ''; ?>"></div>
                            </div>
                            <div class="progress-title">
                                <h6>Page Trust</h6>
                            </div>
                        </div>
                        <div class="circular-progress domain-trust">
                            <div class="progress-text">
                                <div role="progressbar" aria-valuenow="<?php echo $da ?: ''; ?>" aria-valuemin="0" aria-valuemax="100" style="--value:<?php echo $da ?: ''; ?>"></div>
                            </div>
                            <div class="progress-title">
                                <h6>Domain Trust</h6>
                            </div>
                        </div>
                    </div>
                    <div class="ws-card-img">
                        <img decoding="async" src="<?php echo $domain_image; ?>" alt="<?php echo get_the_title() ?>">
                    </div>
                    <div class="ws-card-contents ws-flex">
                        <?php echo get_wstr_price_percentage(get_the_ID());  ?>
                        <img decoding="async" src="<?php echo $logo_url ?: $domain_image ?>" alt="<?php echo get_the_title() ?>" title="<?php echo get_the_title() ?>" class="card_logo_img">
                        <span class="ws-card-inner-contents">
                            <h5><a href="<?php echo get_permalink(get_the_ID()) ?: ''; ?> "> <?php echo get_the_title() ?> </a></h5>
                            <?php echo $price ?: ''; ?>
                            <!-- <div class="ws_card_price_wrapper ws_flex gap_10">
                            <p class="regular_price">£2500</p>
                            <p class="sale_price">£1000</p>
                        </div> -->
                        </span>
                        <div class="ws-card-likes">
                            <h6><span>2k</span><i class="fa-solid fa-heart"></i></h6>
                        </div>
                    </div>
                </div>


        <?php endwhile;
            // <!-- pagination -->

            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('<', 'webstarter'),
                'next_text' => __('>', 'webstarter'),
            ));

        //  else : 
        // <!-- No posts found -->
        endif;
        ?>
    </div>


<?php

    return ob_get_clean();
}





add_action('wp_ajax_wstr_domain_filter', 'wstr_domain_filter');
add_action('wp_ajax_no_priv_wstr_domain_filter', 'wstr_domain_filter');
function wstr_domain_filter()
{
    // $indurstry = array_map("strip_tags", $_POST['industry']);
    $industry = !empty($_POST['industry']) ? array_map('sanitize_text_field', $_POST['industry']) : [];
    $styles = !empty($_POST['style']) ? array_map('sanitize_text_field', $_POST['style']) : [];
    $tld = !empty($_POST['tld']) ? sanitize_text_field($_POST['tld']) : '';
    $length = !empty($_POST['length']) ? sanitize_text_field($_POST['length']) : '';
    $sortBy = !empty($_POST['sortBy']) ? sanitize_text_field($_POST['sortBy']) : '';
    $min_price = !empty($_POST['min_price']) ? sanitize_text_field($_POST['min_price']) : '';
    $max_price = !empty($_POST['max_price']) ? sanitize_text_field($_POST['max_price']) : '';
    $sort_by_price = !empty($_POST['sort_by_price']) ? sanitize_text_field($_POST['sort_by_price']) : '';
    $sort_by_list = !empty($_POST['sort_by_list']) ? sanitize_text_field($_POST['sort_by_list']) : '';
    $targeted_id = !empty($_POST['targeted_id']) ? sanitize_text_field($_POST['targeted_id']) : '';

    // Fetch the current page number from the POST request
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    // Update search counts for styles
    if (!empty($_POST['styles']) && is_array($_POST['styles'])) {
        foreach ($_POST['styles'] as $style) {
            $new_count = 1;
            $previous_count = get_term_meta($style, '_search_count', true);
            if ($previous_count) {
                $new_count = $previous_count + 1;
            }
            update_term_meta($style, '_search_count', $new_count);
        }
    }

    // Update search counts for industries
    if (!empty($_POST['industry']) && is_array($_POST['industry'])) {
        foreach ($_POST['industry'] as $ind) {
            $new_count = 1;
            $previous_count = get_term_meta($ind, '_search_count', true);
            if ($previous_count) {
                $new_count = $previous_count + 1;
            }
            update_term_meta($ind, '_search_count', $new_count);
        }
    }

    // Build the initial query arguments
    $args = [
        'post_type' => 'domain',
        'post_status' => 'publish',
        'posts_per_page' => -1, // Fetch all posts initially
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!='
            ]
        ]
    ];

    // Initialize the tax_query array
    $tax_query = [];
    // Initialize meta_query if it's not already
    // if (!isset($args['meta_query'])) {
    //     $args['meta_query'] = [];
    // }

    // $args['meta_query'] = [
    //     'relation' => 'AND', // Ensures both min and max constraints are applied together
    // ];

    if (!empty($industry) && is_array($industry)) {
        // Add a separate subquery for each industry term with an 'AND' relation
        $industry_subqueries = [];
        foreach ($industry as $ind) {
            $industry_subqueries[] = [
                'taxonomy' => 'domain_industry',
                'field' => 'term_id',
                'terms' => $ind,
                'operator' => 'IN',
            ];
        }
        $tax_query[] = [
            'relation' => 'AND',
            $industry_subqueries,
        ];
    }

    if (!empty($styles) && is_array($styles)) {
        // Add a separate subquery for each style term with an 'AND' relation
        $style_subqueries = [];
        foreach ($styles as $style) {
            $style_subqueries[] = [
                'taxonomy' => 'domain_cat',
                'field' => 'term_id',
                'terms' => $style,
                'operator' => 'IN',
            ];
        }
        $tax_query[] = [
            'relation' => 'AND',
            $style_subqueries,
        ];
    }

    // If there are tax queries, add them to args with 'relation' set to AND
    if (!empty($tax_query)) {
        $args['tax_query'] = [
            'relation' => 'AND',
            $tax_query
        ];
    }
    // // // Add meta query for _tld if it's set
    if (!empty($tld)) {
        $args['meta_query'][] = [
            'key' => '_tld',
            'value' => $tld,
            'compare' => '='
        ];
    }

    // Add meta query for _length if it's set
    if (!empty($length)) {
        $args['meta_query'][] = [
            'key' => '_length',
            'value' => $length,
            'compare' => '<=',
            'type' => 'NUMERIC'
        ];
    };

    if (!empty($min_price)) {
        $args['meta_query'][] = [
            'relation' => 'OR',
            // Check if sale price exists and is greater than or equal to min_price
            [
                'key' => '_sale_price',
                'value' => $min_price,
                'compare' => '>=',
                'type' => 'NUMERIC'
            ],
            // If sale price doesn't exist, check regular price
            [
                'relation' => 'AND',
                [
                    'key' => '_sale_price',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key' => '_regular_price',
                    'value' => $min_price,
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                ]
            ]
        ];
    }

    if (!empty($max_price)) {
        // $current_date = current_time('Y-m-d');
        $args['meta_query'][] = [
            'relation' => 'OR',
            // Check if sale price exists and is less than or equal to max_price
            [
                'key' => '_sale_price',
                'value' => $max_price,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ],
            // If sale price doesn't exist, check regular price
            [
                'relation' => 'AND',
                [
                    'key' => '_sale_price',
                    'compare' => 'NOT EXISTS'
                ],
                [
                    'key' => '_regular_price',
                    'value' => $max_price,
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                ]
            ]
        ];
    }
    if (!empty($sort_by_list)) {

        if ($sort_by_list === 'high') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'ws_product_view_count';
            $args['order'] = 'DESC';
        } else if ($sort_by_list === 'new') {
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
        }
        // else if($sort_by_list == 'recommended'){
        //     $args['orderby'] = 'meta_value_num';
        //     $args['meta_key'] = 'ws_product_view_count';
        //     $args['order'] = 'DESC';
        // }
    }

    if (!empty($sortBy)) {
        $args['orderby'] = 'title';
        $args['order'] = ($sortBy === 'a-z') ? 'ASC' : 'DESC';
    }

    // Query all posts matching the criteria
    $all_query = new WP_Query($args);

    $response = '';

    if ($all_query->have_posts()) {
        $posts = $all_query->posts;
        if ($targeted_id != 'sort-by') {
            // Sort posts by price if necessary
            if (!empty($_POST['sort_by_price'])) {
                $sort_by_price = $_POST['sort_by_price'];
                usort($posts, function ($a, $b) use ($sort_by_price) {
                    $sale_price_a = (float) get_post_meta($a->ID, '_sale_price', true);
                    $regular_price_a = (float) get_post_meta($a->ID, '_regular_price', true);
                    $price_a = $sale_price_a ? $sale_price_a : $regular_price_a;

                    $sale_price_b = (float) get_post_meta($b->ID, '_sale_price', true);
                    $regular_price_b = (float) get_post_meta($b->ID, '_regular_price', true);
                    $price_b = $sale_price_b ? $sale_price_b : $regular_price_b;

                    if ($sort_by_price == 'low-to-high') {
                        return $price_a - $price_b;
                    } else {
                        return $price_b - $price_a;
                    }
                });
            }
        }

        // Apply pagination to the sorted posts
        $posts_per_page = 20;
        $offset = ($paged - 1) * $posts_per_page;
        $paginated_posts = array_slice($posts, $offset, $posts_per_page);

        // Generate the response for each paginated post
        foreach ($paginated_posts as $post) {
            // var_dump($post);
            setup_postdata($post);

            $domain_image = get_the_post_thumbnail_url($post->ID, 'medium_large');
            if (!$domain_image) {
                $domain_image = get_stylesheet_directory_uri() . '/assets/images/alternate-domain.png';
            }

            $logo = get_post_meta($post->ID, '_logo_image', true);
            $logo_url = wp_get_attachment_url($logo);

            $price = get_wstr_price($post->ID);
            $sale_price = (float) get_post_meta($post->ID, '_sale_price', true);
            $regular_price = (float) get_post_meta($post->ID, '_regular_price', true);

            $percentage_discount = 0;
            if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
                $percentage_discount = round((($regular_price - $sale_price) / $regular_price) * 100);
            }

            $da_pa = get_post_meta($post->ID, '_da_pa', true);
            $da = $pa = '';
            if ($da_pa) {
                $da_pa_split = explode('/', $da_pa);
                $da = $da_pa_split[0];
                $pa = $da_pa_split[1];
            }

            $term_exist = wstr_check_existing_term($post->ID, 'domain_cat', 'premium-names');

            // Generate HTML and append it to the response
            // $response .= '<div class="ws-cards-container swiper-slide">';
            // $response .= $term_exist ? '<div class="premium_icon"><img decoding="async" src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon"></div>' : '';
            // $response .= '<div class="ws_card_hover_charts ws_flex">';
            // $response .= '<div class="circular-progress page-trust">';
            // $response .= '<div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($pa) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($pa) . '"></div></div>';
            // $response .= '<div class="progress-title"><h6>Page Trust</h6></div></div>';
            // $response .= '<div class="circular-progress domain-trust"><div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($da) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($da) . '"></div></div>';
            // $response .= '<div class="progress-title"><h6>Domain Trust</h6></div></div></div>';
            // $response .= '<div class="ws-card-img"><img decoding="async" src="' . esc_url($domain_image) . '" alt="' . esc_attr(get_the_title()) . '"></div>';
            // $response .= '<div class="ws-card-contents ws-flex">';
            // $response .= get_wstr_price_percentage($post->ID);
            // $response .= '<img decoding="async" src="' . esc_url($logo_url ?: $domain_image) . '" alt="' . esc_attr(get_the_title()) . '" title="' . esc_attr(get_the_title()) . '" class="card_logo_img">';
            // $response .= '<span class="ws-card-inner-contents"><h5><a href="' . esc_url(get_permalink($post->ID)) . '"> ' . esc_html(get_the_title()) . '</a></h5>';
            // $response .= $price ?: '';
            // $response .= '</span><div class="ws-card-likes"><h6><span>2k</span><i class="fa-solid fa-heart"></i></h6></div></div></div>';
            // $response .= '</div>';


            $response .= '<div class="ws-cards-container swiper-slide">';
            $response .= $term_exist ? '<div class="premium_icon"><img decoding="async" src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon"></div>' : '';
            $response .= '<div class="ws_card_hover_charts ws_flex">';
            $response .= '<div class="circular-progress page-trust">';
            $response .= '<div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($pa) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($pa) . '"></div></div>';
            $response .= '<div class="progress-title"><h6>Page Trust</h6></div></div>';
            $response .= '<div class="circular-progress domain-trust"><div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($da) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($da) . '"></div></div>';
            $response .= '<div class="progress-title"><h6>Domain Trust</h6></div></div></div>';
            $response .= '<div class="ws-card-img"><img decoding="async" src="' . esc_url($domain_image) . '" alt="' . esc_attr($post->post_title) . '"></div>';
            $response .= '<div class="ws-card-contents ws-flex">';
            $response .= get_wstr_price_percentage($post->ID);
            $response .= '<img decoding="async" src="' . esc_url($logo_url ?: $domain_image) . '" alt="' . esc_attr($post->post_title) . '" title="' . esc_attr($post->post_title) . '" class="card_logo_img">';
            $response .= '<span class="ws-card-inner-contents"><h5><a href="' . esc_url(get_permalink($post->ID)) . '"> ' . esc_html($post->post_title) . '</a></h5>';
            $response .= $price ?: '';
            $response .= '</span><div class="ws-card-likes"><h6><span>2k</span><i class="fa-solid fa-heart"></i></h6></div></div></div>';
            $response .= '</div>';
        }

        wp_reset_postdata();

        // Generate pagination
        $total_pages = ceil(count($posts) / $posts_per_page);
        $pagination = paginate_links([
            'base' => '%_%',
            'format' => '?paged=%#%',
            'current' => max(1, $paged),
            'total' => $total_pages,
            'prev_text' => __('<', 'webstarter'),
            'next_text' => __('>', 'webstarter'),
            'type' => 'array',
        ]);

        if ($pagination) {
            $response .= '<div class="pagination">';
            foreach ($pagination as $page_link) {
                $response .= '<span>' . $page_link . '</span>';
            }
            $response .= '</div>';
        }
    } else {
        $response .= '<p>' . __('No domain found.', 'webstarter') . '</p>';
    }

    wp_send_json_success($response);
    wp_die();
}
