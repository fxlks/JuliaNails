<?php

/**
 * Divi 5 Amelia Modules Bootstrap
 *
 * This file loads and registers all Divi 5 Amelia modules.
 * phpcs:disable PSR1.Files.SideEffects
 */

namespace Divi5Amelia;

use AmeliaBooking\Infrastructure\Licence\Licence;

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

// Check if Divi 5 is active before loading modules
function is_divi_5_active()
{
    $theme = wp_get_theme();
    $is_divi_theme = 'Divi' === $theme->name || 'Divi' === $theme->parent_theme;

    return $is_divi_theme;
}

/**
 * Custom value expansion function for converting comma-separated strings to arrays
 * Used during Divi 4 to Divi 5 conversion for multi-select fields
 */
function ameliaConvertParams($value, $context = [])
{
    if (is_string($value) && $value !== '') {
        $items = array_map('trim', explode(',', $value));
        return array_filter($items, function ($item) {
            return $item !== '';
        });
    }

    if (is_array($value)) {
        return $value;
    }

    return [];
}

/**
 * Convert Divi 4 yes_no_button values to Divi 5 toggle string values
 * Keep as 'on'/'off' strings since that's what Divi Toggle component expects
 */
function ameliaConvertToggle($value, $context = [])
{
    // Handle empty/null/undefined - default to 'on' (Divi 4 defaults were 'on')
    if ($value === null || $value === '') {
        return 'on';
    }

    // Already correct string format
    if ($value === 'on' || $value === '1' || $value === 1 || $value === true || $value === 'true') {
        return 'on';
    }

    if ($value === 'off' || $value === '0' || $value === 0 || $value === false || $value === 'false') {
        return 'off';
    }

    // Default to 'on' for Customer Panel (matches D4 behavior)
    return 'on';
}

/**
 * Convert catalog items (categories/services/packages) and automatically set catalog_view
 *
 * @param mixed $value The value to convert (comma-separated string or array)
 * @param array $context Context information including field name and attributes
 * @return array Converted array of items
 */
function ameliaConvertCatalogItems($value, $context = [])
{
    $result = ameliaConvertParams($value, $context);

    // If items were selected, also update the catalog_view based on field name
    if (!empty($result) && isset($context['attrs']) && isset($context['fieldName'])) {
        // Determine catalog view type from field name
        $catalogViewMap = [
            'categories' => 'category',
            'services' => 'service',
            'packages' => 'package',
        ];

        $fieldName = $context['fieldName'];
        if (isset($catalogViewMap[$fieldName])) {
            if (!isset($context['attrs']['catalog_view'])) {
                $context['attrs']['catalog_view'] = [];
            }
            $context['attrs']['catalog_view']['innerContent'] = [
                'desktop' => ['value' => $catalogViewMap[$fieldName]]
            ];
        }
    }

    return $result;
}

/**
 * Register custom value expansion functions for Divi conversion system
 */
add_filter('divi.moduleLibrary.conversion.valueExpansionFunctionMap', function ($functionMap) {
    $functionMap['ameliaConvertParams'] = 'Divi5Amelia\ameliaConvertParams';
    $functionMap['ameliaConvertToggle'] = 'Divi5Amelia\ameliaConvertToggle';
    $functionMap['ameliaConvertCatalogItems'] = 'Divi5Amelia\ameliaConvertCatalogItems';

    return $functionMap;
});

if (is_divi_5_active()) {
    // Load Divi dependency interface before loading modules
    $divi_dependency_interface = get_template_directory() . '/includes/builder-5/server/Framework/DependencyManagement/Interfaces/DependencyInterface.php';

    if (!file_exists($divi_dependency_interface)) {
        return;
    }

    require_once $divi_dependency_interface;

    require_once __DIR__ . '/AmeliaStepBookingModule.php';
    require_once __DIR__ . '/AmeliaBookingModule.php';
    require_once __DIR__ . '/AmeliaCatalogBookingModule.php';
    require_once __DIR__ . '/AmeliaCatalogModule.php';
    require_once __DIR__ . '/AmeliaEventsListModule.php';
    require_once __DIR__ . '/AmeliaEventsModule.php';
    require_once __DIR__ . '/AmeliaSearchModule.php';

    if (Licence::$premium) {
        require_once __DIR__ . '/AmeliaEventsCalendarModule.php';
        require_once __DIR__ . '/AmeliaCustomerPanelModule.php';
        require_once __DIR__ . '/AmeliaEmployeePanelModule.php';
    }

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaStepBookingModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaBookingModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaCatalogBookingModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaCatalogModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaEventsListModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaEventsModule());
        }
    );

    add_action(
        'divi_module_library_modules_dependency_tree',
        function ($dependency_tree) {
            $dependency_tree->add_dependency(new AmeliaSearchModule());
        }
    );

    // Register premium modules only if license is premium
    if (Licence::$premium) {
        add_action(
            'divi_module_library_modules_dependency_tree',
            function ($dependency_tree) {
                $dependency_tree->add_dependency(new AmeliaEventsCalendarModule());
            }
        );

        add_action(
            'divi_module_library_modules_dependency_tree',
            function ($dependency_tree) {
                $dependency_tree->add_dependency(new AmeliaCustomerPanelModule());
            }
        );

        add_action(
            'divi_module_library_modules_dependency_tree',
            function ($dependency_tree) {
                $dependency_tree->add_dependency(new AmeliaEmployeePanelModule());
            }
        );
    }
}
