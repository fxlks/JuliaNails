<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The public-facing functionality of the plugin.
 */
class PFG_Public {

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
     * Galleries to render on current page.
     *
     * @var array
     */
    private static $galleries_on_page = array();

    /**
     * Initialize the class.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the public-facing side.
     */
    public function enqueue_styles() {
        // Only enqueue if a gallery is on the page
        if ( empty( self::$galleries_on_page ) ) {
            return;
        }

        // Core gallery styles
        wp_enqueue_style(
            'pfg-core',
            PFG_PLUGIN_URL . 'public/css/pfg-gallery.css',
            array(),
            $this->version
        );

        // Check if any gallery needs specific features
        $needs_hover    = false;
        
        $global_settings = get_option( 'pfg_global_settings', array() );
        $active_lightbox = isset( $global_settings['lightbox'] ) ? $global_settings['lightbox'] : 'built-in';

        foreach ( self::$galleries_on_page as $gallery_id ) {
            $gallery  = new PFG_Gallery( $gallery_id );
            $settings = $gallery->get_settings();

            if ( ! empty( $settings['hover_effect'] ) && $settings['hover_effect'] !== 'none' ) {
                $needs_hover = true;
                break;
            }
        }

        // Conditionally load hover effect styles
        if ( $needs_hover ) {
            wp_enqueue_style(
                'pfg-hover',
                PFG_PLUGIN_URL . 'public/css/pfg-hover.css',
                array(),
                $this->version
            );
        }

        // Conditionally load lightbox styles
        if ( $active_lightbox === 'built-in' ) {
            wp_enqueue_style(
                'pfg-lightbox',
                PFG_PLUGIN_URL . 'public/css/pfg-lightbox.css',
                array(),
                $this->version
            );
        } elseif ( $active_lightbox === 'ld-lightbox' ) {
            wp_enqueue_style(
                'ld-lightbox',
                PFG_PLUGIN_URL . 'public/lightbox/ld-lightbox/css/lightbox.css',
                array(),
                $this->version
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side.
     */
    public function enqueue_scripts() {
        // Always REGISTER scripts to prevent theme compatibility issues
        wp_register_script(
            'pfg-gallery',
            PFG_PLUGIN_URL . 'public/js/pfg-gallery.js',
            array(),
            $this->version,
            true
        );

        // Only ENQUEUE if a gallery is on the page
        if ( empty( self::$galleries_on_page ) ) {
            return;
        }

        // Enqueue the already-registered script
        wp_enqueue_script( 'pfg-gallery' );

        $global_settings = get_option( 'pfg_global_settings', array() );
        $active_lightbox = isset( $global_settings['lightbox'] ) ? $global_settings['lightbox'] : 'built-in';

        // Load appropriate lightbox script
        if ( $active_lightbox === 'built-in' ) {
            wp_enqueue_script(
                'pfg-lightbox',
                PFG_PLUGIN_URL . 'public/js/pfg-lightbox.js',
                array(),
                $this->version,
                true
            );
        } elseif ( $active_lightbox === 'ld-lightbox' ) {
            wp_enqueue_script(
                'ld-lightbox',
                PFG_PLUGIN_URL . 'public/lightbox/ld-lightbox/js/lightbox.js',
                array( 'jquery' ),
                $this->version,
                true
            );
        }

        // Load masonry script
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
     * Register a gallery to be rendered on current page.
     * This allows conditional asset loading.
     *
     * @param int $gallery_id Gallery ID.
     */
    public static function register_gallery_on_page( $gallery_id ) {
        if ( ! in_array( $gallery_id, self::$galleries_on_page, true ) ) {
            self::$galleries_on_page[] = $gallery_id;
        }
    }

    /**
     * Get registered galleries.
     *
     * @return array
     */
    public static function get_registered_galleries() {
        return self::$galleries_on_page;
    }

    /**
     * Add async/defer to scripts.
     *
     * @param string $tag    Script HTML tag.
     * @param string $handle Script handle.
     * @param string $src    Script source.
     * @return string Modified script tag.
     */
    public function add_async_defer( $tag, $handle, $src ) {
        $async_scripts = array( 'pfg-gallery' );

        if ( in_array( $handle, $async_scripts, true ) ) {
            return str_replace( ' src', ' defer src', $tag );
        }

        return $tag;
    }

    /**
     * Add preload hints for critical assets.
     */
    public function add_preload_hints() {
        if ( empty( self::$galleries_on_page ) ) {
            return;
        }

        // Preload core CSS
        echo '<link rel="preload" href="' . esc_url( PFG_PLUGIN_URL . 'public/css/pfg-gallery.css?ver=' . $this->version ) . '" as="style">' . "\n";
    }
}
