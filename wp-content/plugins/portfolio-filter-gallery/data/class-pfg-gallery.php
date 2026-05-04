<?php
/**
 * Gallery data model class.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/data
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Gallery data model for handling gallery settings and images.
 */
class PFG_Gallery {

    /**
     * Gallery post ID.
     *
     * @var int
     */
    protected $id;

    /**
     * Gallery post object.
     *
     * @var WP_Post|null
     */
    protected $post;

    /**
     * Gallery settings.
     *
     * @var array
     */
    protected $settings;

    /**
     * Default settings schema with types for sanitization.
     *
     * @var array
     */
    protected static $settings_schema = array(
        // Layout settings
        'layout_type'          => array( 'default' => 'masonry', 'type' => 'key' ),
        'columns_xl'           => array( 'default' => 4, 'type' => 'int' ),
        'columns_lg'           => array( 'default' => 3, 'type' => 'int' ),
        'columns_md'           => array( 'default' => 2, 'type' => 'int' ),
        'columns_sm'           => array( 'default' => 1, 'type' => 'int' ),
        'gap'                  => array( 'default' => 20, 'type' => 'int' ),
        'direction'            => array( 'default' => 'ltr', 'type' => 'key' ),

        // Grid aspect ratio
        'grid_aspect_ratio'    => array( 'default' => '4:3', 'type' => 'text' ),

        // Thumbnail settings
        'show_title'           => array( 'default' => true, 'type' => 'bool' ),
        'show_description'     => array( 'default' => false, 'type' => 'bool' ),
        'title_position'       => array( 'default' => 'overlay', 'type' => 'key' ),
        'show_categories'      => array( 'default' => false, 'type' => 'bool' ),
        'caption_bg_color'     => array( 'default' => '#ffffff', 'type' => 'hex_color' ),
        'caption_text_color'   => array( 'default' => '#1e293b', 'type' => 'hex_color' ),
        'show_numbering'       => array( 'default' => false, 'type' => 'bool' ),
        'border_width'         => array( 'default' => 0, 'type' => 'int' ),
        'border_color'         => array( 'default' => '#ffffff', 'type' => 'hex_color' ),
        'border_radius'        => array( 'default' => 0, 'type' => 'int' ),
        'grayscale'            => array( 'default' => false, 'type' => 'bool' ),
        'hover_effect'         => array( 'default' => 'fade', 'type' => 'key' ),
        'overlay_color'        => array( 'default' => '#000000', 'type' => 'hex_color' ),
        'overlay_opacity'      => array( 'default' => 70, 'type' => 'int' ),
        'primary_color'        => array( 'default' => '#94a3b8', 'type' => 'hex_color' ),

        // Filter settings
        'filters_enabled'      => array( 'default' => true, 'type' => 'bool' ),
        'filters_position'     => array( 'default' => 'top', 'type' => 'key' ),
        'filters_style'        => array( 'default' => 'buttons', 'type' => 'key' ),
        'show_all_button'      => array( 'default' => true, 'type' => 'bool' ),
        'all_button_text'      => array( 'default' => 'All', 'type' => 'text' ),
        'sort_filters'         => array( 'default' => false, 'type' => 'bool' ),
        'multi_level_filters'  => array( 'default' => false, 'type' => 'bool' ),
        'filter_logic'         => array( 'default' => 'or', 'type' => 'key' ),
        'show_logic_toggle'    => array( 'default' => true, 'type' => 'bool' ),
        'show_filter_colors'   => array( 'default' => false, 'type' => 'bool' ),
        'filter_bg_color'      => array( 'default' => '#3858e9', 'type' => 'hex_color' ),
        'filter_text_color'    => array( 'default' => 'auto', 'type' => 'text' ),
        'filter_active_color'  => array( 'default' => '#3858e9', 'type' => 'hex_color' ),
        'filter_active_text_color' => array( 'default' => 'auto', 'type' => 'text' ),

        // Link settings
        'url_target'           => array( 'default' => '_blank', 'type' => 'key' ),

        // Search settings
        'search_enabled'       => array( 'default' => false, 'type' => 'bool' ),
        'search_placeholder'   => array( 'default' => 'Search...', 'type' => 'text' ),

        // Sort settings
        'sort_order'           => array( 'default' => 'custom', 'type' => 'key' ),
        'sort_by_title'        => array( 'default' => '', 'type' => 'key' ), // Legacy - kept for backward compat

        // Advanced settings

        'bootstrap_disabled'   => array( 'default' => false, 'type' => 'bool' ),
        'lazy_loading'         => array( 'default' => false, 'type' => 'bool' ),
        'show_image_count'     => array( 'default' => false, 'type' => 'bool' ),

        // Template setting
        'template'             => array( 'default' => 'modern-cards', 'type' => 'key' ),

        // Lightbox setting
        'lightbox'             => array( 'default' => 'built-in', 'type' => 'key' ),
        'show_lightbox_title'  => array( 'default' => true, 'type' => 'bool' ),
        'show_lightbox_description' => array( 'default' => false, 'type' => 'bool' ),
        'show_dual_icons'      => array( 'default' => false, 'type' => 'bool' ),

        // URL Deep Linking
        'deep_linking'         => array( 'default' => false, 'type' => 'bool' ),
        'url_param_name'       => array( 'default' => 'filter', 'type' => 'key' ),

        // Shuffle Images
        'shuffle_images'       => array( 'default' => false, 'type' => 'bool' ),

        // Gallery Preloader
        'show_preloader'       => array( 'default' => true, 'type' => 'bool' ),

        // Hide Type Icons (video/link indicators)
        'hide_type_icons'      => array( 'default' => false, 'type' => 'bool' ),

        // Default Filter
        'default_filter'       => array( 'default' => '', 'type' => 'key' ),

        // Filter Count
        'show_filter_count'    => array( 'default' => false, 'type' => 'bool' ),
        'filter_count_style'   => array( 'default' => 'always', 'type' => 'key' ),

        // Image Size
        'image_size'           => array( 'default' => 'large', 'type' => 'key' ),


        // Watermark Settings
        'watermark_enabled'    => array( 'default' => false, 'type' => 'bool' ),
        'watermark_type'       => array( 'default' => 'text', 'type' => 'key' ),
        'watermark_text'       => array( 'default' => '', 'type' => 'text' ),
        'watermark_image'      => array( 'default' => '', 'type' => 'url' ),
        'watermark_position'   => array( 'default' => 'bottom-right', 'type' => 'key' ),
        'watermark_opacity'    => array( 'default' => 50, 'type' => 'int' ),
        'watermark_size'       => array( 'default' => 24, 'type' => 'int' ), // Font size for text
        'watermark_image_size' => array( 'default' => 15, 'type' => 'int' ), // % of image width
    );

