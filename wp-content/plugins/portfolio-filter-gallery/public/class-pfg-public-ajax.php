<?php
/**
 * Public AJAX handler for frontend operations.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/public
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles public AJAX requests for gallery operations.
 */
class PFG_Public_Ajax {

    /**
     * Register AJAX actions.
     */
    public function register_actions() {
        // No actions registered yet, so it's empty.
    }

    /**
     * Render a single gallery item.
     *
     * @param array $image      Image data.
     * @param int   $index      Item index.
     * @param array $settings   Gallery settings.
     * @param int   $gallery_id Gallery ID.
     */
    protected function render_item( $image, $index, $settings, $gallery_id ) {
        // Get filter classes
        $filter_classes = $this->get_image_filter_classes( $image );
        
        // Hover effect class
        $hover_class = 'pfg-item-hover--' . esc_attr( $settings['hover_effect'] );

        // Layout-specific classes and styles
        $layout_type    = $settings['layout_type'] ?? 'grid';
        $title_position = $settings['title_position'] ?? 'overlay';
        $size_class     = '';
        $item_style     = '';

        // Get image dimensions for aspect ratio
        $attachment_id = $image['id'];
        $image_meta    = wp_get_attachment_metadata( $attachment_id );
        $width         = isset( $image_meta['width'] ) ? (int) $image_meta['width'] : 1;
        $height        = isset( $image_meta['height'] ) ? (int) $image_meta['height'] : 1;
        $aspect_ratio  = $width / max( $height, 1 );


        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $item_style is constructed from $aspect_ratio (float) with known safe format.
        echo '<div class="pfg-item ' . esc_attr( $filter_classes . ' ' . $hover_class . $size_class ) . '" data-id="' . esc_attr( $image['id'] ) . '"' . $item_style . '>';

        // Type indicator icon (video or link)
        $is_video = isset( $image['type'] ) && $image['type'] === 'video' && ! empty( $image['link'] );
        
        if ( $is_video ) {
            // Video indicator
            echo '<span class="pfg-item-type-icon pfg-item-type-icon--video" title="' . esc_attr__( 'Video', 'portfolio-filter-gallery' ) . '">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>';
            echo '</span>';
        } elseif ( ! empty( $image['link'] ) ) {
            // External link indicator
            echo '<span class="pfg-item-type-icon pfg-item-type-icon--link" title="' . esc_attr__( 'External Link', 'portfolio-filter-gallery' ) . '">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/></svg>';
            echo '</span>';
        }

        // Render by type: video or image
        if ( $is_video ) {
            $this->render_video_item( $image, $index, $settings, $gallery_id );
        } else {
            $this->render_image_item( $image, $index, $settings, $gallery_id );
        }

        echo '</div>';
    }

