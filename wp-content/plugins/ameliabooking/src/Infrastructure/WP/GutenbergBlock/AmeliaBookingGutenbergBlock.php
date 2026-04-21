<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class AmeliaBookingGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaBookingGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return array(
            'short_code' => array('type' => 'string', 'default' => '[ameliabooking]'),
            'trigger'    => array('type' => 'string', 'default' => ''),
            'show'       => array('type' => 'string', 'default' => ''),
            'location'   => array('type' => 'string', 'default' => ''),
            'category'   => array('type' => 'string', 'default' => ''),
            'service'    => array('type' => 'string', 'default' => ''),
            'employee'   => array('type' => 'string', 'default' => ''),
            'parametars' => array('type' => 'boolean', 'default' => false),
        );
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = isset($attributes['short_code']) ? $attributes['short_code'] : '[ameliabooking]';

        if (strpos($shortCode, '[ameliabooking') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/booking-gutenberg-block',
            array(
                'attributes'      => self::getBlockAttributes(),
                'render_callback' => array(__CLASS__, 'renderBlock'),
            )
        );
    }

    /**
     * Register Amelia Booking block for Gutenberg
     */
    public static function registerBlockType()
    {
        // Enqueue shared icon and styles
        parent::enqueueSharedIcon();
        parent::enqueueSharedStyles();

        wp_enqueue_script(
            'amelia_booking_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-booking/amelia-booking-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_localize_script(
            'amelia_booking_gutenberg_block',
            'wpAmeliaLabels',
            array_merge(
                BackendStrings::getAllStrings(),
                self::getEntitiesData()
            )
        );

        wp_enqueue_style(
            'amelia_booking_gutenberg_styles',
            AMELIA_URL . 'public/js/gutenberg/amelia-booking/amelia-booking-gutenberg.css',
            [],
            AMELIA_VERSION
        );
    }
}