    /**
     * Constructor.
     *
     * @param int|WP_Post $gallery Gallery ID or post object.
     */
    public function __construct( $gallery = 0 ) {
        if ( $gallery instanceof WP_Post ) {
            $this->id   = $gallery->ID;
            $this->post = $gallery;
        } elseif ( is_numeric( $gallery ) && $gallery > 0 ) {
            $this->id   = absint( $gallery );
            $this->post = get_post( $this->id );
        }

        $this->load_settings();
    }

    /**
     * Get gallery ID.
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get gallery title.
     *
     * @return string
     */
    public function get_title() {
        return $this->post ? $this->post->post_title : '';
    }

    /**
     * Check if gallery exists.
     *
     * @return bool
     */
    public function exists() {
        return $this->post && $this->post->post_type === 'awl_filter_gallery';
    }

    /**
     * Load settings from database.
     */
    protected function load_settings() {
        $this->settings = array();

        if ( ! $this->id ) {
            $this->settings = $this->get_defaults();
            return;
        }

        // Try new format first
        $settings = get_post_meta( $this->id, '_pfg_settings', true );

        if ( empty( $settings ) ) {
            // Fall back to legacy format
            $legacy_key = 'awl_filter_gallery' . $this->id;
            $legacy     = get_post_meta( $this->id, $legacy_key, true );

            if ( ! empty( $legacy ) ) {
                $settings = $this->transform_legacy_settings( $legacy );
                
                // Auto-save migrated settings so they persist
                update_post_meta( $this->id, '_pfg_settings', $settings );
                
                // Also migrate images if present
                if ( isset( $legacy['image-ids'] ) ) {
                    $images = $this->transform_legacy_images( $legacy );
                    update_post_meta( $this->id, '_pfg_images', $images );
                }
            }
        }

        // Merge with defaults
        $this->settings = wp_parse_args( $settings, $this->get_defaults() );
    }

