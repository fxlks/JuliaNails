<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Events" (legacy) module output in frontend.
 */
class AmeliaEventsModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaEventsModule::class, 'registerModule']);
    }

    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/Events';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaEventsModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render module HTML output.
     */
    public static function renderCallback($attrs, $content, $block, $elements)
    {
        $shortcode = '[ameliaevents';

        $type = $attrs['type']['innerContent']['desktop']['value'] ?? null;
        if ($type !== null && $type !== '') {
            $shortcode .= ' type=' . $type;
        } else {
            $shortcode .= ' type=list';
        }

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $bookingParams = $attrs['booking_params']['innerContent']['desktop']['value'] ?? 'off';
        if ($bookingParams === 'on') {
            $event = $attrs['events']['innerContent']['desktop']['value'] ?? '0';
            if ($event !== '0') {
                $shortcode .= ' event=' . $event;
            }

            $tag = $attrs['tags']['innerContent']['desktop']['value'] ?? '0';
            if ($tag !== null && $tag !== '' && $tag !== '0') {
                $shortcode .= ' tag="' . esc_attr($tag) . '"';
            }

            $recurring = $attrs['recurring']['innerContent']['desktop']['value'] ?? 'off';
            if ($recurring === 'on') {
                $shortcode .= ' recurring=1';
            }
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
