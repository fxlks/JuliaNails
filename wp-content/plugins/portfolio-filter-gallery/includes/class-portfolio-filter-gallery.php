<?php
/**
 * The core plugin class.
 *
 * @package    Portfolio_Filter_Gallery
 * @subpackage Portfolio_Filter_Gallery/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The core plugin class that orchestrates all hooks and functionality.
 */
class Portfolio_Filter_Gallery {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @var PFG_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @var string
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->version     = defined( 'PFG_VERSION' ) ? PFG_VERSION : '2.0.0';
        $this->plugin_name = 'portfolio-filter-gallery';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->run_migration();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        // Core classes
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-loader.php';
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-i18n.php';
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-security.php';
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-features.php';

        // Data classes
        require_once PFG_PLUGIN_PATH . 'data/class-pfg-gallery.php';
        require_once PFG_PLUGIN_PATH . 'data/class-pfg-migrator.php';
        require_once PFG_PLUGIN_PATH . 'data/class-pfg-templates.php';

        // Admin classes
        require_once PFG_PLUGIN_PATH . 'admin/class-pfg-admin.php';
        require_once PFG_PLUGIN_PATH . 'admin/class-pfg-ajax-handler.php';

        // Public classes
        require_once PFG_PLUGIN_PATH . 'public/class-pfg-public.php';
        require_once PFG_PLUGIN_PATH . 'public/class-pfg-shortcode.php';
        require_once PFG_PLUGIN_PATH . 'public/class-pfg-public-ajax.php';
        require_once PFG_PLUGIN_PATH . 'public/class-pfg-renderer.php';

        // Integrations
        
        // Onboarding Tour
        require_once PFG_PLUGIN_PATH . 'includes/class-pfg-onboarding-tour.php';

        $this->loader = new PFG_Loader();
    }

    /**
     * Define the locale for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new PFG_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area.
     */
    private function define_admin_hooks() {
        $plugin_admin = new PFG_Admin( $this->get_plugin_name(), $this->get_version() );
        $ajax_handler = new PFG_Ajax_Handler();

        // Admin assets
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Post type and meta boxes
        $this->loader->add_action( 'init', $plugin_admin, 'register_post_type' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'save_post' );

        // Admin menus
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_pages' );

        // Custom columns
        $this->loader->add_filter( 'manage_awl_filter_gallery_posts_columns', $plugin_admin, 'add_shortcode_column' );
        $this->loader->add_action( 'manage_awl_filter_gallery_posts_custom_column', $plugin_admin, 'render_column_content', 10, 2 );

        // AJAX handlers
        $ajax_handler->register_actions();
        
        // Onboarding Tour
        PFG_Onboarding_Tour::init();
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new PFG_Public( $this->get_plugin_name(), $this->get_version() );
        $shortcode     = new PFG_Shortcode();
        $public_ajax   = new PFG_Public_Ajax();

        // Register public AJAX handlers
        $public_ajax->register_actions();

        // Public assets - loaded late to allow conditional loading
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 20 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 20 );

        // Script optimization
        $this->loader->add_filter( 'script_loader_tag', $plugin_public, 'add_async_defer', 10, 3 );

        // Preload hints
        $this->loader->add_action( 'wp_head', $plugin_public, 'add_preload_hints', 1 );

        // Register shortcodes
        $this->loader->add_action( 'init', $shortcode, 'register' );

        // Gutenberg block - DISABLED
        // Block registration was causing page creation interference and the block
        // lacks live preview, making it no more useful than a shortcode.
        // @see class-pfg-block.php for implementation if re-enabling
        /*
        require_once PFG_PLUGIN_PATH . 'blocks/class-pfg-block.php';
        $block = new PFG_Block();
        $block->init();
        */
    }

    /**
     * Run database migration if needed.
     */
    private function run_migration() {
        $migrator = new PFG_Migrator();
        $this->loader->add_action( 'admin_init', $migrator, 'maybe_migrate' );

        // Schedule continued migration
        $this->loader->add_action( 'pfg_continue_migration', $migrator, 'migrate_galleries' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin.
     *
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the loader class.
     *
     * @return PFG_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return string
     */
    public function get_version() {
        return $this->version;
    }
}