    /**
     * Get default settings.
     *
     * @return array
     */
    public function get_defaults() {
        $defaults = array();
        foreach ( self::$settings_schema as $key => $config ) {
            $defaults[ $key ] = $config['default'];
        }
        return $defaults;
    }

    /**
     * Get all settings.
     *
     * @param array $overrides Optional settings overrides from shortcode attributes.
     * @return array
     */
    public function get_settings( $overrides = array() ) {
        return wp_parse_args( $overrides, $this->settings );
    }

    /**
     * Get a single setting.
     *
     * @param string $key     Setting key.
     * @param mixed  $default Default value if not set.
     * @return mixed
     */
    public function get_setting( $key, $default = null ) {
        if ( isset( $this->settings[ $key ] ) ) {
            return $this->settings[ $key ];
        }

        if ( $default !== null ) {
            return $default;
        }

        return isset( self::$settings_schema[ $key ] ) ? self::$settings_schema[ $key ]['default'] : null;
    }

    /**
     * Set a setting value.
     *
     * @param string $key   Setting key.
     * @param mixed  $value Setting value.
     */
    public function set_setting( $key, $value ) {
        if ( isset( self::$settings_schema[ $key ] ) ) {
            $type = self::$settings_schema[ $key ]['type'];
            $this->settings[ $key ] = PFG_Security::sanitize( $value, $type );
        }
    }

    /**
     * Save settings to database.
     *
     * @return bool
     */
    public function save() {
        if ( ! $this->id ) {
            return false;
        }

        // Sanitize all settings before saving
        $sanitized = $this->sanitize_settings( $this->settings );

        return update_post_meta( $this->id, '_pfg_settings', $sanitized );
    }

    /**
     * Sanitize settings array.
     *
     * @param array $settings Raw settings.
     * @return array Sanitized settings.
     */
    protected function sanitize_settings( $settings ) {
        $sanitized = array();

        foreach ( self::$settings_schema as $key => $config ) {
            if ( isset( $settings[ $key ] ) ) {
                $sanitized[ $key ] = PFG_Security::sanitize( $settings[ $key ], $config['type'] );
            } else {
                $sanitized[ $key ] = $config['default'];
            }
        }

        return $sanitized;
    }

    public function get_images() {
        $images_meta = get_post_meta( $this->id, '_pfg_images', true );

        if ( empty( $images_meta ) ) {
            // Try legacy format
            $legacy_key = 'awl_filter_gallery' . $this->id;
            $legacy     = get_post_meta( $this->id, $legacy_key, true );

            if ( ! empty( $legacy ) && isset( $legacy['image-ids'] ) ) {
                return $this->transform_legacy_images( $legacy );
            }

            return array();
        }

        if ( is_array( $images_meta ) ) {
            // Apply late migration for link types if they were broken in 2.1.x
            foreach ( $images_meta as &$image ) {
                if ( ( ! isset( $image['type'] ) || $image['type'] === 'image' ) && ! empty( $image['link'] ) ) {
                    // Check if it looks like a video link
                    if ( strpos( $image['link'], 'youtube.com' ) !== false || strpos( $image['link'], 'youtu.be' ) !== false || strpos( $image['link'], 'vimeo.com' ) !== false ) {
                        $image['type'] = 'video';
                    } else {
                        // Otherwise it's an external URL
                        $image['type'] = 'url';
                    }
                }
            }
            return $images_meta;
        }

        return array();
    }

