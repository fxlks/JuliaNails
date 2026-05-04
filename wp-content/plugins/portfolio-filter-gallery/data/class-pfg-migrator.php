<?php
/**
 * Data migration class.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/data
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles data migration from old versions to new format.
 */
class PFG_Migrator {

    /**
     * Current plugin version requiring migration.
     */
    const CURRENT_VERSION = '2.0.0';

    /**
     * Option key for storing database version.
     */
    const VERSION_OPTION = 'pfg_db_version';

    /**
     * Option key for storing migration status.
     */
    const STATUS_OPTION = 'pfg_migration_status';

    /**
     * Check and run migrations if needed.
     */
    public function maybe_migrate() {
        $current_version = get_option( self::VERSION_OPTION, '1.0.0' );

        if ( version_compare( $current_version, self::CURRENT_VERSION, '<' ) ) {
            $this->run_migrations( $current_version );
        }
    }

    /**
     * Run migrations from specified version.
     *
     * @param string $from_version Version to migrate from.
     */
    protected function run_migrations( $from_version ) {
        // Migration map: version => method
        $migrations = array(
            '2.0.0' => 'migrate_to_2_0_0',
        );

        foreach ( $migrations as $version => $method ) {
            if ( version_compare( $from_version, $version, '<' ) ) {
                $this->log( "Starting migration to version {$version}" );
                
                try {
                    call_user_func( array( $this, $method ) );
                    $this->log( "Completed migration to version {$version}" );
                } catch ( Exception $e ) {
                    $this->log( "Migration to {$version} failed: " . $e->getMessage(), 'error' );
                    return; // Stop migrations on error
                }
            }
        }

        // Update version after all migrations complete
        update_option( self::VERSION_OPTION, self::CURRENT_VERSION );
        update_option( self::STATUS_OPTION, 'completed' );
    }

    /**
     * Migrate to version 2.0.0.
     */
    protected function migrate_to_2_0_0() {
        // Create backup first
        $this->create_backup();

        // Migrate global filters to a cleaner format FIRST
        // This must happen before galleries so filter ID-to-slug mapping is available
        $this->migrate_filters();

        // Migrate galleries (images will use the filter mapping created above)
        $this->migrate_galleries();
    }

