<?php
/**
 * Shortcode handler for the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles shortcode rendering.
 */
class PFG_Shortcode {

    /**
     * Register shortcodes.
     */
    public function register() {
        // Legacy shortcode (keep for backward compatibility)
        add_shortcode( 'PFG', array( $this, 'render_legacy' ) );
        
        // New format shortcodes
        add_shortcode( 'portfolio_gallery', array( $this, 'render' ) );
        add_shortcode( 'Portfolio_Gallery', array( $this, 'render_legacy' ) );
    }

    /**
     * Render legacy shortcode format.
     *
     * @param array $atts Shortcode attributes.
     * @return string Gallery HTML.
     */
    public function render_legacy( $atts ) {
        $atts = shortcode_atts(
            array(
                'id' => 0,
            ),
            $atts,
            'PFG'
        );

        return $this->render( array( 'id' => $atts['id'] ) );
    }

    /**
     * Render gallery shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string Gallery HTML.
     */
    public function render( $atts ) {
        $atts = shortcode_atts(
            array(
                'id'             => 0,
                'columns'        => null,
                'columns_tablet' => null,
                'columns_mobile' => null,
                'gap'            => null,
                'filter'         => null, // Pre-select a filter
                'template'       => null, // Apply a starter template
                'hover_effect'   => null, // Override hover effect
                'show_filters'   => null, // Override show filters
            ),
            $atts,
            'portfolio_gallery'
        );

        $gallery_id = absint( $atts['id'] );

        if ( ! $gallery_id ) {
            return $this->render_error( __( 'Gallery ID is required.', 'portfolio-filter-gallery' ) );
        }

        // Load gallery
        $gallery = new PFG_Gallery( $gallery_id );

        if ( ! $gallery->exists() ) {
            return $this->render_error( __( 'Gallery not found.', 'portfolio-filter-gallery' ) );
        }

        // Register gallery for conditional asset loading
        PFG_Public::register_gallery_on_page( $gallery_id );

        // Build shortcode overrides
        $overrides = array();

        if ( $atts['columns'] !== null ) {
            $overrides['columns_lg'] = absint( $atts['columns'] );
        }
        if ( $atts['columns_tablet'] !== null ) {
            $overrides['columns_md'] = absint( $atts['columns_tablet'] );
        }
        if ( $atts['columns_mobile'] !== null ) {
            $overrides['columns_sm'] = absint( $atts['columns_mobile'] );
        }
        if ( $atts['gap'] !== null ) {
            $overrides['gap'] = absint( $atts['gap'] );
        }
        if ( $atts['hover_effect'] !== null ) {
            $overrides['hover_effect'] = sanitize_text_field( $atts['hover_effect'] );
        }
        if ( $atts['show_filters'] !== null ) {
            $overrides['show_filters'] = ( $atts['show_filters'] === '1' || $atts['show_filters'] === 'true' || $atts['show_filters'] === true );
        }

        // Get settings with overrides
        $settings = $gallery->get_settings( $overrides );
        
        // Regular media library images
        $images = $gallery->get_images();

        if ( empty( $images ) ) {
            return $this->render_empty( $settings );
        }

        // Apply template - check shortcode attribute first, then saved gallery setting
        $template_to_apply = null;
        if ( $atts['template'] !== null ) {
            $template_to_apply = sanitize_text_field( $atts['template'] );
        } elseif ( ! empty( $settings['template'] ) ) {
            $template_to_apply = $settings['template'];
        }

        if ( $template_to_apply ) {
            $settings = $this->apply_template( $template_to_apply, $settings );
        }

        // Enqueue assets directly (since shortcode runs after wp_enqueue_scripts)
        $this->enqueue_assets( $gallery );

        // Start output buffering
        ob_start();

        // Render the gallery
        $renderer = new PFG_Renderer( $gallery_id, $settings, $images );
        $filter_slug = $atts['filter'] !== null ? sanitize_text_field( $atts['filter'] ) : null;
        $renderer->render( $filter_slug );

        return ob_get_clean();
    }