    /**
     * Transform legacy settings format to new format.
     *
     * @param array $legacy Legacy settings array.
     * @return array Transformed settings.
     */
    protected function transform_legacy_settings( $legacy ) {
        $settings = array();

        // Helper function to convert Bootstrap column class to column count
        $col_to_num = function( $col_class ) {
            // Map Bootstrap column classes to number of columns
            $mappings = array(
                'col-lg-12' => 1, 'col-md-12' => 1, 'col-sm-12' => 1, 'col-xs-12' => 1, 'col-12' => 1,
                'col-lg-6'  => 2, 'col-md-6'  => 2, 'col-sm-6'  => 2, 'col-xs-6'  => 2, 'col-6' => 2,
                'col-lg-4'  => 3, 'col-md-4'  => 3, 'col-sm-4'  => 3, 'col-xs-4'  => 3, 'col-4' => 3,
                'col-lg-3'  => 4, 'col-md-3'  => 4, 'col-sm-3'  => 4, 'col-xs-3'  => 3, 'col-3' => 4,
                'col-lg-2'  => 6, 'col-md-2'  => 6, 'col-sm-2'  => 6, 'col-xs-2'  => 6, 'col-2' => 6,
                'col-lg-1'  => 12, 'col-md-1' => 12, 'col-sm-1' => 12, 'col-xs-1' => 12, 'col-1' => 12,
            );
            return isset( $mappings[ $col_class ] ) ? $mappings[ $col_class ] : 4;
        };

        // Layout mappings
        $settings['layout_type']      = isset( $legacy['gal_size'] ) ? $legacy['gal_size'] : 'masonry';
        $settings['columns_xl']       = isset( $legacy['col_large_desktops'] ) ? $col_to_num( $legacy['col_large_desktops'] ) : 4;
        $settings['columns_lg']       = isset( $legacy['col_desktops'] ) ? $col_to_num( $legacy['col_desktops'] ) : 3;
        $settings['columns_md']       = isset( $legacy['col_tablets'] ) ? $col_to_num( $legacy['col_tablets'] ) : 2;
        $settings['columns_sm']       = isset( $legacy['col_phones'] ) ? $col_to_num( $legacy['col_phones'] ) : 1;
        $settings['gap']              = isset( $legacy['no_spacing'] ) && $legacy['no_spacing'] ? 0 : 20;
        $settings['direction']        = isset( $legacy['gallery_direction'] ) ? $legacy['gallery_direction'] : 'ltr';

        // Thumbnail mappings - check for both 'show/hide' and 'yes/no' formats
        $settings['show_title']       = isset( $legacy['title_thumb'] ) && in_array( $legacy['title_thumb'], array( 'show', 'yes' ), true );
        $settings['show_numbering']   = isset( $legacy['image_numbering'] ) && ( $legacy['image_numbering'] === 'yes' || $legacy['image_numbering'] == 1 );
        $settings['grayscale']        = isset( $legacy['gray_scale'] ) && ( $legacy['gray_scale'] === 'yes' || $legacy['gray_scale'] == 1 );
        $settings['hover_effect']     = isset( $legacy['image_hover_effect_four'] ) ? $legacy['image_hover_effect_four'] : 'fade';

        // Border mappings  
        $settings['border_width']     = isset( $legacy['thumb_border'] ) && $legacy['thumb_border'] === 'yes' ? 1 : 0;
        $settings['border_color']     = isset( $legacy['border_color'] ) ? $legacy['border_color'] : '#ffffff';

        // Filter mappings - check for both 0/1 and yes/no formats
        $settings['filters_enabled']  = ! ( isset( $legacy['hide_filters'] ) && ( $legacy['hide_filters'] === 'yes' || $legacy['hide_filters'] == 1 ) );
        $settings['filters_position'] = isset( $legacy['filter_position'] ) ? $legacy['filter_position'] : 'center';
        $settings['show_all_button']  = true;
        $settings['all_button_text']  = isset( $legacy['all_txt'] ) ? $legacy['all_txt'] : 'All';
        $settings['sort_filters']     = isset( $legacy['sort_filter_order'] ) && ( $legacy['sort_filter_order'] === 'yes' || $legacy['sort_filter_order'] == 1 );
        $settings['filter_bg_color']  = isset( $legacy['filter_bg'] ) ? $legacy['filter_bg'] : '#3858e9';
        $settings['filter_text_color']= isset( $legacy['filter_title_color'] ) ? $legacy['filter_title_color'] : '#ffffff';
        // Copy filter_bg_color to filter_active_color for consistent styling
        $settings['filter_active_color'] = $settings['filter_bg_color'];

        // Lightbox mappings - convert numeric values
        // Old plugin: 0 = None, 4 = LD Lightbox, 5 = Bootstrap Lightbox
        $lightbox_map = array( 0 => 'none', 4 => 'built-in', 5 => 'built-in' );
        $lightbox_val = isset( $legacy['light-box'] ) ? $legacy['light-box'] : 4;
        $settings['lightbox']         = isset( $lightbox_map[ $lightbox_val ] ) ? $lightbox_map[ $lightbox_val ] : 'built-in';
        $settings['url_target']       = isset( $legacy['url_target'] ) ? $legacy['url_target'] : '_blank';

        // Search mappings
        $settings['search_enabled']   = isset( $legacy['search_box'] ) && ( $legacy['search_box'] === 'yes' || $legacy['search_box'] == 1 );
        $settings['search_placeholder'] = isset( $legacy['search_txt'] ) ? $legacy['search_txt'] : 'Search...';

        // Sort mappings
        $settings['sort_by_title']    = isset( $legacy['sort_by_title'] ) && in_array( $legacy['sort_by_title'], array( 'asc', 'desc', 'yes' ), true );

        // Advanced mappings

        $settings['bootstrap_disabled'] = isset( $legacy['bootstrap_disable'] ) && $legacy['bootstrap_disable'] === 'yes';
        
        // Filter count migration - the old plugin used 'show_image_count' for showing counts on filters
        // Check multiple possible legacy keys
        $show_filter_count = false;
        if ( isset( $legacy['show_image_count'] ) && ( $legacy['show_image_count'] === 'yes' || $legacy['show_image_count'] == 1 ) ) {
            $show_filter_count = true;
        }
        if ( isset( $legacy['show_filter_count'] ) && ( $legacy['show_filter_count'] === 'yes' || $legacy['show_filter_count'] == 1 ) ) {
            $show_filter_count = true;
        }
        if ( isset( $legacy['filter_count'] ) && ( $legacy['filter_count'] === 'yes' || $legacy['filter_count'] == 1 ) ) {
            $show_filter_count = true;
        }
        $settings['show_filter_count'] = $show_filter_count;
        $settings['show_image_count'] = $show_filter_count; // Keep in sync

        return $settings;
    }

