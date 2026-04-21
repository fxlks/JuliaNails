<?php

namespace Divi5Amelia;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

/**
 * Class that handle "Amelia Events Calendar" module output in frontend.
 */
class AmeliaEventsCalendarModule implements DependencyInterface
{
    /**
     * Register module.
     * DependencyInterface interface ensures class method name `load()` is executed for initialization.
     */
    public function load()
    {
        // Register module.
        add_action('init', [AmeliaEventsCalendarModule::class, 'registerModule']);
    }

    public static function registerModule()
    {
        // Path to module metadata that is shared between Frontend and Visual Builder.
        $module_json_folder_path = dirname(__DIR__, 1) . '/visual-builder/src/modules/EventsCalendar';

        ModuleRegistration::register_module(
            $module_json_folder_path,
            [
                'render_callback' => [AmeliaEventsCalendarModule::class, 'renderCallback'],
            ]
        );
    }

    /**
     * Render module HTML output.
     */
    public static function renderCallback($attrs, $content, $block, $elements)
    {
        $shortcode = '[ameliaeventscalendarbooking';

        $trigger = $attrs['trigger']['innerContent']['desktop']['value'] ?? null;
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger=' . esc_attr($trigger);
        }

        $trigger_type = $attrs['trigger_type']['innerContent']['desktop']['value'] ?? null;
        if ($trigger && $trigger_type !== null && $trigger_type !== '') {
            $shortcode .= ' trigger_type=' . $trigger_type;
        }

        $in_dialog = $attrs['in_dialog']['innerContent']['desktop']['value'] ?? false;
        if ($trigger && $in_dialog === 'on') {
            $shortcode .= ' in_dialog=1';
        }

        $booking_params = $attrs['booking_params']['innerContent']['desktop']['value'] ?? false;
        if ($booking_params === 'on') {
            $event = $attrs['events']['innerContent']['desktop']['value'] ?? [];
            if ($event && count($event) > 0) {
                $shortcode .= ' event=' . implode(',', $event);
            }

            $tag = $attrs['tags']['innerContent']['desktop']['value'] ?? [];
            if ($tag && count($tag) > 0) {
                $shortcode .= ' tag="' . implode(',', array_map(function ($t) {
                    return '{' . $t . '}';
                }, $tag)) . '"';
            }

            $recurring = $attrs['recurring']['innerContent']['desktop']['value'] ?? false;
            if ($recurring === 'on') {
                $shortcode .= ' recurring=1';
            }

            $locations = $attrs['locations']['innerContent']['desktop']['value'] ?? [];
            if ($locations && count($locations) > 0) {
                $shortcode .= ' location=' . implode(',', $locations);
            }
        }

        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}
