<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Employee Panel" module output in frontend.
 */
class AmeliaEmployeePanelModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaEmployeePanelModule::class, 'registerModule']);
    }

    /**
     * Register module.
     */
    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/EmployeePanel';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaEmployeePanelModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private static function checkValue($value): bool
    {
        return isset($value) && $value !== '';
    }

    /**
     * Render callback for the module
     *
     * @param array $attrs Module attributes from Visual Builder
     * @return string Rendered HTML output
     */
    public static function renderCallback(array $attrs): string
    {
        $shortcode = '[ameliaemployeepanel version=2';

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? '';
        if (self::checkValue($trigger)) {
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

        $profile = $attrs['profile']['innerContent']['desktop']['value'] ?? false;
        if ($profile === true || $profile === 'true' || $profile === 'on' || $profile === 1 || $profile === '1') {
            $shortcode .= ' profile-hidden=1';
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