    /**
     * Enqueue required assets for the gallery.
     *
     * @param PFG_Gallery $gallery Gallery object.
     */
    protected function enqueue_assets( $gallery ) {
        $version  = defined( 'PFG_VERSION' ) ? PFG_VERSION : '2.0.0';
        $settings = $gallery->get_settings();

        // Core gallery styles
        wp_enqueue_style(
            'pfg-gallery',
            PFG_PLUGIN_URL . 'public/css/pfg-gallery.css',
            array(),
            $version
        );

        // Hover effect styles
        if ( ! empty( $settings['hover_effect'] ) && $settings['hover_effect'] !== 'none' ) {
            wp_enqueue_style(
                'pfg-hover',
                PFG_PLUGIN_URL . 'public/css/pfg-hover.css',
                array(),
                $version
            );
        }

        // Core gallery script
        wp_enqueue_script(
            'pfg-gallery',
            PFG_PLUGIN_URL . 'public/js/pfg-gallery.js',
            array(),
            $version,
            true
        );

        // Lightbox styles and scripts
        $global_settings = get_option( 'pfg_global_settings', array() );
        $active_lightbox = isset( $global_settings['lightbox'] ) ? $global_settings['lightbox'] : 'built-in';
        $lightbox_enabled = isset( $settings['lightbox'] ) && $settings['lightbox'] !== 'none';

        if ( $lightbox_enabled ) {
            if ( $active_lightbox === 'built-in' ) {
                wp_enqueue_style( 'pfg-lightbox', PFG_PLUGIN_URL . 'public/css/pfg-lightbox.css', array(), $version );
                wp_enqueue_script( 'pfg-lightbox', PFG_PLUGIN_URL . 'public/js/pfg-lightbox.js', array(), $version, true );
            } elseif ( $active_lightbox === 'ld-lightbox' ) {
                wp_enqueue_style( 'ld-lightbox', PFG_PLUGIN_URL . 'public/lightbox/ld-lightbox/css/lightbox.css', array(), $version );
                wp_enqueue_script( 'ld-lightbox', PFG_PLUGIN_URL . 'public/lightbox/ld-lightbox/js/lightbox.js', array( 'jquery' ), $version, true );
            }
        }

        // Localize script
        wp_localize_script(
            'pfg-gallery',
            'pfgData',
            array(
                'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
                'nonce'          => wp_create_nonce( 'pfg_public_nonce' ),
                'analyticsNonce' => wp_create_nonce( 'pfg_analytics_nonce' ),
                'i18n'           => array(
                    'all'       => __( 'All', 'portfolio-filter-gallery' ),
                    'loading'   => __( 'Loading...', 'portfolio-filter-gallery' ),
                    'noResults' => __( 'No items found.', 'portfolio-filter-gallery' ),
                    'prev'      => __( 'Previous', 'portfolio-filter-gallery' ),
                    'next'      => __( 'Next', 'portfolio-filter-gallery' ),
                    'close'     => __( 'Close', 'portfolio-filter-gallery' ),
                ),
                'lightboxLibrary' => $active_lightbox,
            )
        );
    }

    /**
     * Render error message.
     *
     * @param string $message Error message.
     * @return string Error HTML.
     */
    protected function render_error( $message ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="pfg-error">' . esc_html( $message ) . '</div>';
        }
        return '';
    }

    /**
     * Render empty gallery message.
     *
     * @param array $settings Gallery settings.
     * @return string Empty message HTML.
     */
    protected function render_empty( $settings ) {
        if ( current_user_can( 'edit_posts' ) ) {
            return '<div class="pfg-empty">' . esc_html__( 'This gallery has no images. Add some images in the gallery editor.', 'portfolio-filter-gallery' ) . '</div>';
        }
        return '';
    }

    /**
     * Apply a starter template to settings.
     * Template provides defaults, but user-saved settings take priority.
     *
     * @param string $template_name Template name.
     * @param array  $settings      Current settings (user-saved).
     * @return array Modified settings.
     */
    protected function apply_template( $template_name, $settings ) {
        $templates = PFG_Templates::get_templates();

        if ( isset( $templates[ $template_name ] ) ) {
            $template_settings = $templates[ $template_name ]['settings'];
            // User settings override template defaults
            return wp_parse_args( $settings, $template_settings );
        }

        return $settings;
    }
}
