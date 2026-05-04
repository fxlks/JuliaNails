<?php
/**
 * Plugin deactivation hooks.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fired during plugin deactivation.
 */
class PFG_Deactivator {

    /**
     * Run deactivation tasks.
     *
     * Note: We do NOT delete user data on deactivation.
     * Data is only removed if user explicitly uninstalls via uninstall.php
     */
    public static function deactivate() {
        // Clear any scheduled events
        wp_clear_scheduled_hook( 'pfg_continue_migration' );

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
