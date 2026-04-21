<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Catalog" (legacy) module output in frontend.
 */
class AmeliaCatalogModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaCatalogModule::class, 'registerModule']);
    }

    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/Catalog';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaCatalogModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render module HTML output.
     * This converts old divi_catalog to ameliacatalogbooking shortcode.
     */
    public static function renderCallback($attrs, $content, $block, $elements)
    {
        $shortcode = '[ameliacatalogbooking';

        $catalogView = $attrs['catalog_view']['innerContent']['desktop']['value'] ?? '0';
        
        // Handle catalog view
        if ($catalogView && $catalogView !== '0') {
            // Map the categories/services/packages based on view
            if ($catalogView === 'category') {
                $categories = $attrs['categories_catalog']['innerContent']['desktop']['value'] ?? [];
                if ($categories && count($categories) > 0) {
                    $shortcode .= ' category=' . implode(',', $categories);
                }
            } elseif ($catalogView === 'service') {
                $services = $attrs['services_catalog']['innerContent']['desktop']['value'] ?? [];
                if ($services && count($services) > 0) {
                    $shortcode .= ' service=' . implode(',', $services);
                }
            } elseif ($catalogView === 'package') {
                $packages = $attrs['packages_catalog']['innerContent']['desktop']['value'] ?? [];
                if ($packages && count($packages) > 0) {
                    $shortcode .= ' package=' . implode(',', $packages);
                }
            }
        }

        // Type
        $type = $attrs['type']['innerContent']['desktop']['value'] ?? null;
        if ($type !== null && $type !== '0') {
            $shortcode .= ' show=' . $type;
        }

        // Trigger
        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
