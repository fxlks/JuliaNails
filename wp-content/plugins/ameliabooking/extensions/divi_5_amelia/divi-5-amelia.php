<?php

/*
Plugin Name: Divi 5 Amelia Booking
Plugin URI:  https://wpamelia.com
Description: Divi 5 integration for Amelia Booking Plugin
Version:     1.0.0
Author:      Melograno Ventures
Author URI:  https://wpamelia.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
phpcs:disable PSR1.Files.SideEffects
*/

use AmeliaBooking\Infrastructure\Licence\Licence;
use AmeliaBooking\Infrastructure\WP\GutenbergBlock\GutenbergBlock;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

// Setup constants.
define('DIVI5_AMELIA_PATH', plugin_dir_path(__FILE__));
define('DIVI5_AMELIA_URL', plugin_dir_url(__FILE__));
define('DIVI5_AMELIA_VERSION', '1.0.0');


/**
 * Load Divi 5 modules on init
 * This ensures modules are registered for both Visual Builder and Frontend
 */
function divi5_amelia_load_modules()
{
    // Only load if Divi 5 is available
    if (function_exists('et_builder_d5_enabled') && et_builder_d5_enabled()) {
        // Load Divi 5 modules - this registers them for conversion and rendering
        require_once DIVI5_AMELIA_PATH . 'server/index.php';
    }
}

// Load modules early so they're available for conversion
add_action('after_setup_theme', 'divi5_amelia_load_modules', 20);

/**
 * Enqueue Divi 5 Visual Builder Assets
 */
function divi5_amelia_enqueue_visual_builder_assets()
{
    if (et_core_is_fb_enabled() && function_exists('et_builder_d5_enabled') && et_builder_d5_enabled()) {
        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            [
                'name'    => 'divi-5-amelia-visual-builder',
                'version' => DIVI5_AMELIA_VERSION,
                'script'  => [
                    'src'                => DIVI5_AMELIA_URL . 'visual-builder/build/divi-5-amelia.js',
                    'deps'               => [
                        'react',
                        'jquery',
                        'divi-module-library',
                        'wp-hooks',
                        'divi-rest',
                    ],
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ],
            ]
        );
    }
}

add_action('divi_visual_builder_assets_before_enqueue_scripts', 'divi5_amelia_enqueue_visual_builder_assets');

/**
 * Add Amelia data to Visual Builder window
 */
function divi5_amelia_add_inline_data()
{
    if (et_core_is_fb_enabled() && function_exists('et_builder_d5_enabled') && et_builder_d5_enabled()) {
        // Get Amelia entities data
        $ameliaData = GutenbergBlock::getEntitiesData();
        $entitiesData = isset($ameliaData['data']) ? $ameliaData['data'] : [];

        // Prepare options for the Visual Builder
        $ameliaOptions = [
            'categories' => [['value' => '0', 'label' => 'Show All Categories']],
            'services' => [['value' => '0', 'label' => 'Show All Services']],
            'employees' => [['value' => '0', 'label' => 'Show All Employees']],
            'locations' => [['value' => '0', 'label' => 'Show All Locations']],
            'packages' => [['value' => '0', 'label' => 'Show All Packages']],
            'events' => [['value' => '0', 'label' => 'Show All Events']],
            'tags' => [['value' => '0', 'label' => 'Show All Tags']],
        ];

        // Add categories
        if (!empty($entitiesData['categories'])) {
            foreach ($entitiesData['categories'] as $category) {
                $ameliaOptions['categories'][] = [
                    'value' => (string)$category['id'],
                    'label' => $category['name'] . ' (id: ' . $category['id'] . ')'
                ];
            }
        }

        // Add services
        if (!empty($entitiesData['servicesList'])) {
            foreach ($entitiesData['servicesList'] as $service) {
                if ($service) {
                    $ameliaOptions['services'][] = [
                        'value' => (string)$service['id'],
                        'label' => $service['name'] . ' (id: ' . $service['id'] . ')'
                    ];
                }
            }
        }

        // Add employees
        if (!empty($entitiesData['employees'])) {
            foreach ($entitiesData['employees'] as $employee) {
                $ameliaOptions['employees'][] = [
                    'value' => (string)$employee['id'],
                    'label' => $employee['firstName'] . ' ' . $employee['lastName'] . ' (id: ' . $employee['id'] . ')'
                ];
            }
        }

        // Add locations
        if (!empty($entitiesData['locations'])) {
            foreach ($entitiesData['locations'] as $location) {
                $ameliaOptions['locations'][] = [
                    'value' => (string)$location['id'],
                    'label' => $location['name'] . ' (id: ' . $location['id'] . ')'
                ];
            }
        }

        // Add packages
        if (!empty($entitiesData['packages'])) {
            foreach ($entitiesData['packages'] as $package) {
                $ameliaOptions['packages'][] = [
                    'value' => (string)$package['id'],
                    'label' => $package['name'] . ' (id: ' . $package['id'] . ')'
                ];
            }
        }

        // Add events
        if (!empty($entitiesData['events'])) {
            foreach ($entitiesData['events'] as $event) {
                $ameliaOptions['events'][] = [
                    'value' => (string)$event['id'],
                    'label' => $event['name'] . ' (id: ' . $event['id'] . ') - ' . $event['formattedPeriodStart']
                ];
            }
        }

        // Add tags
        if (!empty($entitiesData['tags'])) {
            foreach ($entitiesData['tags'] as $tag) {
                $ameliaOptions['tags'][] = [
                    'value' => $tag['name'],
                    'label' => $tag['name']
                ];
            }
        }

        // Output inline script that will run in the Visual Builder iframe
        ?>
        <script type="text/javascript">
            window.ameliaDivi5Data = <?php echo wp_json_encode($ameliaOptions); ?>;
            window.wpAmeliaLabels = <?php echo wp_json_encode(BackendStrings::getAllStrings()); ?>;
            window.isAmeliaLite = <?php echo wp_json_encode(!Licence::$premium); ?>;
            window.ameliaLicence = <?php echo wp_json_encode(Licence::getLicence()); ?>;
        </script>
        <?php
    }
}

add_action('et_fb_framework_loaded', 'divi5_amelia_add_inline_data');
