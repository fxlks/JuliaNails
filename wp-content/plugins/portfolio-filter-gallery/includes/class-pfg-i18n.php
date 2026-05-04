<?php
/**
 * Define the internationalization functionality.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class PFG_i18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        // Since WordPress 4.6, translations are automatically loaded for plugins
        // hosted on WordPress.org. Manual loading is no longer needed.
    }
}
