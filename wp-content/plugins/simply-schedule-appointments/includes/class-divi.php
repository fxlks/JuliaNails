<?php
/**
 * Simply Schedule Appointments Divi module.
 *
 * @since   3.7.6
 * @package Simply_Schedule_Appointments
 */

/**
 * Simply Schedule Appointments Divi module.
 *
 * Handles both Divi 4 and Divi 5 compatibility:
 * - Divi 4: Uses DiviExtension class
 * - Divi 5: Uses WordPress Block API with module.json
 *
 * @since 3.7.6
 */
class SSA_Divi {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.3
	 *
	 * @var   Simply_Schedule_Appointments
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.3
	 *
	 * @param  Simply_Schedule_Appointments $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Setup hooks if the builder is installed and activated.
	 */
	public function hooks() {
		// Check Divi version BEFORE adding any hooks so Divi's compatibility
		// check doesn't see Divi 4 hooks when we're running Divi 5
		$is_divi_5 = $this->is_divi_5();
		
		if ( $is_divi_5 ) {
			// Divi 5: Load server files IMMEDIATELY so the hook registration
			// is in $wp_filter when Divi's compatibility check runs
			$this->load_divi5_server_files();
			
			// Load Divi 5 module
			add_action( 'init', array( $this, 'maybe_load_divi5_module' ), 5 );
			
			// Enqueue scripts for Divi 5
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_divi5_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_divi5_scripts' ) );
		} else {
			// Divi 4: Only hook into Divi 4 hooks if NOT Divi 5
			add_action( 'divi_extensions_init', array( $this, 'maybe_load_divi4_module' ) );
		}
		
		// REST API for Visual Builder (both versions)
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Load Divi 5 server files for module registration.
	 * 
	 * @since 5.9.0
	 */
	public function load_divi5_server_files() {
		if ( ! $this->is_divi_5() ) {
			return;
		}
		
		$server_modules_file = __DIR__ . '/divi5/server/Modules.php';
		if ( file_exists( $server_modules_file ) ) {
			require_once $server_modules_file;
		}
	}

	/**
	 * Check if Divi 5 is active.
	 *
	 * @return bool True if Divi 5 is active, false otherwise.
	 */
	public function is_divi_5() {
		// Check active theme
		$theme = wp_get_theme();
		$theme_name = $theme->get( 'Name' );
		$theme_version = $theme->get( 'Version' );
		
		// Check if the active theme is Divi and version 5+
		if ( ( $theme_name === 'Divi' || $theme->get_template() === 'Divi' ) && version_compare( $theme_version, '5.0', '>=' ) ) {
			return true;
		}
		
		// Check if Divi 5 ModuleRegistration class exists
		if ( class_exists( 'ET\Builder\Packages\ModuleLibrary\ModuleRegistration' ) ) {
			return true;
		}

		// Also check ET_BUILDER_PRODUCT_VERSION constant (Divi 5 is version 5.x)
		if ( defined( 'ET_BUILDER_PRODUCT_VERSION' ) ) {
			$version = ET_BUILDER_PRODUCT_VERSION;
			// Check if version starts with 5
			if ( version_compare( $version, '5.0', '>=' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Loads Divi 4 module.
	 * 
	 * Only loads if Divi 5 is NOT active to prevent duplicate entries in migrator.
	 *
	 * @since 3.7.6
	 */
	public function maybe_load_divi4_module() {
		// Skip Divi 4 module if Divi 5 is active
		if ( $this->is_divi_5() ) {
			return;
		}
		
		require_once ( __DIR__ . '/divi/includes/SsaDiviModule.php' );
	}

	/**
	 * Loads Divi 5 module if Divi 5 IS active.
	 *
	 * @since 5.9.0
	 */
	public function maybe_load_divi5_module() {
		// Check if this is actually Divi 5
		if ( ! $this->is_divi_5() ) {
			return;
		}
		
		$this->load_divi5_module();
	}

	/**
	 * Loads Divi 5 module.
	 *
	 * @since 5.9.0
	 */
	public function load_divi5_module() {
		// The module class will be loaded and instantiated via server/Modules.php
		// when Divi fires the divi_module_library_modules_dependency_tree hook.
		// Divi's dependency management system will automatically call the load() method.
		// We don't need to do anything here - just keeping this method for potential future use.
	}

	/**
	 * Enqueue scripts for Divi 5 Visual Builder.
	 *
	 * @since 5.9.0
	 */
	public function enqueue_divi5_scripts() {
		$script_url = plugin_dir_url( __FILE__ ) . 'divi5/build/ssa-booking-module.js';
		$script_path = __DIR__ . '/divi5/build/ssa-booking-module.js';

		if ( file_exists( $script_path ) ) {
			wp_register_script(
				'ssa-divi5-booking-module',
				$script_url,
				array( 'react', 'react-dom', 'wp-blocks', 'wp-element', 'wp-components', 'wp-hooks', 'wp-i18n', 'divi-module-library' ),
				filemtime( $script_path ),
				true // Load in footer after dependencies
			);
			
			// Get appointment types
			$appointment_types = $this->get_appointment_types_for_js();
			
			// Localize appointment types data
			wp_localize_script(
				'ssa-divi5-booking-module',
				'ssaAppointmentTypes',
				$appointment_types
			);
			
			// Enqueue in block editor OR Visual Builder
			if ( is_admin() || ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) || isset( $_GET['et_fb'] ) ) {
				wp_enqueue_script( 'ssa-divi5-booking-module' );
			}
		}
	}

	/**
	 * Get appointment types for JavaScript.
	 *
	 * @since 5.9.0
	 * @return array Array of appointment types.
	 */
	private function get_appointment_types_for_js() {
		$types = array();
		
		// Add "All Types" option
		$types[] = array(
			'label' => 'All Types',
			'value' => '',
		);

		// Get appointment types from SSA
		if ( isset( $this->plugin->appointment_type_model ) ) {
			$appointment_types = $this->plugin->appointment_type_model->query( array(
				'status' => 'publish',
				'orderby' => 'title',
				'order' => 'ASC',
			) );

			foreach ( $appointment_types as $type ) {
				$types[] = array(
					'label' => $type['title'],
					'value' => (string) $type['id'],
				);
			}
		}

		return $types;
	}

	/**
	 * Register REST API routes for Divi 5.
	 *
	 * @since 5.9.0
	 */
	public function register_rest_routes() {
		register_rest_route( 'ssa/v1', '/render-shortcode', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'render_shortcode_callback' ),
			'permission_callback' => function() {
				return current_user_can( 'edit_posts' );
			},
		) );
	}

	/**
	 * Render shortcode callback for REST API.
	 *
	 * @since 5.9.0
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function render_shortcode_callback( $request ) {
		$shortcode = $request->get_param( 'shortcode' );
		
		if ( empty( $shortcode ) ) {
			return new WP_Error( 'no_shortcode', 'No shortcode provided', array( 'status' => 400 ) );
		}

		// Render the shortcode
		$html = do_shortcode( $shortcode );

		return rest_ensure_response( array(
			'html' => $html,
		) );
	}

}
