<?php
/**
 * Onboarding Tour for Portfolio Filter Gallery
 * Shows tooltip-style guided tour on first use
 *
 * @package Portfolio_Filter_Gallery
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Onboarding Tour Class
 */
class PFG_Onboarding_Tour {

    /**
     * Option name for tracking tour completion
     */
    const TOUR_COMPLETED_OPTION = 'pfg_tour_completed';

    /**
     * Initialize the tour
     */
    public static function init() {
        // Enqueue tour assets on relevant pages
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'maybe_enqueue_tour' ) );
        
        // AJAX handler to mark tour complete
        add_action( 'wp_ajax_pfg_complete_tour', array( __CLASS__, 'ajax_complete_tour' ) );
        add_action( 'wp_ajax_pfg_dismiss_tour', array( __CLASS__, 'ajax_dismiss_tour' ) );
    }

    /**
     * Set tour flag on activation
     */
    public static function activate() {
        // Only set if tour hasn't been completed
        if ( ! get_option( self::TOUR_COMPLETED_OPTION ) ) {
            update_option( 'pfg_show_tour', true );
        }
    }

    /**
     * Maybe enqueue tour assets
     */
    public static function maybe_enqueue_tour( $hook ) {
        // Only for admins
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        // Check if tour should show
        if ( get_option( self::TOUR_COMPLETED_OPTION ) ) {
            return;
        }
        
        // Only on Add New Gallery page (post-new.php), not existing galleries
        global $post_type;
        $is_add_new_gallery = $hook === 'post-new.php' && $post_type === 'awl_filter_gallery';
        
        if ( ! $is_add_new_gallery ) {
            return;
        }
        
        // Enqueue the tour
        self::enqueue_tour_assets( $hook );
    }

    /**
     * Enqueue tour assets
     */
    private static function enqueue_tour_assets( $hook ) {
        wp_enqueue_style( 'dashicons' );
        
        // Inline CSS for tour tooltips
        wp_add_inline_style( 'dashicons', self::get_tour_css() );
        
        // Determine current step based on page
        $step = 1;
        if ( strpos( $hook, 'post-new' ) !== false || strpos( $hook, 'post.php' ) !== false ) {
            $step = 2; // Gallery editor
        }
        
        // Add inline script
        wp_add_inline_script( 'jquery-core', self::get_tour_js( $step ), 'after' );
    }

    /**
     * Get tour CSS
     */
    private static function get_tour_css() {
        return '
        .pfg-tour-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 99998;
            pointer-events: none;
        }
        
        .pfg-tour-highlight {
            position: relative;
            z-index: 99999 !important;
            box-shadow: 0 0 0 4px rgba(56, 88, 233, 0.4), 0 0 0 9999px rgba(0, 0, 0, 0.4) !important;
            border-radius: 4px;
        }
        
        .pfg-tour-tooltip {
            position: absolute;
            z-index: 100000;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 20px;
            width: 320px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .pfg-tour-tooltip::before {
            content: "";
            position: absolute;
            width: 12px;
            height: 12px;
            background: #fff;
            transform: rotate(45deg);
        }
        
        .pfg-tour-tooltip.arrow-top::before {
            bottom: -6px;
            left: 24px;
        }
        
        .pfg-tour-tooltip.arrow-bottom::before {
            top: -6px;
            left: 24px;
        }
        
        .pfg-tour-tooltip.arrow-left::before {
            right: -6px;
            top: 24px;
        }
        
        .pfg-tour-tooltip.arrow-right::before {
            left: -6px;
            top: 24px;
        }
        
        .pfg-tour-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 20px;
            color: #94a3b8;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }
        
        .pfg-tour-close:hover {
            color: #64748b;
        }
        
        .pfg-tour-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin: 0 0 8px 0;
            padding-right: 20px;
            list-style: none;
        }
        
        .pfg-tour-title::before {
            content: none !important;
        }
        
        .pfg-tour-content {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin: 0 0 16px 0;
        }
        
        .pfg-tour-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .pfg-tour-step {
            font-size: 12px;
            color: #94a3b8;
        }
        
        .pfg-tour-actions {
            display: flex;
            gap: 8px;
        }
        
        .pfg-tour-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        
        .pfg-tour-btn-primary {
            background: #3858e9;
            color: #fff;
        }
        
        .pfg-tour-btn-primary:hover {
            background: #2d4ac7;
        }
        
        .pfg-tour-btn-secondary {
            background: #f1f5f9;
            color: #64748b;
        }
        
        .pfg-tour-btn-secondary:hover {
            background: #e2e8f0;
        }
        ';
    }

    /**
     * Get tour JS
     */
    private static function get_tour_js( $current_step ) {
        $nonce = wp_create_nonce( 'pfg_tour_nonce' );
        
        return "
        jQuery(document).ready(function($) {
            var PFGTour = {
                currentStep: 0,
                steps: [
                    {
                        element: '.wrap h1, .wrap .wp-heading-inline',
                        title: '" . esc_js( __( 'Welcome to Portfolio Filter Gallery!', 'portfolio-filter-gallery' ) ) . "',
                        content: '" . esc_js( __( 'Take a quick tour to learn how to create stunning filterable galleries.', 'portfolio-filter-gallery' ) ) . "',
                        position: 'bottom'
                    },
                    {
                        element: '#menu-posts-awl_filter_gallery .wp-submenu a[href*=\"pfg-filters\"], .pfg-filters-link',
                        title: '" . esc_js( __( 'Add Filters', 'portfolio-filter-gallery' ) ) . "',
                        content: '" . esc_js( __( 'Go to the Filters menu to create filter categories. These let visitors filter your gallery items.', 'portfolio-filter-gallery' ) ) . "',
                        position: 'right'
                    },
                    {
                        element: '#pfg-gallery-images',
                        title: '" . esc_js( __( 'Add Images', 'portfolio-filter-gallery' ) ) . "',
                        content: '" . esc_js( __( 'Click Add Images to upload or select images from your media library. Drag to reorder.', 'portfolio-filter-gallery' ) ) . "',
                        position: 'bottom'
                    },
                    {
                        element: '#pfg-gallery-settings, .pfg-settings-tabs',
                        title: '" . esc_js( __( 'Configure Settings', 'portfolio-filter-gallery' ) ) . "',
                        content: '" . esc_js( __( 'Customize your gallery layout, hover effects, and more using these tabs.', 'portfolio-filter-gallery' ) ) . "',
                        position: 'top'
                    },
                    {
                        element: '#publish, .editor-post-publish-button, #major-publishing-actions',
                        title: '" . esc_js( __( 'Publish Your Gallery', 'portfolio-filter-gallery' ) ) . "',
                        content: '" . esc_js( __( 'Save your gallery to get the shortcode. Copy and paste it anywhere on your site!', 'portfolio-filter-gallery' ) ) . "',
                        position: 'left'
                    }
                ],
                
                init: function() {
                    // Check if any step elements exist
                    var hasElements = false;
                    for (var i = 0; i < this.steps.length; i++) {
                        if ($(this.steps[i].element).length) {
                            hasElements = true;
                            break;
                        }
                    }
                    
                    if (!hasElements) return;
                    
                    this.showStep(0);
                },
                
                showStep: function(index) {
                    var self = this;
                    this.currentStep = index;
                    
                    // Remove existing tooltip
                    $('.pfg-tour-tooltip').remove();
                    $('.pfg-tour-highlight').removeClass('pfg-tour-highlight');
                    
                    if (index >= this.steps.length) {
                        this.complete();
                        return;
                    }
                    
                    var step = this.steps[index];
                    var \$el = $(step.element).first();
                    
                    if (!\$el.length) {
                        // Skip to next step if element not found
                        this.showStep(index + 1);
                        return;
                    }
                    
                    // Highlight element
                    \$el.addClass('pfg-tour-highlight');
                    
                    // Scroll to element first, then position tooltip after scroll completes
                    $('html, body').animate({
                        scrollTop: Math.max(0, \$el.offset().top - 150)
                    }, 300, function() {
                        // Create tooltip after scroll completes
                        var tooltip = $('<div class=\"pfg-tour-tooltip arrow-' + step.position + '\">' +
                            '<button class=\"pfg-tour-close\">&times;</button>' +
                            '<h4 class=\"pfg-tour-title\">' + step.title + '</h4>' +
                            '<p class=\"pfg-tour-content\">' + step.content + '</p>' +
                            '<div class=\"pfg-tour-footer\">' +
                                '<span class=\"pfg-tour-step\">' + '" . esc_js( __( 'Step', 'portfolio-filter-gallery' ) ) . "' + ' ' + (index + 1) + ' ' + '" . esc_js( __( 'of', 'portfolio-filter-gallery' ) ) . "' + ' ' + self.steps.length + '</span>' +
                                '<div class=\"pfg-tour-actions\">' +
                                    (index > 0 ? '<button class=\"pfg-tour-btn pfg-tour-btn-secondary pfg-tour-prev\">' + '" . esc_js( __( 'Back', 'portfolio-filter-gallery' ) ) . "' + '</button>' : '') +
                                    '<button class=\"pfg-tour-btn pfg-tour-btn-primary pfg-tour-next\">' + 
                                        (index === self.steps.length - 1 ? '" . esc_js( __( 'Finish', 'portfolio-filter-gallery' ) ) . "' : '" . esc_js( __( 'Next', 'portfolio-filter-gallery' ) ) . "') + 
                                    '</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>');
                        
                        $('body').append(tooltip);
                        
                        // Position tooltip after it's in DOM
                        setTimeout(function() {
                            var pos = \$el.offset();
                            var elHeight = \$el.outerHeight();
                            var elWidth = \$el.outerWidth();
                            var tooltipWidth = tooltip.outerWidth();
                            var tooltipHeight = tooltip.outerHeight();
                            
                            switch(step.position) {
                                case 'bottom':
                                    tooltip.css({
                                        top: pos.top + elHeight + 15,
                                        left: Math.max(20, pos.left)
                                    });
                                    break;
                                case 'top':
                                    tooltip.css({
                                        top: pos.top - tooltipHeight - 15,
                                        left: Math.max(20, pos.left)
                                    });
                                    break;
                                case 'left':
                                    tooltip.css({
                                        top: pos.top,
                                        left: pos.left - tooltipWidth - 15
                                    });
                                    break;
                                case 'right':
                                    tooltip.css({
                                        top: pos.top,
                                        left: pos.left + elWidth + 15
                                    });
                                    break;
                            }
                        }, 50);
                        
                        // Event handlers
                        tooltip.find('.pfg-tour-next').on('click', function() {
                            self.showStep(index + 1);
                        });
                        
                        tooltip.find('.pfg-tour-prev').on('click', function() {
                            self.showStep(index - 1);
                        });
                        
                        tooltip.find('.pfg-tour-close').on('click', function() {
                            self.dismiss();
                        });
                    });
                },
                
                complete: function() {
                    $('.pfg-tour-tooltip').remove();
                    $('.pfg-tour-highlight').removeClass('pfg-tour-highlight');
                    
                    $.post(ajaxurl, {
                        action: 'pfg_complete_tour',
                        security: '" . $nonce . "'
                    });
                },
                
                dismiss: function() {
                    $('.pfg-tour-tooltip').remove();
                    $('.pfg-tour-highlight').removeClass('pfg-tour-highlight');
                    
                    $.post(ajaxurl, {
                        action: 'pfg_dismiss_tour',
                        security: '" . $nonce . "'
                    });
                }
            };
            
            // Start tour after a short delay
            setTimeout(function() {
                PFGTour.init();
            }, 500);
        });
        ";
    }

    /**
     * AJAX: Complete tour
     */
    public static function ajax_complete_tour() {
        check_ajax_referer( 'pfg_tour_nonce', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Insufficient permissions.', 'portfolio-filter-gallery' ) ) );
        }
        
        update_option( self::TOUR_COMPLETED_OPTION, true );
        delete_option( 'pfg_show_tour' );
        
        wp_send_json_success();
    }

    /**
     * AJAX: Dismiss tour
     */
    public static function ajax_dismiss_tour() {
        check_ajax_referer( 'pfg_tour_nonce', 'security' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Insufficient permissions.', 'portfolio-filter-gallery' ) ) );
        }
        
        update_option( self::TOUR_COMPLETED_OPTION, true );
        delete_option( 'pfg_show_tour' );
        
        wp_send_json_success();
    }

    /**
     * Check if tour is completed
     */
    public static function is_completed() {
        return (bool) get_option( self::TOUR_COMPLETED_OPTION );
    }

    /**
     * Reset tour (for testing)
     */
    public static function reset() {
        delete_option( self::TOUR_COMPLETED_OPTION );
        delete_option( 'pfg_show_tour' );
    }
}
