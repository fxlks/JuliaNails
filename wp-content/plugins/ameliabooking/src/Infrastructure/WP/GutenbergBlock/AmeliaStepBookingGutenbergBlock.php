<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class AmeliaStepBookingGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaStepBookingGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return [
            'short_code'  => ['type' => 'string', 'default' => '[ameliastepbooking]'],
            'trigger'     => ['type' => 'string', 'default' => ''],
            'trigger_type' => ['type' => 'string', 'default' => 'id'],
            'in_dialog'   => ['type' => 'boolean', 'default' => false],
            'show'        => ['type' => 'string', 'default' => ''],
            'location'    => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'package'     => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'category'    => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'service'     => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'employee'    => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'parametars'  => ['type' => 'boolean', 'default' => false],
            'layout'      => ['type' => 'string', 'default' => '1'],
        ];
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = $attributes['short_code'] ?? '[ameliastepbooking]';

        if (strpos($shortCode, '[ameliastepbooking') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/step-booking-gutenberg-block',
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
            'amelia_step_booking_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-step-booking/amelia-step-booking-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_localize_script(
            'amelia_step_booking_gutenberg_block',
            'wpAmeliaLabels',
            array_merge(
                BackendStrings::getAllStrings(),
                self::getEntitiesData()
            )
        );


        wp_enqueue_style(
            'amelia_step_booking_gutenberg_styles',
            AMELIA_URL . 'public/js/gutenberg/amelia-step-booking/amelia-gutenberg-styles.css',
            [],
            AMELIA_VERSION
        );
    }
}
