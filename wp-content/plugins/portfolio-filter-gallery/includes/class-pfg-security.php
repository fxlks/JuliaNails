<?php
/**
 * Security utilities for the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Centralized security class for handling nonces, capabilities, and sanitization.
 */
class PFG_Security {

    /**
     * Nonce action prefix.
     */
    const NONCE_PREFIX = 'pfg_';

    /**
     * Verify a nonce from POST data.
     *
     * @param string $nonce_name The nonce field name.
     * @param string $action     The nonce action.
     * @return bool True if nonce is valid.
     */
    public static function verify_nonce( $nonce_name, $action ) {
        $nonce = isset( $_POST[ $nonce_name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ) ) : '';
        return wp_verify_nonce( $nonce, self::NONCE_PREFIX . $action );
    }

    /**
     * Verify AJAX nonce.
     *
     * @param string $action The nonce action (without prefix).
     * @param string $nonce_key The key in $_POST or $_REQUEST. Default 'security'.
     * @return bool True if valid, sends JSON error and exits if not.
     */
    public static function verify_ajax_nonce( $action, $nonce_key = 'security' ) {
        $nonce = isset( $_POST[ $nonce_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ) : '';
        
        if ( ! wp_verify_nonce( $nonce, self::NONCE_PREFIX . $action ) ) {
            wp_send_json_error(
                array(
                    'message' => __( 'Security check failed. Please refresh the page and try again.', 'portfolio-filter-gallery' ),
                    'code'    => 'invalid_nonce',
                ),
                403
            );
        }
        
        return true;
    }

    /**
     * Check if current user can manage galleries.
     *
     * @return bool
     */
    public static function can_manage_galleries() {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if current user can edit a specific gallery.
     *
     * @param int $gallery_id The gallery post ID.
     * @return bool
     */
    public static function can_edit_gallery( $gallery_id ) {
        return current_user_can( 'edit_post', $gallery_id );
    }

    /**
     * Check if current user can delete galleries.
     *
     * @return bool
     */
    public static function can_delete_galleries() {
        return current_user_can( 'delete_posts' );
    }

    /**
     * Check if current user is an administrator.
     *
     * @return bool
     */
    public static function is_admin() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Sanitize input based on type.
     *
     * @param mixed  $data The data to sanitize.
     * @param string $type The type of sanitization.
     * @return mixed Sanitized data.
     */
    public static function sanitize( $data, $type = 'text' ) {
        switch ( $type ) {
            case 'text':
                return sanitize_text_field( $data );

            case 'textarea':
                return sanitize_textarea_field( $data );

            case 'url':
                return esc_url_raw( $data );

            case 'int':
                return intval( $data );

            case 'float':
                return floatval( $data );

            case 'email':
                return sanitize_email( $data );

            case 'html':
                return wp_kses_post( $data );

            case 'css':
                return self::sanitize_css( $data );

            case 'hex_color':
                return self::sanitize_hex_color( $data );

            case 'bool':
                return filter_var( $data, FILTER_VALIDATE_BOOLEAN );

            case 'array':
                return is_array( $data ) ? array_map( 'sanitize_text_field', $data ) : array();

            case 'key':
                return sanitize_key( $data );

            default:
                return sanitize_text_field( $data );
        }
    }

    /**
     * Sanitize an array of data based on a schema.
     *
     * @param array $data   The data to sanitize.
     * @param array $schema The sanitization schema (field => type).
     * @return array Sanitized data.
     */
    public static function sanitize_array( $data, $schema ) {
        $sanitized = array();

        foreach ( $schema as $key => $type ) {
            if ( isset( $data[ $key ] ) ) {
                $sanitized[ $key ] = self::sanitize( $data[ $key ], $type );
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize custom CSS to prevent XSS.
     *
     * @param string $css The CSS to sanitize.
     * @return string Sanitized CSS.
     */
    public static function sanitize_css( $css ) {
        // Remove any script tags or JavaScript
        $css = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $css );
        
        // Remove expressions and JavaScript URLs
        $css = preg_replace( '/expression\s*\(/i', '', $css );
        $css = preg_replace( '/javascript\s*:/i', '', $css );
        $css = preg_replace( '/behavior\s*:/i', '', $css );
        $css = preg_replace( '/-moz-binding\s*:/i', '', $css );
        
        // Strip HTML tags
        $css = wp_strip_all_tags( $css );
        
        return $css;
    }

    /**
     * Sanitize hex color value.
     *
     * @param string $color The color value.
     * @return string Sanitized hex color or empty string.
     */
    public static function sanitize_hex_color( $color ) {
        if ( empty( $color ) ) {
            return '';
        }

        // 3 or 6 hex digits, or the empty string.
        if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
            return $color;
        }

        // Try adding # if missing.
        if ( preg_match( '/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
            return '#' . $color;
        }

        return '';
    }

    /**
     * Generate a nonce field.
     *
     * @param string $action The nonce action (without prefix).
     * @param string $name   Optional. Nonce name. Default '_pfg_nonce'.
     * @param bool   $echo   Optional. Whether to display or return. Default true.
     * @return string The nonce field HTML if $echo is false.
     */
    public static function nonce_field( $action, $name = '_pfg_nonce', $echo = true ) {
        return wp_nonce_field( self::NONCE_PREFIX . $action, $name, true, $echo );
    }

    /**
     * Create a nonce value.
     *
     * @param string $action The nonce action (without prefix).
     * @return string The nonce value.
     */
    public static function create_nonce( $action ) {
        return wp_create_nonce( self::NONCE_PREFIX . $action );
    }

    /**
     * Verify request method.
     *
     * @param string $method Expected method (GET, POST, etc.).
     * @return bool
     */
    public static function verify_request_method( $method = 'POST' ) {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- comparison only, no output.
        return isset( $_SERVER['REQUEST_METHOD'] ) && strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) === strtoupper( $method );
    }

    /**
     * Safely get POST data.
     *
     * @param string $key     The POST key.
     * @param mixed  $default Default value if not set.
     * @param string $type    Sanitization type.
     * @return mixed
     */
    public static function get_post( $key, $default = '', $type = 'text' ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- this is a utility method called after nonce verification.
        if ( ! isset( $_POST[ $key ] ) ) {
            return $default;
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by self::sanitize().
        return self::sanitize( wp_unslash( $_POST[ $key ] ), $type );
    }

    /**
     * Safely get GET data.
     *
     * @param string $key     The GET key.
     * @param mixed  $default Default value if not set.
     * @param string $type    Sanitization type.
     * @return mixed
     */
    public static function get_query( $key, $default = '', $type = 'text' ) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- this is a utility method called after nonce verification.
        if ( ! isset( $_GET[ $key ] ) ) {
            return $default;
        }
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by self::sanitize().
        return self::sanitize( wp_unslash( $_GET[ $key ] ), $type );
    }
}
