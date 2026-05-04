<?php
/**
 * Plugin Name: Portfolio Filter Gallery
 * Plugin URI: https://awplife.com/
 * Description: Create stunning filterable portfolio galleries with masonry layouts and drag-drop management.
 * Version: 2.1.5
 * Author: A WP Life
 * Author URI: https://awplife.com/
 * License: GPLv2 or later
 * Text Domain: portfolio-filter-gallery
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin constants - wrapped with defined() checks to prevent
 * duplicate definitions if the plugin file is loaded more than once.
 */
if ( ! defined( 'PFG_VERSION' ) ) {
    define( 'PFG_VERSION', '2.1.5' );
}
if ( ! defined( 'PFG_PLUGIN_FILE' ) ) {
    define( 'PFG_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'PFG_PLUGIN_PATH' ) ) {
    define( 'PFG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'PFG_PLUGIN_URL' ) ) {
    define( 'PFG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'PFG_PLUGIN_BASENAME' ) ) {
    define( 'PFG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

// Legacy constants for backward compatibility
if ( ! defined( 'PFG_PLUGIN_VER' ) ) {
    define( 'PFG_PLUGIN_VER', PFG_VERSION );
}
if ( ! defined( 'PFG_PLUGIN_NAME' ) ) {
    define( 'PFG_PLUGIN_NAME', 'Portfolio Filter Gallery' );
}
if ( ! defined( 'PFG_PLUGIN_SLUG' ) ) {
    define( 'PFG_PLUGIN_SLUG', 'awl_filter_gallery' );
}
if ( ! defined( 'PFG_PLUGIN_DIR' ) ) {
    define( 'PFG_PLUGIN_DIR', PFG_PLUGIN_PATH );
}

/**
 * The code that runs during plugin activation.
 */
if ( ! function_exists( 'portfolio_filter_gallery_activate' ) ) {
    function portfolio_filter_gallery_activate() {
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-activator.php';
        PFG_Activator::activate();

        // Onboarding tour
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-onboarding-tour.php';
        PFG_Onboarding_Tour::activate();
    }
}

/**
 * The code that runs during plugin deactivation.
 */
if ( ! function_exists( 'portfolio_filter_gallery_deactivate' ) ) {
    function portfolio_filter_gallery_deactivate() {
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-deactivator.php';
        PFG_Deactivator::deactivate();
    }
}

register_activation_hook( __FILE__, 'portfolio_filter_gallery_activate' );
register_deactivation_hook( __FILE__, 'portfolio_filter_gallery_deactivate' );

/**
 * Begins execution of the plugin.
 */
if ( ! function_exists( 'portfolio_filter_gallery_run' ) ) {
    function portfolio_filter_gallery_run() {
        // Prevent fatal error if PRO version is already active
        if ( class_exists( 'Portfolio_Filter_Gallery' ) ) {
            return;
        }
        
        require_once PFG_PLUGIN_PATH . 'includes/class-portfolio-filter-gallery.php';
        
        // Double check to be completely safe
        if ( ! class_exists( 'Portfolio_Filter_Gallery' ) ) {
            return;
        }

        $plugin = new Portfolio_Filter_Gallery();
        $plugin->run();
    }
}
add_action( 'plugins_loaded', 'portfolio_filter_gallery_run', 20 );