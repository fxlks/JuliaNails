<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 */
class PFG_Admin
{

    /**
     * The plugin name.
     *
     * @var string
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @var string
     */
    private $version;

    /**
     * Initialize the class.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Initialize hooks for admin functionality.
     */
    public function init()
    {
        // Register meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

        // Register save hook
        add_action('save_post', array($this, 'save_post'), 10, 2);

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Duplicate gallery feature
        add_filter('post_row_actions', array($this, 'add_duplicate_action'), 10, 2);
        add_action('admin_action_pfg_duplicate_gallery', array($this, 'duplicate_gallery'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_styles($hook)
    {
        global $post_type;

        // Only load on our plugin pages
        if ($post_type !== 'awl_filter_gallery' && strpos($hook, 'pfg') === false) {
            return;
        }

        // WordPress color picker
        wp_enqueue_style('wp-color-picker');

        // Main admin styles
        wp_enqueue_style(
            'pfg-admin',
            PFG_PLUGIN_URL . 'admin/css/pfg-admin.css',
            array(),
            $this->version
        );
    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_scripts($hook)
    {
        global $post_type, $post;

        // Only load on our plugin pages
        if ($post_type !== 'awl_filter_gallery' && strpos($hook, 'pfg') === false) {
            return;
        }

        // WordPress dependencies
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');

        // Main admin script
        wp_enqueue_script(
            'pfg-admin',
            PFG_PLUGIN_URL . 'admin/js/pfg-admin.js',
            array('jquery', 'jquery-ui-sortable', 'wp-color-picker'),
            $this->version,
            true
        );

        // Prepare template settings data
        $template_data = array();
        if (class_exists('PFG_Templates')) {
            $templates = PFG_Templates::get_all();
        } else {
            $templates = array(
                'default' => array('name' => __('Default', 'portfolio-filter-gallery')),
                'minimal' => array('name' => __('Minimal', 'portfolio-filter-gallery')),
                'modern' => array('name' => __('Modern', 'portfolio-filter-gallery')),
            );
        }

        foreach ($templates as $id => $template) {
            $template_data[$id] = array(
                'columns' => isset($template['settings']['columns_lg']) ? $template['settings']['columns_lg'] : 3,
                'columns_md' => isset($template['settings']['columns_md']) ? $template['settings']['columns_md'] : 2,
                'columns_sm' => isset($template['settings']['columns_sm']) ? $template['settings']['columns_sm'] : 1,
                'gap' => isset($template['settings']['gap']) ? $template['settings']['gap'] : 20,
                'border_radius' => isset($template['settings']['border_radius']) ? $template['settings']['border_radius'] : 0,
                'hover_effect' => isset($template['settings']['hover_effect']) ? $template['settings']['hover_effect'] : 'fade',
                'show_title' => isset($template['settings']['show_title']) ? (bool) $template['settings']['show_title'] : false,
                'title_position' => isset($template['settings']['title_position']) ? $template['settings']['title_position'] : 'overlay',
                'show_categories' => isset($template['settings']['show_categories']) ? (bool) $template['settings']['show_categories'] : false,
            );
        }

        // Localize script
        wp_localize_script(
            'pfg-admin',
            'pfgAdmin',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pfg_admin_action'),
                'galleryId' => $post ? absint($post->ID) : 0,
                'templateSettings' => $template_data,
                'i18n' => array(
                    'confirmDelete' => __('Are you sure you want to delete this item?', 'portfolio-filter-gallery'),
                    'confirmDeleteAll' => __('Are you sure you want to delete all selected items?', 'portfolio-filter-gallery'),
                    'selectImages' => __('Select Images', 'portfolio-filter-gallery'),
                    'useSelected' => __('Use Selected', 'portfolio-filter-gallery'),
                    'saving' => __('Saving...', 'portfolio-filter-gallery'),
                    'saved' => __('Saved!', 'portfolio-filter-gallery'),
                    'error' => __('An error occurred. Please try again.', 'portfolio-filter-gallery'),
                    'errorAddingFilter' => __('Error adding filter', 'portfolio-filter-gallery'),

                    'serverError' => __('Server error. Please try again.', 'portfolio-filter-gallery'),
                    'errorLoadingPreview' => __('Error loading preview', 'portfolio-filter-gallery'),
                    'noImagesYet' => __('No images yet. Add some to get started!', 'portfolio-filter-gallery'),
                    'savingGallery' => __('Saving Gallery...', 'portfolio-filter-gallery'),
                    'preparing' => __('Preparing...', 'portfolio-filter-gallery'),
                    'savingImages' => __('Saving images:', 'portfolio-filter-gallery'),
                    'saveFailed' => __('Save failed', 'portfolio-filter-gallery'),
                    'networkError' => __('Network error.', 'portfolio-filter-gallery'),
                    'deleteFilter' => __('Delete filter', 'portfolio-filter-gallery'),
                    'filtersWillBeDeleted' => __('filter(s) will be permanently deleted.', 'portfolio-filter-gallery'),
                    'failedDeleteFilters' => __('Failed to delete filters.', 'portfolio-filter-gallery'),
                    'adding' => __('Adding...', 'portfolio-filter-gallery'),

                    'errorOccurred' => __('An error occurred. Please try again.', 'portfolio-filter-gallery'),
                    'confirmRemigrate' => __('This will re-import data from the legacy format, overwriting current values. Continue?', 'portfolio-filter-gallery'),
                    /* translators: %s: Number of images */
                    'confirmRemoveImages' => __('Are you sure you want to remove %s image(s) from the gallery?', 'portfolio-filter-gallery'),
                ),
            )
        );
    }

    /**
     * Register the custom post type for galleries.
     */
    public function register_post_type()
    {
        $labels = array(
            'name' => _x('Portfolio Galleries', 'Post Type General Name', 'portfolio-filter-gallery'),
            'singular_name' => _x('Portfolio Gallery', 'Post Type Singular Name', 'portfolio-filter-gallery'),
            'menu_name' => __('Portfolio Gallery', 'portfolio-filter-gallery'),
            'name_admin_bar' => __('Portfolio Gallery', 'portfolio-filter-gallery'),
            'archives' => __('Gallery Archives', 'portfolio-filter-gallery'),
            'attributes' => __('Gallery Attributes', 'portfolio-filter-gallery'),
            'all_items' => __('All Galleries', 'portfolio-filter-gallery'),
            'add_new_item' => __('Add New Gallery', 'portfolio-filter-gallery'),
            'add_new' => __('Add New', 'portfolio-filter-gallery'),
            'new_item' => __('New Gallery', 'portfolio-filter-gallery'),
            'edit_item' => __('Edit Gallery', 'portfolio-filter-gallery'),
            'update_item' => __('Update Gallery', 'portfolio-filter-gallery'),
            'view_item' => __('View Gallery', 'portfolio-filter-gallery'),
            'view_items' => __('View Galleries', 'portfolio-filter-gallery'),
            'search_items' => __('Search Gallery', 'portfolio-filter-gallery'),
            'not_found' => __('Not found', 'portfolio-filter-gallery'),
            'not_found_in_trash' => __('Not found in Trash', 'portfolio-filter-gallery'),
        );

        $args = array(
            'label' => __('Portfolio Gallery', 'portfolio-filter-gallery'),
            'description' => __('Create filterable portfolio galleries', 'portfolio-filter-gallery'),
            'labels' => $labels,
            'supports' => array('title'),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-grid-view',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'capabilities' => array(
                'edit_post'          => 'edit_post',
                'read_post'          => 'read_post',
                'delete_post'        => 'delete_post',
                'edit_posts'         => 'edit_posts',
                'edit_others_posts'  => 'edit_others_posts',
                'publish_posts'      => 'publish_posts',
                'read_private_posts' => 'read_private_posts',
                'create_posts'       => 'edit_posts',
            ),
            'show_in_rest' => true, // Enable Gutenberg support
        );

        register_post_type('awl_filter_gallery', $args);
    }

    /**
     * Add meta boxes.
     */
    public function add_meta_boxes()
    {
        add_meta_box(
            'pfg-gallery-images',
            __('Gallery Images', 'portfolio-filter-gallery'),
            array($this, 'render_images_meta_box'),
            'awl_filter_gallery',
            'normal',
            'high'
        );

        add_meta_box(
            'pfg-gallery-settings',
            __('Gallery Settings', 'portfolio-filter-gallery'),
            array($this, 'render_settings_meta_box'),
            'awl_filter_gallery',
            'normal',
            'high'
        );

        add_meta_box(
            'pfg-shortcode',
            __('Shortcode', 'portfolio-filter-gallery'),
            array($this, 'render_shortcode_meta_box'),
            'awl_filter_gallery',
            'side',
            'high'
        );
    }

    /**
     * Render images meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_images_meta_box($post)
    {
        $gallery = new PFG_Gallery($post->ID);
        $images = $gallery->get_images();
        $filters = $this->get_filters();

        // Output nonce field for save verification
        wp_nonce_field('pfg_save_gallery', '_pfg_nonce');

        include PFG_PLUGIN_PATH . 'admin/views/meta-box-images.php';
    }

    /**
     * Render settings meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_settings_meta_box($post)
    {
        $gallery = new PFG_Gallery($post->ID);
        $settings = $gallery->get_settings();
        $defaults = $gallery->get_defaults();

        include PFG_PLUGIN_PATH . 'admin/views/meta-box-settings.php';
    }

    /**
     * Render shortcode meta box.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_shortcode_meta_box($post)
    {
        ?>
        <div class="pfg-shortcode-box">
            <p><?php esc_html_e('Copy this shortcode and paste it into your post, page, or text widget:', 'portfolio-filter-gallery'); ?></p>
            <div class="pfg-shortcode-wrapper">
                <code id="pfg-shortcode-code">[PFG id="<?php echo esc_attr($post->ID); ?>"]</code>
                <button type="button" class="button pfg-copy-shortcode" data-clipboard-target="#pfg-shortcode-code">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php esc_html_e('Copy', 'portfolio-filter-gallery'); ?>
                </button>
            </div>
            <p class="pfg-shortcode-note"><?php esc_html_e('Or use the new format:', 'portfolio-filter-gallery'); ?></p>
            <div class="pfg-shortcode-wrapper">
                <code id="pfg-shortcode-new">[portfolio_gallery id="<?php echo esc_attr($post->ID); ?>"]</code>
                <button type="button" class="button pfg-copy-shortcode" data-clipboard-target="#pfg-shortcode-new">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php esc_html_e('Copy', 'portfolio-filter-gallery'); ?>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Save post meta.
     *
     * @param int $post_id Post ID.
     */
    public function save_post($post_id)
    {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check post type
        if (get_post_type($post_id) !== 'awl_filter_gallery') {
            return;
        }

        // Check nonce
        if (!PFG_Security::verify_nonce('_pfg_nonce', 'save_gallery')) {
            return;
        }

        // Check capability
        if (!PFG_Security::can_edit_gallery($post_id)) {
            return;
        }

        // Save gallery settings from pfg_settings array
        $gallery = new PFG_Gallery($post_id);
        $schema = PFG_Gallery::get_schema();

        // Map form field names to schema keys
        $field_map = array(
            'layout' => 'layout_type',
            'columns' => 'columns_lg',
            'gap' => 'gap',
            'show_filters' => 'filters_enabled',
            'show_all_button' => 'show_all_button',
            'all_button_text' => 'all_button_text',
            'filter_position' => 'filters_position',
            'filters_style' => 'filters_style',
            'show_search' => 'search_enabled',
            'search_placeholder' => 'search_placeholder',
            'multi_level_filters' => 'multi_level_filters',
            'filter_logic' => 'filter_logic',
            'show_logic_toggle' => 'show_logic_toggle',
            'show_filter_colors' => 'show_filter_colors',
            'hover_effect' => 'hover_effect',
            'show_title_overlay' => 'show_title',
            'border_radius' => 'border_radius',
            'overlay_color' => 'overlay_color',
            'overlay_opacity' => 'overlay_opacity',
            'primary_color' => 'primary_color',
            'filter_active_color' => 'filter_active_color',
            'filter_text_color' => 'filter_text_color',
            'filter_active_text_color' => 'filter_active_text_color',
            'lazy_loading' => 'lazy_loading',
            'title_position' => 'title_position',
            'show_categories' => 'show_categories',
            'show_description' => 'show_description',
            'caption_bg_color' => 'caption_bg_color',
            'caption_text_color' => 'caption_text_color',
            'template' => 'template',
            // URL Deep Linking
            'deep_linking' => 'deep_linking',
            'url_param_name' => 'url_param_name',
            // Sort Order & Default Filter
            'sort_order' => 'sort_order',
            'hide_type_icons' => 'hide_type_icons',
            'default_filter' => 'default_filter',
            'direction' => 'direction',
            'url_target' => 'url_target',
            // Filter Count
            'show_filter_count' => 'show_filter_count',
            'filter_count_style' => 'filter_count_style',
            // Grid aspect ratio
            'grid_aspect_ratio' => 'grid_aspect_ratio',
            // Image size
            'image_size' => 'image_size',
            // Gallery Preloader
            'show_preloader' => 'show_preloader',
            // Lightbox
            'lightbox' => 'lightbox',
            'show_lightbox_title' => 'show_lightbox_title',
            'show_lightbox_description' => 'show_lightbox_description',
            'show_dual_icons' => 'show_dual_icons',
        );

        // Set each setting — access each $_POST field individually per WordPress coding standards.
        foreach ($field_map as $form_key => $schema_key) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified on line 319, sanitized below.
            if (isset($_POST['pfg_settings'][$form_key])) {
                $type = isset($schema[$schema_key]) ? $schema[$schema_key]['type'] : 'text';
                // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified on line 319.
                $value = PFG_Security::sanitize(wp_unslash($_POST['pfg_settings'][$form_key]), $type);

                $gallery->set_setting($schema_key, $value);
            } else {
                // Handle unchecked checkboxes for boolean fields.
                if (isset($schema[$schema_key]) && $schema[$schema_key]['type'] === 'bool') {
                    $gallery->set_setting($schema_key, false);
                }

            }
        }

        // Set responsive column values (use single column value for all breakpoints for now).
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified on line 319.
        if (isset($_POST['pfg_settings']['columns'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified on line 319.
            $cols = absint(wp_unslash($_POST['pfg_settings']['columns']));
            $gallery->set_setting('columns_xl', $cols);
            $gallery->set_setting('columns_lg', $cols);
            $gallery->set_setting('columns_md', max(2, floor($cols / 2)));
            $gallery->set_setting('columns_sm', 1);
        }

        $gallery->save();

        // Save images
        $this->save_images($post_id);

        // Also save in legacy format for backward compatibility
        $this->save_legacy_format($post_id, $gallery);
    }

    /**
     * Save gallery images.
     *
     * @param int $post_id Post ID.
     */
    protected function save_images($post_id)
    {


        $images = array();

        // Check for JSON-serialized data first (bypasses max_input_vars limit)
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_post() caller.
        if (isset($_POST['pfg_images_json']) && !empty($_POST['pfg_images_json'])) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON string: wp_unslash applied here, individual fields sanitized after json_decode below.
            $json_data = wp_unslash($_POST['pfg_images_json']);

            // Check for special flags
            if ($json_data === '__UNCHANGED__') {
                // Images weren't modified - skip saving images entirely
                return;
            }

            if ($json_data === '__CHUNKED_SAVE__') {
                // Images were already saved via chunked AJAX - skip
                return;
            }

            $raw_images = json_decode($json_data, true);

            if (is_array($raw_images) && !empty($raw_images)) {
                foreach ($raw_images as $image) {
                    if (empty($image['id'])) {
                        continue;
                    }

                    $filters = isset($image['filters']) ? $image['filters'] : '';
                    // Use sanitize_text_field instead of sanitize_key to preserve Unicode filter slugs (Japanese, Chinese, etc.)
                    if (is_string($filters)) {
                        $filters = array_filter(array_map('sanitize_text_field', explode(',', $filters)));
                    } elseif (is_array($filters)) {
                        $filters = array_filter(array_map('sanitize_text_field', $filters));
                    } else {
                        $filters = array();
                    }

                    $type = isset($image['type']) ? sanitize_key($image['type']) : 'image';
                    $link = isset($image['link']) ? esc_url_raw($image['link']) : '';
                    if ( 'image' === $type ) {
                        $link = '';
                    }

                    $images[] = array(
                        'id' => absint($image['id']),
                        'title' => isset($image['title']) ? sanitize_text_field($image['title']) : '',
                        'alt' => isset($image['alt']) ? sanitize_text_field($image['alt']) : '',
                        'description' => isset($image['description']) ? sanitize_textarea_field($image['description']) : '',
                        'link' => $link,
                        'type' => $type,
                        'filters' => $filters,
                        'product_id' => isset($image['product_id']) ? absint($image['product_id']) : 0,
                        'product_name' => isset($image['product_name']) ? sanitize_text_field($image['product_name']) : '',
                        'original_id' => isset($image['original_id']) ? absint($image['original_id']) : absint($image['id']),
                    );
                }

                update_post_meta($post_id, '_pfg_images', $images);
                return;
            }
        }

        // Fallback: If no JSON data, check for individual fields (backwards compatibility)
        // If pfg_images is not set at all, check if we should clear images
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_post() caller.
        if (!isset($_POST['pfg_images'])) {
            // Only clear if this is a gallery post being saved with our nonce
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in save_post() caller.
            if (isset($_POST['_pfg_nonce'])) {
                update_post_meta($post_id, '_pfg_images', array());
                // Also clear legacy format to prevent fallback
                $legacy_key = 'awl_filter_gallery' . $post_id;
                $legacy = get_post_meta($post_id, $legacy_key, true);
                if (is_array($legacy)) {
                    $legacy['image-ids'] = array();
                    $legacy['image_title'] = array();
                    $legacy['image_desc'] = array();
                    $legacy['image-link'] = array();
                    $legacy['slide-type'] = array();
                    $legacy['filters'] = array();
                    $legacy['filter-image'] = array();
                    update_post_meta($post_id, $legacy_key, $legacy);
                }
            }
            return;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified in save_post() caller, nested array: each field sanitized individually below.
        $raw_images = wp_unslash($_POST['pfg_images']);

        if (is_array($raw_images)) {
            foreach ($raw_images as $image) {
                if (empty($image['id'])) {
                    continue;
                }

                $type = isset($image['type']) ? sanitize_key($image['type']) : 'image';
                $link = isset($image['link']) ? esc_url_raw($image['link']) : '';
                if ( 'image' === $type ) {
                    $link = '';
                }

                $images[] = array(
                    'id' => absint($image['id']),
                    'title' => isset($image['title']) ? sanitize_text_field($image['title']) : '',
                    'alt' => isset($image['alt']) ? sanitize_text_field($image['alt']) : '',
                    'description' => isset($image['description']) ? sanitize_textarea_field($image['description']) : '',
                    'link' => $link,
                    'type' => $type,
                    'filters' => isset($image['filters']) ? array_filter(array_map('sanitize_text_field', explode(',', $image['filters']))) : array(),
                    'product_id' => isset($image['product_id']) ? absint($image['product_id']) : 0,
                    'product_name' => isset($image['product_name']) ? sanitize_text_field($image['product_name']) : '',
                    'original_id' => isset($image['original_id']) ? absint($image['original_id']) : absint($image['id']),
                );
            }
        }

        update_post_meta($post_id, '_pfg_images', $images);
    }

    /**
     * Save in legacy format for backward compatibility.
     *
     * @param int         $post_id Post ID.
     * @param PFG_Gallery $gallery Gallery object.
     */
    protected function save_legacy_format($post_id, $gallery)
    {
        $settings = $gallery->get_settings();
        $images = $gallery->get_images();

        // Map column values to legacy Bootstrap classes
        $col_lg_map = array(1 => 'col-lg-12', 2 => 'col-lg-6', 3 => 'col-lg-4', 4 => 'col-lg-3', 5 => 'col-lg-2', 6 => 'col-lg-2');
        $col_md_map = array(1 => 'col-sm-12', 2 => 'col-sm-6', 3 => 'col-sm-4', 4 => 'col-sm-3', 5 => 'col-sm-2', 6 => 'col-sm-2');
        $col_sm_map = array(1 => 'col-xs-12', 2 => 'col-xs-6', 3 => 'col-xs-4', 4 => 'col-xs-3', 5 => 'col-xs-2', 6 => 'col-xs-2');

        // Transform to legacy format
        $legacy = array(
            'image-ids' => array(),
            'image_title' => array(),
            'image_desc' => array(),
            'image-link' => array(),
            'slide-type' => array(),
            'filters' => array(),
            'filter-image' => array(),
            'gal_size' => 'medium',
            'col_large_desktops' => isset($col_lg_map[$settings['columns_xl']]) ? $col_lg_map[$settings['columns_xl']] : 'col-lg-3',
            'col_desktops' => isset($col_lg_map[$settings['columns_lg']]) ? $col_lg_map[$settings['columns_lg']] : 'col-lg-3',
            'col_tablets' => isset($col_md_map[$settings['columns_md']]) ? $col_md_map[$settings['columns_md']] : 'col-sm-4',
            'col_phones' => isset($col_sm_map[$settings['columns_sm']]) ? $col_sm_map[$settings['columns_sm']] : 'col-xs-6',
            'no_spacing' => $settings['gap'] == 0 ? 1 : 0,
            'gallery_direction' => $settings['direction'],
            'title_thumb' => $settings['show_title'] ? 'show' : 'hide',
            'image_numbering' => $settings['show_numbering'] ? 1 : 0,
            'gray_scale' => $settings['grayscale'] ? 1 : 0,
            'image_hover_effect_four' => $settings['hover_effect'] !== 'none' ? 'hvr-box-shadow-outset' : 'none',
            'thumb_border' => $settings['border_width'] > 0 ? 'yes' : 'no',
            'hide_filters' => $settings['filters_enabled'] ? 0 : 1,
            'filter_position' => $settings['filters_position'],
            'all_txt' => $settings['all_button_text'],
            'sort_filter_order' => $settings['sort_filters'] ? 1 : 0,
            'filter_bg' => $settings['filter_bg_color'],
            'filter_title_color' => $settings['filter_text_color'],
            'light-box' => 0,
            'url_target' => $settings['url_target'],
            'search_box' => $settings['search_enabled'] ? 1 : 0,
            'search_txt' => $settings['search_placeholder'],
            'sort_by_title' => in_array($settings['sort_order'], array('title_asc', 'title_desc'), true) ? ($settings['sort_order'] === 'title_desc' ? 'desc' : 'asc') : 'no',
            'bootstrap_disable' => 'no',

            'show_image_count' => $settings['show_image_count'] ? 1 : 0,
        );

        // Add images to legacy format
        foreach ($images as $image) {
            $image_id = $image['id'];

            $legacy['image-ids'][] = $image_id;
            $legacy['image_title'][] = $image['title'];
            $legacy['image_desc'][] = isset($image['description']) ? $image['description'] : '';
            $legacy['image-link'][] = isset($image['link']) ? $image['link'] : '';
            $legacy['slide-type'][] = isset($image['type']) ? $image['type'] : 'image';

            // Add filters for this image
            if (!empty($image['filters'])) {
                $legacy['filters'][$image_id] = $image['filters'];

                // Build the filter-image reverse mapping
                foreach ($image['filters'] as $filter_id) {
                    if (!isset($legacy['filter-image'][$filter_id])) {
                        $legacy['filter-image'][$filter_id] = array();
                    }
                    $legacy['filter-image'][$filter_id][] = $image_id;
                }
            }
        }

        $legacy_key = 'awl_filter_gallery' . $post_id;
        update_post_meta($post_id, $legacy_key, $legacy);
    }

    /**
     * Get all filters.
     *
     * @return array
     */
    public function get_filters()
    {
        // Try new format first
        $filters = get_option('pfg_filters', array());

        if (!empty($filters)) {
            return $filters;
        }

        // Fall back to legacy format
        $legacy = get_option('awl_portfolio_filter_gallery_categories', array());
        $result = array();

        foreach ($legacy as $id => $name) {
            // Handle non-Latin characters in slug generation
            $slug = sanitize_title($name);
            // If empty OR URL-encoded (contains %xx hex), use Unicode-aware slug
            if (empty($slug) || preg_match('/%[0-9a-f]{2}/i', $slug)) {
                // Keep Unicode letters and numbers, use mb_strtolower for proper UTF-8 handling
                $slug = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/ui', '-', $name), 'UTF-8');
                $slug = trim($slug, '-');
                if (empty($slug)) {
                    $slug = 'filter-' . substr(md5($name), 0, 8);
                }
            }

            $result[] = array(
                'id' => sanitize_key($id) ?: 'filter' . substr(md5($name), 0, 8),
                'name' => sanitize_text_field($name),
                'slug' => $slug,
            );
        }

        return $result;
    }

    /**
     * Add admin menu pages.
     */
    public function add_menu_pages()
    {
        add_submenu_page(
            'edit.php?post_type=awl_filter_gallery',
            __('Filters', 'portfolio-filter-gallery'),
            __('Filters', 'portfolio-filter-gallery'),
            'edit_posts',
            'pfg-filters',
            array($this, 'render_filters_page')
        );

        add_submenu_page(
            'edit.php?post_type=awl_filter_gallery',
            __('Settings', 'portfolio-filter-gallery'),
            __('Settings', 'portfolio-filter-gallery'),
            'manage_options',
            'pfg-settings',
            array($this, 'render_settings_page')
        );



        add_submenu_page(
            'edit.php?post_type=awl_filter_gallery',
            __('Docs', 'portfolio-filter-gallery'),
            __('Docs', 'portfolio-filter-gallery'),
            'edit_posts',
            'pfg-docs',
            array($this, 'render_docs_page')
        );

        add_submenu_page(
            'edit.php?post_type=awl_filter_gallery',
            __('Pro Features', 'portfolio-filter-gallery'),
            __('Pro Features', 'portfolio-filter-gallery'),
            'edit_posts',
            'pfg-pro-features',
            array($this, 'render_pro_features_page')
        );

        // "Go Pro" external link - highlighted in the menu.
        global $submenu;
        $submenu['edit.php?post_type=awl_filter_gallery'][] = array(
            '<span style="color: #f59e0b; font-weight: 600;">' . esc_html__('Go Pro', 'portfolio-filter-gallery') . '</span>',
            'edit_posts',
            'https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/',
        );
    }

    /**
     * Render filters management page.
     */
    public function render_filters_page()
    {
        $filters = $this->get_filters();
        include PFG_PLUGIN_PATH . 'admin/views/page-filters.php';
    }

    /**
     * Render global settings page.
     */
    public function render_settings_page()
    {
        include PFG_PLUGIN_PATH . 'admin/views/page-settings.php';
    }

    /**
     * Render documentation page.
     */
    public function render_docs_page()
    {
        include PFG_PLUGIN_DIR . 'admin/views/page-docs.php';
    }

    /**
     * Render pro features page.
     */
    public function render_pro_features_page()
    {
        include PFG_PLUGIN_PATH . 'admin/views/page-pro-features.php';
    }

    /**
     * Add shortcode column to gallery list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function add_shortcode_column($columns)
    {
        $new_columns = array();

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['shortcode'] = __('Shortcode', 'portfolio-filter-gallery');
                $new_columns['images'] = __('Images', 'portfolio-filter-gallery');
                $new_columns['layout'] = __('Layout', 'portfolio-filter-gallery');
                $new_columns['filters'] = __('Filters', 'portfolio-filter-gallery');
                $new_columns['source'] = __('Source', 'portfolio-filter-gallery');
                $new_columns['lightbox'] = __('Lightbox', 'portfolio-filter-gallery');
            }
        }

        return $new_columns;
    }

    /**
     * Render custom column content.
     *
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public function render_column_content($column, $post_id)
    {
        switch ($column) {
            case 'shortcode':
                echo '<code>[PFG id="' . esc_attr($post_id) . '"]</code>';
                break;

            case 'images':
                $gallery = new PFG_Gallery($post_id);
                $images = $gallery->get_images();
                echo '<span class="pfg-image-count">' . esc_html(count($images)) . '</span>';
                break;

            case 'layout':
                $gallery_settings = get_post_meta($post_id, '_pfg_settings', true);
                if ( empty( $gallery_settings ) ) {
                    $gallery_settings = get_post_meta($post_id, 'pfg_settings', true);
                }
                $layout = isset($gallery_settings['layout_type']) ? ucfirst($gallery_settings['layout_type']) : 'Grid';
                $columns = isset($gallery_settings['columns_xl']) ? $gallery_settings['columns_xl'] : '4';
                echo '<strong>' . esc_html($layout) . '</strong><br>';
                echo '<small>' . esc_html($columns) . ' cols</small>';
                break;

            case 'filters':
                $gallery_settings = get_post_meta($post_id, '_pfg_settings', true);
                if ( empty( $gallery_settings ) ) {
                    $gallery_settings = get_post_meta($post_id, 'pfg_settings', true);
                }
                $style = isset($gallery_settings['filters_style']) ? ucfirst($gallery_settings['filters_style']) : 'Buttons';
                
                $gallery = new PFG_Gallery($post_id);
                $images = $gallery->get_images();
                $categories = array();
                foreach ( $images as $image ) {
                    if ( ! empty( $image['filters'] ) ) {
                        $cats = is_array( $image['filters'] ) ? $image['filters'] : explode( ',', $image['filters'] );
                        foreach ( $cats as $c ) {
                            if ( ! empty( trim( $c ) ) ) {
                                $categories[] = trim( $c );
                            }
                        }
                    }
                }
                $filters = array_unique( array_filter( $categories ) );
                
                echo '<strong>' . esc_html(count($filters)) . '</strong><br>';
                echo '<small>' . esc_html($style) . '</small>';
                break;

            case 'source':
                echo '<span class="pfg-badge pfg-badge-blue">Media</span>';
                break;

            case 'lightbox':
                $gallery_settings = get_post_meta($post_id, '_pfg_settings', true);
                if ( empty( $gallery_settings ) ) {
                    $gallery_settings = get_post_meta($post_id, 'pfg_settings', true);
                }
                if (isset($gallery_settings['lightbox']) && $gallery_settings['lightbox'] === 'none') {
                    echo esc_html__('None', 'portfolio-filter-gallery');
                } else {
                    $global_settings = get_option('pfg_global_settings', array());
                    $active_lightbox = isset($global_settings['lightbox']) ? $global_settings['lightbox'] : 'built-in';
                    echo esc_html($active_lightbox === 'ld-lightbox' ? 'LD Lightbox' : 'Built-in');
                }
                break;

        }
    }

    /**
     * Add duplicate action link to gallery row actions.
     *
     * @param array   $actions Existing row actions.
     * @param WP_Post $post    The post object.
     * @return array Modified row actions.
     */
    public function add_duplicate_action($actions, $post)
    {
        if ($post->post_type !== 'awl_filter_gallery') {
            return $actions;
        }

        if (!current_user_can('edit_posts')) {
            return $actions;
        }

        $duplicate_url = wp_nonce_url(
            admin_url('admin.php?action=pfg_duplicate_gallery&gallery_id=' . $post->ID),
            'pfg_duplicate_gallery_' . $post->ID
        );

        $actions['duplicate'] = sprintf(
            '<a href="%s" title="%s" style="color: #2271b1;"><span class="dashicons dashicons-admin-page" style="font-size: 14px; vertical-align: text-bottom;"></span> %s</a>',
            esc_url($duplicate_url),
            esc_attr__('Duplicate this gallery', 'portfolio-filter-gallery'),
            esc_html__('Duplicate', 'portfolio-filter-gallery')
        );

        return $actions;
    }

    /**
     * Handle gallery duplication.
     */
    public function duplicate_gallery()
    {
        // Verify request
        if (!isset($_GET['gallery_id']) || !isset($_GET['_wpnonce'])) {
            wp_die(esc_html__('Invalid request.', 'portfolio-filter-gallery'));
        }

        $gallery_id = absint(wp_unslash($_GET['gallery_id']));

        // Verify nonce
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'pfg_duplicate_gallery_' . $gallery_id)) {
            wp_die(esc_html__('Security check failed.', 'portfolio-filter-gallery'));
        }

        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_die(esc_html__('You do not have permission to duplicate galleries.', 'portfolio-filter-gallery'));
        }

        // Get original gallery
        $original = get_post($gallery_id);
        if (!$original || $original->post_type !== 'awl_filter_gallery') {
            wp_die(esc_html__('Gallery not found.', 'portfolio-filter-gallery'));
        }

        // Create duplicate post
        $new_gallery = array(
            'post_title' => sprintf(
                /* translators: %s: Original gallery title */
                __('%s (Copy)', 'portfolio-filter-gallery'),
                $original->post_title
            ),
            'post_status' => 'draft',
            'post_type' => 'awl_filter_gallery',
            'post_author' => get_current_user_id(),
            'post_content' => $original->post_content,
            'post_excerpt' => $original->post_excerpt,
        );

        $new_id = wp_insert_post($new_gallery);

        if (is_wp_error($new_id)) {
            wp_die(esc_html__('Failed to duplicate gallery.', 'portfolio-filter-gallery'));
        }

        // Copy all post meta
        $meta_keys = get_post_custom_keys($gallery_id);
        if (!empty($meta_keys)) {
            foreach ($meta_keys as $meta_key) {
                // Skip internal WordPress meta
                if (strpos($meta_key, '_edit_') === 0) {
                    continue;
                }

                $meta_values = get_post_meta($gallery_id, $meta_key);
                foreach ($meta_values as $meta_value) {
                    add_post_meta($new_id, $meta_key, $meta_value);
                }
            }
        }

        // Redirect to edit the new gallery
        wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_id));
        exit;
    }
}
