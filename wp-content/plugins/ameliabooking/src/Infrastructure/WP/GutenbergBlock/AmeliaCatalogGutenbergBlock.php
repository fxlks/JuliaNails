<?php

/**
 * @copyright © Melograno Ventures. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\GutenbergBlock;

/**
 * Class AmeliaCatalogGutenbergBlock
 *
 * @package AmeliaBooking\Infrastructure\WP\GutenbergBlock
 */
class AmeliaCatalogGutenbergBlock extends GutenbergBlock
{
    public static function getBlockAttributes(): array
    {
        return [
            'short_code'      => ['type' => 'string', 'default' => '[ameliacatalog]'],
            'trigger'         => ['type' => 'string', 'default' => ''],
            'show'            => ['type' => 'string', 'default' => ''],
            'location'        => ['type' => 'string', 'default' => ''],
            'package'         => ['type' => 'string', 'default' => ''],
            'category'        => ['type' => 'string', 'default' => ''],
            'categoryOptions' => ['type' => 'string', 'default' => ''],
            'service'         => ['type' => 'string', 'default' => ''],
            'employee'        => ['type' => 'string', 'default' => ''],
            'parametars'      => ['type' => 'boolean', 'default' => false],
        ];
    }

    public static function renderBlock(array $attributes): string
    {
        $shortCode = $attributes['short_code'] ?? '[ameliacatalog]';

        if (strpos($shortCode, '[ameliacatalog') !== 0) {
            return '';
        }

        return do_shortcode($shortCode);
    }

    public static function registerBlockForRendering()
    {
        register_block_type(
            'amelia/catalog-gutenberg-block',
            array(
                'attributes'      => self::getBlockAttributes(),
                'render_callback' => array(__CLASS__, 'renderBlock'),
            )
        );
    }

    /**
     * Register Amelia Catalog block for gutenberg
     */
    public static function registerBlockType()
    {
        // Enqueue shared icon and styles
        parent::enqueueSharedIcon();
        parent::enqueueSharedStyles();

        wp_enqueue_script(
            'amelia_catalog_gutenberg_block',
            AMELIA_URL . 'public/js/gutenberg/amelia-catalog/amelia-catalog-gutenberg.js',
            array('wp-blocks', 'wp-components', 'wp-element', 'wp-block-editor', 'amelia_block_icon')
        );

        wp_enqueue_style(
            'amelia_catalog_gutenberg_styles',
            AMELIA_URL . 'public/js/gutenberg/amelia-catalog/amelia-catalog-gutenberg.css',
            [],
            AMELIA_VERSION
        );
    }
}
