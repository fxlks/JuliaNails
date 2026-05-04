<?php
/**
 * Feature availability helper.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class for managing feature availability.
 * All features in this plugin are free and fully available.
 */
class PFG_Features {

    /**
     * Check if a feature is available.
     * All features are available in this free plugin.
     *
     * @param string $feature Feature key.
     * @return bool Always true.
     */
    public static function is_available( $feature ) {
        return true;
    }

    /**
     * Get feature limit (if any).
     *
     * @param string $feature Feature key.
     * @return int|null Null means no limit.
     */
    public static function get_limit( $feature ) {
        return null;
    }
}
