<?php
/**
 * Setup Wizard for Portfolio Filter Gallery
 * Shows first-time setup wizard after plugin activation
 *
 * @package Portfolio_Filter_Gallery
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Setup Wizard Class
 */
class PFG_Setup_Wizard {

    /**
     * Option name for tracking wizard completion
     */
    const WIZARD_COMPLETED_OPTION = 'pfg_wizard_completed';
    
    /**
     * Option name for tracking wizard redirect
     */
    const WIZARD_REDIRECT_OPTION = 'pfg_wizard_redirect';

    /**
     * Initialize the wizard
     */
    public static function init() {
        // Register wizard page
        add_action( 'admin_menu', array( __CLASS__, 'register_wizard_page' ) );
        
        // Handle redirect after activation
        add_action( 'admin_init', array( __CLASS__, 'maybe_redirect_to_wizard' ) );
        
        // Handle wizard actions
        add_action( 'admin_post_pfg_wizard_complete', array( __CLASS__, 'complete_wizard' ) );
        add_action( 'admin_post_pfg_wizard_skip', array( __CLASS__, 'skip_wizard' ) );
        
        // Enqueue wizard assets
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
    }

    /**
     * Set redirect flag on activation
     */
    public static function activate() {
        // Only set redirect if wizard hasn't been completed
        if ( ! get_option( self::WIZARD_COMPLETED_OPTION ) ) {
            update_option( self::WIZARD_REDIRECT_OPTION, true );
        }
    }

    /**
     * Redirect to wizard after activation
     */
    public static function maybe_redirect_to_wizard() {
        // Check redirect flag
        if ( ! get_option( self::WIZARD_REDIRECT_OPTION ) ) {
            return;
        }
        
        // Don't redirect on multisite
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- activation redirect, no data processing.
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            delete_option( self::WIZARD_REDIRECT_OPTION );
            return;
        }
        
        // Don't redirect if wizard already completed
        if ( get_option( self::WIZARD_COMPLETED_OPTION ) ) {
            delete_option( self::WIZARD_REDIRECT_OPTION );
            return;
        }
        
        // Clear redirect flag
        delete_option( self::WIZARD_REDIRECT_OPTION );
        
        // Redirect to wizard
        wp_safe_redirect( admin_url( 'admin.php?page=pfg-setup-wizard' ) );
        exit;
    }

    /**
     * Register hidden wizard page
     */
    public static function register_wizard_page() {
        add_submenu_page(
            '', // Hidden from menu
            __( 'Portfolio Filter Gallery Setup', 'portfolio-filter-gallery' ),
            __( 'Setup Wizard', 'portfolio-filter-gallery' ),
            'manage_options',
            'pfg-setup-wizard',
            array( __CLASS__, 'render_wizard' )
        );
    }

    /**
     * Enqueue wizard assets
     */
    public static function enqueue_assets( $hook ) {
        if ( 'admin_page_pfg-setup-wizard' !== $hook ) {
            return;
        }
        
        // Dashicons for icons
        wp_enqueue_style( 'dashicons' );
    }

    /**
     * Render wizard page
     */
    public static function render_wizard() {
        // Get current step
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only step number for display.
        $step = isset( $_GET['step'] ) ? absint( wp_unslash( $_GET['step'] ) ) : 1;
        $total_steps = 4;
        
        // Clamp step
        $step = max( 1, min( $step, $total_steps ) );
        
        include PFG_PLUGIN_DIR . 'admin/views/setup-wizard.php';
    }

    /**
     * Complete wizard
     */
    public static function complete_wizard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access', 'portfolio-filter-gallery' ) );
        }
        
        check_admin_referer( 'pfg_wizard_complete' );
        
        update_option( self::WIZARD_COMPLETED_OPTION, true );
        
        wp_safe_redirect( admin_url( 'edit.php?post_type=awl_filter_gallery' ) );
        exit;
    }

    /**
     * Skip wizard
     */
    public static function skip_wizard() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized access', 'portfolio-filter-gallery' ) );
        }
        
        check_admin_referer( 'pfg_wizard_skip' );
        
        update_option( self::WIZARD_COMPLETED_OPTION, true );
        
        wp_safe_redirect( admin_url( 'edit.php?post_type=awl_filter_gallery' ) );
        exit;
    }

    /**
     * Check if wizard is completed
     */
    public static function is_completed() {
        return (bool) get_option( self::WIZARD_COMPLETED_OPTION );
    }

    /**
     * Reset wizard (for testing)
     */
    public static function reset() {
        delete_option( self::WIZARD_COMPLETED_OPTION );
        delete_option( self::WIZARD_REDIRECT_OPTION );
    }
}
