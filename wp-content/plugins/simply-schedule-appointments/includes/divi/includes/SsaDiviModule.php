<?php

class SSADM_SsaDiviModule extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'ssadm-ssa-divi-module';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name = 'ssa-divi-module';

	/**
	 * The extension's version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.2';

	/**
	 * SSADM_SsaDiviModule constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct( $name = 'ssa-divi-module', $args = array() ) {
		$this->plugin_dir     = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( $this->plugin_dir );

		parent::__construct( $name, $args );
	}
}

// Only instantiate for Divi 4 (not Divi 5)
// In Divi 5, this would cause duplicate entries in the migrator
if ( ! defined( 'ET_BUILDER_PRODUCT_VERSION' ) || version_compare( ET_BUILDER_PRODUCT_VERSION, '5.0', '<' ) ) {
	new SSADM_SsaDiviModule;
}