    /**
     * Transform legacy images format to new format.
     *
     * @param array $legacy Legacy settings array.
     * @return array Transformed images array.
     */
    protected function transform_legacy_images( $legacy ) {
        $images = array();

        if ( ! isset( $legacy['image-ids'] ) || ! is_array( $legacy['image-ids'] ) ) {
            return $images;
        }

        $image_ids   = $legacy['image-ids'];
        $titles      = isset( $legacy['image_title'] ) ? $legacy['image_title'] : array();
        
        // Note: Legacy uses 'image-desc' (hyphen, indexed array)
        $descs       = isset( $legacy['image-desc'] ) ? $legacy['image-desc'] : array();
        
        // Note: Legacy uses 'slide-alt' (keyed by image ID)
        $alts        = isset( $legacy['slide-alt'] ) ? $legacy['slide-alt'] : array();
        
        // Note: Legacy uses 'image-link' (keyed by image ID)
        $links       = isset( $legacy['image-link'] ) ? $legacy['image-link'] : array();
        
        // Note: Legacy uses 'slide-type' (keyed by image ID)
        $types       = isset( $legacy['slide-type'] ) ? $legacy['slide-type'] : array();
        
        $filters     = isset( $legacy['filters'] ) ? $legacy['filters'] : array();

        // Build a lookup map: filter ID => filter slug
        $filter_id_to_slug = $this->build_legacy_filter_map();

        foreach ( $image_ids as $index => $id ) {
            $id = absint( $id );
            if ( ! $id ) {
                continue;
            }

            // Try both index and id as keys for legacy compatibility
            $legacy_filter_ids = array();
            if ( isset( $filters[ $index ] ) && is_array( $filters[ $index ] ) ) {
                $legacy_filter_ids = array_map( 'absint', $filters[ $index ] );
            } elseif ( isset( $filters[ $id ] ) && is_array( $filters[ $id ] ) ) {
                $legacy_filter_ids = array_map( 'absint', $filters[ $id ] );
            }

            // Convert legacy filter IDs to filter slugs
            $filter_slugs = array();
            foreach ( $legacy_filter_ids as $filter_id ) {
                if ( isset( $filter_id_to_slug[ $filter_id ] ) ) {
                    $filter_slugs[] = $filter_id_to_slug[ $filter_id ];
                }
            }

            // Get alt text: Legacy 'slide-alt' is keyed by image ID, not index
            $alt_text = '';
            if ( isset( $alts[ $id ] ) && ! empty( $alts[ $id ] ) ) {
                $alt_text = $alts[ $id ];
            } elseif ( isset( $alts[ $index ] ) && ! empty( $alts[ $index ] ) ) {
                $alt_text = $alts[ $index ];
            } else {
                // Fall back to WordPress attachment alt text
                $alt_text = get_post_meta( $id, '_wp_attachment_image_alt', true );
            }
            
            // Get description: Legacy 'image-desc' is indexed array
            $description = '';
            if ( isset( $descs[ $index ] ) && ! empty( $descs[ $index ] ) ) {
                $description = $descs[ $index ];
            }
            
            // Get link: Legacy 'image-link' is keyed by image ID
            $link = '';
            if ( isset( $links[ $id ] ) && ! empty( $links[ $id ] ) ) {
                $link = $links[ $id ];
            } elseif ( isset( $links[ $index ] ) && ! empty( $links[ $index ] ) ) {
                $link = $links[ $index ];
            }
            
            // Get type: Legacy 'slide-type' is keyed by image ID
            // In legacy: 'image' = lightbox, 'video' = video lightbox
            // In new format: 'image' = lightbox, 'video' = video lightbox, 'url' = external link
            // If legacy type is 'image' but has a link URL, it means external link (type='url')
            $type = 'image';
            if ( isset( $types[ $id ] ) && ! empty( $types[ $id ] ) ) {
                $type = $types[ $id ];
            } elseif ( isset( $types[ $index ] ) && ! empty( $types[ $index ] ) ) {
                $type = $types[ $index ];
            }
            
            // Convert legacy 'image' type with link to new 'url' type (external link)
            if ( $type === 'image' && ! empty( $link ) ) {
                $type = 'url';
            }

            $images[] = array(
                'id'          => $id,
                'title'       => isset( $titles[ $index ] ) ? sanitize_text_field( $titles[ $index ] ) : sanitize_text_field( get_the_title( $id ) ),
                'alt'         => sanitize_text_field( $alt_text ),
                'description' => wp_kses_post( $description ),
                'link'        => esc_url_raw( $link ),
                'type'        => sanitize_key( $type ),
                'filters'     => array_map( 'sanitize_title', $filter_slugs ),
            );
        }

        return $images;
    }

