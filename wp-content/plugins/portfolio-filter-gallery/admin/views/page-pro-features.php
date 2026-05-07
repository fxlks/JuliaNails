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

    <div class="pfg-pro-hero">
        <div class="pfg-pro-hero-content">
            <h1 class="pfg-pro-hero-title">
                <span class="dashicons dashicons-star-filled"></span>
                <?php esc_html_e( 'Experience Portfolio Filter Gallery Pro', 'portfolio-filter-gallery' ); ?>
            </h1>
            <p class="pfg-pro-hero-subtitle"><?php esc_html_e( 'Take your portfolios to the next level with advanced layouts, video support, and premium filtering options.', 'portfolio-filter-gallery' ); ?></p>
        </div>
        <div class="pfg-pro-hero-actions">
            <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" class="pfg-btn-premium" target="_blank" rel="noopener noreferrer">
                <span class="dashicons dashicons-cart"></span>
                <?php esc_html_e( 'Get Pro Version Now', 'portfolio-filter-gallery' ); ?>
            </a>
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
                <span class="dashicons dashicons-cart"></span>
            </div>
            <h3><?php esc_html_e( 'WooCommerce Integration', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Link gallery items to products with Add to Cart buttons and pricing display.', 'portfolio-filter-gallery' ); ?></p>
        </div>

        <div class="pfg-pro-card">
            <div class="pfg-pro-card-icon">
                <span class="dashicons dashicons-update"></span>
            </div>
            <h3><?php esc_html_e( 'Load More & Infinite Scroll', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Handle large galleries with ease using AJAX-based pagination, Load More button, or Infinite Scroll loading.', 'portfolio-filter-gallery' ); ?></p>
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
            <p><?php esc_html_e( '15+ stunning hover effects with custom animations and overlay styles.', 'portfolio-filter-gallery' ); ?></p>
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
                <span class="dashicons dashicons-admin-customizer"></span>
            </div>
            <h3><?php esc_html_e( 'Custom Styling', 'portfolio-filter-gallery' ); ?></h3>
            <p><?php esc_html_e( 'Custom fonts, colors, spacing, borders, and shadow controls for every element.', 'portfolio-filter-gallery' ); ?></p>
        </div>

    </div>

    <div class="pfg-comparison-section">
        <h2 class="pfg-comparison-title"><?php esc_html_e( 'Free vs. Pro Comparison', 'portfolio-filter-gallery' ); ?></h2>
        
        <table class="pfg-comparison-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Features', 'portfolio-filter-gallery' ); ?></th>
                    <th><?php esc_html_e( 'Free', 'portfolio-filter-gallery' ); ?></th>
                    <th><?php esc_html_e( 'Premium', 'portfolio-filter-gallery' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php esc_html_e( 'Layout Engines', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Grid, Masonry', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Grid, Masonry, Justified, Packed, Slider', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Filter Logic', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Basic Flat Buttons', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Multi-Level, AND/OR Logic, Cascading Dropdowns', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Self-hosted videos', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'WooCommerce Integration', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'Price, Sale Badge, Add to Cart', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Pagination Systems', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><?php esc_html_e( 'Numbered, AJAX Load More, Infinite Scroll', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Filtering via URL', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'Load specific filter state via URL parameters', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Watermarking', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'Text & Image', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Analytics & Export', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'CSV Export included', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Hover Effects', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Basic Effects', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( '15+ Premium Animations', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Social Sharing', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Custom CSS Tools', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Standard', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Advanced (per-gallery with placeholders)', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Responsive Breakpoints', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Basic Columns', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Fine-tuned (XL, Desktop, Tablet, Mobile)', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Gallery Duplication', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'One-click Clone', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                
                <tr>
                    <td><strong><?php esc_html_e( 'Updates & Support', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><?php esc_html_e( 'Community Support', 'portfolio-filter-gallery' ); ?></td>
                    <td><?php esc_html_e( 'Priority Support & Auto-Updates', 'portfolio-filter-gallery' ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Global Primary Color', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'Skin the entire gallery with one color', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Optimized Lazy Loading', 'portfolio-filter-gallery' ); ?></strong></td>
                    <td><span class="dashicons dashicons-no-alt pfg-no"></span></td>
                    <td><span class="dashicons dashicons-yes pfg-yes"></span> (<?php esc_html_e( 'Enhanced SEO & Page Speed', 'portfolio-filter-gallery' ); ?>)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pfg-pro-cta">

        <h2><?php esc_html_e( 'Ready to upgrade?', 'portfolio-filter-gallery' ); ?></h2>
        <p><?php esc_html_e( 'Get access to all premium features with a single license.', 'portfolio-filter-gallery' ); ?></p>
        <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" class="pfg-btn pfg-btn-primary pfg-btn-lg" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e( 'Get Pro Version', 'portfolio-filter-gallery' ); ?>
        </a>
    </div>

</div>

