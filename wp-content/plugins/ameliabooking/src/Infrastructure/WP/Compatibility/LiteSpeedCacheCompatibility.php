<?php

/**
 * LiteSpeed Cache Compatibility
 *
 * Automatically adds Amelia scripts to LiteSpeed Cache exclusion lists
 * to prevent JavaScript errors caused by script optimization interfering with wp_localize_script data.
 *
 * @since 9.1
 */

namespace AmeliaBooking\Infrastructure\WP\Compatibility;

/**
 * Class LiteSpeedCacheCompatibility
 *
 * @package AmeliaBooking\Infrastructure\WP\Compatibility
 */
class LiteSpeedCacheCompatibility
{
    /**
     * Initialize LiteSpeed Cache compatibility hooks
     */
    public static function init()
    {
        if (self::isLiteSpeedActive()) {
            add_filter('litespeed_optimize_js_excludes', array(__CLASS__, 'excludeAmeliaScripts'));
            add_filter('litespeed_optm_js_defer_exc', array(__CLASS__, 'excludeAmeliaScripts'));
        }
    }

    /**
     * Exclude Amelia scripts from LiteSpeed Cache optimization
     */
    public static function excludeAmeliaScripts(array $excluded_js): array
    {
        if (!is_array($excluded_js)) {
            $excluded_js = array();
        }

        $amelia_patterns = array(
            'amelia',
            'wpAmeliaUrls',
            'wpAmeliaLabels',
            'wpAmeliaSettings',
            'amelia_booking_script',
            'amelia_booking_scripts',
            '/ameliabooking/',
            '/wpamelia-',
        );

        return array_merge($excluded_js, $amelia_patterns);
    }

    /**
     * Check if LiteSpeed Cache is active
     */
    private static function isLiteSpeedActive(): bool
    {
        return defined('LSCWP_V') || class_exists('LiteSpeed\Core');
    }
}
