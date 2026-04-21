<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Search" module output in frontend.
 */
class AmeliaSearchModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaSearchModule::class, 'registerModule']);
    }

    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/Search';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaSearchModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render module HTML output.
     */
    public static function renderCallback($attrs, $content, $block, $elements)
    {
        $shortcode = '[ameliasearch';

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $bookingParams = $attrs['booking_params']['innerContent']['desktop']['value'] ?? 'off';
        if ($bookingParams === 'on') {
            $showAll = $attrs['type']['innerContent']['desktop']['value'] ?? '0';
            if ($showAll !== null && $showAll !== '' && $showAll !== '0') {
                $shortcode .= ' show=' . $showAll;
            }
            $shortcode .= ' today=1';
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
