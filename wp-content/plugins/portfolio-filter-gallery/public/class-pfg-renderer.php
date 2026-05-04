<?php
/**
 * Gallery renderer for the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles gallery HTML rendering with performance optimizations.
 */
class PFG_Renderer {

    /**
     * Gallery ID.
     *
     * @var int
     */
    protected $gallery_id;

    /**
     * Gallery settings.
     *
     * @var array
     */
    protected $settings;

    /**
     * Gallery images.
     *
     * @var array
     */
    protected $images;

    /**
     * Available filters.
     *
     * @var array
     */
    protected $filters;

    /**
     * Constructor.
     *
     * @param int   $gallery_id Gallery ID.
     * @param array $settings   Gallery settings.
     * @param array $images     Gallery images.
     */
    public function __construct( $gallery_id, $settings, $images ) {
        $this->gallery_id = $gallery_id;
        $this->settings   = $settings;
        $this->images     = $images;
        $this->filters    = $this->get_used_filters();
    }

    /**
     * Render the gallery.
     *
     * @param string|null $active_filter Pre-selected filter slug.
     */
    public function render( $active_filter = null ) {
        // Use consistent ID for gallery targeting (no random component)
        $unique_id = 'pfg-gallery-' . $this->gallery_id;

        // Generate CSS variables for this gallery
        $this->output_styles( $unique_id );

        // Build data attributes for multi-filter support
        $multi_select = ! empty( $this->settings['multi_level_filters'] ) ? 'true' : 'false';
        $filter_logic = ! empty( $this->settings['filter_logic'] ) ? $this->settings['filter_logic'] : 'or';

        // Build filter hierarchy map (parent slug => array of child slugs)
        $filter_hierarchy = $this->build_filter_hierarchy();


        // Sort images based on sort_order setting
        $sort_order = $this->settings['sort_order'] ?? 'custom';

        // Backward compat: derive sort_order from old settings if still 'custom'
        if ( $sort_order === 'custom' ) {
            if ( ! empty( $this->settings['shuffle_images'] ) ) {
                $sort_order = 'random';
            } elseif ( ! empty( $this->settings['sort_by_title'] ) ) {
                $sort_order = ( $this->settings['sort_by_title'] === 'desc' ) ? 'title_desc' : 'title_asc';
            }
        }

        switch ( $sort_order ) {
            case 'random':
                shuffle( $this->images );
                break;
            case 'title_asc':
                usort( $this->images, function( $a, $b ) {
                    return strcasecmp( $a['title'] ?? '', $b['title'] ?? '' );
                });
                break;
            case 'title_desc':
                usort( $this->images, function( $a, $b ) {
                    return strcasecmp( $b['title'] ?? '', $a['title'] ?? '' );
                });
                break;
            case 'date_newest':
                usort( $this->images, function( $a, $b ) {
                    return ( $b['id'] ?? 0 ) - ( $a['id'] ?? 0 );
                });
                break;
            case 'date_oldest':
                usort( $this->images, function( $a, $b ) {
                    return ( $a['id'] ?? 0 ) - ( $b['id'] ?? 0 );
                });
                break;
            default: // 'custom' - keep original order
                break;
        }

        // Determine active filter: URL param > Default setting > null
        // (only if filters are enabled)
        $filters_enabled = ! empty( $this->settings['filters_enabled'] );
        $deep_linking   = ! empty( $this->settings['deep_linking'] );
        $url_param_name = $this->settings['url_param_name'] ?? 'filter';
        $default_filter = $this->settings['default_filter'] ?? '';
        
        // Only apply filter logic if filters are enabled
        if ( $filters_enabled ) {
            // ALWAYS check URL param for filter (URL param always takes priority)
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only frontend URL parameter for gallery filter deep-linking; no data is modified.
            if ( isset( $_GET[ $url_param_name ] ) ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Same as above; value is sanitized with sanitize_key().
                $active_filter = sanitize_key( wp_unslash( $_GET[ $url_param_name ] ) );
            } elseif ( empty( $active_filter ) && ! empty( $default_filter ) ) {
                $active_filter = $default_filter;
            }
        } else {
            // Filters disabled - don't apply any filter
            $active_filter = null;
        }

        // Build wrapper data attributes
        $wrapper_attrs = array(
            'id'                   => $unique_id,
            'class'                => 'pfg-gallery-wrapper',
            'data-gallery-id'      => $this->gallery_id,
            'data-multi-select'    => $multi_select,
            'data-filter-logic'    => $filter_logic,
            'data-filter-hierarchy'=> wp_json_encode( $filter_hierarchy ),
            'data-url-param'       => $url_param_name, // Always pass for JS compatibility
            'data-version'         => PFG_VERSION,
            'data-lightbox-title'  => ( ! isset( $this->settings['show_lightbox_title'] ) || $this->settings['show_lightbox_title'] ) ? 'true' : 'false',
            'data-lightbox-description' => ! empty( $this->settings['show_lightbox_description'] ) ? 'true' : 'false',
        );

        // Add preloader class if enabled
        $show_preloader = $this->settings['show_preloader'] ?? true;
        if ( $show_preloader ) {
            $wrapper_attrs['class'] .= ' pfg-loading';
            $wrapper_attrs['data-show-preloader'] = 'true';
        }

        // Add template class if a template is set
        if ( ! empty( $this->settings['template'] ) ) {
            $wrapper_attrs['class'] .= ' pfg-template-' . sanitize_html_class( $this->settings['template'] );
        }

        // Check for sidebar layout
        $filters_position = $this->settings['filters_position'] ?? 'left';
        $is_sidebar = in_array( $filters_position, array( 'sidebar-left', 'sidebar-right' ), true );
        
        if ( $is_sidebar ) {
            $wrapper_attrs['class'] .= ' pfg-gallery-wrapper--sidebar pfg-gallery-wrapper--' . $filters_position;
        }

        // Add deep linking attribute (controls whether filter clicks update URL)
        if ( $deep_linking ) {
            $wrapper_attrs['data-deep-linking'] = 'true';
        }

        // Add default filter attribute
        if ( ! empty( $default_filter ) ) {
            $wrapper_attrs['data-default-filter'] = $default_filter;
        }



        // Add image link settings
        $wrapper_attrs['data-image-links'] = ! empty( $this->settings['image_links'] ) ? 'true' : 'false';

        // Build wrapper opening tag
        echo '<div';
        foreach ( $wrapper_attrs as $attr => $value ) {
            if ( $attr === 'class' ) {
                echo ' class="' . esc_attr( $value ) . '"';
            } else {
                echo ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
            }
        }
        echo '>';

        // Render filters
        if ( $this->settings['filters_enabled'] && ! empty( $this->filters ) ) {
            $this->render_filters( $active_filter );
        }

        // For sidebar layouts, wrap remaining content in a main content div
        if ( $is_sidebar ) {
            echo '<div class="pfg-gallery-main">';
        }

        // Render search box
        if ( $this->settings['search_enabled'] ) {
            $this->render_search();
        }

        // Render gallery grid
        $this->render_grid( $active_filter );



        // Close main content wrapper for sidebar
        if ( $is_sidebar ) {
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Calculate contrast text color based on background luminance.
     * 
     * Returns dark text for light backgrounds and white text for dark backgrounds.
     *
     * @param string $hex_color Hex color code (with or without #).
     * @return string Contrast text color (#ffffff or #1e293b).
     */
    protected function get_contrast_color( $hex_color ) {
        // Remove # if present
        $hex = ltrim( $hex_color, '#' );
        
        // Handle shorthand hex (e.g., #fff)
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        // Convert to RGB
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        
        // Calculate relative luminance using sRGB formula
        // Formula: (0.299 * R + 0.587 * G + 0.114 * B) / 255
        $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
        
        // Return dark text for light backgrounds, white for dark backgrounds
        return $luminance > 0.5 ? '#1e293b' : '#ffffff';
    }

    /**
     * Output gallery-specific CSS variables.
     *
     * @param string $unique_id Unique gallery container ID.
     */
    protected function output_styles( $unique_id ) {
        $filter_bg = ! empty( $this->settings['primary_color'] ) ? $this->settings['primary_color'] : ( ! empty( $this->settings['filter_bg_color'] ) ? $this->settings['filter_bg_color'] : '#3858e9' );
        $filter_active_bg = ! empty( $this->settings['filter_active_color'] ) ? $this->settings['filter_active_color'] : $filter_bg;
        
        // Get filter text colors - use specified or auto-calculate based on background luminance
        $filter_text_setting = isset( $this->settings['filter_text_color'] ) ? $this->settings['filter_text_color'] : 'auto';
        $filter_text = ( $filter_text_setting === 'auto' || empty( $filter_text_setting ) ) ? $this->get_contrast_color( $filter_bg ) : $filter_text_setting;
        
        $filter_active_text_setting = isset( $this->settings['filter_active_text_color'] ) ? $this->settings['filter_active_text_color'] : 'auto';
        $filter_active_text = ( $filter_active_text_setting === 'auto' || empty( $filter_active_text_setting ) ) ? $this->get_contrast_color( $filter_active_bg ) : $filter_active_text_setting;
        
        // Sanitize numeric values (columns, gap, border widths/radius).
        $cols_xl       = absint( $this->settings['columns_xl'] );
        $cols_lg       = absint( $this->settings['columns_lg'] );
        $cols_md       = absint( $this->settings['columns_md'] );
        $cols_sm       = absint( $this->settings['columns_sm'] );
        $gap           = absint( $this->settings['gap'] );
        $border_width  = absint( $this->settings['border_width'] );
        $border_radius = absint( $this->settings['border_radius'] );

        // Sanitize color values with the CSS-specific function.
        $border_color       = sanitize_hex_color( $this->settings['border_color'] ) ?? '';
        $filter_bg          = sanitize_hex_color( $filter_bg ) ?? '';
        $filter_text        = sanitize_hex_color( $filter_text ) ?? '';
        $filter_active_bg   = sanitize_hex_color( $filter_active_bg ) ?? '';
        $filter_active_text = sanitize_hex_color( $filter_active_text ) ?? '';
        $caption_bg         = sanitize_hex_color( isset( $this->settings['caption_bg_color'] ) ? $this->settings['caption_bg_color'] : '#ffffff' ) ?? '#ffffff';
        $caption_text       = sanitize_hex_color( isset( $this->settings['caption_text_color'] ) ? $this->settings['caption_text_color'] : '#1e293b' ) ?? '#1e293b';

        $styles = array(
            '--pfg-cols-xl'            => $cols_xl,
            '--pfg-cols-lg'            => $cols_lg,
            '--pfg-cols-md'            => $cols_md,
            '--pfg-cols-sm'            => $cols_sm,
            '--pfg-gap'                => $gap . 'px',
            '--pfg-border-width'       => $border_width . 'px',
            '--pfg-border-color'       => $border_color,
            '--pfg-border-radius'      => $border_radius . 'px',
            '--pfg-filter-bg'          => $filter_bg,
            '--pfg-filter-text'        => $filter_text,
            '--pfg-filter-active-bg'   => $filter_active_bg,
            '--pfg-filter-active-text' => $filter_active_text,
            '--pfg-caption-bg'         => $caption_bg,
            '--pfg-caption-text'       => $caption_text,
        );

        $css = '#' . sanitize_html_class( $unique_id ) . ' {';
        foreach ( $styles as $var => $value ) {
            // $var is a hardcoded CSS custom property name (safe); $value is already sanitized above.
            $css .= $var . ':' . $value . ';';
        }
        $css .= '}';

        // Since shortcodes run after wp_enqueue_scripts, adding inline styles to 'pfg-core'
        // fails because 'pfg-core' has already been printed in the head.
        // We register a dummy handle for this specific gallery instance and enqueue it,
        // so WordPress outputs these dynamic styles in the footer.
        $handle = sanitize_html_class( $unique_id ) . '-style';
        wp_register_style( $handle, false );
        wp_enqueue_style( $handle );
        wp_add_inline_style( $handle, wp_strip_all_tags( $css ) );
    }

    /**
     * Render filter buttons.
     *
     * @param string|null $active_filter Pre-selected filter slug.
     */
    protected function render_filters( $active_filter = null ) {
        $position_class = 'pfg-filters--' . esc_attr( $this->settings['filters_position'] );
        $style_class    = 'pfg-filters--' . esc_attr( $this->settings['filters_style'] );
        $multi_class    = ! empty( $this->settings['multi_level_filters'] ) ? ' pfg-filters--multi' : '';
        $hierarchical   = $this->has_hierarchical_filters();

        if ( $hierarchical ) {
            $multi_class .= ' pfg-filters--hierarchical';
        }

        // Add count style class if showing counts on hover
        $count_class = '';
        if ( ! empty( $this->settings['show_filter_count'] ) && ( $this->settings['filter_count_style'] ?? 'always' ) === 'hover' ) {
            $count_class = ' pfg-filters--count-hover';
        }

        // Check if dropdown style is selected
        $filter_style = $this->settings['filters_style'] ?? 'buttons';
        
        if ( $filter_style === 'dropdown' ) {
            // Render dropdown filter style
            $this->render_dropdown_filters( $active_filter );
            return;
        }

        echo '<div class="pfg-filters ' . esc_attr( $position_class . ' ' . $style_class . $multi_class . $count_class ) . '">';

        // "All" button
        if ( $this->settings['show_all_button'] ) {
            $all_active = empty( $active_filter ) ? ' pfg-filter--active' : '';
            echo '<button type="button" class="pfg-filter' . esc_attr( $all_active ) . '" data-filter="*">';
            echo esc_html( $this->settings['all_button_text'] );
            
            if ( $this->settings['show_filter_count'] ) {
                echo ' <span class="pfg-filter-count">(' . count( $this->images ) . ')</span>';
            }
            
            echo '</button>';
        }

        // Filter buttons - hierarchical or flat
        $filters = $this->settings['sort_filters'] ? $this->sort_filters( $this->filters ) : $this->filters;

        if ( $hierarchical ) {
            $this->render_hierarchical_filters( $filters, $active_filter );
        } else {
            $this->render_flat_filters( $filters, $active_filter );
        }

        // AND/OR toggle for multi-select mode (only if setting enabled, defaults to true)
        $show_toggle = isset( $this->settings['show_logic_toggle'] ) ? $this->settings['show_logic_toggle'] : true;
        if ( ! empty( $this->settings['multi_level_filters'] ) && $show_toggle ) {
            $this->render_logic_toggle();
        }

        echo '</div>';
    }

    /**
     * Render dropdown-style filters (single-level hierarchy).
     *
     * @param string|null $active_filter Pre-selected filter slug.
     */
    protected function render_dropdown_filters( $active_filter ) {
        $filters = $this->settings['sort_filters'] ? $this->sort_filters( $this->filters ) : $this->filters;
        $show_count = $this->settings['show_filter_count'];

        echo '<div class="pfg-filters pfg-filters--dropdown">';
        echo '<div class="pfg-cascading-dropdowns">';
        
        // Single dropdown for flat filters
        echo '<div class="pfg-dropdown-wrap pfg-dropdown-level1">';
        echo '<select class="pfg-filter-select pfg-level1-select" data-filter-level="1">';
        
        // All option
        echo '<option value="*">' . esc_html( $this->settings['all_button_text'] );
        if ( $show_count ) {
            echo ' (' . count( $this->images ) . ')';
        }
        echo '</option>';
        
        // Filter options
        foreach ( $filters as $filter ) {
            $count = $this->count_images_in_filter( $filter['id'] );
            $selected = ( $active_filter === $filter['slug'] ) ? ' selected' : '';
            
            echo '<option value="' . esc_attr( $filter['slug'] ) . '"' . esc_attr( $selected ) . '>';
            echo esc_html( $filter['name'] );
            if ( $show_count ) {
                echo ' (' . esc_html( $count ) . ')';
            }
            echo '</option>';
        }
        
        echo '</select>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Check if filters have hierarchical structure.
     *
     * @return bool
     */
    protected function has_hierarchical_filters() {
        foreach ( $this->filters as $filter ) {
            if ( ! empty( $filter['parent'] ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render flat filter buttons.
     *
     * @param array       $filters       Filters array.
     * @param string|null $active_filter Active filter slug.
     */
    protected function render_flat_filters( $filters, $active_filter ) {
        $show_colors = ! empty( $this->settings['show_filter_colors'] );
        
        foreach ( $filters as $filter ) {
            $is_active = ( $active_filter === $filter['slug'] ) ? ' pfg-filter--active' : '';
            $count     = $this->count_images_in_filter( $filter['id'] );
            $color     = ! empty( $filter['color'] ) ? $filter['color'] : '';

            echo '<button type="button" class="pfg-filter' . esc_attr( $is_active ) . '" data-filter="' . esc_attr( $filter['slug'] ) . '">';
            
            // Color dot indicator (only if enabled in settings)
            if ( $show_colors && $color ) {
                echo '<span class="pfg-filter-color" style="background:' . esc_attr( $color ) . '"></span>';
            }
            
            echo esc_html( $filter['name'] );
            
            if ( $this->settings['show_filter_count'] ) {
                echo ' <span class="pfg-filter-count">(' . esc_html( $count ) . ')</span>';
            }
            
            echo '</button>';
        }
    }

    /**
     * Render hierarchical filter buttons with multi-level parent/child grouping.
     *
     * @param array       $filters       Filters array.
     * @param string|null $active_filter Active filter slug.
     */
    protected function render_hierarchical_filters( $filters, $active_filter ) {
        // Build a lookup table by filter ID
        $filter_lookup = array();
        foreach ( $filters as $filter ) {
            $filter_lookup[ $filter['id'] ] = $filter;
        }

        // Find root filters (no parent, or parent not in our filter set)
        $roots = array();
        $children_map = array();

        foreach ( $filters as $filter ) {
            $parent_id = ! empty( $filter['parent'] ) ? $filter['parent'] : 0;
            
            // If no parent OR parent is not in our used filter set, it's a root
            if ( empty( $parent_id ) || ! isset( $filter_lookup[ $parent_id ] ) ) {
                $roots[] = $filter;
            } else {
                // It's a child of an existing filter
                if ( ! isset( $children_map[ $parent_id ] ) ) {
                    $children_map[ $parent_id ] = array();
                }
                $children_map[ $parent_id ][] = $filter;
            }
        }

        // Render each root filter with its children
        foreach ( $roots as $root ) {
            $this->render_filter_with_children( $root, $children_map, $active_filter, 0 );
        }
    }

    /**
     * Recursively render a filter and its children.
     *
     * @param array       $filter        The filter to render.
     * @param array       $children_map  Map of parent_id => children.
     * @param string|null $active_filter Active filter slug.
     * @param int         $level         Current nesting level.
     */
    protected function render_filter_with_children( $filter, $children_map, $active_filter, $level ) {
        $has_children = ! empty( $children_map[ $filter['id'] ] );
        $is_active = ( $active_filter === $filter['slug'] ) ? ' pfg-filter--active' : '';
        $count = $this->count_images_in_filter( $filter['id'] );
        $color = ! empty( $filter['color'] ) ? $filter['color'] : '';
        $show_colors = ! empty( $this->settings['show_filter_colors'] );

        // Determine class based on level
        $level_class = '';
        if ( $level === 0 && $has_children ) {
            $level_class = ' pfg-filter--parent';
        } elseif ( $level > 0 ) {
            $level_class = ' pfg-filter--child';
        }

        // Start group wrapper if this filter has children
        if ( $has_children ) {
            echo '<div class="pfg-filter-group">';
        }

        // Render the filter button
        echo '<button type="button" class="pfg-filter' . esc_attr( $level_class . $is_active ) . '" data-filter="' . esc_attr( $filter['slug'] ) . '">';
        
        // Color dot indicator (only if enabled in settings)
        if ( $show_colors && $color ) {
            echo '<span class="pfg-filter-color" style="background:' . esc_attr( $color ) . '"></span>';
        }
        
        echo esc_html( $filter['name'] );
        if ( $this->settings['show_filter_count'] ) {
            echo ' <span class="pfg-filter-count">(' . esc_html( $count ) . ')</span>';
        }
        echo '</button>';

        // Render children if any
        if ( $has_children ) {
            echo '<div class="pfg-filter-children">';
            foreach ( $children_map[ $filter['id'] ] as $child ) {
                $this->render_filter_with_children( $child, $children_map, $active_filter, $level + 1 );
            }
            echo '</div>';
            echo '</div>'; // Close pfg-filter-group
        }
    }

    /**
     * Render AND/OR logic toggle.
     */
    protected function render_logic_toggle() {
        $current_logic = ! empty( $this->settings['filter_logic'] ) ? $this->settings['filter_logic'] : 'or';
        $or_active     = $current_logic === 'or' ? ' pfg-logic-btn--active' : '';
        $and_active    = $current_logic === 'and' ? ' pfg-logic-btn--active' : '';

        echo '<div class="pfg-logic-toggle">';
        echo '<button type="button" class="pfg-logic-btn' . esc_attr( $or_active ) . '" data-logic="or">' . esc_html__( 'OR', 'portfolio-filter-gallery' ) . '</button>';
        echo '<button type="button" class="pfg-logic-btn' . esc_attr( $and_active ) . '" data-logic="and">' . esc_html__( 'AND', 'portfolio-filter-gallery' ) . '</button>';
        echo '</div>';
    }

    /**
     * Build filter hierarchy map for JavaScript (parent slug => all descendant slugs).
     *
     * @return array
     */
    protected function build_filter_hierarchy() {
        // Build lookup by ID
        $by_id = array();
        foreach ( $this->filters as $filter ) {
            $by_id[ $filter['id'] ] = $filter;
        }

        // Build children map (parent_id => child filters)
        $children_by_parent = array();
        foreach ( $this->filters as $filter ) {
            if ( ! empty( $filter['parent'] ) && isset( $by_id[ $filter['parent'] ] ) ) {
                if ( ! isset( $children_by_parent[ $filter['parent'] ] ) ) {
                    $children_by_parent[ $filter['parent'] ] = array();
                }
                $children_by_parent[ $filter['parent'] ][] = $filter;
            }
        }

        // Recursively get all descendant slugs for a filter
        $get_descendants = function( $filter_id ) use ( &$get_descendants, $children_by_parent ) {
            $descendants = array();
            if ( isset( $children_by_parent[ $filter_id ] ) ) {
                foreach ( $children_by_parent[ $filter_id ] as $child ) {
                    $descendants[] = $child['slug'];
                    $descendants = array_merge( $descendants, $get_descendants( $child['id'] ) );
                }
            }
            return $descendants;
        };

        // Build hierarchy map: parent_slug => [all descendant slugs]
        $hierarchy = array();
        foreach ( $this->filters as $filter ) {
            $descendants = $get_descendants( $filter['id'] );
            if ( ! empty( $descendants ) ) {
                $hierarchy[ $filter['slug'] ] = $descendants;
            }
        }

        return $hierarchy;
    }

    /**
     * Render search box.
     */
    protected function render_search() {
        // Use translatable default if placeholder is the default value
        $placeholder = $this->settings['search_placeholder'];
        if ( empty( $placeholder ) || $placeholder === 'Search...' ) {
            $placeholder = __( 'Search...', 'portfolio-filter-gallery' );
        }
        
        echo '<div class="pfg-search">';
        echo '<input type="text" class="pfg-search-input" placeholder="' . esc_attr( $placeholder ) . '" aria-label="' . esc_attr__( 'Search gallery', 'portfolio-filter-gallery' ) . '">';
        echo '</div>';
    }

    /**
     * Render gallery grid.
     *
     * @param string|null $active_filter Pre-selected filter slug.
     */
    protected function render_grid( $active_filter = null ) {
        $layout_type   = $this->settings['layout_type'];
        $layout_class  = 'pfg-grid--' . esc_attr( $layout_type );
        $direction     = $this->settings['direction'] === 'rtl' ? ' dir="rtl"' : '';
        $grayscale     = $this->settings['grayscale'] ? ' pfg-grid--grayscale' : '';
        
        // Check if using card layout (title below image)
        $title_position = isset( $this->settings['title_position'] ) ? $this->settings['title_position'] : 'overlay';
        
        // Add fixed-height class for grid layouts (not masonry or card layout)
        $fixed_height = '';
        if ( $layout_type === 'grid' && $title_position !== 'below' ) {
            $fixed_height = ' pfg-grid--fixed-height';
        }

        // Build inline styles for layout-specific CSS variables
        $inline_styles = array();
        
        // Gap and border radius from settings
        $gap = isset( $this->settings['gap'] ) ? absint( $this->settings['gap'] ) : 20;
        $border_radius = isset( $this->settings['border_radius'] ) ? absint( $this->settings['border_radius'] ) : 0;
        $inline_styles[] = '--pfg-gap: ' . $gap . 'px';
        $inline_styles[] = '--pfg-border-radius: ' . $border_radius . 'px';
        $inline_styles[] = '--pfg-radius: ' . $border_radius . 'px';
        

        // Add overlay color and opacity CSS variables.
        // Sanitize all hex colors before embedding in inline styles (escape late principle).
        $overlay_color   = sanitize_hex_color( $this->settings['overlay_color'] ?? '#000000' ) ?? '#000000';
        $overlay_opacity = isset( $this->settings['overlay_opacity'] ) ? ( floatval( $this->settings['overlay_opacity'] ) / 100 ) : 0.7;
        // Clamp opacity to valid 0-1 range.
        $overlay_opacity = max( 0.0, min( 1.0, $overlay_opacity ) );
        $inline_styles[] = '--pfg-overlay-color: ' . $this->hex_to_rgba( $overlay_color, $overlay_opacity );

        // Primary color for categories in overlay.
        $primary_color   = sanitize_hex_color( $this->settings['primary_color'] ?? '#3858e9' ) ?? '#3858e9';
        $inline_styles[] = '--pfg-primary-color: ' . $primary_color;

        // Caption/title colors for card mode (below).
        if ( ! empty( $this->settings['caption_text_color'] ) ) {
            $caption_text = sanitize_hex_color( $this->settings['caption_text_color'] );
            if ( $caption_text ) {
                $inline_styles[] = '--pfg-caption-text: ' . $caption_text;
            }
        }

        // Caption background for card mode.
        if ( ! empty( $this->settings['caption_bg_color'] ) ) {
            $caption_bg = sanitize_hex_color( $this->settings['caption_bg_color'] );
            if ( $caption_bg ) {
                $inline_styles[] = '--pfg-caption-bg: ' . $caption_bg;
            }
        }

        $style_attr = ! empty( $inline_styles ) ? ' style="' . esc_attr( implode( '; ', $inline_styles ) ) . '"' : '';

        // Output preloader if enabled
        $show_preloader = $this->settings['show_preloader'] ?? true;
        if ( $show_preloader ) {
            echo '<div class="pfg-preloader">';
            echo '<div class="pfg-preloader-spinner"></div>';
            echo '<span class="pfg-preloader-text">' . esc_html__( 'Loading...', 'portfolio-filter-gallery' ) . '</span>';
            echo '</div>';
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $direction is a static ' dir="rtl"' string, $style_attr is built with esc_attr() on line 716.
        echo '<div class="pfg-grid ' . esc_attr( $layout_class . $grayscale . $fixed_height ) . '"' . $direction . $style_attr . '>';

        // Determine how many items to render
        $total_images       = count( $this->images );
        $images_to_render = $this->images;

        $index = 0;
        foreach ( $images_to_render as $key => $image ) {
            $this->render_item( $image, $index, $active_filter );
            $index++;
        }

        echo '</div>';
    }

    /**
     * Render a single gallery item.
     *
     * @param array       $image              Image data.
     * @param int         $index              Image index.
     * @param string|null $active_filter      Active filter slug.
     */
    protected function render_item( $image, $index, $active_filter = null ) {
        // Skip if attachment no longer exists (deleted from Media Library)
        $is_product = isset( $image['type'] ) && $image['type'] === 'product';
        if ( ! $is_product && ! empty( $image['id'] ) && ! wp_get_attachment_url( $image['id'] ) ) {
            return;
        }

        // Get filter classes
        $filter_classes = $this->get_image_filter_classes( $image );

        // Check if should be hidden by active filter
        $hidden_class = '';
        if ( $active_filter !== null ) {
            $should_show = false;
            
            // Media library images have filter IDs that need lookup
            foreach ( $image['filters'] as $filter_id ) {
                $filter = $this->get_filter_by_id( $filter_id );
                if ( $filter && $filter['slug'] === $active_filter ) {
                    $should_show = true;
                    break;
                }
            }
            
            if ( ! $should_show ) {
                $hidden_class = ' pfg-item--hidden';
            }
        }
        
        // Hover effect class
        $hover_class = 'pfg-item-hover--' . esc_attr( $this->settings['hover_effect'] );

        // Layout-specific classes and styles
        $layout_type    = $this->settings['layout_type'];
        $title_position = isset( $this->settings['title_position'] ) ? $this->settings['title_position'] : 'overlay';
        $size_class     = '';
        $item_style     = '';

        // Get dimensions for aspect ratio from attachment metadata
        $attachment_id = $image['id'];
        $image_meta    = wp_get_attachment_metadata( $attachment_id );
        $width         = isset( $image_meta['width'] ) ? (int) $image_meta['width'] : 1;
        $height        = isset( $image_meta['height'] ) ? (int) $image_meta['height'] : 1;
        $aspect_ratio  = $width / max( $height, 1 );


        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $item_style is constructed from round($aspect_ratio, 2) float value.
        echo '<div class="pfg-item ' . esc_attr( $filter_classes . ' ' . $hover_class . $hidden_class . $size_class ) . '" data-id="' . esc_attr( $image['id'] ) . '"' . $item_style . '>';

        // Type indicator icon (video or link)
        if ( empty( $this->settings['hide_type_icons'] ) ) {
            if ( $image['type'] === 'video' && ! empty( $image['link'] ) ) {
                // Detect if it's a Vimeo video for color styling
                $is_vimeo = strpos( $image['link'], 'vimeo.com' ) !== false;
                $video_class = $is_vimeo ? 'pfg-item-type-icon--video pfg-item-type-icon--vimeo' : 'pfg-item-type-icon--video';
                
                // Video indicator
                echo '<span class="pfg-item-type-icon ' . esc_attr( $video_class ) . '" title="' . esc_attr__( 'Video', 'portfolio-filter-gallery' ) . '">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>';
                echo '</span>';
            } elseif ( ! empty( $image['link'] ) && $image['type'] !== 'video' ) {
                // External link indicator
                echo '<span class="pfg-item-type-icon pfg-item-type-icon--link" title="' . esc_attr__( 'External Link', 'portfolio-filter-gallery' ) . '">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>';
                echo '</span>';
            }
        }

        // Render image or video
        if ( $image['type'] === 'video' && ! empty( $image['link'] ) ) {
            $this->render_video_item( $image, $index );
        } else {
            $this->render_image_item( $image, $index );
        }

        echo '</div>';
    }

    /**
     * Render an image item.
     *
     * @param array $image Image data.
     * @param int   $index Image index.
     */
    protected function render_image_item( $image, $index ) {
        $attachment_id = $image['id'];
        $size          = $this->get_image_size();

        // Get image data - use watermarked version if enabled
        if ( ! empty( $this->settings['watermark_enabled'] ) && class_exists( 'PFG_Watermark' ) ) {
            $watermark = PFG_Watermark::instance();
            $img_src   = $watermark->get_watermarked_url( $attachment_id, $this->gallery_id, $this->settings, $size );
            $full_src  = $watermark->get_watermarked_url( $attachment_id, $this->gallery_id, $this->settings, 'full' );
            // No srcset for watermarked images (single cached version)
            $img_srcset = false;
        } else {
            $img_src    = wp_get_attachment_image_url( $attachment_id, $size );
            $img_srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
            $full_src   = wp_get_attachment_image_url( $attachment_id, 'full' );
        }
        $img_sizes  = $this->calculate_sizes();
        $alt        = ! empty( $image['alt'] ) ? $image['alt'] : ( ! empty( $image['title'] ) ? $image['title'] : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

        // Determine link behavior
        $has_custom_link = ! empty( $image['link'] ) && ( ! isset( $image['type'] ) || $image['type'] === 'url' );
        $lightbox_enabled = isset( $this->settings['lightbox'] ) && $this->settings['lightbox'] !== 'none';
        
        // Determine the link URL
        if ( $has_custom_link ) {
            $link_url = $image['link'];
        } else {
            $link_url = $full_src;
        }
        $link_target = $this->settings['url_target'];

        // Check title position
        $title_position = isset( $this->settings['title_position'] ) ? $this->settings['title_position'] : 'overlay';
        $show_categories = ! empty( $this->settings['show_categories'] );

        // Image with lazy loading
        $loading = $this->settings['lazy_loading'] && $index > 3 ? 'lazy' : 'eager';

        $is_lightbox = ! $has_custom_link && $lightbox_enabled;
        $should_link = $has_custom_link || $lightbox_enabled;

        $show_dual_icons = $has_custom_link && $lightbox_enabled && ! empty( $this->settings['show_dual_icons'] );

        if ( $show_dual_icons ) {
            // Dual action mode: show both link and lightbox icons
            echo '<div class="pfg-item-link pfg-item-link--dual">';
            
            // Image
            echo '<img src="' . esc_url( $img_src ) . '"';
            if ( $img_srcset ) {
                echo ' srcset="' . esc_attr( $img_srcset ) . '" sizes="' . esc_attr( $img_sizes ) . '"';
            }
            echo ' alt="' . esc_attr( $alt ) . '" loading="' . esc_attr( $loading ) . '" decoding="async" class="pfg-item-image">';
            
            // Watermark overlay
            $this->render_watermark();
            
            // Action buttons overlay
            echo '<div class="pfg-item-actions">';
            
            // Link button
            echo '<a href="' . esc_url( $link_url ) . '" class="pfg-action-btn pfg-action-link" target="' . esc_attr( $link_target ) . '" rel="noopener" title="' . esc_attr__( 'Open Link', 'portfolio-filter-gallery' ) . '">';
            echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';
            echo '</a>';
            
            // Lightbox button
            echo '<a href="' . esc_url( $full_src ) . '" class="pfg-action-btn pfg-action-view" data-lightbox="pfg-' . esc_attr( $this->gallery_id ) . '"';
            $show_lb_title = ! isset( $this->settings['show_lightbox_title'] ) || $this->settings['show_lightbox_title'];
            $show_lb_desc = ! empty( $this->settings['show_lightbox_description'] );
            if ( $show_lb_title && ! empty( $image['title'] ) ) {
                echo ' data-title="' . esc_attr( $image['title'] ) . '"';
            }
            if ( $show_lb_desc && ! empty( $image['description'] ) ) {
                echo ' data-description="' . esc_attr( nl2br( $image['description'] ) ) . '"';
            }
            echo ' title="' . esc_attr__( 'View Image', 'portfolio-filter-gallery' ) . '">';
            echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
            echo '</a>';
            
            echo '</div>';
            
            // Overlay with title/description (only if title_position is 'overlay')
            if ( $title_position === 'overlay' && ( $this->settings['show_title'] || $this->settings['show_numbering'] || $show_categories ) ) {
                echo '<div class="pfg-item-caption pfg-item-caption--overlay">';
                if ( $this->settings['show_numbering'] ) {
                    echo '<span class="pfg-item-number">' . esc_html( $index + 1 ) . '</span>';
                }
                if ( $this->settings['show_title'] && ! empty( $image['title'] ) ) {
                    echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
                }
                if ( $show_categories ) {
                    $filter_names = $this->get_image_filter_names( $image );
                    if ( ! empty( $filter_names ) ) {
                        echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                    }
                }
                echo '</div>';
            }
            
            echo '</div>';
        } else {
            if ( $should_link ) {
                // Link attributes
                $link_attrs = array(
                    'href'   => esc_url( $link_url ),
                    'class'  => 'pfg-item-link',
                );

                if ( $is_lightbox ) {
                    $link_attrs['data-lightbox'] = 'pfg-' . $this->gallery_id;
                    
                    $show_lb_title = ! isset( $this->settings['show_lightbox_title'] ) || $this->settings['show_lightbox_title'];
                    $show_lb_desc = ! empty( $this->settings['show_lightbox_description'] );
                    
                    if ( $show_lb_title && ! empty( $image['title'] ) ) {
                        $link_attrs['data-title'] = esc_attr( $image['title'] );
                    }
                    if ( $show_lb_desc && ! empty( $image['description'] ) ) {
                        $link_attrs['data-description'] = esc_attr( nl2br( $image['description'] ) );
                    }
                } else {
                    $link_attrs['target'] = esc_attr( $link_target );
                    $link_attrs['rel']    = 'noopener';
                }

                echo '<a';
                foreach ( $link_attrs as $attr => $value ) {
                    echo ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
                }
                echo '>';
            } else {
                // No link - just a div wrapper
                echo '<div class="pfg-item-link pfg-item-link--noclick">';
            }

                echo '<img';
                echo ' src="' . esc_url( $img_src ) . '"';
                if ( $img_srcset ) {
                    echo ' srcset="' . esc_attr( $img_srcset ) . '"';
                    echo ' sizes="' . esc_attr( $img_sizes ) . '"';
                }
                echo ' alt="' . esc_attr( $alt ) . '"';
                echo ' loading="' . esc_attr( $loading ) . '"';
                echo ' decoding="async"';
                echo ' class="pfg-item-image"';
                echo '>';
                
                // Watermark overlay
                $this->render_watermark();

                // Overlay with title/description (only if title_position is 'overlay')
                if ( $title_position === 'overlay' && ( $this->settings['show_title'] || $this->settings['show_numbering'] || $show_categories ) ) {
                    echo '<div class="pfg-item-caption pfg-item-caption--overlay">';
                    
                    if ( $this->settings['show_numbering'] ) {
                        echo '<span class="pfg-item-number">' . esc_html( $index + 1 ) . '</span>';
                    }
                    
                    if ( $this->settings['show_title'] && ! empty( $image['title'] ) ) {
                        echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
                    }
                    
                    if ( $show_categories ) {
                        $filter_names = $this->get_image_filter_names( $image );
                        if ( ! empty( $filter_names ) ) {
                            echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                        }
                    }
                    
                    echo '</div>';
                }

                if ( $should_link ) {
                    echo '</a>';
                } else {
                    echo '</div>';
                }
        }


        // Card caption below image (when title_position is 'below')
        if ( $title_position === 'below' && ( $this->settings['show_title'] || $this->settings['show_description'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption">';
            
            if ( $this->settings['show_numbering'] ) {
                echo '<span class="pfg-item-number">' . esc_html( $index + 1 ) . '</span>';
            }
            
            if ( $this->settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( ! empty( $this->settings['show_description'] ) && ! empty( $image['description'] ) ) {
                echo '<p class="pfg-item-description">' . esc_html( $image['description'] ) . '</p>';
            }
            
            if ( $show_categories ) {
                $filter_names = $this->get_image_filter_names( $image );
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }
    }

    /**
     * Get filter names for an image.
     *
     * @param array $image Image data.
     * @return array Filter names.
     */
    protected function get_image_filter_names( $image ) {
        if ( empty( $image['filters'] ) ) {
            return array();
        }

        $names = array();
        foreach ( $image['filters'] as $filter_id ) {
            // Try lookup by ID first (media library filters)
            $filter = $this->get_filter_by_id( $filter_id );
            
            // If not found, try by slug
            if ( ! $filter ) {
                $filter = $this->get_filter_by_slug( $filter_id );
            }
            
            if ( $filter ) {
                $names[] = $filter['name'];
            }
        }

        return $names;
    }

    /**
     * Render a video item.
     *
     * @param array $image Image data (with video URL in link).
     * @param int   $index Item index.
     */
    protected function render_video_item( $image, $index ) {
        $thumbnail_id = $image['id'];
        $video_url    = $image['link'];
        $size         = $this->get_image_size();

        // Get thumbnail
        $img_src = wp_get_attachment_image_url( $thumbnail_id, $size );
        $alt     = ! empty( $image['alt'] ) ? $image['alt'] : ( ! empty( $image['title'] ) ? $image['title'] : get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) );

        // Check title position
        $title_position = isset( $this->settings['title_position'] ) ? $this->settings['title_position'] : 'overlay';
        $show_categories = ! empty( $this->settings['show_categories'] );

        // Video link properties
        $lightbox_enabled = isset( $this->settings['lightbox'] ) && $this->settings['lightbox'] !== 'none';
        $link_attrs = array(
            'href'   => esc_url( $video_url ),
            'class'  => 'pfg-item-link pfg-item-link--video',
        );

        if ( $lightbox_enabled ) {
            $global_settings = get_option( 'pfg_global_settings', array() );
            $active_lightbox = isset( $global_settings['lightbox'] ) ? $global_settings['lightbox'] : 'built-in';
            
            // LD Lightbox strictly does not support videos. If selected, bypass lightbox integration for videos.
            if ( $active_lightbox !== 'ld-lightbox' ) {
                $link_attrs['data-lightbox'] = 'pfg-' . $this->gallery_id;
                $link_attrs['data-type'] = 'video';
                if ( ! empty( $image['title'] ) ) {
                    $link_attrs['data-title'] = esc_attr( $image['title'] );
                }
                $show_lb_desc = ! empty( $this->settings['show_lightbox_description'] );
                if ( $show_lb_desc && ! empty( $image['description'] ) ) {
                    $link_attrs['data-description'] = esc_attr( nl2br( $image['description'] ) );
                }
            } else {
                $link_attrs['target'] = esc_attr( isset( $this->settings['url_target'] ) ? $this->settings['url_target'] : '_blank' );
                $link_attrs['rel']    = 'noopener';
            }
        }

        echo '<a';
        foreach ( $link_attrs as $attr => $value ) {
            echo ' ' . esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
        }
        echo '>';

        // Thumbnail image
        $loading = $this->settings['lazy_loading'] && $index > 3 ? 'lazy' : 'eager';
        echo '<img src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $alt ) . '" loading="' . esc_attr( $loading ) . '" decoding="async" class="pfg-item-image">';
        
        // Watermark overlay
        $this->render_watermark();

        // Play button overlay
        echo '<div class="pfg-video-play">';
        echo '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
        echo '</div>';

        // Overlay caption (when title_position is 'overlay')
        if ( $title_position === 'overlay' && ( $this->settings['show_title'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption pfg-item-caption--overlay">';
            
            if ( $this->settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( $show_categories ) {
                $filter_names = $this->get_image_filter_names( $image );
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }

        echo '</a>';

        // Card caption below image (when title_position is 'below')
        if ( $title_position === 'below' && ( $this->settings['show_title'] || $this->settings['show_description'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption">';
            
            if ( $this->settings['show_numbering'] ) {
                echo '<span class="pfg-item-number">' . esc_html( $index + 1 ) . '</span>';
            }
            
            if ( $this->settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( ! empty( $this->settings['show_description'] ) && ! empty( $image['description'] ) ) {
                echo '<p class="pfg-item-description">' . esc_html( $image['description'] ) . '</p>';
            }
            
            if ( $show_categories ) {
                $filter_names = $this->get_image_filter_names( $image );
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }
    }

    /**
     * Get filters that are actually used in this gallery.
     *
     * @return array
     */
    protected function get_used_filters() {
        // Regular media library filters
        return $this->get_media_library_filters();
    }
    
    /**
     * Get regular media library filters.
     *
     * @return array
     */
    protected function get_media_library_filters() {
        $all_filters = get_option( 'pfg_filters', array() );

        // Fall back to legacy format
        if ( empty( $all_filters ) ) {
            $legacy = get_option( 'awl_portfolio_filter_gallery_categories', array() );
            foreach ( $legacy as $id => $name ) {
                $all_filters[] = array(
                    'id'   => $id,
                    'name' => $name,
                    'slug' => sanitize_title( $name ),
                );
            }
        }

        // Find which filters are used (images store slugs, not IDs)
        $used_filters_keys = array();
        foreach ( $this->images as $image ) {
            if ( ! empty( $image['filters'] ) ) {
                foreach ( $image['filters'] as $filter_key ) {
                    // Store as lowercase key for matching
                    $used_filters_keys[ strtolower( $filter_key ) ] = true;
                }
            }
        }

        // Filter to only used ones - check by both ID and slug
        $used_filters = array();
        foreach ( $all_filters as $filter ) {
            $filter_id = isset( $filter['id'] ) ? strtolower( (string) $filter['id'] ) : '';
            $filter_slug = isset( $filter['slug'] ) ? strtolower( $filter['slug'] ) : '';
            
            // Match by ID OR slug (images may store either)
            if ( isset( $used_filters_keys[ $filter_id ] ) || isset( $used_filters_keys[ $filter_slug ] ) ) {
                $used_filters[] = $filter;
            }
        }

        return $used_filters;
    }

    /**
     * Get filter classes for an image.
     *
     * Filters can be stored as:
     * - Filter IDs (integers)
     * - Filter slugs (strings)
     *
     * @param array $image Image data.
     * @return string Space-separated filter classes.
     */
    protected function get_image_filter_classes( $image ) {
        if ( empty( $image['filters'] ) ) {
            return '';
        }

        $classes = array();
        
        foreach ( $image['filters'] as $filter_value ) {
            // Check if this is a slug (string-like) or an ID (numeric)
            // After migration, filters are stored as slugs
            // Try to find by ID first for backward compatibility
            $filter = $this->get_filter_by_id( $filter_value );
            if ( $filter ) {
                $classes[] = 'pfg-filter-' . $filter['slug'];
            } else {
                // Try to find by slug (for migrated data)
                $filter = $this->get_filter_by_slug( $filter_value );
                if ( $filter ) {
                    $classes[] = 'pfg-filter-' . $filter['slug'];
                } else {
                    // Try to find by slug (for migrated data)
                    $filter = $this->get_filter_by_slug( $filter_value );
                    if ( $filter ) {
                        $classes[] = 'pfg-filter-' . $filter['slug'];
                    } else {
                        // Fallback: treat as a slug directly
                        $classes[] = 'pfg-filter-' . sanitize_html_class( $filter_value );
                    }
                }
            }
        }

        return implode( ' ', $classes );
    }

    /**
     * Get a filter by its ID.
     *
     * @param string $filter_id Filter ID.
     * @return array|null Filter data or null.
     */
    protected function get_filter_by_id( $filter_id ) {
        foreach ( $this->filters as $filter ) {
            if ( $filter['id'] === $filter_id ) {
                return $filter;
            }
        }
        return null;
    }

    /**
     * Get a filter by its slug.
     *
     * @param string $filter_slug Filter slug.
     * @return array|null Filter data or null.
     */
    protected function get_filter_by_slug( $filter_slug ) {
        foreach ( $this->filters as $filter ) {
            if ( isset( $filter['slug'] ) && $filter['slug'] === $filter_slug ) {
                return $filter;
            }
        }
        return null;
    }

    /**
     * Count images in a filter.
     *
     * @param string $filter_id Filter ID.
     * @return int
     */
    protected function count_images_in_filter( $filter_id ) {
        $count = 0;
        
        // Get all filter IDs to match (this filter + all children)
        $filter_ids_to_match = $this->get_filter_with_children( $filter_id );
        
        // Build list of both IDs and slugs to match (case-insensitive)
        $keys_to_match = array();
        foreach ( $filter_ids_to_match as $fid ) {
            $keys_to_match[ strtolower( (string) $fid ) ] = true;
            
            // Also add the slug for this filter ID
            foreach ( $this->filters as $filter ) {
                if ( (string) $filter['id'] === (string) $fid && ! empty( $filter['slug'] ) ) {
                    $keys_to_match[ strtolower( $filter['slug'] ) ] = true;
                    break;
                }
            }
        }
        
        foreach ( $this->images as $image ) {
            if ( ! empty( $image['filters'] ) && is_array( $image['filters'] ) ) {
                // Check if any of the image's filters match our target keys
                foreach ( $image['filters'] as $img_filter ) {
                    if ( isset( $keys_to_match[ strtolower( (string) $img_filter ) ] ) ) {
                        $count++;
                        break; // Count each image only once
                    }
                }
            }
        }
        return $count;
    }

    /**
     * Get filter ID and all its child filter IDs recursively.
     *
     * @param string|int $filter_id Parent filter ID.
     * @return array Array of filter IDs including parent and all children.
     */
    protected function get_filter_with_children( $filter_id ) {
        $filter_id = (string) $filter_id;
        $result = array( $filter_id );
        
        foreach ( $this->filters as $filter ) {
            $parent = isset( $filter['parent'] ) ? (string) $filter['parent'] : '';
            if ( $parent === $filter_id ) {
                // This is a direct child, add it and recurse for grandchildren
                $child_ids = $this->get_filter_with_children( $filter['id'] );
                $result = array_merge( $result, $child_ids );
            }
        }
        
        return array_unique( $result );
    }

    /**
     * Sort filters alphabetically.
     *
     * @param array $filters Filters array.
     * @return array Sorted filters.
     */
    protected function sort_filters( $filters ) {
        usort( $filters, function( $a, $b ) {
            return strcasecmp( $a['name'], $b['name'] );
        } );
        return $filters;
    }

    /**
     * Get image size based on column count.
     *
     * @return string WordPress image size name.
     */
    protected function get_image_size() {
        // Use user-defined image size if set
        if ( ! empty( $this->settings['image_size'] ) ) {
            return $this->settings['image_size'];
        }

        // Fallback: auto-calculate based on column count
        $columns = max( $this->settings['columns_lg'], 1 );

        if ( $columns >= 4 ) {
            return 'medium';
        } elseif ( $columns >= 3 ) {
            return 'medium_large';
        } else {
            return 'large';
        }
    }

    /**
     * Calculate responsive sizes attribute.
     *
     * @return string Sizes attribute value.
     */
    protected function calculate_sizes() {
        $xl = max( $this->settings['columns_xl'], 1 );
        $lg = max( $this->settings['columns_lg'], 1 );
        $md = max( $this->settings['columns_md'], 1 );
        $sm = max( $this->settings['columns_sm'], 1 );

        $sizes = array(
            '(min-width: 1200px) ' . round( 100 / $xl ) . 'vw',
            '(min-width: 992px) ' . round( 100 / $lg ) . 'vw',
            '(min-width: 768px) ' . round( 100 / $md ) . 'vw',
            round( 100 / $sm ) . 'vw',
        );

        return implode( ', ', $sizes );
    }


    /**
     * Convert hex color to rgba with opacity.
     *
     * @param string $hex     Hex color code.
     * @param float  $opacity Opacity value (0-1).
     * @return string RGBA color string.
     */
    protected function hex_to_rgba( $hex, $opacity = 1 ) {
        $hex = ltrim( $hex, '#' );
        
        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );
        
        return "rgba({$r}, {$g}, {$b}, {$opacity})";
    }

    /**
     * Render watermark overlay if enabled.
     */
    protected function render_watermark() {
        // Check if watermark is enabled
        if ( empty( $this->settings['watermark_enabled'] ) ) {
            return;
        }

        $type     = $this->settings['watermark_type'] ?? 'text';
        $position = $this->settings['watermark_position'] ?? 'bottom-right';
        $opacity  = ( $this->settings['watermark_opacity'] ?? 50 ) / 100;

        // Position CSS classes
        $position_class = 'pfg-watermark--' . esc_attr( $position );

        echo '<div class="pfg-watermark ' . esc_attr( $position_class ) . '" style="opacity: ' . esc_attr( $opacity ) . ';">';

        if ( $type === 'text' ) {
            $text = $this->settings['watermark_text'] ?? '';
            if ( ! empty( $text ) ) {
                echo '<span class="pfg-watermark-text">' . esc_html( $text ) . '</span>';
            }
        } else {
            $image_url = $this->settings['watermark_image'] ?? '';
            if ( ! empty( $image_url ) ) {
                echo '<img src="' . esc_url( $image_url ) . '" alt="Watermark" class="pfg-watermark-image">';
            }
        }

        echo '</div>';
    }
}
