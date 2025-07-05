<?php
/**
 * Plugin Name: Woo URL Manager
 * Description: Manage WooCommerce slugs, taxonomy rewrites, redirects, and breadcrumbs with UI. Developed for Watchoice.pk
 * Version: 2.0.8
 * Author: Watchoice.pk
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 */

if (!defined('ABSPATH')) exit;

class Woo_URL_Manager {
    private $option_key = 'woo_url_manager_settings';
    private $category_slug_map = [
        'watches' => 'watches',
        'wall-clocks' => 'wall-clocks',
        'accessories' => 'accessories',
    ];
    private $default_slug = 'accessories';
    private $plugin_version = '2.0.8';

    public function __construct() {
        // Check if WooCommerce is active
        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', [$this, 'woocommerce_not_active_notice']);
            return;
        }

        // Initialize plugin hooks
        $this->init_hooks();
    }

    private function is_woocommerce_active() {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    public function woocommerce_not_active_notice() {
        ?>
        <div class="notice notice-error">
            <p><strong>Woo URL Manager:</strong> WooCommerce is required for this plugin to work. Please install and activate WooCommerce.</p>
        </div>
        <?php
    }

    private function init_hooks() {
        try {
            // Admin settings
            add_action('admin_menu', [$this, 'add_settings_page']);
            add_action('admin_init', [$this, 'register_settings']);
            
            // Rewrite and query handling
            add_action('init', [$this, 'add_custom_rewrite_rules'], 1);
            add_filter('query_vars', [$this, 'add_query_vars']);
            add_filter('request', [$this, 'support_custom_product_url'], 1);
            
            // Taxonomy and redirects
            add_filter('term_link', [$this, 'custom_term_links'], 10, 3);
            add_action('template_redirect', [$this, 'maybe_redirect_old_product_url']);
            
            // Breadcrumbs
            add_filter('wpseo_breadcrumb_links', [$this, 'custom_breadcrumbs'], 10, 1);
            add_filter('rank_math/frontend/breadcrumb/items', [$this, 'custom_breadcrumbs'], 10, 1);
            add_filter('woocommerce_breadcrumb', [$this, 'custom_breadcrumbs'], 10, 1);
            
            // Product permalinks
            add_filter('post_type_link', [$this, 'custom_product_permalink'], 10, 2);
            
            // Flush rewrite rules
            add_action('update_option_woo_url_manager_settings', [$this, 'flush_rewrite_rules']);
            register_activation_hook(__FILE__, [$this, 'flush_rewrite_rules']);
            register_deactivation_hook(__FILE__, [$this, 'flush_rewrite_rules']);
            
            // Debug logging
            add_action('parse_request', [$this, 'debug_parse_request'], 999);
            add_action('wp', [$this, 'debug_query'], 999);
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error initializing hooks: ' . $e->getMessage());
        }
    }

    public function add_settings_page() {
        add_submenu_page(
            'woocommerce',
            'Woo URL Manager',
            'URL Manager',
            'manage_woocommerce',
            'woo-url-manager',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('woo_url_manager_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    public function sanitize_settings($input) {
        $sanitized = [];
        
        try {
            // Handle product slugs
            $product_slugs_input = isset($input['product_slugs']) ? trim($input['product_slugs']) : 'watches,wall-clocks,accessories';
            $slugs = array_filter(array_map('sanitize_title', array_map('trim', explode(',', $product_slugs_input))));
            $sanitized['product_slugs'] = array_unique($slugs);
            
            // Ensure we have at least the default slugs
            if (empty($sanitized['product_slugs'])) {
                $sanitized['product_slugs'] = ['watches', 'wall-clocks', 'accessories'];
            }
            
            $sanitized['redirect'] = isset($input['redirect']) ? 1 : 0;
            $sanitized['taxonomies'] = [];
            
            // Handle taxonomies
            if (isset($input['taxonomies']) && is_array($input['taxonomies'])) {
                foreach ($input['taxonomies'] as $tax => $path) {
                    $clean_tax = sanitize_key($tax);
                    $clean_path = sanitize_text_field($path);
                    if (!empty($clean_tax) && !empty($clean_path)) {
                        $sanitized['taxonomies'][$clean_tax] = $clean_path;
                    }
                }
            }
            
            // Handle new taxonomy input
            if (!empty($input['new_taxonomy']) && !empty($input['new_taxonomy_path'])) {
                $new_tax = sanitize_key($input['new_taxonomy']);
                $new_path = sanitize_text_field($input['new_taxonomy_path']);
                if (!empty($new_tax) && !empty($new_path) && !isset($sanitized['taxonomies'][$new_tax])) {
                    $sanitized['taxonomies'][$new_tax] = $new_path;
                }
            }
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error sanitizing settings: ' . $e->getMessage());
            // Return defaults on error
            return [
                'product_slugs' => ['watches', 'wall-clocks', 'accessories'],
                'redirect' => 1,
                'taxonomies' => [
                    'style' => 'watches/style',
                    'brand' => 'watches/brand',
                    'price_range' => 'watches/price',
                ]
            ];
        }
        
        return $sanitized;
    }

    public function get_settings() {
        return wp_parse_args(get_option($this->option_key, []), [
            'product_slugs' => ['watches', 'wall-clocks', 'accessories'],
            'redirect' => 1,
            'taxonomies' => [
                'style' => 'watches/style',
                'brand' => 'watches/brand',
                'price_range' => 'watches/price',
            ]
        ]);
    }

    public function add_custom_rewrite_rules() {
        if (!function_exists('add_rewrite_rule')) {
            error_log('Woo URL Manager: add_rewrite_rule function not available');
            return;
        }

        try {
            $settings = $this->get_settings();
            
            // Product rewrite rules
            foreach ($settings['product_slugs'] as $slug) {
                if (empty($slug)) continue;
                
                // Product pages: /watches/product-name/
                add_rewrite_rule(
                    '^' . preg_quote($slug, '/') . '/([^/]+)/?$',
                    'index.php?post_type=product&name=$matches[1]&woo_url_slug=' . urlencode($slug),
                    'top'
                );
                
                // Category pages: /watches/
                add_rewrite_rule(
                    '^' . preg_quote($slug, '/') . '/?$',
                    'index.php?product_cat=' . urlencode($slug),
                    'top'
                );
            }
            
            // Taxonomy rewrite rules
            foreach ($settings['taxonomies'] as $taxonomy => $path) {
                if (empty($taxonomy) || empty($path)) continue;
                
                add_rewrite_rule(
                    '^' . preg_quote($path, '/') . '/([^/]+)/?$',
                    'index.php?' . urlencode($taxonomy) . '=$matches[1]',
                    'top'
                );
            }
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error adding rewrite rules: ' . $e->getMessage());
        }
    }

    public function add_query_vars($vars) {
        try {
            $settings = $this->get_settings();
            $vars[] = 'woo_url_slug';
            
            foreach (array_keys($settings['taxonomies']) as $tax) {
                if (!empty($tax)) {
                    $vars[] = $tax;
                }
            }
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error adding query vars: ' . $e->getMessage());
        }
        
        return $vars;
    }

    public function custom_term_links($url, $term, $taxonomy) {
        try {
            $settings = $this->get_settings();
            if (isset($settings['taxonomies'][$taxonomy])) {
                return home_url(user_trailingslashit($settings['taxonomies'][$taxonomy] . '/' . $term->slug));
            }
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in custom_term_links: ' . $e->getMessage());
        }
        
        return $url;
    }

    public function maybe_redirect_old_product_url() {
        try {
            $settings = $this->get_settings();
            if (!is_singular('product') || empty($settings['redirect'])) return;

            global $wp;
            if (isset($wp->request) && strpos($wp->request, 'product/') === 0) {
                $product_slug = basename($wp->request);
                
                // Try to find the product
                $post = get_page_by_path($product_slug, OBJECT, 'product');
                if ($post) {
                    $new_url = get_permalink($post->ID);
                    if ($new_url && $new_url !== get_permalink()) {
                        wp_safe_redirect($new_url, 301);
                        exit;
                    }
                }
            }
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in redirect function: ' . $e->getMessage());
        }
    }

    public function support_custom_product_url($vars) {
        try {
            if (!isset($vars['name']) || isset($vars['post_type'])) {
                return $vars;
            }

            $settings = $this->get_settings();
            $product_slug = $vars['name'];
            
            // Check if this is a custom product URL
            if (isset($vars['woo_url_slug']) && in_array($vars['woo_url_slug'], $settings['product_slugs'])) {
                // First try get_page_by_path
                $post = get_page_by_path($product_slug, OBJECT, 'product');
                
                // Fallback to get_posts for more robust search
                if (!$post) {
                    $args = [
                        'name' => $product_slug,
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'posts_per_page' => 1,
                        'fields' => 'ids'
                    ];
                    $posts = get_posts($args);
                    
                    if (!empty($posts)) {
                        $post = get_post($posts[0]);
                    }
                }
                
                if ($post) {
                    $vars['post_type'] = 'product';
                    $vars['name'] = $product_slug;
                    $vars['woo_url_slug'] = $vars['woo_url_slug'];
                    
                    // Clean up conflicting vars
                    unset($vars['pagename']);
                    unset($vars['page']);
                    unset($vars['error']);
                    unset($vars['product_cat']);
                    
                    error_log('Woo URL Manager: Successfully mapped URL - slug: ' . $vars['woo_url_slug'] . ', product: ' . $product_slug);
                } else {
                    error_log('Woo URL Manager: Product not found for slug "' . $product_slug . '" under "' . $vars['woo_url_slug'] . '"');
                }
            }
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in support_custom_product_url: ' . $e->getMessage());
        }
        
        return $vars;
    }

    public function custom_product_permalink($permalink, $post) {
        try {
            if (!is_object($post) || $post->post_type !== 'product') {
                return $permalink;
            }

            $terms = get_the_terms($post->ID, 'product_cat');
            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $term) {
                    if (isset($this->category_slug_map[$term->slug])) {
                        return home_url(user_trailingslashit($this->category_slug_map[$term->slug] . '/' . $post->post_name));
                    }
                }
            }

            return home_url(user_trailingslashit($this->default_slug . '/' . $post->post_name));
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in custom_product_permalink: ' . $e->getMessage());
            return $permalink;
        }
    }

    public function custom_breadcrumbs($links) {
        try {
            $settings = $this->get_settings();
            
            if (is_product()) {
                global $post;
                if (!is_object($post)) {
                    return $links;
                }
                
                $slug = $this->default_slug;
                $terms = get_the_terms($post->ID, 'product_cat');
                
                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        if (isset($this->category_slug_map[$term->slug])) {
                            $slug = $this->category_slug_map[$term->slug];
                            break;
                        }
                    }
                }
                
                $new_links = [
                    ['text' => 'Home', 'url' => home_url(), 'allow_html' => true],
                    ['text' => ucwords(str_replace('-', ' ', $slug)), 'url' => home_url('/' . $slug), 'allow_html' => true],
                    ['text' => get_the_title($post->ID), 'url' => '', 'allow_html' => true]
                ];
                
                return $new_links;
                
            } elseif (is_tax()) {
                $queried_object = get_queried_object();
                if ($queried_object && isset($queried_object->taxonomy)) {
                    $taxonomy = $queried_object->taxonomy;
                    if (isset($settings['taxonomies'][$taxonomy])) {
                        $term = $queried_object;
                        $new_links = [
                            ['text' => 'Home', 'url' => home_url(), 'allow_html' => true],
                            ['text' => ucwords(str_replace('-', ' ', $taxonomy)), 'url' => home_url($settings['taxonomies'][$taxonomy]), 'allow_html' => true],
                            ['text' => $term->name, 'url' => '', 'allow_html' => true]
                        ];
                        return $new_links;
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in custom_breadcrumbs: ' . $e->getMessage());
        }
        
        return $links;
    }

    public function debug_parse_request($wp) {
        try {
            if (isset($wp->query_vars['name']) && isset($wp->query_vars['woo_url_slug'])) {
                error_log('Woo URL Manager: Parsed URL - slug: ' . $wp->query_vars['woo_url_slug'] . ', name: ' . $wp->query_vars['name'] . ', post_type: ' . ($wp->query_vars['post_type'] ?? 'none'));
            }
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in debug_parse_request: ' . $e->getMessage());
        }
    }

    public function debug_query($wp) {
        try {
            if (is_404() && isset($wp->query_vars['name']) && isset($wp->query_vars['woo_url_slug'])) {
                error_log('Woo URL Manager: 404 detected for URL - slug: ' . $wp->query_vars['woo_url_slug'] . ', name: ' . $wp->query_vars['name']);
                
                // Additional debug info
                global $wp_query;
                error_log('Woo URL Manager: Query vars: ' . print_r($wp->query_vars, true));
                error_log('Woo URL Manager: Request: ' . $wp->request);
            }
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error in debug_query: ' . $e->getMessage());
        }
    }

    public function flush_rewrite_rules() {
        try {
            if (function_exists('flush_rewrite_rules')) {
                flush_rewrite_rules(true);
                error_log('Woo URL Manager: Rewrite rules flushed');
            }
        } catch (Exception $e) {
            error_log('Woo URL Manager: Error flushing rewrite rules: ' . $e->getMessage());
        }
    }

    public function render_settings_page() {
        $settings = $this->get_settings();
        ?>
        <div class="wrap" style="font-family: sans-serif;">
            <h1 style="color: rgb(0,31,63);">Woo URL Manager v<?php echo $this->plugin_version; ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('woo_url_manager_group'); ?>
                <?php do_settings_sections('woo_url_manager_group'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Product Slugs</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_key; ?>[product_slugs]" value="<?php echo esc_attr(implode(',', $settings['product_slugs'])); ?>" placeholder="watches,wall-clocks,accessories" style="width: 300px;" />
                            <p class="description">Comma-separated list of slugs (e.g., watches,wall-clocks,accessories)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Enable Redirect from /product/</th>
                        <td><input type="checkbox" name="<?php echo $this->option_key; ?>[redirect]" value="1" <?php checked(1, $settings['redirect'], true); ?> /></td>
                    </tr>
                    <tr>
                        <th scope="row">Add New Taxonomy</th>
                        <td>
                            <input type="text" name="<?php echo $this->option_key; ?>[new_taxonomy]" placeholder="Taxonomy slug (e.g., material)" style="width: 200px;" />
                            <input type="text" name="<?php echo $this->option_key; ?>[new_taxonomy_path]" placeholder="Path (e.g., watches/material)" style="width: 200px;" />
                            <p class="description">Enter a new taxonomy slug and its custom path.</p>
                        </td>
                    </tr>
                </table>

                <h2 style="color: rgb(0,31,63);">Custom Taxonomy Paths</h2>
                <p class="description">Edit paths for existing taxonomies registered via CPT UI.</p>
                <table class="form-table" id="taxonomy-table">
                    <?php foreach ($settings['taxonomies'] as $tax => $path): ?>
                    <tr>
                        <th scope="row"><?php echo esc_html($tax); ?></th>
                        <td>
                            <input type="text" name="<?php echo $this->option_key; ?>[taxonomies][<?php echo esc_attr($tax); ?>]" value="<?php echo esc_attr($path); ?>" style="width: 300px;" />
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <?php submit_button('Save & Flush Permalinks', 'primary', 'submit', true, ['style' => 'background: rgb(0,31,63); border-color: rgb(0,31,63);']); ?>
            </form>

            <div style="background: rgb(192,192,192); padding: 15px; margin-top: 20px; border-radius: 5px;">
                <strong style="color: rgb(46,46,46);">Instructions:</strong>
                <ul style="color: rgb(46,46,46);">
                    <li>Enter product slugs as a comma-separated list (e.g., watches,wall-clocks,accessories).</li>
                    <li>Add new taxonomies using the fields above (e.g., slug: material, path: watches/material).</li>
                    <li>Ensure slugs do not conflict with existing pages or taxonomies.</li>
                    <li>Assign products to categories (Watches, Wall Clocks, Accessories) to set their URL slugs.</li>
                    <li>Products without a category default to the 'accessories' slug.</li>
                    <li>After saving, permalinks are automatically flushed.</li>
                    <li>Custom slugs are reflected in breadcrumbs (Yoast, Rank Math, WooCommerce).</li>
                    <li>Check debug.log for rewrite rule issues if 404 errors occur.</li>
                </ul>
                <p style="font-size: 12px; color: rgb(46,46,46);">Plugin styled and built by <strong>Watchoice.pk</strong> | Version <?php echo $this->plugin_version; ?></p>
            </div>
            
            <div style="background: #f9f9f9; padding: 15px; margin-top: 20px; border-left: 4px solid rgb(0,31,63);">
                <h3 style="color: rgb(0,31,63); margin-top: 0;">Debug Information</h3>
                <p><strong>Plugin Version:</strong> <?php echo $this->plugin_version; ?></p>
                <p><strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?></p>
                <p><strong>WooCommerce Version:</strong> <?php echo defined('WC_VERSION') ? WC_VERSION : 'Not detected'; ?></p>
                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                <p><strong>Debug Log:</strong> <?php echo defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'Enabled' : 'Disabled'; ?></p>
                <p><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin with error handling
try {
    new Woo_URL_Manager();
} catch (Exception $e) {
    error_log('Woo URL Manager: Fatal error during initialization: ' . $e->getMessage());
    
    // Show admin notice if in admin
    if (is_admin()) {
        add_action('admin_notices', function() use ($e) {
            echo '<div class="notice notice-error"><p><strong>Woo URL Manager:</strong> Plugin failed to initialize. Error: ' . esc_html($e->getMessage()) . '</p></div>';
        });
    }
}