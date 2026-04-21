<?php

namespace AmeliaBooking\Infrastructure\WP\WPMenu;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class AdminBarMenu
 *
 * @package AmeliaBooking\Infrastructure\WP\WPMenu
 */
class AdminBarMenu
{
    /** @var SettingsService $settingsService */
    private $settingsService;

    /** @var bool $scriptAdded */
    private static $scriptAdded = false;

    /**
     * AdminBarMenu constructor.
     *
     * @param SettingsService $settingsService
     */
    public function __construct($settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Add Amelia menu to WordPress admin bar
     */
    public function addAdminBarMenu(\WP_Admin_Bar $wpAdminBar)
    {
        if (!current_user_can('amelia_read_menu')) {
            return;
        }

        $icon = $this->getAmeliaIcon();

        $wpAdminBar->add_menu([
            'id'    => 'amelia-menu',
            'title' => '<span class="ab-icon">' . $icon . '</span><span class="ab-label">Amelia</span>',
            'href'  => admin_url('admin.php?page=wpamelia-dashboard'),
            'meta'  => [
                'title' => 'Amelia Booking',
                'class' => 'amelia-admin-bar-menu'
            ]
        ]);

        if (current_user_can('amelia_read_calendar')) {
            $wpAdminBar->add_node([
                'parent' => 'amelia-menu',
                'id'     => 'amelia-calendar',
                'title'  => BackendStrings::get('calendar'),
                'href'   => admin_url('admin.php?page=wpamelia-calendar'),
                'meta'   => [
                    'title' => BackendStrings::get('calendar')
                ]
            ]);
        }

        if (current_user_can('amelia_write_appointments') || current_user_can('amelia_write_events')) {
            $wpAdminBar->add_node([
                'parent' => 'amelia-menu',
                'id'     => 'amelia-add-booking',
                'title'  => BackendStrings::get('add_booking'),
                'href'   => '#',
                'meta'   => [
                    'title' => BackendStrings::get('add_booking')
                ]
            ]);

            if (current_user_can('amelia_write_appointments')) {
                $wpAdminBar->add_node([
                    'parent' => 'amelia-add-booking',
                    'id'     => 'amelia-add-appointment',
                    'title'  => BackendStrings::get('add_appointment'),
                    'href'   => admin_url('admin.php?page=wpamelia-bookings#/book-appointment'),
                    'meta'   => [
                        'title' => BackendStrings::get('add_appointment')
                    ]
                ]);

                if ($this->settingsService->isFeatureEnabled('packages')) {
                    $wpAdminBar->add_node([
                        'parent' => 'amelia-add-booking',
                        'id'     => 'amelia-add-package',
                        'title'  => BackendStrings::get('add_package'),
                        'href'   => admin_url('admin.php?page=wpamelia-bookings#/book-package'),
                        'meta'   => [
                            'title' => BackendStrings::get('add_package')
                        ]
                    ]);
                }
            }

            if (current_user_can('amelia_write_events')) {
                $wpAdminBar->add_node([
                    'parent' => 'amelia-add-booking',
                    'id'     => 'amelia-add-event',
                    'title'  => BackendStrings::get('add_event'),
                    'href'   => admin_url('admin.php?page=wpamelia-bookings#/book-event'),
                    'meta'   => [
                        'title' => BackendStrings::get('add_event')
                    ]
                ]);
            }
        }

        if (current_user_can('amelia_write_customers')) {
            $wpAdminBar->add_node([
                'parent' => 'amelia-menu',
                'id'     => 'amelia-add-customer',
                'title'  => BackendStrings::get('add_customer'),
                'href'   => admin_url('admin.php?page=wpamelia-customers#/manage'),
                'meta'   => [
                    'title' => BackendStrings::get('add_customer')
                ]
            ]);
        }

        if (current_user_can('amelia_read_settings')) {
            $wpAdminBar->add_node([
                'parent' => 'amelia-menu',
                'id'     => 'amelia-settings',
                'title'  => BackendStrings::get('settings'),
                'href'   => admin_url('admin.php?page=wpamelia-settings'),
                'meta'   => [
                    'title' => BackendStrings::get('settings')
                ]
            ]);
        }
    }

    /**
     * Get the inline SVG icon for Amelia
     */
    private function getAmeliaIcon(): string
    {
        $svgPath1 = 'M246.903 104.295V211.599C246.903 220.45 251.629 228.61 259.285 233.004L352.076 286.197';
        $svgPath1 .= 'C368.474 295.598 388.878 283.725 388.878 264.792V157.996C388.878 149.188 384.194 141.049 ';
        $svgPath1 .= '376.582 136.645L283.791 82.9336C267.392 73.4457 246.892 85.3083 246.892 104.284L246.903 104.295Z';

        $svgPath2 = 'M221.57 104.295V211.599C221.57 220.45 216.844 228.61 209.188 233.004L116.397 286.197';
        $svgPath2 .= 'C99.9988 295.598 79.5951 283.725 79.5951 264.792V157.996C79.5951 149.188 84.2788 141.049 ';
        $svgPath2 .= '91.8912 136.645L184.682 82.9336C201.08 73.4457 221.581 85.3083 221.581 104.284L221.57 104.295Z';

        $svgPath3 = 'M220.666 254.228L127.347 307.723C110.874 317.168 110.82 340.969 127.261 350.479L220.58 404.492';
        $svgPath3 .= 'C228.192 408.896 237.57 408.896 245.172 404.492L338.49 350.479C354.932 340.969 354.878 ';
        $svgPath3 .= '317.168 338.404 307.723L245.086 254.228C237.516 249.889 228.235 249.889 220.666 254.228Z';

        return '<svg width="20" height="20" viewBox="40 62 390 370" fill="none" xmlns="http://www.w3.org/2000/svg" '
            . 'style="vertical-align: middle; position: relative; top: 2px;">'
            . '<path d="' . $svgPath1 . '" fill="currentColor"/>'
            . '<path d="' . $svgPath2 . '" fill="currentColor"/>'
            . '<path d="' . $svgPath3 . '" fill="currentColor"/>'
            . '</svg>';
    }

    /**
     * Enqueue admin bar menu script for both admin and frontend
     */
    public static function enqueueScripts()
    {
        if (self::$scriptAdded) {
            return;
        }

        self::$scriptAdded = true;

        $enqueueCallback = function () {
            if (is_admin_bar_showing() && current_user_can('amelia_read_menu')) {
                wp_enqueue_script(
                    'amelia-admin-bar-menu',
                    AMELIA_URL . 'public/js/admin-bar/admin-bar-menu.js',
                    ['jquery'],
                    AMELIA_VERSION,
                    true
                );
            }
        };

        add_action('admin_enqueue_scripts', $enqueueCallback);
        add_action('wp_enqueue_scripts', $enqueueCallback);
        add_action('admin_head', [self::class, 'addAdminBarStyles']);
        add_action('wp_head', [self::class, 'addAdminBarStyles']);
    }

    /**
     * Add admin bar icon styling
     */
    public static function addAdminBarStyles()
    {
        $isRtl = is_rtl();

        $css = '
        #wpadminbar #wp-admin-bar-amelia-menu > .ab-item svg {
            color: #a7aaad;
        }

        #wpadminbar #wp-admin-bar-amelia-menu:hover > .ab-item svg {
            color: #72aee6;
        }
        ';

        // Add RTL-specific styles - rotate arrow 180 degrees
        if ($isRtl) {
            $css .= '
            /* RTL Support: Flip submenu arrow to point left */
            #wpadminbar li#wp-admin-bar-amelia-add-booking .ab-item .wp-admin-bar-arrow:before {
                content: "\f141" !important;
            }
            ';
        }

        echo '<style>' . $css . '</style>';
    }
}
