<?php
/**
 * Gallery Settings Meta Box Template.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if (!defined('ABSPATH')) {
    exit;
}



$gallery_id = $post->ID;
$gallery = new PFG_Gallery($gallery_id);
$settings = $gallery->get_settings();

// Get templates - use PFG_Templates if available, otherwise use default
if (class_exists('PFG_Templates')) {
    $templates = PFG_Templates::get_all();
} else {
    // Default templates fallback
    $templates = array(
        'default' => array('name' => __('Default', 'portfolio-filter-gallery')),
        'minimal' => array('name' => __('Minimal', 'portfolio-filter-gallery')),
        'modern' => array('name' => __('Modern', 'portfolio-filter-gallery')),
    );
}
?>

<div class="pfg-meta-box pfg-settings-meta-box">

    <div class="pfg-tabs-wrapper">
        <!-- Tabs Navigation -->
        <div class="pfg-tabs">
            <button type="button" class="pfg-tab active" data-tab="pfg-tab-layout">
                <span class="dashicons dashicons-layout"></span>
                <?php esc_html_e('Layout', 'portfolio-filter-gallery'); ?>
            </button>
            <button type="button" class="pfg-tab" data-tab="pfg-tab-filters">
                <span class="dashicons dashicons-filter"></span>
                <?php esc_html_e('Filters', 'portfolio-filter-gallery'); ?>
            </button>
            <button type="button" class="pfg-tab" data-tab="pfg-tab-style">
                <span class="dashicons dashicons-art"></span>
                <?php esc_html_e('Styling', 'portfolio-filter-gallery'); ?>
            </button>
            <button type="button" class="pfg-tab" data-tab="pfg-tab-lightbox">
                <span class="dashicons dashicons-format-image"></span>
                <?php esc_html_e('Lightbox', 'portfolio-filter-gallery'); ?>
            </button>
            <button type="button" class="pfg-tab" data-tab="pfg-tab-advanced">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php esc_html_e('Advanced', 'portfolio-filter-gallery'); ?>
            </button>

        </div>

        <!-- Layout Tab -->
        <div id="pfg-tab-layout" class="pfg-tab-content active">

            <!-- Quick Start Section -->
            <h4 class="pfg-form-section-title pfg-section-icon">
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <?php esc_html_e('Quick Start', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Template Selection -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Template', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Start with a pre-designed style', 'portfolio-filter-gallery'); ?></small>
                </label>
                <div class="pfg-template-grid">
                    <?php foreach ($templates as $id => $template):
                        // Determine icon and type based on layout type
                        $layout_type = isset($template['settings']['layout_type']) ? $template['settings']['layout_type'] : 'grid';

                        // Map layout types to icons and labels
                        $layout_icons = array(
                            'grid' => 'dashicons-grid-view',
                            'masonry' => 'dashicons-images-alt',
                        );
                        $layout_labels = array(
                            'grid' => __('Grid', 'portfolio-filter-gallery'),
                            'masonry' => __('Masonry', 'portfolio-filter-gallery'),
                        );

                        $icon = isset($layout_icons[$layout_type]) ? $layout_icons[$layout_type] : 'dashicons-grid-view';
                        $layout_label = isset($layout_labels[$layout_type]) ? $layout_labels[$layout_type] : __('Grid', 'portfolio-filter-gallery');


                        ?>
                        <div class="pfg-template-card <?php echo esc_attr(($settings['template'] ?? '') === $id ? 'selected' : ''); ?>"
                            data-template="<?php echo esc_attr($id); ?>" data-layout="<?php echo esc_attr($layout_type); ?>"
                            title="<?php echo esc_attr($template['description'] ?? ''); ?>">
                            <div class="pfg-template-preview">
                                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                            </div>
                            <span class="pfg-template-name"><?php echo esc_html($template['name']); ?></span>
                            <span
                                class="pfg-template-type pfg-type-<?php echo esc_attr($layout_type); ?>"><?php echo esc_html($layout_label); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="pfg_settings[template]" id="pfg-template"
                    value="<?php echo esc_attr($settings['template'] ?? ''); ?>">
            </div>

            <!-- Fine-Tune Layout Section -->
            <hr class="pfg-form-separator">
            <h4 class="pfg-form-section-title pfg-section-icon">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('Fine-Tune Layout', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Layout Type -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Layout Type', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('How images are arranged in the gallery', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[layout]" id="pfg-layout" class="pfg-select">
                    <option value="grid" <?php selected($settings['layout_type'] ?? 'masonry', 'grid'); ?>>
                        <?php esc_html_e('Grid - Equal sized cells', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="masonry" <?php selected($settings['layout_type'] ?? 'masonry', 'masonry'); ?>>
                        <?php esc_html_e('Masonry - Variable height columns', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Columns (for Grid/Masonry) -->
            <div class="pfg-form-row pfg-layout-option" data-layouts="grid,masonry">
                <label class="pfg-form-label">
                    <?php esc_html_e('Columns', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Number of columns per device type', 'portfolio-filter-gallery'); ?></small>
                </label>
                <div class="pfg-responsive-columns">
                    <!-- Device Type Toggle -->
                    <div class="pfg-device-toggle">
                        <button type="button" class="pfg-device-btn active" data-device="desktop"
                            title="<?php esc_attr_e('Desktop', 'portfolio-filter-gallery'); ?>">
                            <span class="dashicons dashicons-desktop"></span>
                        </button>
                    </div>

                    <!-- Desktop Columns -->
                    <div class="pfg-device-panel pfg-device-desktop active">
                        <div class="pfg-range">
                            <input type="range" name="pfg_settings[columns]" min="1" max="10"
                                value="<?php echo esc_attr($settings['columns_lg'] ?? 3); ?>" data-suffix="">
                            <span class="pfg-range-value"><?php echo esc_html($settings['columns_lg'] ?? 3); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Aspect Ratio (Grid only) -->
            <div class="pfg-form-row pfg-layout-option" data-layouts="grid">
                <label class="pfg-form-label">
                    <?php esc_html_e('Grid Aspect Ratio', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Aspect ratio for grid image cells', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[grid_aspect_ratio]" class="pfg-select">
                    <option value="4:3" <?php selected($settings['grid_aspect_ratio'] ?? '4:3', '4:3'); ?>>
                        <?php esc_html_e('4:3 (Default)', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>




            <!-- Gap -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Gap (px)', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Spacing between gallery items', 'portfolio-filter-gallery'); ?></small>
                </label>
                <div class="pfg-range">
                    <input type="range" name="pfg_settings[gap]" min="0" max="50"
                        value="<?php echo esc_attr($settings['gap'] ?? 20); ?>" data-suffix="px">
                    <span class="pfg-range-value"><?php echo esc_html($settings['gap'] ?? 20); ?>px</span>
                </div>
            </div>

            <!-- Border Radius -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Border Radius (px)', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Rounded corners for images', 'portfolio-filter-gallery'); ?></small>
                </label>
                <div class="pfg-range">
                    <input type="range" name="pfg_settings[border_radius]" min="0" max="30"
                        value="<?php echo esc_attr($settings['border_radius'] ?? 8); ?>" data-suffix="px">
                    <span class="pfg-range-value"><?php echo esc_html($settings['border_radius'] ?? 8); ?>px</span>
                </div>
            </div>

            <!-- Image Size -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Image Size', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('WordPress image size for thumbnails', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[image_size]" class="pfg-select">
                    <?php
                    $sizes = get_intermediate_image_sizes();
                    $sizes[] = 'full';
                    foreach ($sizes as $size): ?>
                        <option value="<?php echo esc_attr($size); ?>" <?php selected($settings['image_size'] ?? 'large', $size); ?>>
                            <?php echo esc_html(ucwords(str_replace('_', ' ', $size))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Separator -->
            <hr class="pfg-form-separator">
            <h4 class="pfg-form-section-title"><?php esc_html_e('Item Display', 'portfolio-filter-gallery'); ?></h4>

            <!-- Hover Effect -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Hover Effect', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Animation when hovering over images', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[hover_effect]" class="pfg-select">
                    <option value="none" <?php selected($settings['hover_effect'] ?? 'zoom', 'none'); ?>>
                        <?php esc_html_e('None', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="zoom" <?php selected($settings['hover_effect'] ?? 'zoom', 'zoom'); ?>>
                        <?php esc_html_e('Zoom', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="fade" <?php selected($settings['hover_effect'] ?? 'zoom', 'fade'); ?>>
                        <?php esc_html_e('Fade', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="slide-up" <?php selected($settings['hover_effect'] ?? 'zoom', 'slide-up'); ?>>
                        <?php esc_html_e('Slide Up', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Title Position -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Title Position', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Choose where to display the title', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[title_position]" class="pfg-select">
                    <option value="overlay" <?php selected($settings['title_position'] ?? 'overlay', 'overlay'); ?>>
                        <?php esc_html_e('Overlay on hover', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="below" <?php selected($settings['title_position'] ?? 'overlay', 'below'); ?>>
                        <?php esc_html_e('Below image (card style)', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Show Title Overlay -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Title', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display image title on gallery items', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_title_overlay]" value="1" <?php checked($settings['show_title'] ?? true); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Show Description -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Description', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display image description below the title', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_description]" value="1" <?php checked($settings['show_description'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Show Categories -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Categories', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display filter/category names on cards', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_categories]" value="1" <?php checked($settings['show_categories'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

        </div>

        <!-- Filters Tab -->
        <div id="pfg-tab-filters" class="pfg-tab-content">

            <!-- Filter Display Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 0;">
                <span class="dashicons dashicons-filter" style="margin-right: 5px;"></span>
                <?php esc_html_e('Filter Display', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Show Filters -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Filter Buttons', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display filter buttons above gallery', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_filters]" value="1" <?php checked($settings['filters_enabled'] ?? true); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Filter Settings Group (depends on show_filters) -->
            <div class="pfg-conditional pfg-settings-group" data-depends="pfg_settings[show_filters]">

                <!-- Filter Appearance Section -->
                <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                    <span class="dashicons dashicons-admin-appearance" style="margin-right: 5px;"></span>
                    <?php esc_html_e('Filter Appearance', 'portfolio-filter-gallery'); ?>
                </h4>

                <!-- Filter Position -->
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Filter Position', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Where to display filter buttons', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <select name="pfg_settings[filter_position]" class="pfg-select">
                        <option value="left" <?php selected($settings['filters_position'] ?? 'center', 'left'); ?>>
                            <?php esc_html_e('Top Left', 'portfolio-filter-gallery'); ?>
                        </option>
                        <option value="center" <?php selected($settings['filters_position'] ?? 'center', 'center'); ?>>
                            <?php esc_html_e('Top Center', 'portfolio-filter-gallery'); ?></option>
                        <option value="right" <?php selected($settings['filters_position'] ?? 'center', 'right'); ?>>
                            <?php esc_html_e('Top Right', 'portfolio-filter-gallery'); ?>
                        </option>
                    </select>
                </div>

                <!-- Filter Style -->
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Filter Style', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Visual style for filter buttons', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <select name="pfg_settings[filters_style]" class="pfg-select">
                        <option value="buttons" <?php selected($settings['filters_style'] ?? 'buttons', 'buttons'); ?>>
                            <?php esc_html_e('Buttons (Filled)', 'portfolio-filter-gallery'); ?></option>
                        <option value="minimal" <?php selected($settings['filters_style'] ?? 'buttons', 'minimal'); ?>>
                            <?php esc_html_e('Minimal (Text Only)', 'portfolio-filter-gallery'); ?></option>
                        <option value="dropdown" <?php selected($settings['filters_style'] ?? 'buttons', 'dropdown'); ?>><?php esc_html_e('Dropdown', 'portfolio-filter-gallery'); ?></option>
                        <option value="flat" <?php selected($settings['filters_style'] ?? 'buttons', 'flat'); ?>>
                            <?php esc_html_e('Flat', 'portfolio-filter-gallery'); ?>
                        </option>
                    </select>
                </div>

                <!-- "All" Button Section -->
                <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                    <span class="dashicons dashicons-screenoptions" style="margin-right: 5px;"></span>
                    <?php esc_html_e('"All" Button', 'portfolio-filter-gallery'); ?>
                </h4>

                <!-- Show All Button -->
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Show "All" Button', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Button to show all items without filtering', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <label class="pfg-toggle">
                        <input type="checkbox" name="pfg_settings[show_all_button]" value="1" <?php checked($settings['show_all_button'] ?? true); ?>>
                        <span class="pfg-toggle-slider"></span>
                    </label>
                </div>

                <!-- All Button Text -->
                <div class="pfg-form-row pfg-conditional" data-depends="pfg_settings[show_all_button]">
                    <label class="pfg-form-label">
                        <?php esc_html_e('"All" Button Text', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Custom text for the All button', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <input type="text" name="pfg_settings[all_button_text]" class="pfg-input"
                        value="<?php echo esc_attr($settings['all_button_text'] ?? 'All'); ?>">
                </div>

                <!-- Filter Enhancements Section -->
                <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                    <span class="dashicons dashicons-star-filled" style="margin-right: 5px;"></span>
                    <?php esc_html_e('Filter Enhancements', 'portfolio-filter-gallery'); ?>
                </h4>

                <!-- Show Filter Color Dots -->
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Show Filter Color Dots', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Display colored dots on filter buttons based on Color Tag', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <label class="pfg-toggle">
                        <input type="checkbox" name="pfg_settings[show_filter_colors]" value="1" <?php checked($settings['show_filter_colors'] ?? true); ?>>
                        <span class="pfg-toggle-slider"></span>
                    </label>
                </div>

                <!-- Show Filter Item Count -->
                <div class="pfg-form-row">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Show Filter Item Count', 'portfolio-filter-gallery'); ?>
                        <small><?php esc_html_e('Display number of items next to each filter, e.g., Portraits (5)', 'portfolio-filter-gallery'); ?></small>
                    </label>
                    <label class="pfg-toggle">
                        <input type="checkbox" name="pfg_settings[show_filter_count]" value="1" <?php checked($settings['show_filter_count'] ?? false); ?>>
                        <span class="pfg-toggle-slider"></span>
                    </label>
                </div>

                <!-- Count Display Style (conditional on show_filter_count) -->
                <div class="pfg-form-row pfg-conditional" data-depends="pfg_settings[show_filter_count]">
                    <label class="pfg-form-label">
                        <?php esc_html_e('Count Display Style', 'portfolio-filter-gallery'); ?>
                    </label>
                    <select name="pfg_settings[filter_count_style]" class="pfg-select">
                        <option value="always" <?php selected($settings['filter_count_style'] ?? 'always', 'always'); ?>><?php esc_html_e('Always Visible', 'portfolio-filter-gallery'); ?></option>
                        <option value="hover" <?php selected($settings['filter_count_style'] ?? 'always', 'hover'); ?>>
                            <?php esc_html_e('On Hover Only', 'portfolio-filter-gallery'); ?></option>
                    </select>
                </div>


            </div><!-- End Filter Settings Group (pfg-conditional) -->

            <!-- Search Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                <span class="dashicons dashicons-search" style="margin-right: 5px;"></span>
                <?php esc_html_e('Search', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Show Search -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Search Box', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Search bar to filter items by title', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_search]" value="1" <?php checked($settings['search_enabled'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Search Placeholder Text (conditional) -->
            <div class="pfg-form-row pfg-conditional" data-depends="pfg_settings[show_search]">
                <label class="pfg-form-label">
                    <?php esc_html_e('Search Placeholder', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Text shown in the search box before user types', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[search_placeholder]" class="pfg-input"
                    value="<?php echo esc_attr($settings['search_placeholder'] ?? 'Search...'); ?>"
                    placeholder="<?php esc_attr_e('Search...', 'portfolio-filter-gallery'); ?>">
            </div>

        </div>



        <!-- Styling Tab -->
        <div id="pfg-tab-style" class="pfg-tab-content">

            <!-- Caption Colors Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 0;">
                <span class="dashicons dashicons-admin-customizer" style="margin-right: 5px;"></span>
                <?php esc_html_e('Caption Colors', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Caption Background Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Caption Background', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Background color for title below image', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[caption_bg_color]" class="pfg-color-input"
                    value="<?php echo esc_attr($settings['caption_bg_color'] ?? '#ffffff'); ?>">
            </div>

            <!-- Caption Text Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Caption Text Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Text color for title below image', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[caption_text_color]" class="pfg-color-input"
                    value="<?php echo esc_attr($settings['caption_text_color'] ?? '#1e293b'); ?>">
            </div>

            <!-- Overlay Colors Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                <span class="dashicons dashicons-visibility" style="margin-right: 5px;"></span>
                <?php esc_html_e('Overlay Colors', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Overlay Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Overlay Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Color of overlay on hover effect', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[overlay_color]" class="pfg-color-input"
                    value="<?php echo esc_attr($settings['overlay_color'] ?? '#000000'); ?>">
            </div>

            <!-- Overlay Opacity -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Overlay Opacity', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Transparency level of the overlay', 'portfolio-filter-gallery'); ?></small>
                </label>
                <div class="pfg-range">
                    <input type="range" name="pfg_settings[overlay_opacity]" min="0" max="100"
                        value="<?php echo esc_attr($settings['overlay_opacity'] ?? 70); ?>" data-suffix="%">
                    <span class="pfg-range-value"><?php echo esc_html($settings['overlay_opacity'] ?? 70); ?>%</span>
                </div>
            </div>

            <!-- Filter Colors Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                <span class="dashicons dashicons-filter" style="margin-right: 5px;"></span>
                <?php esc_html_e('Filter Button Colors', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Primary Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Primary Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Inactive filter buttons, category badges, and other accent elements', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[primary_color]" class="pfg-color-input"
                    value="<?php echo esc_attr($settings['primary_color'] ?? '#94a3b8'); ?>">
            </div>

            <!-- Filter Active Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Filter Active Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Currently selected filter button color', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[filter_active_color]" class="pfg-color-input"
                    value="<?php echo esc_attr($settings['filter_active_color'] ?? '#3858e9'); ?>">
            </div>

            <!-- Filter Text Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Filter Text Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Inactive filter button text (auto = contrast-based)', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[filter_text_color]" class="pfg-color-input pfg-color-auto"
                    value="<?php echo esc_attr($settings['filter_text_color'] ?? 'auto'); ?>" placeholder="auto">
            </div>

            <!-- Filter Active Text Color -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Filter Active Text Color', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Active filter button text (auto = contrast-based)', 'portfolio-filter-gallery'); ?></small>
                </label>
                <input type="text" name="pfg_settings[filter_active_text_color]" class="pfg-color-input pfg-color-auto"
                    value="<?php echo esc_attr($settings['filter_active_text_color'] ?? 'auto'); ?>" placeholder="auto">
            </div>

        </div>

        <!-- Lightbox Tab -->
        <div id="pfg-tab-lightbox" class="pfg-tab-content">
            <!-- Lightbox Settings -->
            <h4 class="pfg-form-section-title" style="margin-top: 0;">
                <span class="dashicons dashicons-format-image" style="margin-right: 5px;"></span>
                <?php esc_html_e('Lightbox Settings', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Enable Lightbox -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Enable Lightbox', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Open images in a popup overlay when clicked', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="hidden" name="pfg_settings[lightbox]" value="none">
                    <input type="checkbox" name="pfg_settings[lightbox]" value="1" <?php checked(!isset($settings['lightbox']) || in_array($settings['lightbox'], array('1', 'built-in', 'ld-lightbox'), true)); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Lightbox Content Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                <span class="dashicons dashicons-editor-justify" style="margin-right: 5px;"></span>
                <?php esc_html_e('Lightbox Content', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Dual Action Icons -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Dual Action Icons', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Show both Lightbox and Link icons on hover if an image has a custom link', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_dual_icons]" value="1" <?php checked($settings['show_dual_icons'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Show Title in Lightbox -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Title in Lightbox', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display image title in the lightbox popup', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_lightbox_title]" value="1" <?php checked($settings['show_lightbox_title'] ?? true); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Show Description in Lightbox -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Description in Lightbox', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Display image description/caption in the lightbox popup', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_lightbox_description]" value="1" <?php checked($settings['show_lightbox_description'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>
        </div>

        <!-- Advanced Tab -->
        <div id="pfg-tab-advanced" class="pfg-tab-content">
            <!-- Display & Behavior Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 0;">
                <span class="dashicons dashicons-visibility" style="margin-right: 5px;"></span>
                <?php esc_html_e('Display & Behavior', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Sort Order -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Sort Order', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Default display order for gallery images', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[sort_order]" class="pfg-select">
                    <option value="custom" <?php selected($settings['sort_order'] ?? 'custom', 'custom'); ?>>
                        <?php esc_html_e('Custom (Manual Order)', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="date_newest" <?php selected($settings['sort_order'] ?? 'custom', 'date_newest'); ?>>
                        <?php esc_html_e('Newest First', 'portfolio-filter-gallery'); ?></option>
                    <option value="date_oldest" <?php selected($settings['sort_order'] ?? 'custom', 'date_oldest'); ?>>
                        <?php esc_html_e('Oldest First', 'portfolio-filter-gallery'); ?></option>
                    <option value="title_asc" <?php selected($settings['sort_order'] ?? 'custom', 'title_asc'); ?>>
                        <?php esc_html_e('Title A → Z', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="title_desc" <?php selected($settings['sort_order'] ?? 'custom', 'title_desc'); ?>>
                        <?php esc_html_e('Title Z → A', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="random" <?php selected($settings['sort_order'] ?? 'custom', 'random'); ?>>
                        <?php esc_html_e('Random / Shuffle', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Hide Type Icons -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Hide Type Icons', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Hide video/link indicator icons on gallery items', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[hide_type_icons]" value="1" <?php checked($settings['hide_type_icons'] ?? false); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Default Filter -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Default Filter', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Pre-select a filter on page load', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[default_filter]" class="pfg-select">
                    <option value=""><?php esc_html_e('-- Show All --', 'portfolio-filter-gallery'); ?></option>
                    <?php
                    // Get filters that are actually used by images in this gallery
                    $images = $gallery->get_images();
                    $used_filter_ids = array();
                    foreach ($images as $image) {
                        if (!empty($image['filters'])) {
                            $used_filter_ids = array_merge($used_filter_ids, (array) $image['filters']);
                        }
                    }
                    $used_filter_ids = array_unique($used_filter_ids);

                    // Get filter details for the used filters
                    $all_filters = get_option('pfg_filters', array());
                    $used_filters = array();
                    foreach ($all_filters as $filter) {
                        // Check if this filter is used by any image (by ID or slug)
                        if (in_array($filter['id'], $used_filter_ids, true) || in_array($filter['slug'], $used_filter_ids, true)) {
                            $used_filters[] = $filter;
                        }
                    }

                    foreach ($used_filters as $filter):
                        $is_child = !empty($filter['parent']);
                        $prefix = $is_child ? '— ' : '';
                        ?>
                        <option value="<?php echo esc_attr($filter['slug']); ?>" <?php selected($settings['default_filter'] ?? '', $filter['slug']); ?>>
                            <?php echo esc_html($prefix . $filter['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>



            <!-- Gallery Direction -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Gallery Direction', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Text and layout direction', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[direction]" class="pfg-select">
                    <option value="ltr" <?php selected($settings['direction'] ?? 'ltr', 'ltr'); ?>>
                        <?php esc_html_e('Left to Right (LTR)', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="rtl" <?php selected($settings['direction'] ?? 'ltr', 'rtl'); ?>>
                        <?php esc_html_e('Right to Left (RTL)', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Link URL Target -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Open Link URL in', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('How external links open (when image has a custom URL)', 'portfolio-filter-gallery'); ?></small>
                </label>
                <select name="pfg_settings[url_target]" class="pfg-select">
                    <option value="_self" <?php selected($settings['url_target'] ?? '_self', '_self'); ?>>
                        <?php esc_html_e('Same Window', 'portfolio-filter-gallery'); ?>
                    </option>
                    <option value="_blank" <?php selected($settings['url_target'] ?? '_self', '_blank'); ?>>
                        <?php esc_html_e('New Tab/Window', 'portfolio-filter-gallery'); ?>
                    </option>
                </select>
            </div>

            <!-- Performance Section -->
            <h4 class="pfg-form-section-title" style="margin-top: 20px;">
                <span class="dashicons dashicons-performance" style="margin-right: 5px;"></span>
                <?php esc_html_e('Performance', 'portfolio-filter-gallery'); ?>
            </h4>

            <!-- Lazy Loading -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Lazy Loading', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Load images as they come into view', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[lazy_loading]" value="1" <?php checked($settings['lazy_loading'] ?? true); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>

            <!-- Gallery Preloader -->
            <div class="pfg-form-row">
                <label class="pfg-form-label">
                    <?php esc_html_e('Show Loading Spinner', 'portfolio-filter-gallery'); ?>
                    <small><?php esc_html_e('Show spinner while gallery images load', 'portfolio-filter-gallery'); ?></small>
                </label>
                <label class="pfg-toggle">
                    <input type="checkbox" name="pfg_settings[show_preloader]" value="1" <?php checked($settings['show_preloader'] ?? true); ?>>
                    <span class="pfg-toggle-slider"></span>
                </label>
            </div>



        </div>

        <!-- Pro Banner (Desktop Grid) -->
        <div class="pfg-pro-exact-banner">

            <!-- Header Section -->
            <div class="pfg-pro-header">
                <div class="pfg-pro-header-content">
                    <div class="pfg-pro-badge">PRO</div>
                    <h2 class="pfg-pro-title">
                        <?php esc_html_e('Take your layouts to the next level', 'portfolio-filter-gallery'); ?>
                    </h2>
                    <p class="pfg-pro-desc">
                        <?php esc_html_e('Unlock bespoke editorial tools designed for high-end digital publishing.', 'portfolio-filter-gallery'); ?>
                    </p>
                </div>
                <a href="https://awplife.com/wordpress-plugins/portfolio-filter-gallery-wordpress-plugin/" target="_blank" class="pfg-btn pfg-btn-primary">
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php esc_html_e('Upgrade to Pro', 'portfolio-filter-gallery'); ?>
                </a>
            </div>

            <!-- Features Stack (Desktop Grid Layout) -->
            <div class="pfg-pro-features-grid">

                <!-- Feature 1: Grids -->
                <div class="pfg-pro-feature">
                    <div class="pfg-pro-feature-img-box pfg-feature-grids">
                        <div class="pfg-pro-feature-img-left">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAS_MUHbPy4VbiOOXnlmXUy1SrqiePKTzy-zW4P3QHOwkx-SzqGw6t9xRVr8k4UM77OFlSCaJZURrvMDMBXbgXwHWSXXjOV8jUyuEBne6wH7OlRN1AjWgWkjasZtfYdtOb1Lh9VU1UDrQ8wxonxbOXSj0v7mlxiCrUnZf0bOVXLFzLF79RNUBMpoZuQOJSMRs12qL1lPghZUqBGppG8n_4OsDPP4tdlAiFjwH06QcVdd1Ik2cS3_i5RbCleek8zi_oYdXVb_DaCesxm" />
                        </div>
                        <div class="pfg-pro-feature-img-right">
                            <div class="pfg-pro-feature-img-right-top">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBU1K0w7mvOAOItvENUrK2OtvYFJWPctxUtevnPivG4ce-COmDloniB_T9wXh-X5Roino7fqIMz09b3G4_b4pXArzdKNs_Mi0z4rVCG9W9HzaDe-_J1YdWO90eHqcL3mCkf_HogW9DATI37QynTDqC52u8V6yJJVjrr19MGBBT-PIJSRFr1TINuSCcEruNA2lK8UNzBH7jA7H2Uw_luxnsIpNfSjNmAZFkDvC3EHbZSTVdALOORLl1Eb8uy1ygvrRURuhgcgqyOTe4D" />
                            </div>
                            <div class="pfg-pro-feature-img-right-bottom">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBWo7K-p80QD1mZBchN5H2kje6s3Buhpd5RhQ1Za1jBxRUbb2BhT_-QP9h3RwY0pCzdNBvKfFyYVxjmDgtWxFp7j2pveeG8B9HX2c5PwJwvaAavUYatJeAz49l5MTPN-_aPWuyIsKW90KbCZsiFAa94Nn9cibuWMmEOJny_cfRquXkLmPJ3-j6IWuV1k2MvseDjJYionbdqUE5IEGoiN7PU_U4vlYmkEU1Ouc_iRuncxd7Wv0ZkU0rdNCyK6Drvp9g6CEQEZ2a77Izi" />
                            </div>
                        </div>
                    </div>
                    <h3><?php esc_html_e('Justified & Packed Grids', 'portfolio-filter-gallery'); ?></h3>
                    <p><?php esc_html_e('Dynamically balanced masonry blocks for visual storytelling.', 'portfolio-filter-gallery'); ?></p>
                </div>

                <!-- Feature 2: Pagination -->
                <div class="pfg-pro-feature">
                    <div class="pfg-pro-feature-img-box pfg-feature-pagination">
                        <div class="pfg-pro-progress-bar">
                            <div class="pfg-pro-progress-fill"></div>
                        </div>
                        <div class="pfg-pro-pagination-content">
                            <button type="button" class="pfg-pro-load-more">
                                <span class="dashicons dashicons-update"></span> Load More
                            </button>
                            <span class="pfg-pro-page-text">Page 1 of 5</span>
                        </div>
                    </div>
                    <h3><?php esc_html_e('Advanced Pagination Options', 'portfolio-filter-gallery'); ?></h3>
                    <p><?php esc_html_e('Infinity scroll, load more buttons, and classic numeric controls.', 'portfolio-filter-gallery'); ?></p>
                </div>

                <!-- Feature 3: Filters -->
                <div class="pfg-pro-feature">
                    <div class="pfg-pro-feature-img-box pfg-feature-filters">
                        <div class="pfg-pro-filter-pill">Travel</div>
                        <span class="dashicons dashicons-plus"></span>
                        <div class="pfg-pro-filter-pill">Adventure</div>
                    </div>
                    <h3><?php esc_html_e('Multi-Filter Logic (AND / OR)', 'portfolio-filter-gallery'); ?></h3>
                    <p><?php esc_html_e('Complex query building for sophisticated content discovery.', 'portfolio-filter-gallery'); ?></p>
                </div>

                <!-- Feature 4: More Pro Features -->
                <div class="pfg-pro-feature">
                    <div class="pfg-pro-feature-img-box pfg-feature-more">
                        <div class="pfg-pro-more-item">
                            <span class="dashicons dashicons-cart"></span>
                            <span><?php esc_html_e('WooCommerce Integ.', 'portfolio-filter-gallery'); ?></span>
                        </div>
                        <div class="pfg-pro-more-item">
                            <span class="dashicons dashicons-video-alt3"></span>
                            <span><?php esc_html_e('Video Galleries', 'portfolio-filter-gallery'); ?></span>
                        </div>
                        <div class="pfg-pro-more-item">
                            <span class="dashicons dashicons-youtube"></span>
                            <span><?php esc_html_e('Auto-fetch Thumbnails', 'portfolio-filter-gallery'); ?></span>
                        </div>
                        <div class="pfg-pro-more-item">
                            <span class="dashicons dashicons-search"></span>
                            <span><?php esc_html_e('Advanced Lightbox', 'portfolio-filter-gallery'); ?></span>
                        </div>
                    </div>
                    <h3><?php esc_html_e('And Much More...', 'portfolio-filter-gallery'); ?></h3>
                    <p><?php esc_html_e('Everything you need to build stunning media portfolios.', 'portfolio-filter-gallery'); ?></p>
                </div>

            </div>

        </div>

    </div>

</div>