<?php
/**
 * Documentation Page Template.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin/views
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap pfg-docs-wrap">
    <h1><?php esc_html_e( 'Portfolio Filter Gallery - Documentation', 'portfolio-filter-gallery' ); ?></h1>
    
    <div class="pfg-docs-container">
        <!-- Sidebar Navigation -->
        <div class="pfg-docs-sidebar">
            <nav class="pfg-docs-nav">
                <a href="#getting-started" class="pfg-doc-link active"><?php esc_html_e( 'Getting Started', 'portfolio-filter-gallery' ); ?></a>
                <a href="#creating-gallery" class="pfg-doc-link"><?php esc_html_e( 'Creating a Gallery', 'portfolio-filter-gallery' ); ?></a>
                <a href="#adding-images" class="pfg-doc-link"><?php esc_html_e( 'Adding Images', 'portfolio-filter-gallery' ); ?></a>
                <a href="#filters" class="pfg-doc-link"><?php esc_html_e( 'Working with Filters', 'portfolio-filter-gallery' ); ?></a>
                <a href="#layout-settings" class="pfg-doc-link"><?php esc_html_e( 'Layout Settings', 'portfolio-filter-gallery' ); ?></a>

                <a href="#shortcode" class="pfg-doc-link"><?php esc_html_e( 'Using Shortcodes', 'portfolio-filter-gallery' ); ?></a>
                <a href="#hover-effects" class="pfg-doc-link"><?php esc_html_e( 'Hover Effects', 'portfolio-filter-gallery' ); ?></a>
                <a href="#faq" class="pfg-doc-link"><?php esc_html_e( 'FAQ', 'portfolio-filter-gallery' ); ?></a>
                <a href="#support" class="pfg-doc-link"><?php esc_html_e( 'Support', 'portfolio-filter-gallery' ); ?></a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="pfg-docs-content">
            
            <!-- Getting Started -->
            <section id="getting-started" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Getting Started', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Welcome to Portfolio Filter Gallery! This plugin allows you to create beautiful, filterable image galleries for your WordPress site.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Quick Start Guide', 'portfolio-filter-gallery' ); ?></h3>
                <ol class="pfg-steps">
                    <li>
                        <strong><?php esc_html_e( 'Create Filters', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Go to Portfolio Gallery → Filters to create categories for your images (e.g., Nature, Architecture, People).', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Create a Gallery', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Go to Portfolio Gallery → Add New Gallery to create your first gallery.', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Add Images', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Click "Add Images" to select images from your Media Library or upload new ones.', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Assign Filters', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Click the edit icon on each image to assign filters/categories to it.', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Configure Settings', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Use the Settings tab to configure layout, columns, hover effects, and more.', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Publish & Embed', 'portfolio-filter-gallery' ); ?></strong>
                        <p><?php esc_html_e( 'Click Publish, then copy the shortcode and paste it into any page or post.', 'portfolio-filter-gallery' ); ?></p>
                    </li>
                </ol>
            </section>
            
            <!-- Creating a Gallery -->
            <section id="creating-gallery" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Creating a Gallery', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Each gallery is a separate post that contains images and settings.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Gallery Title', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Give your gallery a descriptive title. This is for your reference only and is not shown on the frontend by default.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Gallery Source', 'portfolio-filter-gallery' ); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e( 'Media Library', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Manually select images from your WordPress Media Library.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
            </section>
            
            <!-- Adding Images -->
            <section id="adding-images" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Adding Images', 'portfolio-filter-gallery' ); ?></h2>
                
                <h3><?php esc_html_e( 'Methods to Add Images', 'portfolio-filter-gallery' ); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e( 'Add Images Button', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Opens the Media Library to select existing images or upload new ones.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Drag and Drop', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Drag images directly onto the gallery area to upload them.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'Editing Images', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Click the edit (pencil) icon on any image to open the Edit Image modal where you can:', 'portfolio-filter-gallery' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Change the image title', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Add a description', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Set a custom link', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Assign filters/categories', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'Reordering Images', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Drag and drop images in the gallery editor to reorder them. The order is saved automatically when you update the gallery.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Bulk Actions', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Use the "Select All" checkbox to select multiple images, then click "Delete Selected" to remove them from the gallery.', 'portfolio-filter-gallery' ); ?></p>
            </section>
            
            <!-- Filters -->
            <section id="filters" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Working with Filters', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Filters (categories) allow visitors to filter your gallery by clicking on buttons.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Creating Filters', 'portfolio-filter-gallery' ); ?></h3>
                <ol>
                    <li><?php esc_html_e( 'Go to Portfolio Gallery → Filters', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Click "Add Filter" button', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Enter a filter name (e.g., "Nature", "Architecture")', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Optionally set a color for the filter tag', 'portfolio-filter-gallery' ); ?></li>
                </ol>
                
                <h3><?php esc_html_e( 'Hierarchical Filters', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Create parent-child relationships between filters for multi-level filtering. Set a parent filter when creating a new filter to make it a child.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Filter Logic', 'portfolio-filter-gallery' ); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e( 'OR Logic', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Show images matching ANY of the selected filters (default).', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'AND Logic', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Show only images matching ALL selected filters.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'Filter Slug', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Each filter has a slug used for URL deep linking. You can edit the slug in the Filters page by clicking on it.', 'portfolio-filter-gallery' ); ?></p>
            </section>
            
            <!-- Layout Settings -->
            <section id="layout-settings" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Layout Settings', 'portfolio-filter-gallery' ); ?></h2>
                
                <h3><?php esc_html_e( 'Layout Types', 'portfolio-filter-gallery' ); ?></h3>
                <ul>
                    <li><strong><?php esc_html_e( 'Masonry', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Pinterest-style layout with images of varying heights.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Grid', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Uniform grid with equal-sized image thumbnails.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'Column Settings', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Set the number of columns for different screen sizes:', 'portfolio-filter-gallery' ); ?></p>
                <ul>
                    <li><?php esc_html_e( 'Desktop (1200px+): 1-6 columns', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Tablet (768px-1199px): 1-4 columns', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Mobile (<768px): 1-2 columns', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'Image Size', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Choose which WordPress image size to use for thumbnails: Thumbnail, Medium, Large, or Full.', 'portfolio-filter-gallery' ); ?></p>
                
                <h3><?php esc_html_e( 'Gap / Spacing', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'Control the spacing between images in pixels.', 'portfolio-filter-gallery' ); ?></p>
            </section>
            

            
            <!-- Shortcode -->
            <section id="shortcode" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Using Shortcodes', 'portfolio-filter-gallery' ); ?></h2>
                
                <h3><?php esc_html_e( 'Basic Shortcode', 'portfolio-filter-gallery' ); ?></h3>
                <p><?php esc_html_e( 'After publishing your gallery, copy the shortcode from the "Shortcode" meta box:', 'portfolio-filter-gallery' ); ?></p>
                <code class="pfg-code-block">[pfg id="123"]</code>
                
                <h3><?php esc_html_e( 'Where to Use', 'portfolio-filter-gallery' ); ?></h3>
                <ul>
                    <li><?php esc_html_e( 'Pages and Posts: Paste directly into the content editor.', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Widgets: Use a Text or Custom HTML widget.', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Page Builders: Use a shortcode block/module.', 'portfolio-filter-gallery' ); ?></li>
                    <li><?php esc_html_e( 'Theme Templates: Use do_shortcode() function.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
                
                <h3><?php esc_html_e( 'PHP Usage', 'portfolio-filter-gallery' ); ?></h3>
                <code class="pfg-code-block">&lt;?php echo do_shortcode( '[pfg id="123"]' ); ?&gt;</code>
            </section>
            
            <!-- Hover Effects -->
            <section id="hover-effects" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Hover Effects', 'portfolio-filter-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Choose from various hover effects to enhance your gallery:', 'portfolio-filter-gallery' ); ?></p>
                <ul>
                    <li><strong><?php esc_html_e( 'Zoom', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Image scales up on hover.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Slide', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Overlay slides in from direction.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Fade', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Smooth fade-in effect.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Blur', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Image blurs on hover.', 'portfolio-filter-gallery' ); ?></li>
                    <li><strong><?php esc_html_e( 'Grayscale', 'portfolio-filter-gallery' ); ?></strong> - <?php esc_html_e( 'Color on hover, grayscale otherwise.', 'portfolio-filter-gallery' ); ?></li>
                </ul>
            </section>
            
            <!-- FAQ -->
            <section id="faq" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Frequently Asked Questions', 'portfolio-filter-gallery' ); ?></h2>
                
                <div class="pfg-faq-item">
                    <h4><?php esc_html_e( 'How many images can I add to a gallery?', 'portfolio-filter-gallery' ); ?></h4>
                    <p><?php esc_html_e( 'There is no hard limit. The plugin supports galleries with hundreds of images. Large galleries are saved in chunks to avoid server limits.', 'portfolio-filter-gallery' ); ?></p>
                </div>
                
                <div class="pfg-faq-item">
                    <h4><?php esc_html_e( 'Can I use the same image in multiple galleries?', 'portfolio-filter-gallery' ); ?></h4>
                    <p><?php esc_html_e( 'Yes! Images are referenced from the Media Library, so one image can appear in multiple galleries with different settings or categories.', 'portfolio-filter-gallery' ); ?></p>
                </div>
                
                <div class="pfg-faq-item">
                    <h4><?php esc_html_e( 'How do I change the filter button style?', 'portfolio-filter-gallery' ); ?></h4>
                    <p><?php esc_html_e( 'Go to gallery Settings → Filter Appearance section to choose button styles, colors, and alignment.', 'portfolio-filter-gallery' ); ?></p>
                </div>
                
                <div class="pfg-faq-item">
                    <h4><?php esc_html_e( 'Why are my images not showing?', 'portfolio-filter-gallery' ); ?></h4>
                    <p><?php esc_html_e( 'Check that: 1) Your gallery is published, 2) You\'ve added at least one image, 3) The shortcode ID matches your gallery ID.', 'portfolio-filter-gallery' ); ?></p>
                </div>
                
                <div class="pfg-faq-item">
                    <h4><?php esc_html_e( 'Can I use custom CSS?', 'portfolio-filter-gallery' ); ?></h4>
                    <p><?php esc_html_e( 'Yes! Use the WordPress Customizer (Appearance → Customize → Additional CSS) to add custom CSS for your galleries. Target a specific gallery with #pfg-gallery-{ID} selector.', 'portfolio-filter-gallery' ); ?></p>
                </div>

            </section>
            
            <!-- Support -->
            <section id="support" class="pfg-doc-section">
                <h2><?php esc_html_e( 'Support', 'portfolio-filter-gallery' ); ?></h2>
                
                <div class="pfg-support-links">
                    <a href="https://wordpress.org/support/plugin/portfolio-filter-gallery/" target="_blank" class="pfg-support-card">
                        <span class="dashicons dashicons-sos"></span>
                        <strong><?php esc_html_e( 'Support Forum', 'portfolio-filter-gallery' ); ?></strong>
                        <span><?php esc_html_e( 'Get help from our team', 'portfolio-filter-gallery' ); ?></span>
                    </a>
                </div>
            </section>
            
        </div>
    </div>
</div>
