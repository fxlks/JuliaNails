<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Catalog Booking" module output in frontend.
 */
class AmeliaCatalogBookingModule implements DependencyInterface
{
    /**
     * Register module.
     */
    public function load()
    {
        add_action('init', [AmeliaCatalogBookingModule::class, 'registerModule']);
    }

    /**
     * Register module.
     */
    public static function registerModule()
    {
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/CatalogBooking';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaCatalogBookingModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render module HTML output.
     */
    public static function renderCallback($attrs, $content, $block, $elements)
    {
        $shortcode = '[ameliacatalogbooking';

        $type_value = $attrs['type']['innerContent']['desktop']['value'] ?? '0';
        if ($type_value !== null && $type_value !== '0') {
            $shortcode .= ' show=' . $type_value;
        }

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $trigger_type = $attrs['trigger_type']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '' && $trigger_type !== null) {
            $shortcode .= ' trigger_type=' . $trigger_type;
        }

        $in_dialog = $attrs['in_dialog']['innerContent']['desktop']['value'] ?? false;
        if ($in_dialog === 'on') {
            $shortcode .= ' in_dialog=1';
        }

        $catalog_view = $attrs['catalog_view']['innerContent']['desktop']['value'] ?? '0';

        if ($catalog_view !== '0') {
            $category = $attrs['categories_catalog']['innerContent']['desktop']['value'] ?? [];
            $service  = $attrs['services_catalog']['innerContent']['desktop']['value'] ?? [];
            $package  = $attrs['packages_catalog']['innerContent']['desktop']['value'] ?? [];

            if ($category && count($category) > 0 && $catalog_view === 'category') {
                $shortcode .= ' category=' . implode(',', $category);
            } elseif ($service && count($service) > 0 && $catalog_view === 'service') {
                $shortcode .= ' service=' . implode(',', $service);
            } elseif ($package && count($package) > 0 && $catalog_view === 'package') {
                $shortcode .= ' package=' . implode(',', $package);
            }
        }

        $filter_params = $attrs['filter_params']['innerContent']['desktop']['value'] ?? false;
        if ($filter_params === 'on') {
            $employee = $attrs['employees']['innerContent']['desktop']['value'] ?? [];
            $location = $attrs['locations']['innerContent']['desktop']['value'] ?? [];

            if ($employee && count($employee) > 0) {
                $shortcode .= ' employee=' . implode(',', $employee);
            }
            if ($location && count($location) > 0) {
                $shortcode .= ' location=' . implode(',', $location);
            }

            $skip_categories = $attrs['skip_categories']['innerContent']['desktop']['value'] ?? false;
            if ($skip_categories === 'on') {
                $shortcode .= ' categories_hidden=1';
            }
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
