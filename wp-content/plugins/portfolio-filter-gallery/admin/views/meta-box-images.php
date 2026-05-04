<?php
/**
 * Gallery Images Meta Box Template.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



$gallery_id = $post->ID;
$gallery    = new PFG_Gallery( $gallery_id );
$settings   = $gallery->get_settings();
$source     = isset( $settings['source'] ) ? $settings['source'] : 'media';

// Fetch images
$images = $gallery->get_images();
$products = array();
// Get filters - use new format first, then legacy
$filters = get_option( 'pfg_filters', array() );

if ( empty( $filters ) ) {
    // Legacy fallback
    $legacy_filters = get_option( 'awl_portfolio_filter_gallery_categories', array() );
    foreach ( $legacy_filters as $id => $name ) {
        if ( is_string( $name ) ) {
            $filters[] = array(
                'id'     => sanitize_key( $id ),
                'name'   => sanitize_text_field( $name ),
                'slug'   => sanitize_title( $name ),
                'parent' => '',
            );
        }
    }
}

// Build hierarchical tree for display
if ( ! function_exists( 'pfg_build_filter_tree_for_images' ) ) {
    function pfg_build_filter_tree_for_images( $filters, $parent_id = '' ) {
        $tree = array();
        foreach ( $filters as $filter ) {
            $filter_parent = isset( $filter['parent'] ) ? $filter['parent'] : '';
            if ( $filter_parent === $parent_id ) {
                $filter['children'] = pfg_build_filter_tree_for_images( $filters, $filter['id'] );
                $tree[] = $filter;
            }
        }
        return $tree;
    }
}

// Render hierarchical filter checkboxes with tree structure matching hierarchy preview
if ( ! function_exists( 'pfg_render_filter_checkboxes' ) ) {
    function pfg_render_filter_checkboxes( $filters, $depth = 0, $is_first_child = true ) {
        $html = '';
        
        foreach ( $filters as $filter ) {
            // Skip 'all' filter - images are automatically included in 'All' view
            if ( strtolower( $filter['slug'] ) === 'all' ) {
                continue;
            }
            
            $has_children = ! empty( $filter['children'] );
            $color = isset( $filter['color'] ) && $filter['color'] ? $filter['color'] : '#94a3b8';
            
            // Wrapper for each filter item
            $html .= '<div class="pfg-tree-filter-item" data-depth="' . esc_attr($depth) . '">';
            
            // Collapsible group wrapper if has children
            if ( $has_children ) {
                $html .= '<div class="pfg-filter-collapsible-group" data-expanded="true">';
            }
            
            // Main filter row
            $html .= '<div class="pfg-tree-filter-row pfg-depth-level-' . esc_attr($depth) . '">';
            
            // 1. Checkbox (first) - include data-parent for JS child detection
            $parent_id = isset( $filter['parent'] ) ? $filter['parent'] : '';
            $html .= '<label class="pfg-tree-checkbox-label" data-filter="' . esc_attr( $filter['slug'] ) . '" data-color="' . esc_attr( $color ) . '" data-parent="' . esc_attr( $parent_id ) . '">';
            $html .= '<input type="checkbox" value="' . esc_attr( $filter['slug'] ) . '">';
            
            // 2. Collapse toggle (+/-) for parents
            if ( $has_children ) {
                $html .= '<span class="pfg-tree-toggle" title="' . esc_attr__( 'Expand/Collapse', 'portfolio-filter-gallery' ) . '">−</span>';
            } else {
                // Spacer for alignment when no toggle
                $html .= '<span class="pfg-tree-toggle-spacer"></span>';
            }
            
            // 3. Tree connector for child items
            if ( $depth > 0 ) {
                $html .= '<span class="pfg-tree-connector">└</span>';
            }
            
            // 4. Color dot
            $html .= '<span class="pfg-tree-dot" style="background-color: ' . esc_attr( $color ) . ';"></span>';
            
            // 5. Filter name
            $html .= '<span class="pfg-tree-filter-name">' . esc_html( $filter['name'] ) . '</span>';
            $html .= '</label>';
            $html .= '</div>'; // Close row
            
            // Recursively render children
            if ( $has_children ) {
                $html .= '<div class="pfg-tree-children pfg-collapsible-content">';
                $html .= pfg_render_filter_checkboxes( $filter['children'], $depth + 1, true );
                $html .= '</div>';
                $html .= '</div>'; // Close collapsible group
            }
            
            $html .= '</div>'; // Close item
        }
        return $html;
    }
}

// Render filter options for bulk dropdown
if ( ! function_exists( 'pfg_render_filter_options_for_bulk' ) ) {
    function pfg_render_filter_options_for_bulk( $filters, $depth = 0 ) {
        foreach ( $filters as $filter ) {
            // Skip 'all' filter
            if ( strtolower( $filter['slug'] ) === 'all' ) {
                continue;
            }
            
            $indent = str_repeat( '— ', $depth );
            $has_children = ! empty( $filter['children'] );
            
            echo '<option value="' . esc_attr( $filter['id'] ) . '">' . esc_html( $indent . $filter['name'] ) . '</option>';
            
            if ( $has_children ) {
                pfg_render_filter_options_for_bulk( $filter['children'], $depth + 1 );
            }
        }
    }
}

$filter_tree = pfg_build_filter_tree_for_images( $filters );
?>

<div class="pfg-meta-box pfg-images-meta-box">
    


    <!-- Regular Upload Area -->
    <div class="pfg-upload-area" id="pfg-upload-area">
        <div class="pfg-upload-icon">
            <span class="dashicons dashicons-cloud-upload"></span>
        </div>
        <div class="pfg-upload-text">
            <?php esc_html_e( 'Drag & drop images here or click to upload', 'portfolio-filter-gallery' ); ?>
        </div>
        <div class="pfg-upload-hint">
            <?php esc_html_e( 'Supports JPG, PNG, GIF, WebP', 'portfolio-filter-gallery' ); ?>
        </div>
    </div>
    
    <div class="pfg-upload-actions">
        <button type="button" class="pfg-btn pfg-btn-primary pfg-add-images">
            <span class="dashicons dashicons-plus-alt"></span>
            <?php esc_html_e( 'Add Images', 'portfolio-filter-gallery' ); ?>
        </button>
    </div>
    
    <!-- Bulk Actions Bar (Always visible when images exist) -->
    <div class="pfg-bulk-actions" id="pfg-bulk-actions" style="<?php echo empty( $images ) ? 'display: none;' : 'display: flex;'; ?> margin: 15px 0; padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; align-items: center; gap: 15px;">
        <label class="pfg-select-all-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500; color: #475569;">
            <input type="checkbox" id="pfg-select-all" style="width: 18px; height: 18px; cursor: pointer;">
            <?php esc_html_e( 'Select All', 'portfolio-filter-gallery' ); ?>
        </label>
        <span class="pfg-selected-count" style="color: #64748b; font-size: 13px;">
            <span id="pfg-selected-num">0</span> <?php esc_html_e( 'selected', 'portfolio-filter-gallery' ); ?>
        </span>
        
        <!-- Bulk Apply Filters Dropdown -->
        <div class="pfg-bulk-filters-dropdown" style="position: relative; display: none; margin-left: auto;">
            <button type="button" class="pfg-btn pfg-btn-secondary pfg-bulk-filters-btn" style="display: flex; align-items: center; gap: 6px;">
                <span class="dashicons dashicons-filter"></span>
                <?php esc_html_e( 'Apply Filters', 'portfolio-filter-gallery' ); ?>
                <span class="dashicons dashicons-arrow-down-alt2" style="font-size: 14px;"></span>
            </button>
            <div class="pfg-bulk-filters-menu" style="display: none; position: absolute; top: 100%; right: 0; min-width: 280px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 100; margin-top: 4px;">
                <div style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                    <label class="pfg-form-label" style="font-weight: 600; color: #1e293b; display: block; margin-bottom: 8px;">
                        <?php esc_html_e( 'Apply Mode:', 'portfolio-filter-gallery' ); ?>
                    </label>
                    <select id="pfg-bulk-filter-mode" class="pfg-select" style="width: 100%;">
                        <option value="add"><?php esc_html_e( 'Add to Existing Filters', 'portfolio-filter-gallery' ); ?></option>
                        <option value="replace"><?php esc_html_e( 'Replace All Filters', 'portfolio-filter-gallery' ); ?></option>
                        <option value="remove"><?php esc_html_e( 'Remove These Filters', 'portfolio-filter-gallery' ); ?></option>
                    </select>
                </div>
                <div style="padding: 12px; max-height: 250px; overflow-y: auto;">
                    <label class="pfg-form-label" style="font-weight: 600; color: #1e293b; display: block; margin-bottom: 8px;">
                        <?php esc_html_e( 'Select Filters:', 'portfolio-filter-gallery' ); ?>
                    </label>
                    <div id="pfg-bulk-filter-list">
                        <?php 
                        $filter_tree = pfg_build_filter_tree_for_images( $filters );
                        foreach ( $filter_tree as $filter ) : 
                            if ( strtolower( $filter['slug'] ) === 'all' ) continue;
                            $color = isset( $filter['color'] ) && $filter['color'] ? $filter['color'] : '#94a3b8';
                        ?>
                            <label class="pfg-bulk-filter-item" style="display: flex; align-items: center; gap: 8px; padding: 6px 4px; cursor: pointer; border-radius: 4px;">
                                <input type="checkbox" value="<?php echo esc_attr( $filter['id'] ); ?>" class="pfg-bulk-filter-checkbox" style="width: 16px; height: 16px;">
                                <span class="pfg-tag-dot" style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo esc_attr( $color ); ?>;"></span>
                                <span style="flex: 1; color: #1e293b;"><?php echo esc_html( $filter['name'] ); ?></span>
                            </label>
                            <?php if ( ! empty( $filter['children'] ) ) : ?>
                                <?php foreach ( $filter['children'] as $child ) : 
                                    $child_color = isset( $child['color'] ) && $child['color'] ? $child['color'] : '#94a3b8';
                                ?>
                                    <label class="pfg-bulk-filter-item" style="display: flex; align-items: center; gap: 8px; padding: 6px 4px 6px 24px; cursor: pointer; border-radius: 4px;">
                                        <input type="checkbox" value="<?php echo esc_attr( $child['id'] ); ?>" class="pfg-bulk-filter-checkbox" style="width: 16px; height: 16px;">
                                        <span style="color: #94a3b8; font-size: 12px;">└</span>
                                        <span class="pfg-tag-dot" style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo esc_attr( $child_color ); ?>;"></span>
                                        <span style="flex: 1; color: #1e293b;"><?php echo esc_html( $child['name'] ); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div style="padding: 12px; border-top: 1px solid #e2e8f0; display: flex; gap: 8px;">
                    <button type="button" class="pfg-btn pfg-btn-primary pfg-apply-bulk-filters" style="flex: 1;">
                        <?php esc_html_e( 'Apply', 'portfolio-filter-gallery' ); ?>
                    </button>
                    <button type="button" class="pfg-btn pfg-btn-secondary pfg-cancel-bulk-filters">
                        <?php esc_html_e( 'Cancel', 'portfolio-filter-gallery' ); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <button type="button" class="pfg-btn pfg-btn-danger pfg-delete-selected" style="background: #ef4444; color: #fff; display: none;">
            <span class="dashicons dashicons-trash"></span>
            <?php esc_html_e( 'Delete Selected', 'portfolio-filter-gallery' ); ?>
        </button>
        
        <!-- Sort Order (inline in toolbar) -->
        <div style="border-left: 1px solid #e2e8f0; padding-left: 15px; margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-sort" style="font-size: 16px; width: 16px; height: 16px; color: #94a3b8;"></span>
            <select id="pfg-sort-order-images" class="pfg-select" style="max-width: 200px; margin: 0; font-size: 13px;">
                <option value="custom" <?php selected( $settings['sort_order'] ?? 'custom', 'custom' ); ?>><?php esc_html_e( 'Custom Order', 'portfolio-filter-gallery' ); ?></option>
                <option value="date_newest" <?php selected( $settings['sort_order'] ?? 'custom', 'date_newest' ); ?>><?php esc_html_e( 'Newest First', 'portfolio-filter-gallery' ); ?></option>
                <option value="date_oldest" <?php selected( $settings['sort_order'] ?? 'custom', 'date_oldest' ); ?>><?php esc_html_e( 'Oldest First', 'portfolio-filter-gallery' ); ?></option>
                <option value="title_asc" <?php selected( $settings['sort_order'] ?? 'custom', 'title_asc' ); ?>><?php esc_html_e( 'Title A → Z', 'portfolio-filter-gallery' ); ?></option>
                <option value="title_desc" <?php selected( $settings['sort_order'] ?? 'custom', 'title_desc' ); ?>><?php esc_html_e( 'Title Z → A', 'portfolio-filter-gallery' ); ?></option>
                <option value="random" <?php selected( $settings['sort_order'] ?? 'custom', 'random' ); ?>><?php esc_html_e( 'Random', 'portfolio-filter-gallery' ); ?></option>
            </select>
        </div>
    </div>
    

    
    <!-- Image Grid -->
    <div class="pfg-image-grid" id="pfg-image-grid">
        <?php if ( empty( $images ) ) : ?>
            <div class="pfg-no-images">
                <span class="dashicons dashicons-format-gallery"></span>
                <p><?php esc_html_e( 'No images yet. Add some to get started!', 'portfolio-filter-gallery' ); ?></p>
            </div>
        <?php else : ?>
            <?php foreach ( $images as $index => $image ) : 
                $attachment = get_post( $image['id'] );
                if ( ! $attachment ) continue;
                
                $thumb_url = wp_get_attachment_image_url( $image['id'], 'thumbnail' );
                $title     = ! empty( $image['title'] ) ? $image['title'] : $attachment->post_title;
                $image_filters = isset( $image['filters'] ) ? $image['filters'] : array();
            ?>
                <div class="pfg-image-item" data-id="<?php echo esc_attr( $image['id'] ); ?>" data-index="<?php echo esc_attr( $index ); ?>">
                    
                    <!-- Selection Checkbox -->
                    <label class="pfg-image-checkbox" style="position: absolute; top: 8px; left: 8px; z-index: 10;">
                        <input type="checkbox" class="pfg-image-select" style="width: 18px; height: 18px; cursor: pointer;">
                    </label>
                    
                    <?php 
                    // Type indicator icons
                    $image_type = isset( $image['type'] ) ? $image['type'] : 'image';
                    $image_link = isset( $image['link'] ) ? $image['link'] : '';
                    
                    // Detect video source for different styling
                    $video_source = '';
                    if ( $image_type === 'video' && $image_link ) {
                        if ( strpos( $image_link, 'youtube.com' ) !== false || strpos( $image_link, 'youtu.be' ) !== false ) {
                            $video_source = 'youtube';
                        } elseif ( strpos( $image_link, 'vimeo.com' ) !== false ) {
                            $video_source = 'vimeo';
                        }
                    }
                    
                    if ( $image_type === 'video' || $image_type === 'url' ) : 
                        $badge_class = 'pfg-image-type-badge';
                        if ( $video_source === 'youtube' ) {
                            $badge_class .= ' pfg-badge-youtube';
                            $badge_title = __( 'YouTube Video', 'portfolio-filter-gallery' );
                            $badge_icon = 'dashicons-youtube';
                        } elseif ( $video_source === 'vimeo' ) {
                            $badge_class .= ' pfg-badge-vimeo';
                            $badge_title = __( 'Vimeo Video', 'portfolio-filter-gallery' );
                            $badge_icon = 'dashicons-video-alt3';
                        } elseif ( $image_type === 'video' ) {
                            $badge_class .= ' pfg-badge-video';
                            $badge_title = __( 'Video', 'portfolio-filter-gallery' );
                            $badge_icon = 'dashicons-video-alt3';
                        } else {
                            $badge_title = __( 'External Link', 'portfolio-filter-gallery' );
                            $badge_icon = 'dashicons-external';
                        }
                    ?>
                    <div class="<?php echo esc_attr( $badge_class ); ?>" title="<?php echo esc_attr( $badge_title ); ?>">
                        <span class="dashicons <?php echo esc_attr( $badge_icon ); ?>"></span>
                    </div>
                    <?php endif; ?>
                    
                    <img src="<?php echo esc_url( $thumb_url ); ?>" 
                         alt="<?php echo esc_attr( $title ); ?>" 
                         class="pfg-image-thumb"
                         loading="lazy">
                    
                    <div class="pfg-image-actions">
                        <button type="button" class="pfg-image-action pfg-image-edit" title="<?php esc_attr_e( 'Edit', 'portfolio-filter-gallery' ); ?>">
                            <span class="dashicons dashicons-edit"></span>
                        </button>
                        <button type="button" class="pfg-image-action pfg-image-delete" title="<?php esc_attr_e( 'Delete', 'portfolio-filter-gallery' ); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                    
                    <div class="pfg-image-info">
                        <p class="pfg-image-title"><?php echo esc_html( $title ); ?></p>
                        
                        <?php if ( ! empty( $image_filters ) ) : ?>
                        <div class="pfg-image-filters">
                            <?php foreach ( $image_filters as $filter_id ) : 
                                // Find filter in our already-loaded filters array
                                $filter = null;
                                foreach ( $filters as $f ) {
                                    if ( $f['id'] === $filter_id || $f['slug'] === $filter_id ) {
                                        $filter = $f;
                                        break;
                                    }
                                }
                                if ( $filter ) : 
                                $tag_color = isset( $filter['color'] ) && $filter['color'] ? $filter['color'] : '#94a3b8';
                                $is_child = ! empty( $filter['parent'] ); ?>
                                <span class="pfg-image-filter-tag"><?php if ( $is_child ) : ?><span class="pfg-tag-connector">└</span><?php endif; ?><span class="pfg-tag-dot" style="background-color: <?php echo esc_attr( $tag_color ); ?>;"></span><?php echo esc_html( $filter['name'] ); ?></span>
                                <?php endif; 
                            endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Hidden inputs -->
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][id]" value="<?php echo esc_attr( $image['id'] ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][alt]" value="<?php echo esc_attr( $image['alt'] ?? '' ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][description]" value="<?php echo esc_attr( $image['description'] ?? '' ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][link]" value="<?php echo esc_url( $image['link'] ?? '' ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][type]" value="<?php echo esc_attr( $image['type'] ?? 'image' ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][filters]" value="<?php echo esc_attr( implode( ',', $image_filters ) ); ?>">
                    <input type="hidden" name="pfg_images[<?php echo esc_attr( $index ); ?>][original_id]" value="<?php echo esc_attr( $image['original_id'] ?? $image['id'] ); ?>">
                    
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    

    <!-- Hidden field for JSON-serialized image data (bypasses max_input_vars limit) -->
    <?php
    // Pre-populate with all images for masterImagesArray initialization
    $json_images = array();
    if ( ! empty( $images ) && is_array( $images ) ) {
        foreach ( $images as $img ) {
            $json_images[] = array(
                'id'           => (int) $img['id'],
                'title'        => $img['title'] ?? '',
                'alt'          => $img['alt'] ?? '',
                'description'  => $img['description'] ?? '',
                'link'         => $img['link'] ?? '',
                'type'         => $img['type'] ?? 'image',
                'filters'      => is_array( $img['filters'] ?? null ) ? implode( ',', $img['filters'] ) : ( $img['filters'] ?? '' ),
                'product_id'   => '',
                'product_name' => '',
                'original_id'  => $img['original_id'] ?? $img['id'],
            );
        }
    }
    ?>
    <textarea name="pfg_images_json" id="pfg-images-json" style="display: none;"><?php echo esc_textarea( wp_json_encode( $json_images ) ); ?></textarea>
    
</div>

<!-- Image Edit Modal -->
<div id="pfg-image-modal" class="pfg-modal" style="display: none;">
    <div class="pfg-modal-content">
        <div class="pfg-modal-header">
            <button type="button" class="pfg-modal-nav pfg-modal-prev" title="<?php esc_attr_e( 'Previous Image', 'portfolio-filter-gallery' ); ?>" style="display: none;">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>
            <h3><?php esc_html_e( 'Edit Image', 'portfolio-filter-gallery' ); ?> <span class="pfg-modal-counter"></span></h3>
            <button type="button" class="pfg-modal-nav pfg-modal-next" title="<?php esc_attr_e( 'Next Image', 'portfolio-filter-gallery' ); ?>" style="display: none;">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
            <button type="button" class="pfg-modal-close">&times;</button>
        </div>
        
        <div class="pfg-modal-body">
            <div class="pfg-modal-preview">
                <img src="" alt="" id="pfg-modal-image">
            </div>
            
            <div class="pfg-modal-fields">
                <div class="pfg-form-row">
                    <label class="pfg-form-label"><?php esc_html_e( 'Title', 'portfolio-filter-gallery' ); ?></label>
                    <input type="text" id="pfg-modal-title" class="pfg-input">
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e( 'Alt Text', 'portfolio-filter-gallery' ); ?>
                        <small><?php esc_html_e( 'Describes the image for accessibility and SEO', 'portfolio-filter-gallery' ); ?></small>
                    </label>
                    <input type="text" id="pfg-modal-alt" class="pfg-input">
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label"><?php esc_html_e( 'Description', 'portfolio-filter-gallery' ); ?></label>
                    <textarea id="pfg-modal-description" class="pfg-textarea" rows="3"></textarea>
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e( 'Link Type', 'portfolio-filter-gallery' ); ?>
                        <small><?php esc_html_e( 'What should happen when clicking this image?', 'portfolio-filter-gallery' ); ?></small>
                    </label>
                    <select id="pfg-modal-type" class="pfg-select">
                        <option value="image"><?php esc_html_e( 'Display Image', 'portfolio-filter-gallery' ); ?></option>
                        <option value="video"><?php esc_html_e( 'Open Video Link (YouTube, Vimeo, etc.)', 'portfolio-filter-gallery' ); ?></option>
                        <option value="url"><?php esc_html_e( 'Open External Link', 'portfolio-filter-gallery' ); ?></option>
                    </select>
                </div>
                
                <div class="pfg-form-row pfg-link-url-row" style="display: none;">
                    <label class="pfg-form-label"><?php esc_html_e( 'URL', 'portfolio-filter-gallery' ); ?></label>
                    <div class="pfg-video-url-wrap">
                        <input type="text" id="pfg-modal-link" class="pfg-input" placeholder="https://" inputmode="url">
                        <small class="pfg-url-hint" style="display: block;"></small>
                        <small class="pfg-upgrade-hint" style="display: none; color: #f56e28; margin-top: 5px;">
                            <?php esc_html_e( 'Need to fetch YouTube/Vimeo poster?', 'portfolio-filter-gallery' ); ?> 
                            <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" target="_blank" style="color: #f56e28; text-decoration: underline; font-weight: bold;"><?php esc_html_e( 'Upgrade here', 'portfolio-filter-gallery' ); ?></a>
                        </small>
                        <button type="button" id="pfg-revert-thumb" class="pfg-btn pfg-btn-secondary" style="margin-top: 8px; margin-left: 8px; display: none;">
                            <span class="dashicons dashicons-undo" style="margin-right: 5px;"></span>
                            <?php esc_html_e( 'Revert to Original', 'portfolio-filter-gallery' ); ?>
                        </button>
                        <span id="pfg-fetch-thumb-status" style="display: none; margin-left: 10px; font-size: 12px;"></span>
                    </div>
                </div>
                
                <div class="pfg-form-row">
                    <label class="pfg-form-label"><?php esc_html_e( 'Filters/Categories', 'portfolio-filter-gallery' ); ?></label>
                    <div class="pfg-filter-checkboxes" id="pfg-modal-filters">
                        <?php
                        // Use wp_kses with extended allowed tags since wp_kses_post strips <input> elements
                        $allowed_tags = wp_kses_allowed_html( 'post' );
                        $allowed_tags['input'] = array(
                            'type'    => true,
                            'value'   => true,
                            'checked' => true,
                            'class'   => true,
                            'id'      => true,
                            'name'    => true,
                            'data-parent' => true,
                            'data-filter' => true,
                        );
                        // Make sure label, div, and span allow our custom data attributes
                        if ( isset( $allowed_tags['label'] ) ) {
                            $allowed_tags['label']['data-color'] = true;
                            $allowed_tags['label']['data-parent'] = true;
                            $allowed_tags['label']['data-filter'] = true;
                        }
                        if ( isset( $allowed_tags['div'] ) ) {
                            $allowed_tags['div']['data-depth'] = true;
                            $allowed_tags['div']['data-expanded'] = true;
                        }

                        echo wp_kses( pfg_render_filter_checkboxes( $filter_tree ), $allowed_tags );
                        ?>
                    </div>
                </div>
                

            </div>
        </div>
        
        <div class="pfg-modal-footer">
            <button type="button" class="pfg-btn pfg-btn-secondary pfg-modal-cancel">
                <?php esc_html_e( 'Cancel', 'portfolio-filter-gallery' ); ?>
            </button>
            <button type="button" class="pfg-btn pfg-btn-primary pfg-modal-save">
                <?php esc_html_e( 'Save Changes', 'portfolio-filter-gallery' ); ?>
            </button>
        </div>
    </div>
</div>

