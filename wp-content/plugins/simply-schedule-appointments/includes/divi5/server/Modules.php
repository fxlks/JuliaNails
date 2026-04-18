<?php
/**
 * Register SSA Divi 5 Modules
 *
 * @package Simply_Schedule_Appointments
 * @since 6.9.21
 */

namespace SSA\Divi5\Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

// Register SSA module in Divi 5's dependency tree
// This makes SSA show as Divi 5 compatible
add_action(
	'divi_module_library_modules_dependency_tree',
	function( $dependency_tree ) {
		// Load the module class if not already loaded
		if ( ! class_exists( 'SSA\Divi5\Module\SsaBookingModule' ) ) {
			$module_class_path = dirname( __DIR__ ) . '/module/SsaBookingModule.php';
			if ( file_exists( $module_class_path ) ) {
				require_once $module_class_path;
			}
		}
		
		// Add SSA module to dependency tree
		if ( class_exists( 'SSA\Divi5\Module\SsaBookingModule' ) ) {
			$dependency_tree->add_dependency( new \SSA\Divi5\Module\SsaBookingModule() );
		}
	}
);
