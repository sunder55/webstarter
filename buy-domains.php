<?php
add_shortcode('buy-domain', 'buy_domain');
function buy_domain()
{
    ob_start();
?>
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
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1; // Add paged parameter

    // Update search count for each style term
    if (!empty($styles) && is_array($styles)) {
        foreach ($styles as $style) {
            $new_count = 1;
            $previous_count = get_term_meta($style, '_search_count', true);
            if ($previous_count) {
                $new_count = $previous_count + 1;
            }
            update_term_meta($style, '_search_count', $new_count);
        }
    }

    // Update search count for each industry term
    if (!empty($industry) && is_array($industry)) {
        foreach ($industry as $ind) {
            $new_count = 1;
            $previous_count = get_term_meta($ind, '_search_count', true);
            if ($previous_count) {
                $new_count = $previous_count + 1;
            }
            update_term_meta($ind, '_search_count', $new_count);
        }
    }

    $args = [

        'post_type' => 'domain',
        'meta_query' => [
            [
                'key' => '_stock_status',
                'value' => 'outofstock',
                'compare' => '!='
            ]
        ],
        // 'posts_per_page' => -1,
        'post_status' => 'publish',
        'posts_per_page' => 20, // Set number of posts per page
        'paged' => $paged, // Pagination
    ];

    // Initialize the tax_query array
    $tax_query = [];
    // Initialize meta_query if it's not already
    if (!isset($args['meta_query'])) {
        $args['meta_query'] = [];
    }

    $args['meta_query'] = [
        'relation' => 'AND', // Ensures both min and max constraints are applied together
    ];

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
        $args['meta_query'][] = [
            'relation' => 'AND',
            // Check if sale price exists and is less than or equal to max_price
            [
                'key' => '_sale_price',
                'value' => $max_price,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ],
            // If sale price doesn't exist, check regular price
            [
                'relation' => 'OR',
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

    $args['orderby'] = 'title';
    $args['order'] = ($sortBy === 'a-z') ? 'ASC' : 'DESC';

    $response = '';
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
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


            // Generate HTML and append it to the response
            $response .= '<div class="ws-cards-container swiper-slide">';
            $response .= $term_exist ? '<div class="premium_icon"><img decoding="async" src="/wp-content/plugins/card-block/images/diamond.png" alt="Diamond Icon"></div>' : '';
            $response .= '<div class="ws_card_hover_charts ws_flex">';
            $response .= '<div class="circular-progress page-trust">';
            $response .= '<div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($pa) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($pa) . '"></div></div>';
            $response .= '<div class="progress-title"><h6>Page Trust</h6></div></div>';
            $response .= '<div class="circular-progress domain-trust"><div class="progress-text"><div role="progressbar" aria-valuenow="' . esc_attr($da) . '" aria-valuemin="0" aria-valuemax="100" style="--value:' . esc_attr($da) . '"></div></div>';
            $response .= '<div class="progress-title"><h6>Domain Trust</h6></div></div></div>';
            $response .= '<div class="ws-card-img"><img decoding="async" src="' . esc_url($domain_image) . '" alt="' . esc_attr(get_the_title()) . '"></div>';
            $response .= '<div class="ws-card-contents ws-flex">';
            $response .= get_wstr_price_percentage(get_the_ID());
            $response .= '<img decoding="async" src="' . esc_url($logo_url ?: $domain_image) . '" alt="' . esc_attr(get_the_title()) . '" title="' . esc_attr(get_the_title()) . '" class="card_logo_img">';
            $response .= '<span class="ws-card-inner-contents"><h5><a href="' . esc_url(get_permalink(get_the_ID())) . '"> ' . esc_html(get_the_title()) . '</a></h5>';
            $response .= $price ?: '';
            $response .= '</span><div class="ws-card-likes"><h6><span>2k</span><i class="fa-solid fa-heart"></i></h6></div></div></div>';
            $response .= '</div>';
        }
        // Generate pagination
        $total_pages = $query->max_num_pages;
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

function buy_domains()
{
    // Initialize output
    ob_start();
    $get_style = $_GET['style'];
    $get_industry = $_GET['industry'];

?>
    <div class="domain-filters-container">
        <!-- Category Section -->
        <section class="category-section">
            <ul class="categories-list">
                <li>
                    <div class=" reviews_images_lists ws_flex jc_center ai_center">
                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-1.jpeg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-2.jpg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-3.jpeg"
                            alt="Client Image">

                        <img decoding="async"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/clients-4.jpeg"
                            alt="Client Image">
                        <p>Popular Searches</p>
                    </div>
                </li>
                <?php
                // Get top industry terms by _search_count
                $top_industries = get_terms([
                    'taxonomy' => 'domain_industry',
                    'meta_key' => '_search_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                    'number' => 5, // Limit to top 5 terms
                    'hide_empty' => false,
                ]);

                // Get top style terms by _search_count
                $top_styles = get_terms([
                    'taxonomy' => 'domain_cat',
                    'meta_key' => '_search_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                    'number' => 10, // Limit to top 5 terms
                    'hide_empty' => false,
                ]);

                // Merge industries and styles into a single array
                $all_terms = array_merge($top_industries, $top_styles);

                // Sort all terms by _search_count in descending order
                usort($all_terms, function ($a, $b) {
                    $count_a = get_term_meta($a->term_id, '_search_count', true);
                    $count_b = get_term_meta($b->term_id, '_search_count', true);
                    return $count_b - $count_a; // Descending order
                });

                // Display the top 5 terms
                foreach (array_slice($all_terms, 0, 5) as $term) {
                    $count = get_term_meta($term->term_id, '_search_count', true);
                    echo "<li class='popular-searched-item' id='$term->term_id' data-taxonomy='{$term->taxonomy}'>";
                    echo "{$term->name}";
                    echo "</li>";
                }

                ?>
            </ul>
        </section>

        <!-- Filters Section -->
        <section class="filters-section">
            <div class="filter-item">
                <label for="sort-by">Sort By:</label>
                <div class="filter-item-aligned filter_item_name">
                    <select id="sort-by" name="sort-by">
                        <option value="a-z">A-Z</option>
                        <option value="z-a">Z-A</option>
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
                        if ($get_industry) {
                            $selected = $get_industry == $industry->term_id ? 'selected = "selected"' : '';
                        }
                    ?> <option value="<?php echo $industry->term_id; ?>" <?php echo $selected ?>><?php echo $industry->name; ?></option>
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
                        if ($get_style) {
                            $selected = $get_style == $style->term_id ? 'selected = "selected"' : '';
                        }
                    ?> <option value="<?php echo $style->term_id; ?>" <?php echo $selected ?>><?php echo $style->name; ?></option>
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
                        <option value="10000">$10,000</option>
                        <option value="25000">$25,000</option>
                        <option value="50000">$50,000</option>
                        <option value="100000">$100,000</option>
                        <option value="250000">$250,000</option>
                        <option value="500000">$500,000</option>
                        <option value="750000">$750,000</option>
                        <option value="1000000">$1,000,000</option>
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
    </div>
    <!-- Reset Button -->
    <div class="reset-filter">
        <a href="<?php echo get_home_url() . '/buy'; ?>" id="reset-filters"><i
                class="fa-solid fa-arrow-rotate-right"></i>Reset Filter</a>
        <!-- <button type="button" id="reset-filters" onclick="resetFilters()"><i
                class="fa-solid fa-arrow-rotate-right"></i>Reset Filters</button> -->
        <?php
        echo do_shortcode('[wstr-domain-count]'); ?>
    </div>
    <!-- cards contaner -->
    <div class="swiper-container ws-container buy_card_lists">
        <div class="ws-cards-container-wrapper ws_cards_xl" id="buy-domain-main">

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
                'orderby' => 'title', // Sort by title
                'order' => 'ASC' // Ascending or desce
            ];

            if ($get_industry) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'domain_industry',
                        'field' => 'term_id',
                        'terms' => sanitize_text_field($get_industry),
                    ),
                );
            }
            if ($get_style) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'domain_cat',
                        'field' => 'term_id',
                        'terms' => sanitize_text_field($get_style),
                    ),
                );
            }

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
                                    <div role="progressbar" aria-valuenow="<?php echo $pa ?: ''; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="--value:<?php echo $pa ?: ''; ?>"></div>
                                </div>
                                <div class="progress-title">
                                    <h6>Page Trust</h6>
                                </div>
                            </div>
                            <div class="circular-progress domain-trust">
                                <div class="progress-text">
                                    <div role="progressbar" aria-valuenow="<?php echo $da ?: ''; ?>" aria-valuemin="0"
                                        aria-valuemax="100" style="--value:<?php echo $da ?: ''; ?>"></div>
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
                            <?php echo get_wstr_price_percentage(get_the_ID()); ?>
                            <img decoding="async" src="<?php echo $logo_url ?: $domain_image ?>"
                                alt="<?php echo get_the_title() ?>" title="<?php echo get_the_title() ?>" class="card_logo_img">
                            <span class="ws-card-inner-contents">
                                <h5><a href="<?php echo get_permalink(get_the_ID()) ?: ''; ?> "> <?php echo get_the_title() ?>
                                    </a></h5>
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
            else :
                echo '<p> No posts found </p>';
            endif;
            ?>

        </div>
    </div>
    <style>
        .buy_card_lists {
            margin-top: 30px;
        }

        .domain-filters-container {
            margin: 20px 0;
        }

        .domain-filters-container .categories-list li {
            font-weight: 400;
            background: #fff;
            padding: 15px;
            border-radius: 50px;
            color: #00d9f5;
        }

        .domain-filters-container .categories-list li:first-child p {
            background: #00d9f5;
            padding: 15px;
            border-radius: 50px;
            color: #fff;
            margin-left: -15px;
        }

        .domain-filters-container .categories-list li:first-child {
            background-color: transparent;
            padding: 0;
        }

        .domain-filters-container .categories-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 15px 0;
        }

        .domain-filters-container .categories-list li.active {
            border: 2px solid;
        }

        .category-section {
            margin: 2.5rem 0;
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

        .reset-filter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }

        .reset-filter p {
            font-size: 14px;
            line-height: 18px;
            font-weight: 500;
            background-color: #fff;
            padding: 15px;
            border-radius: 20px;
            border: 1px solid #edf0f7;

        }

        #reset-filters {
            border: transparent;
            background-color: #fff;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 50px;
            cursor: pointer;
        }

        .buy_card_lists .pagination {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .buy_card_lists .nav-links {
            display: flex;
            gap: 15px;
        }

        .buy_card_lists span.page-numbers:not(.dots) {
            background-color: #f00073;
            color: #fff;
            padding: 4px 12px;
            border-radius: 5px;
        }

        .buy_card_lists a.page-numbers {
            border: 1px solid #edf0f7;
            border-radius: 5px;
            padding: 4px 12px;
            background-color: #fff;
        }

        @media screen and (max-width: 1024px) {
            .filter-item {
                width: calc(50% - 8px);
            }
        }

        @media screen and (max-width: 767px) {
            .filter-item {
                width: 100%;
            }

            .category-section {
                margin: 0rem 0 1rem 0;
            }

            .high_banner_mobile .ws_home_banner::before {
                height: 1500px;
            }
        }
    </style>
    <script>
        function updateLengthOutput(value) {
            document.getElementById('length-output').textContent = value;
            document.getElementById('lengthSlider').value = value;
        }
    </script>
<?php
    return ob_get_clean();
}

add_shortcode('buy-domains', 'buy_domains');
