<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaBooking\Infrastructure\Licence;

/**
 * Class AmeliaEventsGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaEventsGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return [
            'short_code'   => ['type' => 'string', 'default' => '[ameliaevents]'],
            'trigger'      => ['type' => 'string', 'default' => ''],
            'event'        => ['type' => 'string', 'default' => ''],
            'type'         => ['type' => 'string', 'default' => 'list'],
            'recurring'    => ['type' => 'boolean', 'default' => false],
            'tag'          => ['type' => 'string', 'default' => ''],
            'eventOptions' => ['type' => 'string', 'default' => ''],
            'parametars'   => ['type' => 'boolean', 'default' => false],
        ];
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = $attributes['short_code'] ?? '[ameliaevents]';

        if (strpos($shortCode, '[ameliaevents') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/events-gutenberg-block',
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
            'amelia_events_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-events/amelia-events-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_localize_script(
            'amelia_events_gutenberg_block',
            'wpAmeliaLabels',
            array_merge(
                BackendStrings::getAllStrings(),
                self::getEntitiesData(),
                array('isLite' => !Licence\Licence::isPremium())
            )
        );

        wp_enqueue_style(
            'amelia_events_gutenberg_styles',
            AMELIA_URL . 'public/js/gutenberg/amelia-events/amelia-events-gutenberg.css',
            [],
            AMELIA_VERSION
        );
    }
}
