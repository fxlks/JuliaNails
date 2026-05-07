<?php
/**
 * WooCommerce Integration Page View
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap pfg-admin-wrap pfg-woocommerce-page">
    <div class="pfg-pro-hero">
        <div class="pfg-pro-hero-content">
            <h1 class="pfg-pro-hero-title">
                <span class="dashicons dashicons-cart"></span>
                <?php esc_html_e( 'WooCommerce Integration', 'portfolio-filter-gallery' ); ?>
            </h1>
            <p class="pfg-pro-hero-subtitle"><?php esc_html_e( 'Sell your products directly from your portfolio galleries with seamless WooCommerce integration.', 'portfolio-filter-gallery' ); ?></p>
        </div>
        <div class="pfg-pro-hero-actions">
            <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" class="pfg-btn-premium" target="_blank" rel="noopener noreferrer">
                <span class="dashicons dashicons-cart"></span>
                <?php esc_html_e( 'Upgrade to Pro for WooCommerce', 'portfolio-filter-gallery' ); ?>
            </a>
        </div>
    </div>

    <div class="pfg-card" style="text-align: center; padding: 100px 50px;">
        <div style="margin-bottom: 30px;">
            <span class="dashicons dashicons-products" style="font-size: 80px; width: 80px; height: 80px; color: var(--pfg-border);"></span>
        </div>
        <h2 style="font-size: 28px; margin-bottom: 15px; color: var(--pfg-text);"><?php esc_html_e( 'Build Shoppable Galleries', 'portfolio-filter-gallery' ); ?></h2>
        <p style="font-size: 16px; color: var(--pfg-text-muted); max-width: 600px; margin: 0 auto 40px; line-height: 1.6;">
            <?php esc_html_e( 'This feature allows you to link gallery items to WooCommerce products. Display prices, "Add to Cart" buttons, and product ratings directly on your gallery items to turn your portfolio into a powerful sales tool.', 'portfolio-filter-gallery' ); ?>
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; max-width: 900px; margin: 0 auto; text-align: left;">
            <div class="pfg-feature-item">
                <h4 style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span class="dashicons dashicons-yes" style="color: var(--pfg-success);"></span>
                    <?php esc_html_e( 'Direct Checkout', 'portfolio-filter-gallery' ); ?>
                </h4>
                <p style="font-size: 13px; color: var(--pfg-text-muted);"><?php esc_html_e( 'Allow customers to add products to their cart without leaving the gallery page.', 'portfolio-filter-gallery' ); ?></p>
            </div>
            <div class="pfg-feature-item">
                <h4 style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span class="dashicons dashicons-yes" style="color: var(--pfg-success);"></span>
                    <?php esc_html_e( 'Price Display', 'portfolio-filter-gallery' ); ?>
                </h4>
                <p style="font-size: 13px; color: var(--pfg-text-muted);"><?php esc_html_e( 'Automatically pull and display product prices, including sale prices and currency symbols.', 'portfolio-filter-gallery' ); ?></p>
            </div>
            <div class="pfg-feature-item">
                <h4 style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span class="dashicons dashicons-yes" style="color: var(--pfg-success);"></span>
                    <?php esc_html_e( 'Product Sync', 'portfolio-filter-gallery' ); ?>
                </h4>
                <p style="font-size: 13px; color: var(--pfg-text-muted);"><?php esc_html_e( 'Link any gallery item to an existing WooCommerce product with a single click.', 'portfolio-filter-gallery' ); ?></p>
            </div>
        </div>
    </div>
</div>
