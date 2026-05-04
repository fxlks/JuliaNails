<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Cleans up plugin options and transients.
 * Gallery posts and their meta are preserved so users
 * can re-activate the plugin without data loss.
 *
 * @package Portfolio_Filter_Gallery
 */

// If uninstall not called from WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'pfg_global_settings' );
delete_option( 'pfg_db_version' );
delete_option( 'pfg_migration_status' );
delete_option( 'pfg_migration_log' );
delete_option( 'pfg_last_backup' );
delete_option( 'pfg_last_backup_date' );
delete_option( 'pfg_filters_legacy_backup' );
delete_option( 'pfg_installed_version' );
delete_option( 'pfg_previous_version' );
delete_option( 'pfg_version_timestamp' );
delete_option( 'pfg_tour_completed' );
delete_option( 'pfg_show_tour' );
delete_option( 'pfg_wizard_completed' );
delete_option( 'pfg_wizard_redirect' );

// Clean up transients.
global $wpdb;
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time cleanup on uninstall.
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( '_transient_pfg_' ) . '%'
	)
);
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time cleanup on uninstall.
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( '_transient_timeout_pfg_' ) . '%'
	)
);
