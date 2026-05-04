<?php
/**
 * AJAX handler for admin operations.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles all admin AJAX requests with proper security.
 */
class PFG_Ajax_Handler
{

    /**
     * Register AJAX actions.
     */
    public function register_actions()
    {
        // Filter actions
        add_action('wp_ajax_pfg_add_filter', array($this, 'add_filter'));
        add_action('wp_ajax_pfg_delete_filter', array($this, 'delete_filter'));
        add_action('wp_ajax_pfg_update_filter', array($this, 'update_filter'));
        add_action('wp_ajax_pfg_reorder_filters', array($this, 'reorder_filters'));
        add_action('wp_ajax_pfg_update_filter_parent', array($this, 'update_filter_parent'));
        add_action('wp_ajax_pfg_update_filter_color', array($this, 'update_filter_color'));
        add_action('wp_ajax_pfg_update_filter_slug', array($this, 'update_filter_slug'));
        add_action('wp_ajax_pfg_delete_all_filters', array($this, 'delete_all_filters'));

        // Image actions
        add_action('wp_ajax_pfg_upload_images', array($this, 'upload_images'));
        add_action('wp_ajax_pfg_remove_image', array($this, 'remove_image'));
        add_action('wp_ajax_pfg_reorder_images', array($this, 'reorder_images'));
        add_action('wp_ajax_pfg_update_image', array($this, 'update_image'));

        // Gallery actions
        add_action('wp_ajax_pfg_save_gallery', array($this, 'save_gallery'));
        add_action('wp_ajax_pfg_duplicate_gallery', array($this, 'duplicate_gallery'));

        // Migration actions
        add_action('wp_ajax_pfg_run_migration', array($this, 'run_migration'));
        add_action('wp_ajax_pfg_restore_backup', array($this, 'restore_backup'));
        add_action('wp_ajax_pfg_get_migration_status', array($this, 'get_migration_status'));
        add_action('wp_ajax_pfg_force_remigrate', array($this, 'force_remigrate'));

        // Get attachment URL (for revert functionality)
        add_action('wp_ajax_pfg_get_attachment_url', array($this, 'get_attachment_url'));

        // Drag and drop file upload
        add_action('wp_ajax_pfg_upload_dropped_files', array($this, 'upload_dropped_files'));

        // Chunked image saving for large galleries
        add_action('wp_ajax_pfg_save_images_chunk', array($this, 'save_images_chunk'));

        // Admin pagination for large galleries
        add_action('wp_ajax_pfg_get_admin_images_page', array($this, 'get_admin_images_page'));

        // Get thumbnails for paginated images
        add_action('wp_ajax_pfg_get_thumbnails', array($this, 'get_thumbnails'));

        // Delete video thumbnail (on revert to original)
        add_action('wp_ajax_pfg_delete_video_thumbnail', array($this, 'delete_video_thumbnail'));
    }