    /**
     * Render an image item.
     */
    protected function render_image_item( $image, $index, $settings, $gallery_id ) {
        $attachment_id = $image['id'];
        $size          = $this->get_image_size( $settings );

        $img_src    = wp_get_attachment_image_url( $attachment_id, $size );
        $img_srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
        $full_src   = wp_get_attachment_image_url( $attachment_id, 'full' );
        $alt        = $image['title'] ?: get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

        $has_custom_link = ! empty( $image['link'] ) && ( ! isset( $image['type'] ) || $image['type'] === 'url' );
        $show_dual_icons = false;

        $link_url    = $has_custom_link ? $image['link'] : 'javascript:void(0);';
        $link_target = $settings['url_target'];

        // Link attributes
        $link_attrs = 'href="' . esc_url( $link_url ) . '" class="pfg-item-link"';

        if ( $has_custom_link ) {
            $link_attrs .= ' target="' . esc_attr( $link_target ) . '" rel="noopener"';
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $link_attrs is built from esc_url() and esc_attr() calls above.
        echo '<a ' . $link_attrs . '>';

        echo '<img';
        echo ' src="' . esc_url( $img_src ) . '"';
        if ( $img_srcset ) {
            echo ' srcset="' . esc_attr( $img_srcset ) . '"';
        }
        echo ' alt="' . esc_attr( $alt ) . '"';
        echo ' loading="lazy"';
        echo ' decoding="async"';
        echo ' class="pfg-item-image"';
        echo '>';

        // Overlay with title
        $title_position = $settings['title_position'] ?? 'overlay';
        $show_categories = ! empty( $settings['show_categories'] );
        if ( $title_position === 'overlay' && ( $settings['show_title'] || ! empty( $settings['show_numbering'] ) || $show_categories ) ) {
            echo '<div class="pfg-item-caption pfg-item-caption--overlay">';
            
            if ( ! empty( $settings['show_numbering'] ) ) {
                echo '<span class="pfg-item-number">' . esc_html( $index + 1 ) . '</span>';
            }
            
            if ( $settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( $show_categories && ! empty( $image['filters'] ) ) {
                $filter_names = array();
                foreach ( $image['filters'] as $filter_id ) {
                    $name = $this->get_filter_name( $filter_id );
                    if ( $name ) {
                        $filter_names[] = $name;
                    }
                }
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }

        echo '</a>';

        // Card caption (title below image)
        if ( $title_position === 'below' && ( $settings['show_title'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption">';
            if ( $settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            if ( $show_categories && ! empty( $image['filters'] ) ) {
                $filter_names = array();
                foreach ( $image['filters'] as $filter_id ) {
                    $name = $this->get_filter_name( $filter_id );
                    if ( $name ) {
                        $filter_names[] = $name;
                    }
                }
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            echo '</div>';
        }
    }

    /**
     * Render a video item.
     */
    protected function render_video_item( $image, $index, $settings, $gallery_id ) {
        $thumbnail_id = $image['id'];
        $video_url    = $image['link'];
        $size         = $this->get_image_size( $settings );

        $img_src = wp_get_attachment_image_url( $thumbnail_id, $size );
        $alt     = $image['title'] ?: get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

        // Check title position
        $title_position = $settings['title_position'] ?? 'overlay';
        $show_categories = ! empty( $settings['show_categories'] );

        // Video link
        echo '<a href="' . esc_url( $video_url ) . '" class="pfg-item-link pfg-item-link--video" target="_blank" rel="noopener">';

        echo '<img src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async" class="pfg-item-image">';

        // Play button overlay
        echo '<div class="pfg-video-play">';
        echo '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
        echo '</div>';

        // Overlay caption (when title_position is 'overlay')
        if ( $title_position === 'overlay' && ( $settings['show_title'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption pfg-item-caption--overlay">';
            
            if ( $settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( $show_categories && ! empty( $image['filters'] ) ) {
                $filter_names = array();
                foreach ( $image['filters'] as $filter_id ) {
                    $name = $this->get_filter_name( $filter_id );
                    if ( $name ) {
                        $filter_names[] = $name;
                    }
                }
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }

        echo '</a>';

        // Card caption below image (when title_position is 'below')
        if ( $title_position === 'below' && ( $settings['show_title'] || $show_categories ) ) {
            echo '<div class="pfg-item-caption">';
            
            if ( $settings['show_title'] && ! empty( $image['title'] ) ) {
                echo '<h3 class="pfg-item-title">' . esc_html( $image['title'] ) . '</h3>';
            }
            
            if ( $show_categories && ! empty( $image['filters'] ) ) {
                $filter_names = array();
                foreach ( $image['filters'] as $filter_id ) {
                    $name = $this->get_filter_name( $filter_id );
                    if ( $name ) {
                        $filter_names[] = $name;
                    }
                }
                if ( ! empty( $filter_names ) ) {
                    echo '<div class="pfg-item-categories">' . esc_html( implode( ', ', $filter_names ) ) . '</div>';
                }
            }
            
            echo '</div>';
        }
    }



    /**
     * Get filter classes for an image.
     */
    protected function get_image_filter_classes( $image ) {
        if ( empty( $image['filters'] ) ) {
            return '';
        }

        $classes = array();
        
        // Media library images may have filter IDs or slugs - need to handle both
        $all_filters = get_option( 'pfg_filters', array() );
        
        foreach ( $image['filters'] as $filter_key ) {
            $filter_key_lower = strtolower( (string) $filter_key );
            foreach ( $all_filters as $filter ) {
                // Match by ID or slug
                $filter_id = strtolower( (string) $filter['id'] );
                $filter_slug = strtolower( $filter['slug'] );
                if ( $filter_id === $filter_key_lower || $filter_slug === $filter_key_lower ) {
                    $classes[] = 'pfg-filter-' . $filter['slug'];
                    break;
                }
            }
        }

        return implode( ' ', $classes );
    }

    /**
     * Get appropriate image size based on columns.
     */
    protected function get_image_size( $settings ) {
        $columns = max( $settings['columns_lg'], 1 );

        if ( $columns >= 4 ) {
            return 'medium';
        } elseif ( $columns >= 3 ) {
            return 'medium_large';
        } else {
            return 'large';
        }
    }

    /**
     * Get filter name by ID from pfg_filters option.
     *
     * @param int $filter_id Filter ID.
     * @return string Filter name or empty string.
     */
    protected function get_filter_name( $filter_id ) {
        static $filters = null;
        
        if ( $filters === null ) {
            $filters = get_option( 'pfg_filters', array() );
        }
        
        $filter_key_lower = strtolower( (string) $filter_id );
        foreach ( $filters as $filter ) {
            // Match by ID or slug
            $fid = strtolower( (string) ( $filter['id'] ?? '' ) );
            $fslug = strtolower( $filter['slug'] ?? '' );
            if ( $fid === $filter_key_lower || $fslug === $filter_key_lower ) {
                return $filter['name'] ?? '';
            }
        }
        
        return '';
    }
}
