<?php
/**
 * SSA Booking Module for Divi 5
 *
 * @package Simply_Schedule_Appointments
 * @since TBD
 */

namespace SSA\Divi5\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use WP_Block;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * SsaBookingModule class.
 *
 * This class handles the registration and rendering of the SSA Booking module for Divi 5.
 * It implements the DependencyInterface so it can be loaded by Divi's dependency management system.
 */
class SsaBookingModule implements \ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface {

	/**
	 * Load the module.
	 *
	 * This function adds Divi 5 conversion support to the existing Gutenberg block.
	 * The block is already registered by SSA_Block_Booking, we just add conversion outline.
	 *
	 * @return void
	 */
	public function load(): void {
		$module_json_folder_path = __DIR__;

		add_action(
			'init',
			function() use ( $module_json_folder_path ) {
				// Process conversion outline to add SSA module to Divi's conversion map
				$conversion_outline_file = $module_json_folder_path . '/conversion-outline.json';
				$module_json_file = $module_json_folder_path . '/module.json';
				
				if ( file_exists( $conversion_outline_file ) && file_exists( $module_json_file ) ) {
					$metadata = json_decode( file_get_contents( $module_json_file ), true );
					if ( $metadata && isset( $metadata['name'] ) && class_exists( '\ET\Builder\Packages\ModuleLibrary\ModuleRegistration' ) ) {
						// Process the conversion outline to register in Divi's conversion map
						\ET\Builder\Packages\ModuleLibrary\ModuleRegistration::process_conversion_outline( $metadata, $conversion_outline_file );
					}
				}
				
				// Update the render callback for Divi 5 compatibility
				$registry = \WP_Block_Type_Registry::get_instance();
				if ( $registry->is_registered( 'ssa/booking' ) ) {
					$block = $registry->get_registered( 'ssa/booking' );
					$original_callback = $block->render_callback;
					
					// Wrap the original callback to handle Divi 5 rendering
					$block->render_callback = function( $attrs, $content = '', $block = null ) use ( $original_callback ) {
						return self::render_callback( $attrs, $content, $block, [], $original_callback );
					};
				}
			},
			20 // Run after SSA_Block_Booking registration (priority 10)
		);
	}

	/**
	 * Render callback for the SSA Booking module.
	 *
	 * This function is responsible for rendering the server-side HTML of the module on the frontend.
	 * Works for both Gutenberg and Divi 5 contexts.
	 *
	 * @since TBD
	 *
	 * @param array    $attrs   Block attributes that were saved by Divi Builder.
	 * @param string   $content The block's content.
	 * @param WP_Block $block   Parsed block object that is being rendered.
	 * @param array    $elements Module elements.
	 * @param callable $original_callback Original render callback from Gutenberg registration.
	 *
	 * @return string The HTML rendered output of the SSA Booking module.
	 */
	public static function render_callback( $attrs, $content = '', $block = null, $elements = [], $original_callback = null ) {
		// Get SSA plugin instance
		if ( ! function_exists( 'ssa' ) || ! isset( ssa()->shortcodes ) ) {
			return '';
		}

		// Check if this is a Divi 5 context
		$is_divi5 = $block && isset( $block->parsed_block['id'] ) && 
		            class_exists( 'ET\Builder\Packages\Module\Module' );

		// Extract appointment type from attributes (handle both formats)
		$appointment_type = '';
		
		// Check Divi 5 format first (most specific)
		if ( isset( $attrs['module']['innerContent']['appointmentType']['desktop']['value'] ) && 
		     ! empty( $attrs['module']['innerContent']['appointmentType']['desktop']['value'] ) ) {
			// Divi 5 format (responsive)
			$appointment_type = $attrs['module']['innerContent']['appointmentType']['desktop']['value'];
		} elseif ( isset( $attrs['module']['innerContent']['appointmentType'] ) ) {
			// Check if it's already a string value (not responsive format)
			$apt_value = $attrs['module']['innerContent']['appointmentType'];
			if ( is_string( $apt_value ) && ! empty( $apt_value ) ) {
				$appointment_type = $apt_value;
			}
		} elseif ( isset( $attrs['type'] ) && ! empty( $attrs['type'] ) ) {
			// Gutenberg format (only use if not empty)
			$appointment_type = $attrs['type'];
		}

		// Build shortcode attributes
		$shortcode_atts = array();
		if ( $appointment_type ) {
			$shortcode_atts['type'] = sanitize_text_field( $appointment_type );
		}

		// Get the booking form HTML
		$booking_html = ssa()->shortcodes->ssa_booking( $shortcode_atts );

		// If Divi 5 context, use Module::render()
		if ( $is_divi5 ) {
			$parent = \ET\Builder\FrontEnd\BlockParser\BlockParserStore::get_parent( 
				$block->parsed_block['id'], 
				$block->parsed_block['storeInstance'] 
			);
			$parent_attrs = $parent->attrs ?? [];

			return \ET\Builder\Packages\Module\Module::render(
				[
					'orderIndex'    => $block->parsed_block['orderIndex'],
					'storeInstance' => $block->parsed_block['storeInstance'],
					'id'            => $block->parsed_block['id'],
					'name'          => $block->block_type->name,
					'moduleCategory' => $block->block_type->category ?? 'module',
					'attrs'         => $attrs,
					'elements'      => $elements,
					'parentAttrs'   => $parent_attrs,
					'parentId'      => $parent->id ?? '',
					'parentName'    => $parent->blockName ?? '',
					'children'      => $booking_html,
					'childrenIds'   => [],
				]
			);
		}

		// Regular Gutenberg context
		return $booking_html;
	}
}
