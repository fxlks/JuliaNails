<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Customer Panel" module output in frontend.
 */
class AmeliaCustomerPanelModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaCustomerPanelModule::class, 'registerModule']);
    }

    /**
     * Register module.
     */
    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/CustomerPanel';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaCustomerPanelModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render callback for the module
     *
     * @param array $attrs Module attributes from Visual Builder
     * @return string Rendered HTML output
     */
    public static function renderCallback(array $attrs): string
    {
        $shortcode = '[ameliacustomerpanel version=2';

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? '';
        if ($trigger) {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $appointments = $attrs['appointments']['innerContent']['desktop']['value'] ?? true;
        if ($appointments === true || $appointments === 'true' || $appointments === 'on' || $appointments === 1 || $appointments === '1') {
            $shortcode .= ' appointments=1';
        }

        $events = $attrs['events']['innerContent']['desktop']['value'] ?? true;
        if ($events === true || $events === 'true' || $events === 'on' || $events === 1 || $events === '1') {
            $shortcode .= ' events=1';
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
