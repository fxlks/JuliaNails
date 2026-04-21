<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class AmeliaCatalogBookingGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaCatalogBookingGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return [
            'short_code'      => ['type' => 'string', 'default' => '[ameliacatalogbooking]'],
            'trigger'         => ['type' => 'string', 'default' => ''],
            'trigger_type'    => ['type' => 'string', 'default' => 'id'],
            'in_dialog'       => ['type' => 'boolean', 'default' => false],
            'show'            => ['type' => 'string', 'default' => ''],
            'location'        => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'package'         => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'category'        => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'categoryOptions' => ['type' => 'string', 'default' => ''],
            'service'         => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'employee'        => ['type' => 'array', 'default' => [], 'items' => ['type' => 'string']],
            'parametars'      => ['type' => 'boolean', 'default' => false],
            'skip_categories' => ['type' => 'boolean', 'default' => false],
        ];
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = $attributes['short_code'] ?? '[ameliacatalogbooking]';

        if (strpos($shortCode, '[ameliacatalogbooking') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/catalog-booking-gutenberg-block',
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
            'amelia_catalog_booking_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-catalog-booking/amelia-catalog-booking-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_localize_script(
            'amelia_catalog_booking_gutenberg_block',
            'wpAmeliaLabels',
            array_merge(
                BackendStrings::getAllStrings(),
                self::getEntitiesData()
            )
        );
    }
}
