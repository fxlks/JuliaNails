<?php
/**
 * Global Settings Page Template.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get current settings
$settings = get_option( 'pfg_global_settings', array() );
$defaults = array(
    'disable_lazy_load'     => false,
    'lightbox'              => 'built-in',
);

$settings = wp_parse_args( $settings, $defaults );

// Handle form submission
if ( isset( $_POST['pfg_save_global_settings'] ) ) {
    if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_pfg_global_nonce'] ?? '' ) ), 'pfg_global_settings' ) && current_user_can( 'manage_options' ) ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified on line above.
        $new_settings = array(
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Using explicit typecasting for boolean fields.
            'disable_lazy_load'     => isset( $_POST['disable_lazy_load'] ) ? (bool) intval( wp_unslash( $_POST['disable_lazy_load'] ) ) : false,
            'lightbox'              => isset( $_POST['lightbox'] ) ? sanitize_key( wp_unslash( $_POST['lightbox'] ) ) : 'built-in',
        );

        update_option( 'pfg_global_settings', $new_settings );
        $settings = $new_settings;

        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'portfolio-filter-gallery' ) . '</p></div>';
    }
}
?>

<div class="wrap pfg-admin-wrap">
    
    <div class="pfg-admin-header">
        <div>
            <h1 class="pfg-admin-title"><?php esc_html_e( 'Global Settings', 'portfolio-filter-gallery' ); ?></h1>
            <p class="pfg-admin-subtitle"><?php esc_html_e( 'Configure global options for all portfolio filter galleries.', 'portfolio-filter-gallery' ); ?></p>
        </div>
    </div>
    
    <form method="post" class="pfg-settings-form">
        <?php wp_nonce_field( 'pfg_global_settings', '_pfg_global_nonce' ); ?>
        
        <div class="pfg-settings-grid">
            
            <!-- Asset Settings -->
            <div class="pfg-card">
                <div class="pfg-card-header">
                    <h3 class="pfg-card-title"><?php esc_html_e( 'Asset Loading', 'portfolio-filter-gallery' ); ?></h3>
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e( 'Disable Lazy Loading', 'portfolio-filter-gallery' ); ?>
                        <small><?php esc_html_e( 'Turn off lazy loading for compatibility', 'portfolio-filter-gallery' ); ?></small>
                    </label>
                    <label class="pfg-toggle">
                        <input type="checkbox" name="disable_lazy_load" value="1" <?php checked( $settings['disable_lazy_load'] ); ?>>
                        <span class="pfg-toggle-slider"></span>
                    </label>
                </div>
            </div>

            
            <!-- Lightbox Settings -->
            <div class="pfg-card">
                <div class="pfg-card-header">
                    <h3 class="pfg-card-title"><?php esc_html_e( 'Lightbox', 'portfolio-filter-gallery' ); ?></h3>
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Lightbox Library', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Choose how images open when clicked (Applies to all galleries)', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <select name="lightbox" class="pfg-select">
                        <option value="built-in" <?php selected($settings['lightbox'] ?? 'built-in', 'built-in'); ?>>
                            <?php esc_html_e('Built-in (Recommended)', 'portfolio-filter-gallery'); ?>
                        </option>
                        <option value="ld-lightbox" <?php selected($settings['lightbox'] ?? 'built-in', 'ld-lightbox'); ?>>
                            <?php esc_html_e('LD Lightbox (Legacy)', 'portfolio-filter-gallery'); ?>
                        </option>
                        <option value="none" <?php selected($settings['lightbox'] ?? 'built-in', 'none'); ?>>
                            <?php esc_html_e('None (Disabled)', 'portfolio-filter-gallery'); ?>
                        </option>
                    </select>
                    <p class="description" style="color: #d63638; margin-top: 5px;">
                        <?php esc_html_e('Note: LD Lightbox does not support Video playback. Please use the Built-in lightbox for videos.', 'portfolio-filter-gallery'); ?>
                    </p>
                </div>
            </div>

            <!-- System Info -->
            <div class="pfg-card">
                <div class="pfg-card-header">
                    <h3 class="pfg-card-title"><?php esc_html_e( 'System Info', 'portfolio-filter-gallery' ); ?></h3>
                </div>
                
                <table class="pfg-info-table">
                    <tr>
                        <th><?php esc_html_e( 'Plugin Version', 'portfolio-filter-gallery' ); ?></th>
                        <td><?php echo esc_html( defined( 'PFG_VERSION' ) ? PFG_VERSION : '2.0.0' ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'WordPress Version', 'portfolio-filter-gallery' ); ?></th>
                        <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'PHP Version', 'portfolio-filter-gallery' ); ?></th>
                        <td><?php echo esc_html( PHP_VERSION ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Total Galleries', 'portfolio-filter-gallery' ); ?></th>
                        <td><?php echo esc_html( wp_count_posts( 'awl_filter_gallery' )->publish ); ?></td>
                    </tr>
                </table>
            </div>
            
        </div>
        
        <div class="pfg-form-actions">
            <button type="submit" name="pfg_save_global_settings" class="pfg-btn pfg-btn-primary">
                <span class="dashicons dashicons-saved"></span>
                <?php esc_html_e( 'Save Settings', 'portfolio-filter-gallery' ); ?>
            </button>
        </div>
        
    </form>
    
</div>