    /**
     * Create a backup of all gallery data.
     *
     * @return string|false Backup file path or false on failure.
     */
    public function create_backup() {
        $backup_data = array(
            'version'    => get_option( self::VERSION_OPTION, '1.0.0' ),
            'timestamp'  => current_time( 'mysql' ),
            'galleries'  => array(),
            'filters'    => get_option( 'awl_portfolio_filter_gallery_categories', array() ),
        );

        // Get all galleries
        $galleries = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ) );

        foreach ( $galleries as $gallery ) {
            $legacy_key = 'awl_filter_gallery' . $gallery->ID;
            $backup_data['galleries'][ $gallery->ID ] = array(
                'post'     => $gallery,
                'settings' => get_post_meta( $gallery->ID, $legacy_key, true ),
            );
        }

        // Save to file
        $upload_dir  = wp_upload_dir();
        $backup_dir  = $upload_dir['basedir'] . '/pfg-backups';

        if ( ! file_exists( $backup_dir ) ) {
            wp_mkdir_p( $backup_dir );
            
            // Add index.php for security
            file_put_contents( $backup_dir . '/index.php', '<?php // Silence is golden' );
            
            // Add .htaccess to deny direct access
            file_put_contents( $backup_dir . '/.htaccess', 'deny from all' );
        }

        $backup_file = $backup_dir . '/backup-' . gmdate( 'Y-m-d-His' ) . '.json';
        $result = file_put_contents( $backup_file, wp_json_encode( $backup_data, JSON_PRETTY_PRINT ) );

        if ( $result ) {
            update_option( 'pfg_last_backup', $backup_file );
            update_option( 'pfg_last_backup_date', current_time( 'mysql' ) );
            $this->log( "Backup created: {$backup_file}" );
            return $backup_file;
        }

        return false;
    }

    /**
     * Migrate all galleries to new format.
     */
    protected function migrate_galleries() {
        $galleries = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => 50,
            'post_status'    => 'any',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to find un-migrated galleries during one-time migration.
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'key'     => '_pfg_migrated',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => '_pfg_migrated',
                    'value'   => self::CURRENT_VERSION,
                    'compare' => '!=',
                ),
            ),
        ) );

        foreach ( $galleries as $gallery ) {
            $this->migrate_single_gallery( $gallery->ID );
        }

        // Check if more galleries need migration
        $remaining = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => 1,
            'post_status'    => 'any',
            'fields'         => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to check remaining un-migrated galleries.
            'meta_query'     => array(
                array(
                    'key'     => '_pfg_migrated',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ) );

        if ( ! empty( $remaining ) ) {
            // Schedule next batch
            wp_schedule_single_event( time() + 5, 'pfg_continue_migration' );
        }
    }

    /**
     * Migrate a single gallery.
     *
     * @param int $gallery_id Gallery post ID.
     * @return bool
     */
    public function migrate_single_gallery( $gallery_id ) {
        $gallery_id = absint( $gallery_id );
        
        if ( ! $gallery_id ) {
            return false;
        }

        // Check if already migrated to current version
        $migrated = get_post_meta( $gallery_id, '_pfg_migrated', true );
        if ( $migrated === self::CURRENT_VERSION ) {
            return true;
        }

        // Get legacy settings
        $legacy_key = 'awl_filter_gallery' . $gallery_id;
        $legacy     = get_post_meta( $gallery_id, $legacy_key, true );

        if ( empty( $legacy ) ) {
            // No legacy data, mark as migrated
            update_post_meta( $gallery_id, '_pfg_migrated', self::CURRENT_VERSION );
            return true;
        }

        // Use Gallery class to handle transformation
        $gallery = new PFG_Gallery( $gallery_id );
        
        // IMPORTANT: Force re-extract images directly from legacy data
        // This ensures alt, link, description are properly migrated even if
        // _pfg_images already exists with incomplete data from a previous migration
        if ( isset( $legacy['image-ids'] ) && is_array( $legacy['image-ids'] ) ) {
            $images = $this->extract_images_from_legacy( $legacy, $gallery_id );
            if ( ! empty( $images ) ) {
                update_post_meta( $gallery_id, '_pfg_images', $images );
            }
        }

        // Save new format settings
        $gallery->save();

        // Keep legacy data as backup
        update_post_meta( $gallery_id, '_pfg_legacy_backup', $legacy );

        // Mark as migrated
        update_post_meta( $gallery_id, '_pfg_migrated', self::CURRENT_VERSION );
        
        // Mark migration as completed (hides re-migrate button in UI)
        update_post_meta( $gallery_id, '_pfg_migration_completed', true );

        $this->log( "Migrated gallery #{$gallery_id}" );

        return true;
    }
    
    /**
     * Extract images from legacy data format.
     * This method ensures all fields (alt, link, description) are properly extracted.
     *
     * @param array $legacy     Legacy settings array.
     * @param int   $gallery_id Gallery ID for filter mapping.
     * @return array Transformed images array.
     */
    protected function extract_images_from_legacy( $legacy, $gallery_id ) {
        $images = array();

        if ( ! isset( $legacy['image-ids'] ) || ! is_array( $legacy['image-ids'] ) ) {
            return $images;
        }

        $image_ids   = $legacy['image-ids'];
        $titles      = isset( $legacy['image_title'] ) ? $legacy['image_title'] : array();
        
        // Note: Legacy uses 'image-desc' (hyphen, indexed array)
        $descs       = isset( $legacy['image-desc'] ) ? $legacy['image-desc'] : array();
        
        // Note: Legacy uses 'slide-alt' (keyed by image ID)
        $alts        = isset( $legacy['slide-alt'] ) ? $legacy['slide-alt'] : array();
        
        // Note: Legacy uses 'image-link' (keyed by image ID)
        $links       = isset( $legacy['image-link'] ) ? $legacy['image-link'] : array();
        
        // Note: Legacy uses 'slide-type' (keyed by image ID)
        $types       = isset( $legacy['slide-type'] ) ? $legacy['slide-type'] : array();
        
        $filters     = isset( $legacy['filters'] ) ? $legacy['filters'] : array();

        // Build filter slug map
        $filter_id_to_slug = $this->build_legacy_filter_map();

        foreach ( $image_ids as $index => $id ) {
            $id = absint( $id );
            if ( ! $id ) {
                continue;
            }

            // Try both index and id as keys for legacy compatibility
            $legacy_filter_ids = array();
            if ( isset( $filters[ $index ] ) && is_array( $filters[ $index ] ) ) {
                $legacy_filter_ids = array_map( 'absint', $filters[ $index ] );
            } elseif ( isset( $filters[ $id ] ) && is_array( $filters[ $id ] ) ) {
                $legacy_filter_ids = array_map( 'absint', $filters[ $id ] );
            }

            // Convert legacy filter IDs to filter slugs
            $filter_slugs = array();
            foreach ( $legacy_filter_ids as $filter_id ) {
                if ( isset( $filter_id_to_slug[ $filter_id ] ) ) {
                    $filter_slugs[] = $filter_id_to_slug[ $filter_id ];
                }
            }

            // Get alt text: Legacy 'slide-alt' is keyed by image ID, not index
            // Try by image ID first, then by index, then fall back to attachment meta
            $alt_text = '';
            if ( isset( $alts[ $id ] ) && ! empty( $alts[ $id ] ) ) {
                $alt_text = $alts[ $id ];
            } elseif ( isset( $alts[ $index ] ) && ! empty( $alts[ $index ] ) ) {
                $alt_text = $alts[ $index ];
            } else {
                // Fall back to WordPress attachment alt text
                $alt_text = get_post_meta( $id, '_wp_attachment_image_alt', true );
            }
            
            // Get description: Legacy 'image-desc' is indexed array
            $description = '';
            if ( isset( $descs[ $index ] ) && ! empty( $descs[ $index ] ) ) {
                $description = $descs[ $index ];
            }
            
            // Get link: Legacy 'image-link' is keyed by image ID
            $link = '';
            if ( isset( $links[ $id ] ) && ! empty( $links[ $id ] ) ) {
                $link = $links[ $id ];
            } elseif ( isset( $links[ $index ] ) && ! empty( $links[ $index ] ) ) {
                $link = $links[ $index ];
            }
            
            // Get type: Legacy 'slide-type' is keyed by image ID
            // In legacy: 'image' = lightbox, 'video' = video lightbox
            // In new format: 'image' = lightbox, 'video' = video lightbox, 'url' = external link
            // If legacy type is 'image' but has a link URL, it means external link (type='url')
            $type = 'image';
            if ( isset( $types[ $id ] ) && ! empty( $types[ $id ] ) ) {
                $type = $types[ $id ];
            } elseif ( isset( $types[ $index ] ) && ! empty( $types[ $index ] ) ) {
                $type = $types[ $index ];
            }
            
            // Convert legacy 'image' type with link to new 'url' type (external link)
            if ( $type === 'image' && ! empty( $link ) ) {
                $type = 'url';
            }

            $images[] = array(
                'id'          => $id,
                'title'       => isset( $titles[ $index ] ) ? sanitize_text_field( $titles[ $index ] ) : sanitize_text_field( get_the_title( $id ) ),
                'alt'         => sanitize_text_field( $alt_text ),
                'description' => wp_kses_post( $description ),
                'link'        => esc_url_raw( $link ),
                'type'        => sanitize_key( $type ),
                'filters'     => array_map( 'sanitize_title', $filter_slugs ),
            );
        }

        return $images;
    }
    
    /**
     * Build legacy filter ID to slug mapping.
     *
     * @return array Map of filter ID => slug.
     */
    protected function build_legacy_filter_map() {
        $map = array();
        
        // Get new format filters
        $new_filters = get_option( 'pfg_filters', array() );
        foreach ( $new_filters as $filter ) {
            if ( isset( $filter['id'] ) && isset( $filter['slug'] ) ) {
                // Extract numeric ID from the filter ID if present
                $parts = explode( '_', $filter['id'] );
                $numeric_id = absint( $parts[0] );
                if ( $numeric_id ) {
                    $map[ $numeric_id ] = $filter['slug'];
                }
                // Also map by full ID
                $map[ $filter['id'] ] = $filter['slug'];
            }
        }
        
        // Get legacy filters and try to match by name
        $legacy_filters = get_option( 'awl_portfolio_filter_gallery_categories', array() );
        foreach ( $legacy_filters as $legacy_id => $legacy_name ) {
            if ( ! isset( $map[ $legacy_id ] ) ) {
                // Try to find matching new filter by name
                foreach ( $new_filters as $filter ) {
                    if ( isset( $filter['name'] ) && $filter['name'] === $legacy_name ) {
                        $map[ $legacy_id ] = $filter['slug'];
                        break;
                    }
                }
            }
        }
        
        return $map;
    }
    
    /**
     * Force re-migrate a single gallery, even if already migrated.
     * This is useful for repairing galleries that were migrated with incomplete data.
     *
     * @param int $gallery_id Gallery post ID.
     * @return bool
     */
    public function force_remigrate_gallery( $gallery_id ) {
        $gallery_id = absint( $gallery_id );
        
        if ( ! $gallery_id ) {
            return false;
        }

        // Get legacy settings (original or backup)
        $legacy_key = 'awl_filter_gallery' . $gallery_id;
        $legacy     = get_post_meta( $gallery_id, $legacy_key, true );
        
        // Try backup if original is empty
        if ( empty( $legacy ) ) {
            $legacy = get_post_meta( $gallery_id, '_pfg_legacy_backup', true );
        }

        if ( empty( $legacy ) || ! isset( $legacy['image-ids'] ) ) {
            $this->log( "No legacy data found for gallery #{$gallery_id}" );
            return false;
        }

        // Force extract images from legacy
        $images = $this->extract_images_from_legacy( $legacy, $gallery_id );
        if ( ! empty( $images ) ) {
            update_post_meta( $gallery_id, '_pfg_images', $images );
            $this->log( "Force re-migrated gallery #{$gallery_id} with " . count( $images ) . " images" );
            return true;
        }

        return false;
    }
    
    /**
     * Force re-migrate all galleries.
     * This is useful for repairing all galleries that were migrated with incomplete data.
     *
     * @return int Number of galleries re-migrated.
     */
    public function force_remigrate_all() {
        $galleries = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
        ) );

        $count = 0;
        foreach ( $galleries as $gallery_id ) {
            if ( $this->force_remigrate_gallery( $gallery_id ) ) {
                $count++;
            }
        }

        $this->log( "Force re-migrated {$count} galleries" );
        return $count;
    }

    /**
     * Migrate global filters.
     */
    protected function migrate_filters() {
        $old_filters = get_option( 'awl_portfolio_filter_gallery_categories', array() );

        if ( empty( $old_filters ) || ! is_array( $old_filters ) ) {
            return;
        }

        // Create a cleaner format
        $new_filters = array();
        $index = 0;

        foreach ( $old_filters as $id => $name ) {
            // Skip if name is not a string (malformed data)
            if ( is_array( $name ) ) {
                // Try to extract name from array if it has a 'name' key
                if ( isset( $name['name'] ) && is_string( $name['name'] ) ) {
                    $name = $name['name'];
                } else {
                    // Skip this filter entry
                    $this->log( 'Skipped malformed filter: ' . wp_json_encode( $name ), 'warning' );
                    continue;
                }
            }

            if ( ! is_string( $name ) ) {
                continue;
            }

            $new_filters[] = array(
                'id'    => sanitize_key( $id ) ?: 'filter' . substr( md5( $name ), 0, 8 ),
                'name'  => sanitize_text_field( $name ),
                'slug'  => $this->generate_slug_from_name( $name ),
                'order' => $index,
            );
            $index++;
        }

        update_option( 'pfg_filters', $new_filters );

        // Auto-repair: fix any URL-encoded slugs from previous migrations
        $this->repair_broken_slugs();

        // Keep old format as backup
        update_option( 'pfg_filters_legacy_backup', $old_filters );

        $this->log( 'Migrated ' . count( $new_filters ) . ' filters' );
    }
    
    /**
     * Generate a URL-safe slug from filter name.
     * Handles non-Latin characters (Japanese, Chinese, Arabic, etc.)
     *
     * @param string $name Filter name.
     * @return string URL-safe slug.
     */
    protected function generate_slug_from_name( $name ) {
        // First try WordPress sanitize_title
        $slug = sanitize_title( $name );
        
        // If empty OR URL-encoded (contains %xx hex), create a Unicode-aware slug
        // sanitize_title() converts Japanese to %e6%97%a5... which we don't want
        if ( empty( $slug ) || preg_match( '/%[0-9a-f]{2}/i', $slug ) ) {
            // Keep Unicode letters and numbers, use mb_strtolower for proper UTF-8 handling
            $slug = mb_strtolower( preg_replace( '/[^\p{L}\p{N}]+/ui', '-', $name ), 'UTF-8' );
            $slug = trim( $slug, '-' );
            
            // If still empty, use hash fallback
            if ( empty( $slug ) ) {
                $slug = 'filter-' . substr( md5( $name ), 0, 8 );
            }
        }
        
        return $slug;
    }

    /**
     * Repair URL-encoded filter slugs from previous migrations.
     * This fixes slugs created by older versions that used sanitize_title() directly.
     */
    protected function repair_broken_slugs() {
        $filters = get_option( 'pfg_filters', array() );
        
        if ( empty( $filters ) || ! is_array( $filters ) ) {
            return;
        }
        
        $repaired = 0;
        $slug_map = array(); // Track old => new for image association updates
        
        foreach ( $filters as &$filter ) {
            if ( ! isset( $filter['slug'] ) || ! isset( $filter['name'] ) ) {
                continue;
            }
            
            $old_slug = $filter['slug'];
            
            // Check if slug contains URL-encoded characters (e.g., %e6%9f%b3)
            if ( preg_match( '/%[0-9a-f]{2}/i', $old_slug ) ) {
                $new_slug = $this->generate_slug_from_name( $filter['name'] );
                
                if ( ! empty( $new_slug ) && $new_slug !== $old_slug ) {
                    $filter['slug'] = $new_slug;
                    $slug_map[ $old_slug ] = $new_slug;
                    $repaired++;
                }
            }
        }
        
        if ( $repaired > 0 ) {
            update_option( 'pfg_filters', $filters );
            $this->log( "Auto-repaired {$repaired} URL-encoded filter slug(s)" );
            
            // Update image filter associations
            $this->repair_image_filter_associations( $slug_map );
        }
    }

    /**
     * Update image filter associations after slug repair.
     *
     * @param array $slug_map Map of old slug => new slug.
     */
    protected function repair_image_filter_associations( $slug_map ) {
        if ( empty( $slug_map ) ) {
            return;
        }
        
        // Get all galleries
        $galleries = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
        ) );
        
        $images_updated = 0;
        
        foreach ( $galleries as $gallery_id ) {
            $images = get_post_meta( $gallery_id, '_pfg_images', true );
            
            if ( empty( $images ) || ! is_array( $images ) ) {
                continue;
            }
            
            $updated = false;
            
            foreach ( $images as &$image ) {
                if ( empty( $image['filters'] ) || ! is_array( $image['filters'] ) ) {
                    continue;
                }
                
                $new_filters = array();
                foreach ( $image['filters'] as $filter_slug ) {
                    if ( isset( $slug_map[ $filter_slug ] ) ) {
                        $new_filters[] = $slug_map[ $filter_slug ];
                        $updated = true;
                        $images_updated++;
                    } else {
                        $new_filters[] = $filter_slug;
                    }
                }
                $image['filters'] = $new_filters;
            }
            
            if ( $updated ) {
                update_post_meta( $gallery_id, '_pfg_images', $images );
            }
        }
        
        if ( $images_updated > 0 ) {
            $this->log( "Updated {$images_updated} image filter association(s)" );
        }
    }

    /**
     * Restore from backup.
     *
     * @param string $backup_file Path to backup file.
     * @return bool
     */
    public function restore_backup( $backup_file ) {
        if ( ! file_exists( $backup_file ) ) {
            return false;
        }

        $backup_data = json_decode( file_get_contents( $backup_file ), true );

        if ( ! $backup_data || ! isset( $backup_data['galleries'] ) ) {
            return false;
        }

        // Restore filters
        if ( isset( $backup_data['filters'] ) ) {
            update_option( 'awl_portfolio_filter_gallery_categories', $backup_data['filters'] );
        }

        // Restore galleries
        foreach ( $backup_data['galleries'] as $gallery_id => $data ) {
            if ( isset( $data['settings'] ) ) {
                $legacy_key = 'awl_filter_gallery' . $gallery_id;
                update_post_meta( $gallery_id, $legacy_key, $data['settings'] );
            }

            // Remove migration marker
            delete_post_meta( $gallery_id, '_pfg_migrated' );
            delete_post_meta( $gallery_id, '_pfg_settings' );
            delete_post_meta( $gallery_id, '_pfg_images' );
        }

        // Reset version
        update_option( self::VERSION_OPTION, $backup_data['version'] );
        update_option( self::STATUS_OPTION, 'restored' );

        $this->log( 'Restored from backup: ' . $backup_file );

        return true;
    }

    /**
     * Get migration status.
     *
     * @return array
     */
    public function get_status() {
        $total = wp_count_posts( 'awl_filter_gallery' );
        $total_count = isset( $total->publish ) ? $total->publish : 0;

        $migrated = get_posts( array(
            'post_type'      => 'awl_filter_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to count galleries migrated to current version.
            'meta_query'     => array(
                array(
                    'key'   => '_pfg_migrated',
                    'value' => self::CURRENT_VERSION,
                ),
            ),
        ) );

        return array(
            'current_version' => get_option( self::VERSION_OPTION, '1.0.0' ),
            'target_version'  => self::CURRENT_VERSION,
            'status'          => get_option( self::STATUS_OPTION, 'pending' ),
            'total_galleries' => $total_count,
            'migrated'        => count( $migrated ),
            'last_backup'     => get_option( 'pfg_last_backup', '' ),
            'last_backup_date'=> get_option( 'pfg_last_backup_date', '' ),
        );
    }

    /**
     * Log migration events.
     *
     * @param string $message Log message.
     * @param string $level   Log level (info, error, warning).
     */
    protected function log( $message, $level = 'info' ) {
        $log = get_option( 'pfg_migration_log', array() );
        
        $log[] = array(
            'time'    => current_time( 'mysql' ),
            'level'   => $level,
            'message' => $message,
        );

        // Keep only last 100 entries
        if ( count( $log ) > 100 ) {
            $log = array_slice( $log, -100 );
        }

        update_option( 'pfg_migration_log', $log );
    }

    /**
     * Get migration log.
     *
     * @return array
     */
    public function get_log() {
        return get_option( 'pfg_migration_log', array() );
    }

    /**
     * Clear migration log.
     */
    public function clear_log() {
        delete_option( 'pfg_migration_log' );
    }
}
