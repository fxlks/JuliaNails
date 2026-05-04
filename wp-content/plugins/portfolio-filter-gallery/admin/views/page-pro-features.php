<?php
/**
 * Pro Features Page Template.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap pfg-admin-wrap pfg-pro-features-page">

    <div class="pfg-admin-header">
        <div>
            <h1 class="pfg-admin-title">
                <span class="dashicons dashicons-star-filled pfg-pro-star-icon"></span>
                <?php esc_html_e( 'Pro Features', 'portfolio-filter-gallery' ); ?>
            </h1>
            <p class="pfg-admin-subtitle"><?php esc_html_e( 'Unlock the full potential of Portfolio Filter Gallery with these premium features.', 'portfolio-filter-gallery' ); ?></p>
        </div>
    </div>

    <div class="pfg-pro-features-grid">

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-layout"></span>
            </div>
            <h3><?php esc_html_e( 'Advanced Layouts', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Masonry, Mosaic, Justified, Slider and more layout options for your galleries.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-images-alt2"></span>
            </div>
            <h3><?php esc_html_e( 'Lightbox Options', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Multiple lightbox styles with social sharing, deep linking, and slideshow features.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-video-alt3"></span>
            </div>
            <h3><?php esc_html_e( 'Video Galleries', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Embed YouTube, Vimeo, and self-hosted videos directly in your galleries.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-admin-appearance"></span>
            </div>
            <h3><?php esc_html_e( 'Hover Effects', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( '20+ stunning hover effects with custom animations and overlay styles.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-cart"></span>
            </div>
            <h3><?php esc_html_e( 'WooCommerce Integration', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Link gallery items to products with Add to Cart buttons and pricing display.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-smartphone"></span>
            </div>
            <h3><?php esc_html_e( 'Responsive Breakpoints', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Fine-tune column counts for every screen size — desktop, tablet, and mobile.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-filter"></span>
            </div>
            <h3><?php esc_html_e( 'Multi-Level Filters', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Create sub-categories with parent-child filter relationships and dropdown menus.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-admin-customizer"></span>
            </div>
            <h3><?php esc_html_e( 'Custom Styling', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Custom fonts, colors, spacing, borders, and shadow controls for every element.', 'portfolio-filter-gallery' ); ?></p>
        </div>

    </div>

    <div class="pfg-pro-cta">
        <h2><?php esc_html_e( 'Ready to upgrade?', 'portfolio-filter-gallery' ); ?></h2>
        <p><?php esc_html_e( 'Get access to all premium features with a single license.', 'portfolio-filter-gallery' ); ?></p>
        <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" class="pfg-btn pfg-btn-primary pfg-btn-lg" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e( 'Get Pro Version', 'portfolio-filter-gallery' ); ?>
        </a>
    </div>

</div>

