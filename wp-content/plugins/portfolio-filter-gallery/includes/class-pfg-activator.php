<?php
/**
 * Plugin activation hooks.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fired during plugin activation.
 */
class PFG_Activator {

    /**
     * Run activation tasks.
     */
    public static function activate() {
        // Track version history for upgrade logic
        self::track_version_history();
        
        // Create default options
        self::create_default_options();

        // Create backup directory
        self::create_backup_directory();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation flag for admin notice
        set_transient( 'pfg_activation_redirect', true, 30 );
    }

    /**
     * Track version history for future upgrade logic.
     * This allows conditional behavior based on what version user is upgrading from.
     */
    private static function track_version_history() {
        $current_version = defined( 'PFG_VERSION' ) ? PFG_VERSION : '2.0.0';
        $stored_version = get_option( 'pfg_installed_version', '' );
        
        // If this is a new install or upgrade
        if ( $stored_version !== $current_version ) {
            // Store the previous version before updating
            if ( ! empty( $stored_version ) ) {
                update_option( 'pfg_previous_version', $stored_version );
            }
            // Update to current version
            update_option( 'pfg_installed_version', $current_version );
            
            // Store first install version (never changes after first install)
            if ( false === get_option( 'pfg_first_installed_version' ) ) {
                add_option( 'pfg_first_installed_version', $current_version );
            }
            
            // Store install/upgrade timestamp
            update_option( 'pfg_version_timestamp', time() );
        }
    }

    /**
     * Create default plugin options.
     */
    private static function create_default_options() {
        // Only set if not already exists (preserve user settings on update)
        if ( false === get_option( 'pfg_db_version' ) ) {
            add_option( 'pfg_db_version', '1.0.0' );
        }

        if ( false === get_option( 'pfg_filters' ) ) {
            // Create some default filters
            $default_filters = array(
                array(
                    'id'    => 'all_' . uniqid(),
                    'name'  => __( 'Web Design', 'portfolio-filter-gallery' ),
                    'slug'  => 'web-design',
                    'order' => 0,
                ),
                array(
                    'id'    => 'all_' . uniqid(),
                    'name'  => __( 'Photography', 'portfolio-filter-gallery' ),
                    'slug'  => 'photography',
                    'order' => 1,
                ),
                array(
                    'id'    => 'all_' . uniqid(),
                    'name'  => __( 'Branding', 'portfolio-filter-gallery' ),
                    'slug'  => 'branding',
                    'order' => 2,
                ),
            );
            add_option( 'pfg_filters', $default_filters );
        }
    }

    /**
     * Create backup directory.
     */
    private static function create_backup_directory() {
        $upload_dir = wp_upload_dir();
        $backup_dir = $upload_dir['basedir'] . '/pfg-backups';

        if ( ! file_exists( $backup_dir ) ) {
            wp_mkdir_p( $backup_dir );

            // Add security files
            file_put_contents( $backup_dir . '/index.php', '<?php // Silence is golden' );
            file_put_contents( $backup_dir . '/.htaccess', 'deny from all' );
        }
    }
}