    /**
     * Build a lookup map from legacy filter IDs to new filter slugs.
     *
     * @return array Map of filter_id => filter_slug
     */
    protected function build_legacy_filter_map() {
        $map = array();

        // Get legacy categories (ID => Name format)
        $legacy_categories = get_option( 'awl_portfolio_filter_gallery_categories', array() );
        
        // Get new filters (array with id, slug, name keys)
        $new_filters = get_option( 'pfg_filters', array() );

        // First, build map from new filters if they have matching legacy IDs
        foreach ( $new_filters as $filter ) {
            if ( isset( $filter['id'] ) && isset( $filter['slug'] ) ) {
                // Check if ID is numeric (legacy) or string (new format)
                $filter_id = $filter['id'];
                if ( is_numeric( $filter_id ) ) {
                    $map[ absint( $filter_id ) ] = $filter['slug'];
                }
            }
        }

        // If legacy categories exist, create slugs from names for any missing mappings
        if ( ! empty( $legacy_categories ) ) {
            foreach ( $legacy_categories as $legacy_id => $legacy_name ) {
                $legacy_id = absint( $legacy_id );
                if ( ! isset( $map[ $legacy_id ] ) && ! empty( $legacy_name ) ) {
                    // Ensure $legacy_name is a string (legacy data may have arrays)
                    if ( is_array( $legacy_name ) ) {
                        $legacy_name = isset( $legacy_name['name'] ) ? $legacy_name['name'] : ( isset( $legacy_name[0] ) ? $legacy_name[0] : '' );
                    }
                    // Generate slug from name only if it's a valid string
                    if ( is_string( $legacy_name ) && ! empty( $legacy_name ) ) {
                        // Use Unicode-aware slug generation for non-Latin characters
                        $slug = sanitize_title( $legacy_name );
                        // If sanitize_title returned URL-encoded (contains %xx hex), use Unicode-aware slug
                        if ( empty( $slug ) || preg_match( '/%[0-9a-f]{2}/i', $slug ) ) {
                            $slug = mb_strtolower( preg_replace( '/[^\p{L}\p{N}]+/ui', '-', $legacy_name ), 'UTF-8' );
                            $slug = trim( $slug, '-' );
                            if ( empty( $slug ) ) {
                                $slug = 'filter-' . substr( md5( $legacy_name ), 0, 8 );
                            }
                        }
                        $map[ $legacy_id ] = $slug;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Get settings schema.
     *
     * @return array
     */
    public static function get_schema() {
        return self::$settings_schema;
    }
}
