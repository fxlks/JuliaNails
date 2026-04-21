<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaBooking\Infrastructure\Licence;

/**
 * Class AmeliaEventsListBookingGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaEventsListBookingGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return [
            'short_code'   => ['type' => 'string', 'default' => '[ameliaeventslistbooking]'],
            'trigger'      => ['type' => 'string', 'default' => ''],
            'trigger_type' => ['type' => 'string', 'default' => 'id'],
            'in_dialog'    => ['type' => 'boolean', 'default' => false],
            'event'        => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'recurring'    => ['type' => 'boolean', 'default' => false],
            'tag'          => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'location'     => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'eventOptions' => ['type' => 'string', 'default' => ''],
            'parametars'   => ['type' => 'boolean', 'default' => false],
        ];
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = $attributes['short_code'] ?? '[ameliaeventslistbooking]';

        if (strpos($shortCode, '[ameliaeventslistbooking') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/events-list-booking-gutenberg-block',
            array(
                'attributes'      => self::getBlockAttributes(),
                'render_callback' => array(__CLASS__, 'renderBlock'),
            )
        );
    }

    /**
     * Register Amelia Events block for Gutenberg
     */
    public static function registerBlockType()
    {
        // Enqueue shared icon and styles
        parent::enqueueSharedIcon();
        parent::enqueueSharedStyles();

        wp_enqueue_script(
            'amelia_events_list_booking_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-events-list-booking/amelia-events-list-booking-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_localize_script(
            'amelia_events_list_booking_gutenberg_block',
            'wpAmeliaLabels',
            array_merge(
                BackendStrings::getAllStrings(),
                self::getEntitiesData(),
                array('isLite' => !Licence\Licence::isPremium())
            )
        );
    }
}
