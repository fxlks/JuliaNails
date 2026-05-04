<?php
/**
 * Starter templates for the plugin.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/data
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Provides pre-designed gallery templates.
 */
class PFG_Templates {

    /**
     * Get all available templates.
     *
     * @return array
     */
    public static function get_templates() {
        return array(
            'minimal-grid' => array(
                'name'        => __( 'Minimal Grid', 'portfolio-filter-gallery' ),
                'description' => __( 'Clean, minimal grid layout with subtle hover effects.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/minimal-grid.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 4,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 20,
                    'hover_effect'   => 'fade',
                    'show_title'     => false,
                    'border_width'   => 0,
                    'border_radius'  => 0,
                    'grayscale'      => false,
                ),
            ),

            'masonry-portfolio' => array(
                'name'        => __( 'Masonry Portfolio', 'portfolio-filter-gallery' ),
                'description' => __( 'Pinterest-style masonry layout perfect for portfolios.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/masonry-portfolio.jpg',
                'settings'    => array(
                    'layout_type'    => 'masonry',
                    'columns_xl'     => 4,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 15,
                    'hover_effect'   => 'slide-up',
                    'show_title'     => true,
                    'border_width'   => 0,
                    'border_radius'  => 8,
                    'grayscale'      => false,
                ),
            ),

            'instagram-style' => array(
                'name'        => __( 'Instagram Style', 'portfolio-filter-gallery' ),
                'description' => __( 'Square grid with minimal spacing, like Instagram.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/instagram.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 4,
                    'columns_lg'     => 3,
                    'columns_md'     => 3,
                    'columns_sm'     => 2,
                    'gap'            => 3,
                    'hover_effect'   => 'overlay-zoom',
                    'show_title'     => false,
                    'border_width'   => 0,
                    'border_radius'  => 0,
                    'grayscale'      => false,
                ),
            ),

            'modern-cards' => array(
                'name'        => __( 'Modern Cards', 'portfolio-filter-gallery' ),
                'description' => __( 'Card-style layout with title and category below image.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/modern-cards.jpg',
                'settings'    => array(
                    'layout_type'    => 'masonry',
                    'columns_xl'     => 3,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 30,
                    'hover_effect'   => 'lift',
                    'show_title'     => true,
                    'title_position' => 'below',
                    'show_categories'=> true,
                    'border_width'   => 0,
                    'border_radius'  => 12,
                    'grayscale'      => false,
                ),
            ),

            'elegant-gallery' => array(
                'name'        => __( 'Elegant Gallery', 'portfolio-filter-gallery' ),
                'description' => __( 'Grayscale to color effect with elegant hover.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/elegant-gallery.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 4,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 10,
                    'hover_effect'   => 'color-reveal',
                    'show_title'     => true,
                    'border_width'   => 1,
                    'border_color'   => '#e0e0e0',
                    'border_radius'  => 0,
                    'grayscale'      => true,
                ),
            ),

            'full-width' => array(
                'name'        => __( 'Full Width', 'portfolio-filter-gallery' ),
                'description' => __( 'Large images with 2-column layout.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/full-width.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 2,
                    'columns_lg'     => 2,
                    'columns_md'     => 1,
                    'columns_sm'     => 1,
                    'gap'            => 20,
                    'hover_effect'   => 'fade',
                    'show_title'     => true,
                    'border_width'   => 0,
                    'border_radius'  => 4,
                    'grayscale'      => false,
                ),
            ),

            'tight-grid' => array(
                'name'        => __( 'Tight Grid', 'portfolio-filter-gallery' ),
                'description' => __( 'No-gap grid for a seamless look.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/tight-grid.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 5,
                    'columns_lg'     => 4,
                    'columns_md'     => 3,
                    'columns_sm'     => 2,
                    'gap'            => 0,
                    'hover_effect'   => 'overlay',
                    'show_title'     => false,
                    'border_width'   => 0,
                    'border_radius'  => 0,
                    'grayscale'      => false,
                ),
            ),

            'creative-agency' => array(
                'name'        => __( 'Creative Agency', 'portfolio-filter-gallery' ),
                'description' => __( 'Bold design with animated hover effects.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/creative-agency.jpg',
                'settings'    => array(
                    'layout_type'    => 'masonry',
                    'columns_xl'     => 3,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 25,
                    'hover_effect'   => 'slide-in',
                    'show_title'     => true,
                    'border_width'   => 0,
                    'border_radius'  => 16,
                    'grayscale'      => false,
                    'filter_bg_color' => '#3858e9',
                ),
            ),

            'photography' => array(
                'name'        => __( 'Photography', 'portfolio-filter-gallery' ),
                'description' => __( 'Optimized for photographers with dark overlay.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/photography.jpg',
                'settings'    => array(
                    'layout_type'    => 'masonry',
                    'columns_xl'     => 4,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 8,
                    'hover_effect'   => 'dark-overlay',
                    'show_title'     => true,
                    'border_width'   => 0,
                    'border_radius'  => 0,
                    'grayscale'      => false,
                ),
            ),

            'minimalist' => array(
                'name'        => __( 'Minimalist', 'portfolio-filter-gallery' ),
                'description' => __( 'Super clean with lots of white space.', 'portfolio-filter-gallery' ),
                'preview'     => PFG_PLUGIN_URL . 'assets/templates/minimalist.jpg',
                'settings'    => array(
                    'layout_type'    => 'grid',
                    'columns_xl'     => 3,
                    'columns_lg'     => 3,
                    'columns_md'     => 2,
                    'columns_sm'     => 1,
                    'gap'            => 40,
                    'hover_effect'   => 'subtle',
                    'show_title'     => false,
                    'border_width'   => 0,
                    'border_radius'  => 2,
                    'grayscale'      => false,
                ),
            ),

        );
    }

    /**
     * Get all available templates (alias for get_templates).
     *
     * @return array
     */
    public static function get_all() {
        return self::get_templates();
    }

    /**
     * Get a single template by name.
     *
     * @param string $name Template name/slug.
     * @return array|null Template data or null.
     */
    public static function get_template( $name ) {
        $templates = self::get_templates();
        return isset( $templates[ $name ] ) ? $templates[ $name ] : null;
    }

    /**
     * Get template names for dropdown.
     *
     * @return array Key-value pairs of slug => name.
     */
    public static function get_template_options() {
        $templates = self::get_templates();
        $options   = array( '' => __( 'Select a template...', 'portfolio-filter-gallery' ) );

        foreach ( $templates as $slug => $template ) {
            $options[ $slug ] = $template['name'];
        }

        return $options;
    }
}