    /**
     * Get attachment URL by ID.
     */
    public function get_attachment_url()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'pfg_admin_action')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'portfolio-filter-gallery')), 403);
        }

        // Missing Capability check fixed required by WP.org
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        $attachment_id = PFG_Security::get_post('attachment_id', 0, 'int');

        if (!$attachment_id) {
            wp_send_json_error(array('message' => __('No attachment ID provided.', 'portfolio-filter-gallery')));
        }

        $url = wp_get_attachment_image_url($attachment_id, 'medium');

        if (!$url) {
            wp_send_json_error(array('message' => __('Attachment not found.', 'portfolio-filter-gallery')));
        }

        wp_send_json_success(array('url' => $url));
    }

    /**
     * Delete video thumbnail from media library (on revert to original).
     * This prevents duplicate thumbnails from accumulating.
     */
    public function delete_video_thumbnail()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'pfg_admin_action')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'portfolio-filter-gallery')), 403);
        }

        if (!current_user_can('delete_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        $attachment_id = isset($_POST['attachment_id']) ? absint(wp_unslash($_POST['attachment_id'])) : 0;

        if (!$attachment_id) {
            wp_send_json_error(array('message' => __('No attachment ID provided.', 'portfolio-filter-gallery')));
        }

        // Check if this attachment is a video thumbnail created by our plugin
        // We only delete attachments that have name starting with 'video-thumbnail-'
        $attachment = get_post($attachment_id);
        if (!$attachment || $attachment->post_type !== 'attachment') {
            wp_send_json_error(array('message' => __('Attachment not found.', 'portfolio-filter-gallery')));
        }

        // Verify it's a video thumbnail (by filename pattern)
        $file = get_attached_file($attachment_id);
        $filename = basename($file);

        // Only delete if it matches our video thumbnail naming pattern
        if (strpos($filename, 'video-thumbnail-') !== 0) {
            // Don't delete - it might be user's original image
            wp_send_json_success(array('deleted' => false, 'message' => __('Not a video thumbnail.', 'portfolio-filter-gallery')));
            return;
        }

        // Delete the attachment permanently
        $deleted = wp_delete_attachment($attachment_id, true);

        if ($deleted) {
            wp_send_json_success(array('deleted' => true, 'message' => __('Thumbnail deleted.', 'portfolio-filter-gallery')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete thumbnail.', 'portfolio-filter-gallery')));
        }
    }

    /**
     * Add a new filter.
     */
    public function add_filter()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to manage filters.', 'portfolio-filter-gallery')), 403);
        }

        $name = PFG_Security::get_post('name', '', 'text');
        $parent_id = PFG_Security::get_post('parent_id', '', 'key');
        $color = PFG_Security::get_post('color', '#94a3b8', 'text');

        if (empty($name)) {
            wp_send_json_error(array('message' => __('Filter name is required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());


        // Generate unique ID - handle non-Latin characters
        $id_base = sanitize_key($name);
        // If sanitize_key returned empty (non-Latin chars), use a hash-based ID
        if (empty($id_base)) {
            $id_base = 'filter' . substr(md5($name), 0, 8);
        }
        $id = $id_base . '_' . uniqid();

        // Generate base slug from name - handle non-Latin characters
        $base_slug = sanitize_title($name);
        // If sanitize_title returned empty OR URL-encoded (contains %xx hex), use Unicode-aware slug
        // sanitize_title() converts Japanese to %e6%97%a5... which we don't want
        if (empty($base_slug) || preg_match('/%[0-9a-f]{2}/i', $base_slug)) {
            // Create a slug preserving Unicode characters - use mb_strtolower for proper UTF-8 handling
            $base_slug = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/ui', '-', $name), 'UTF-8');
            $base_slug = trim($base_slug, '-');
            // If still empty, use a hash-based fallback
            if (empty($base_slug)) {
                $base_slug = 'filter-' . substr(md5($name), 0, 8);
            }
        }
        $slug = $base_slug;

        // Check for duplicate slugs and generate unique one
        $existing_slugs = array_column($filters, 'slug');
        if (in_array($slug, $existing_slugs, true)) {
            $counter = 2;
            while (in_array($base_slug . '-' . $counter, $existing_slugs, true)) {
                $counter++;
            }
            $slug = $base_slug . '-' . $counter;
        }

        $new_filter = array(
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'parent' => $parent_id,
            'color' => sanitize_hex_color($color),
            'order' => count($filters),
        );

        $filters[] = $new_filter;
        update_option('pfg_filters', $filters);

        // Also update legacy format for backward compatibility
        $this->sync_legacy_filters($filters);

        wp_send_json_success(array(
            'message' => __('Filter added successfully.', 'portfolio-filter-gallery'),
            'filter' => $new_filter,
        ));
    }

    /**
     * Delete a filter.
     */
    public function delete_filter()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_delete_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to delete filters.', 'portfolio-filter-gallery')), 403);
        }

        $filter_id = PFG_Security::get_post('filter_id', '', 'key');

        if (empty($filter_id)) {
            wp_send_json_error(array('message' => __('Filter ID is required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());
        $updated = array();

        foreach ($filters as $filter) {
            if ($filter['id'] !== $filter_id) {
                $updated[] = $filter;
            }
        }

        // Reindex order
        foreach ($updated as $index => &$filter) {
            $filter['order'] = $index;
        }

        update_option('pfg_filters', $updated);

        // Also update legacy format
        $this->sync_legacy_filters($updated);

        wp_send_json_success(array(
            'message' => __('Filter deleted successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Delete all filters.
     */
    public function delete_all_filters()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_delete_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to delete filters.', 'portfolio-filter-gallery')), 403);
        }

        // Clear all filters
        update_option('pfg_filters', array());

        // Also clear legacy format
        update_option('awl_portfolio_filter_gallery_categories', array());

        wp_send_json_success(array(
            'message' => __('All filters deleted successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Update a filter.
     */
    public function update_filter()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to manage filters.', 'portfolio-filter-gallery')), 403);
        }

        $filter_id = PFG_Security::get_post('filter_id', '', 'key');
        $name = PFG_Security::get_post('name', '', 'text');

        if (empty($filter_id) || empty($name)) {
            wp_send_json_error(array('message' => __('Filter ID and name are required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());

        foreach ($filters as &$filter) {
            if ($filter['id'] === $filter_id) {
                $filter['name'] = $name;
                // Note: slug is now managed separately via update_filter_slug
                break;
            }
        }

        update_option('pfg_filters', $filters);

        // Also update legacy format
        $this->sync_legacy_filters($filters);

        wp_send_json_success(array(
            'message' => __('Filter updated successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Reorder filters.
     */
    public function reorder_filters()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to manage filters.', 'portfolio-filter-gallery')), 403);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
        $order = isset($_POST['order']) ? array_map('sanitize_key', wp_unslash((array) $_POST['order'])) : array();

        if (empty($order)) {
            wp_send_json_error(array('message' => __('Order data is required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());
        $reordered = array();

        foreach ($order as $index => $filter_id) {
            foreach ($filters as $filter) {
                if ($filter['id'] === $filter_id) {
                    $filter['order'] = $index;
                    $reordered[] = $filter;
                    break;
                }
            }
        }

        update_option('pfg_filters', $reordered);

        wp_send_json_success(array(
            'message' => __('Filters reordered successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Update filter parent.
     */
    public function update_filter_parent()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to manage filters.', 'portfolio-filter-gallery')), 403);
        }

        $filter_id = PFG_Security::get_post('filter_id', '', 'key');
        $parent_id = PFG_Security::get_post('parent_id', '', 'key');

        if (empty($filter_id)) {
            wp_send_json_error(array('message' => __('Filter ID is required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());

        foreach ($filters as &$filter) {
            if ($filter['id'] === $filter_id) {
                $filter['parent'] = $parent_id;
                break;
            }
        }

        update_option('pfg_filters', $filters);

        wp_send_json_success(array(
            'message' => __('Filter parent updated successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Update filter color.
     */
    public function update_filter_color()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to manage filters.', 'portfolio-filter-gallery')), 403);
        }

        $filter_id = PFG_Security::get_post('filter_id', '', 'key');
        $color = PFG_Security::get_post('color', '#94a3b8', 'text');

        if (empty($filter_id)) {
            wp_send_json_error(array('message' => __('Filter ID is required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());

        foreach ($filters as &$filter) {
            if ($filter['id'] === $filter_id) {
                $filter['color'] = sanitize_hex_color($color);
                break;
            }
        }

        update_option('pfg_filters', $filters);

        wp_send_json_success(array(
            'message' => __('Filter color updated successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Update filter slug.
     */
    public function update_filter_slug()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
        $filter_id = isset($_POST['filter_id']) ? sanitize_key(wp_unslash($_POST['filter_id'])) : '';
        // Use sanitize_text_field instead of sanitize_key to preserve Unicode characters (Japanese, Chinese, etc.)
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
        $slug = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';

        if (empty($filter_id) || empty($slug)) {
            wp_send_json_error(array('message' => __('Filter ID and slug are required.', 'portfolio-filter-gallery')), 400);
        }

        $filters = get_option('pfg_filters', array());
        $updated = false;

        foreach ($filters as &$filter) {
            if ($filter['id'] === $filter_id) {
                $filter['slug'] = $slug;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            wp_send_json_error(array('message' => __('Filter not found.', 'portfolio-filter-gallery')), 404);
        }

        update_option('pfg_filters', $filters);

        wp_send_json_success(array(
            'message' => __('Filter slug updated successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Handle image upload.
     */
    public function upload_images()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to upload images.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
        $image_ids = isset($_POST['image_ids']) ? array_map('absint', wp_unslash((array) $_POST['image_ids'])) : array();

        if (empty($gallery_id) || empty($image_ids)) {
            wp_send_json_error(array('message' => __('Gallery ID and images are required.', 'portfolio-filter-gallery')), 400);
        }

        $gallery = new PFG_Gallery($gallery_id);
        $images = $gallery->get_images();
        $new_images = array(); // Track only newly added images

        foreach ($image_ids as $image_id) {
            // Check if image already exists
            $exists = false;
            foreach ($images as $img) {
                if ($img['id'] === $image_id) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $attachment = get_post($image_id);
                // Get alt text from attachment meta
                $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $new_image = array(
                    'id' => $image_id,
                    'title' => $attachment ? $attachment->post_title : '',
                    'alt' => $alt_text ? $alt_text : '',
                    'description' => $attachment ? $attachment->post_content : '',
                    'link' => '',
                    'type' => 'image',
                    'filters' => array(),
                );
                $images[] = $new_image;
                $new_images[] = $new_image;
            }
        }

        update_post_meta($gallery_id, '_pfg_images', $images);

        // Return only newly added images to prevent duplication in JS
        wp_send_json_success(array(
            'message' => __('Images added successfully.', 'portfolio-filter-gallery'),
            'images' => $this->prepare_images_for_output($new_images),
        ));
    }

    /**
     * Handle drag and drop file upload.
     */
    public function upload_dropped_files()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to upload images.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');

        if (empty($gallery_id)) {
            wp_send_json_error(array('message' => __('Gallery ID is required.', 'portfolio-filter-gallery')), 400);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified in PFG_Security::verify_ajax_nonce above, files validated by wp_check_filetype below.
        if (empty($_FILES['files'])) {
            wp_send_json_error(array('message' => __('No files were uploaded.', 'portfolio-filter-gallery')), 400);
        }

        // Include required files for media handling
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $gallery = new PFG_Gallery($gallery_id);
        $images = $gallery->get_images();
        $new_images = array();
        $upload_count = 0;

        // Handle multiple files
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- files validated by wp_check_filetype, nonce verified above.
        $files = $_FILES['files'];
        $file_count = count($files['name']);

        for ($i = 0; $i < $file_count; $i++) {
            // Check if valid image
            $file_type = wp_check_filetype($files['name'][$i]);
            if (!in_array($file_type['ext'], array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
                continue;
            }

            // Prepare file array for single upload
            $_FILES['upload_file'] = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            );

            // Upload to media library
            $attachment_id = media_handle_upload('upload_file', 0);

            if (!is_wp_error($attachment_id)) {
                $attachment = get_post($attachment_id);
                // Get alt text from attachment meta
                $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                $new_image = array(
                    'id' => $attachment_id,
                    'title' => $attachment ? $attachment->post_title : '',
                    'alt' => $alt_text ? $alt_text : '',
                    'description' => $attachment ? $attachment->post_content : '',
                    'link' => '',
                    'type' => 'image',
                    'filters' => array(),
                );
                $images[] = $new_image;
                $new_images[] = $new_image;
                $upload_count++;
            }
        }

        if ($upload_count === 0) {
            wp_send_json_error(array('message' => __('No valid images were uploaded.', 'portfolio-filter-gallery')), 400);
        }

        update_post_meta($gallery_id, '_pfg_images', $images);

        wp_send_json_success(array(
            /* translators: %d is the number of images uploaded */
            'message' => sprintf(_n('%d image uploaded successfully.', '%d images uploaded successfully.', $upload_count, 'portfolio-filter-gallery'), $upload_count),
            'images' => $this->prepare_images_for_output($new_images),
        ));
    }

    /**
     * Remove an image from gallery.
     */
    public function remove_image()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to remove images.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');
        $image_id = PFG_Security::get_post('image_id', 0, 'int');

        if (empty($gallery_id) || empty($image_id)) {
            wp_send_json_error(array('message' => __('Gallery ID and image ID are required.', 'portfolio-filter-gallery')), 400);
        }

        $gallery = new PFG_Gallery($gallery_id);
        $images = $gallery->get_images();
        $updated = array();

        foreach ($images as $image) {
            if ($image['id'] !== $image_id) {
                $updated[] = $image;
            }
        }

        update_post_meta($gallery_id, '_pfg_images', $updated);

        wp_send_json_success(array(
            'message' => __('Image removed successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Reorder images.
     */
    public function reorder_images()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to reorder images.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
        $order = isset($_POST['order']) ? array_map('absint', wp_unslash((array) $_POST['order'])) : array();

        if (empty($gallery_id) || empty($order)) {
            wp_send_json_error(array('message' => __('Gallery ID and order data are required.', 'portfolio-filter-gallery')), 400);
        }

        $gallery = new PFG_Gallery($gallery_id);
        $images = $gallery->get_images();
        $reordered = array();

        foreach ($order as $image_id) {
            foreach ($images as $image) {
                if ($image['id'] === $image_id) {
                    $reordered[] = $image;
                    break;
                }
            }
        }

        update_post_meta($gallery_id, '_pfg_images', $reordered);

        wp_send_json_success(array(
            'message' => __('Images reordered successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Update single image data.
     */
    public function update_image()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to update images.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');
        $image_id = PFG_Security::get_post('image_id', 0, 'int');

        if (empty($gallery_id) || empty($image_id)) {
            wp_send_json_error(array('message' => __('Gallery ID and image ID are required.', 'portfolio-filter-gallery')), 400);
        }

        $gallery = new PFG_Gallery($gallery_id);
        $images = $gallery->get_images();

        foreach ($images as &$image) {
            if ($image['id'] === $image_id) {
                $image['title'] = PFG_Security::get_post('title', $image['title'], 'text');
                $image['description'] = PFG_Security::get_post('description', $image['description'], 'textarea');
                $image['link'] = PFG_Security::get_post('link', $image['link'], 'url');
                $image['type'] = PFG_Security::get_post('type', $image['type'], 'key');

                // Use PFG_Security wrapper to extract and sanitize Unicode filter slugs without causing PHPCS warnings.
                $filters = PFG_Security::get_post('filters', null, 'array');
                if ($filters !== null) {
                    $image['filters'] = $filters;
                }

                break;
            }
        }

        update_post_meta($gallery_id, '_pfg_images', $images);

        wp_send_json_success(array(
            'message' => __('Image updated successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Save gallery via AJAX.
     */
    public function save_gallery()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');

        if (empty($gallery_id)) {
            wp_send_json_error(array('message' => __('Gallery ID is required.', 'portfolio-filter-gallery')), 400);
        }

        if (!PFG_Security::can_edit_gallery($gallery_id)) {
            wp_send_json_error(array('message' => __('You do not have permission to edit this gallery.', 'portfolio-filter-gallery')), 403);
        }

        $gallery = new PFG_Gallery($gallery_id);
        $schema = PFG_Gallery::get_schema();

        foreach ($schema as $key => $config) {
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified in PFG_Security::verify_ajax_nonce above.
            if (isset($_POST[$key])) {
                // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified in PFG_Security::verify_ajax_nonce above, sanitized below.
                $value = PFG_Security::sanitize(wp_unslash($_POST[$key]), $config['type']);
                $gallery->set_setting($key, $value);
            } elseif ($config['type'] === 'bool') {
                // Unchecked checkboxes don't send values, so explicitly set to false
                $gallery->set_setting($key, false);
            }
        }

        $gallery->save();

        wp_send_json_success(array(
            'message' => __('Gallery saved successfully.', 'portfolio-filter-gallery'),
        ));
    }

    /**
     * Duplicate a gallery.
     */
    public function duplicate_gallery()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');

        if (empty($gallery_id)) {
            wp_send_json_error(array('message' => __('Gallery ID is required.', 'portfolio-filter-gallery')), 400);
        }

        if (!PFG_Security::can_manage_galleries()) {
            wp_send_json_error(array('message' => __('You do not have permission to duplicate galleries.', 'portfolio-filter-gallery')), 403);
        }

        $original = get_post($gallery_id);

        if (!$original) {
            wp_send_json_error(array('message' => __('Gallery not found.', 'portfolio-filter-gallery')), 404);
        }

        // Create duplicate post
        $new_id = wp_insert_post(array(
            'post_type' => 'awl_filter_gallery',
            'post_title' => $original->post_title . ' ' . __('(Copy)', 'portfolio-filter-gallery'),
            'post_status' => 'publish',
        ));

        if (is_wp_error($new_id)) {
            wp_send_json_error(array('message' => $new_id->get_error_message()), 500);
        }

        // Copy meta data
        $gallery = new PFG_Gallery($gallery_id);
        $settings = $gallery->get_settings();
        $images = $gallery->get_images();

        $new_gallery = new PFG_Gallery($new_id);
        foreach ($settings as $key => $value) {
            $new_gallery->set_setting($key, $value);
        }
        $new_gallery->save();

        update_post_meta($new_id, '_pfg_images', $images);

        wp_send_json_success(array(
            'message' => __('Gallery duplicated successfully.', 'portfolio-filter-gallery'),
            'new_id' => $new_id,
            'edit_link' => get_edit_post_link($new_id, 'raw'),
        ));
    }

    /**
     * Run migration.
     */
    public function run_migration()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::is_admin()) {
            wp_send_json_error(array('message' => __('Only administrators can run migrations.', 'portfolio-filter-gallery')), 403);
        }

        $migrator = new PFG_Migrator();
        $migrator->maybe_migrate();

        wp_send_json_success(array(
            'message' => __('Migration completed successfully.', 'portfolio-filter-gallery'),
            'status' => $migrator->get_status(),
        ));
    }

    /**
     * Restore from backup.
     */
    public function restore_backup()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::is_admin()) {
            wp_send_json_error(array('message' => __('Only administrators can restore backups.', 'portfolio-filter-gallery')), 403);
        }

        $backup_file = PFG_Security::get_post('backup_file', '', 'text');

        if (empty($backup_file)) {
            $backup_file = get_option('pfg_last_backup', '');
        }

        if (empty($backup_file)) {
            wp_send_json_error(array('message' => __('No backup file specified.', 'portfolio-filter-gallery')), 400);
        }

        $migrator = new PFG_Migrator();
        $result = $migrator->restore_backup($backup_file);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Backup restored successfully.', 'portfolio-filter-gallery'),
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to restore backup.', 'portfolio-filter-gallery')), 500);
        }
    }

    /**
     * Get migration status.
     */
    public function get_migration_status()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::is_admin()) {
            wp_send_json_error(array('message' => __('Only administrators can view migration status.', 'portfolio-filter-gallery')), 403);
        }

        $migrator = new PFG_Migrator();

        wp_send_json_success($migrator->get_status());
    }

    /**
     * Force re-migrate galleries to repair incomplete data.
     */
    public function force_remigrate()
    {
        PFG_Security::verify_ajax_nonce('admin_action');

        if (!PFG_Security::is_admin()) {
            wp_send_json_error(array('message' => __('Only administrators can run migrations.', 'portfolio-filter-gallery')), 403);
        }

        // Use standardized get_post method to resolve missing wp_unslash
        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');

        $migrator = new PFG_Migrator();

        if ($gallery_id) {
            // Re-migrate single gallery
            $result = $migrator->force_remigrate_gallery($gallery_id);

            if ($result) {
                // Mark migration as completed to hide the re-migrate button
                update_post_meta($gallery_id, '_pfg_migration_completed', true);

                wp_send_json_success(array(
                    /* translators: %d: gallery ID number */
                    'message' => sprintf(__('Gallery #%d re-migrated successfully.', 'portfolio-filter-gallery'), $gallery_id),
                ));
            } else {
                wp_send_json_error(array(
                    /* translators: %d: gallery ID number */
                    'message' => sprintf(__('No legacy data found for gallery #%d.', 'portfolio-filter-gallery'), $gallery_id),
                ));
            }
        } else {
            // Re-migrate all galleries
            $count = $migrator->force_remigrate_all();

            wp_send_json_success(array(
                /* translators: %d: number of galleries re-migrated */
                'message' => sprintf(__('%d galleries re-migrated successfully.', 'portfolio-filter-gallery'), $count),
                'count' => $count,
            ));
        }
    }

    /**
     * Sync filters to legacy format.
     *
     * @param array $filters New format filters.
     */
    protected function sync_legacy_filters($filters)
    {
        $legacy = array();

        foreach ($filters as $filter) {
            $legacy[$filter['id']] = $filter['name'];
        }

        update_option('awl_portfolio_filter_gallery_categories', $legacy);
    }

    /**
     * Prepare images for JSON output.
     *
     * @param array $images Images array.
     * @return array
     */
    protected function prepare_images_for_output($images)
    {
        $output = array();

        foreach ($images as $image) {
            $thumbnail = wp_get_attachment_image_src($image['id'], 'thumbnail');
            $medium = wp_get_attachment_image_src($image['id'], 'medium');

            $output[] = array_merge($image, array(
                'thumbnail' => $thumbnail ? $thumbnail[0] : '',
                'medium' => $medium ? $medium[0] : '',
            ));
        }

        return $output;
    }

    /**
     * Save images in chunks for large galleries.
     * This bypasses server limits by saving images in smaller batches.
     */
    public function save_images_chunk()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'pfg_admin_action')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'portfolio-filter-gallery')), 403);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = isset($_POST['gallery_id']) ? absint(wp_unslash($_POST['gallery_id'])) : 0;
        $chunk_index = isset($_POST['chunk_index']) ? absint(wp_unslash($_POST['chunk_index'])) : 0;
        $total_chunks = isset($_POST['total_chunks']) ? absint(wp_unslash($_POST['total_chunks'])) : 1;
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON string: wp_unslash applied here, individual fields sanitized after json_decode below.
        $images_json = isset($_POST['images']) ? wp_unslash($_POST['images']) : '[]';

        if (!$gallery_id) {
            wp_send_json_error(array('message' => __('Invalid gallery ID.', 'portfolio-filter-gallery')), 400);
        }

        // Verify gallery exists and user can edit it
        $gallery_post = get_post($gallery_id);
        if (!$gallery_post || $gallery_post->post_type !== 'awl_filter_gallery') {
            wp_send_json_error(array('message' => __('Gallery not found.', 'portfolio-filter-gallery')), 404);
        }

        if (!current_user_can('edit_post', $gallery_id)) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        // Decode images from JSON
        $chunk_images = json_decode($images_json, true);
        if (!is_array($chunk_images)) {
            $chunk_images = array();
        }

        // Sanitize images
        $sanitized_images = array();
        foreach ($chunk_images as $image) {
            if (empty($image['id'])) {
                continue;
            }

            $filters = isset($image['filters']) ? $image['filters'] : '';
            if (is_string($filters)) {
                $filters = array_filter(array_map('sanitize_text_field', explode(',', $filters)));
            } elseif (is_array($filters)) {
                $filters = array_filter(array_map('sanitize_text_field', $filters));
            } else {
                $filters = array();
            }

            $type = isset($image['type']) ? sanitize_key($image['type']) : 'image';
            $link = isset($image['link']) ? esc_url_raw($image['link']) : '';
            if ( 'image' === $type ) {
                $link = '';
            }

            $sanitized_images[] = array(
                'id' => absint($image['id']),
                'title' => isset($image['title']) ? sanitize_text_field($image['title']) : '',
                'description' => isset($image['description']) ? sanitize_textarea_field($image['description']) : '',
                'link' => $link,
                'type' => $type,
                'filters' => $filters,
                'product_id' => isset($image['product_id']) ? absint($image['product_id']) : 0,
                'product_name' => isset($image['product_name']) ? sanitize_text_field($image['product_name']) : '',
                'original_id' => isset($image['original_id']) ? absint($image['original_id']) : absint($image['id']),
            );
        }

        // Use transient to accumulate chunks
        $transient_key = 'pfg_chunk_save_' . $gallery_id;

        if ($chunk_index === 0) {
            // First chunk - start fresh
            $all_images = $sanitized_images;
        } else {
            // Subsequent chunks - append to existing
            $existing = get_transient($transient_key);
            if (!is_array($existing)) {
                $existing = array();
            }
            $all_images = array_merge($existing, $sanitized_images);
        }

        // Store accumulated images
        set_transient($transient_key, $all_images, 300); // 5 minute expiry

        // If this is the last chunk, save to database and clean up
        if ($chunk_index >= $total_chunks - 1) {
            update_post_meta($gallery_id, '_pfg_images', $all_images);
            delete_transient($transient_key);

            wp_send_json_success(array(
                'message' => __('All images saved successfully.', 'portfolio-filter-gallery'),
                'chunk_index' => $chunk_index,
                'total_saved' => count($all_images),
                'complete' => true,
            ));
        } else {
            wp_send_json_success(array(
                /* translators: %1$d: current chunk number, %2$d: total number of chunks */
                'message' => sprintf(__('Chunk %1$d of %2$d saved.', 'portfolio-filter-gallery'), $chunk_index + 1, $total_chunks),
                'chunk_index' => $chunk_index,
                'total_so_far' => count($all_images),
                'complete' => false,
            ));
        }
    }

    /**
     * Get paginated images for admin gallery editor.
     * Returns HTML for a specific page of images.
     */
    public function get_admin_images_page()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'pfg_admin_action')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'portfolio-filter-gallery')), 403);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        $gallery_id = PFG_Security::get_post('gallery_id', 0, 'int');
        $page = max(1, PFG_Security::get_post('page', 1, 'int'));
        $per_page = PFG_Security::get_post('per_page', 50, 'int');

        if (!$gallery_id) {
            wp_send_json_error(array('message' => __('Gallery ID is required.', 'portfolio-filter-gallery')));
        }

        // Get gallery images
        $gallery = new PFG_Gallery($gallery_id);
        $all_images = $gallery->get_images();
        $total_images = count($all_images);
        $total_pages = max(1, ceil($total_images / $per_page));

        // Ensure page is within bounds
        $page = min($page, $total_pages);

        // Calculate offset and get slice
        $offset = ($page - 1) * $per_page;
        $paged_images = array_slice($all_images, $offset, $per_page, true);

        // Get filters for display
        $filters = get_option('pfg_filters', array());

        // Build HTML for this page
        ob_start();

        foreach ($paged_images as $index => $image) {
            $attachment = get_post($image['id']);
            if (!$attachment)
                continue;

            $thumb_url = wp_get_attachment_image_url($image['id'], 'thumbnail');
            $title = !empty($image['title']) ? $image['title'] : $attachment->post_title;
            $image_filters = isset($image['filters']) ? $image['filters'] : array();

            // Type indicator
            $image_type = isset($image['type']) ? $image['type'] : 'image';
            $image_link = isset($image['link']) ? $image['link'] : '';

            $video_source = '';
            if ($image_type === 'video' && $image_link) {
                if (strpos($image_link, 'youtube.com') !== false || strpos($image_link, 'youtu.be') !== false) {
                    $video_source = 'youtube';
                } elseif (strpos($image_link, 'vimeo.com') !== false) {
                    $video_source = 'vimeo';
                }
            }
            ?>
            <div class="pfg-image-item" data-id="<?php echo esc_attr($image['id']); ?>"
                data-index="<?php echo esc_attr($index); ?>">

                <!-- Selection Checkbox -->
                <label class="pfg-image-checkbox" style="position: absolute; top: 8px; left: 8px; z-index: 10;">
                    <input type="checkbox" class="pfg-image-select" style="width: 18px; height: 18px; cursor: pointer;">
                </label>

                <?php if ($image_type === 'video' || $image_type === 'url'):
                    $badge_class = 'pfg-image-type-badge';
                    if ($video_source === 'youtube') {
                        $badge_class .= ' pfg-badge-youtube';
                        $badge_icon = 'dashicons-youtube';
                    } elseif ($video_source === 'vimeo') {
                        $badge_class .= ' pfg-badge-vimeo';
                        $badge_icon = 'dashicons-video-alt3';
                    } elseif ($image_type === 'video') {
                        $badge_class .= ' pfg-badge-video';
                        $badge_icon = 'dashicons-video-alt3';
                    } else {
                        $badge_icon = 'dashicons-external';
                    }
                    ?>
                    <div class="<?php echo esc_attr($badge_class); ?>">
                        <span class="dashicons <?php echo esc_attr($badge_icon); ?>"></span>
                    </div>
                <?php endif; ?>

                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>" class="pfg-image-thumb"
                    loading="lazy">

                <div class="pfg-image-actions">
                    <button type="button" class="pfg-image-action pfg-image-edit"
                        title="<?php esc_attr_e('Edit', 'portfolio-filter-gallery'); ?>">
                        <span class="dashicons dashicons-edit"></span>
                    </button>
                    <button type="button" class="pfg-image-action pfg-image-delete"
                        title="<?php esc_attr_e('Delete', 'portfolio-filter-gallery'); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>

                <div class="pfg-image-info">
                    <p class="pfg-image-title"><?php echo esc_html($title); ?></p>

                    <?php if (!empty($image_filters)): ?>
                        <div class="pfg-image-filters">
                            <?php foreach ($image_filters as $filter_id):
                                $filter = null;
                                foreach ($filters as $f) {
                                    if ($f['id'] === $filter_id || $f['slug'] === $filter_id) {
                                        $filter = $f;
                                        break;
                                    }
                                }
                                if ($filter):
                                    $tag_color = isset($filter['color']) && $filter['color'] ? $filter['color'] : '#94a3b8';
                                    $is_child = !empty($filter['parent']); ?>
                                    <span class="pfg-image-filter-tag"><?php if ($is_child): ?><span
                                                class="pfg-tag-connector">└</span><?php endif; ?><span class="pfg-tag-dot"
                                            style="background-color: <?php echo esc_attr($tag_color); ?>;"></span><?php echo esc_html($filter['name']); ?></span>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][id]"
                    value="<?php echo esc_attr($image['id']); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][title]"
                    value="<?php echo esc_attr($title); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][alt]"
                    value="<?php echo esc_attr($image['alt'] ?? ''); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][description]"
                    value="<?php echo esc_attr($image['description'] ?? ''); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][link]"
                    value="<?php echo esc_url($image['link'] ?? ''); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][type]"
                    value="<?php echo esc_attr($image['type'] ?? 'image'); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][filters]"
                    value="<?php echo esc_attr(implode(',', $image_filters)); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][product_id]"
                    value="<?php echo esc_attr($image['product_id'] ?? ''); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][product_name]"
                    value="<?php echo esc_attr($image['product_name'] ?? ''); ?>">
                <input type="hidden" name="pfg_images[<?php echo esc_attr($index); ?>][original_id]"
                    value="<?php echo esc_attr($image['original_id'] ?? $image['id']); ?>">

            </div>
            <?php
        }

        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'page' => $page,
            'per_page' => $per_page,
            'total_images' => $total_images,
            'total_pages' => $total_pages,
            'showing_start' => $offset + 1,
            'showing_end' => min($offset + $per_page, $total_images),
        ));
    }

    /**
     * Get thumbnail URLs for multiple attachment IDs.
     * Used by client-side pagination to load thumbnails after rendering.
     */
    public function get_thumbnails()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'pfg_admin_action')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'portfolio-filter-gallery')), 403);
        }

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'portfolio-filter-gallery')), 403);
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above
        $image_ids = isset($_POST['image_ids']) ? array_map('absint', wp_unslash((array) $_POST['image_ids'])) : array();

        if (empty($image_ids)) {
            wp_send_json_success(array('thumbnails' => array()));
        }

        $thumbnails = array();
        foreach ($image_ids as $image_id) {
            $thumb_url = wp_get_attachment_image_url($image_id, 'thumbnail');
            if ($thumb_url) {
                $thumbnails[$image_id] = $thumb_url;
            }
        }

        wp_send_json_success(array('thumbnails' => $thumbnails));
    }
}
