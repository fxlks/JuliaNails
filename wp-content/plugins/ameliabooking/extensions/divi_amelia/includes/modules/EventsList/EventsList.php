<?php

use AmeliaBooking\Infrastructure\WP\GutenbergBlock\GutenbergBlock;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

class DIVI_EventsList extends ET_Builder_Module
{

    public $slug       = 'divi_events_list_booking';
    public $vb_support = 'on';

    private $events = array();
    private $tags   = array();

    private $locations = array();


    protected $module_credits = array(
        'module_uri' => '',
        'author'     => '',
        'author_uri' => '',
    );

    public function init()
    {
        $this->name = esc_html__(BackendStrings::get('events_list_booking_divi'), 'divi-divi_amelia');

        if (!is_admin()) {
            return;
        }

        $data = GutenbergBlock::getEntitiesData()['data'];


        foreach ($data['events'] as $event) {
            $this->events[$event['id']] = $event['name'] . ' (id: ' . $event['id'] . ') - ' . $event['formattedPeriodStart'];
        }

        foreach ($data['tags'] as $tag) {
            $this->tags[$tag['name']] = $tag['name'] . ' (id: ' . $tag['id'] . ')';
        }

        foreach ($data['locations'] as $location) {
            $this->locations[$location['id']] = $location['name'];
        }
    }

    /**
     * Advanced Fields Config
     *
     * @return array
     */
    public function get_advanced_fields_config()
    {
        return array(
            'button' => false,
            'link_options' => false
        );
    }

    public function get_fields()
    {
        return array(
            'booking_params' => array(
                'label'           => esc_html__(BackendStrings::get('filter'), 'divi-divi_amelia'),
                'type'            => 'yes_no_button',
                'options' => array(
                    'on'  => esc_html__(BackendStrings::get('yes'), 'divi-divi_amelia'),
                    'off' => esc_html__(BackendStrings::get('no'), 'divi-divi_amelia'),
                ),
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
            ),
            'event_to_show' => array(
                'label'           => esc_html__(BackendStrings::get('event_time_scope'), 'divi-divi_amelia'),
                'type'            => 'select',
                'options'         => array(
                    'all'    => BackendStrings::get('all_events'),
                    'future' => BackendStrings::get('future_events'),
                    'past'   => BackendStrings::get('past_events'),
                    'custom' => BackendStrings::get('custom_range'),
                ),
                'default'         => 'all',
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                ),
            ),
            'start_date' => array(
                'label'           => esc_html__(BackendStrings::get('red_start_date'), 'divi-divi_amelia'),
                'type'            => 'amelia_date_picker',
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                    'event_to_show'  => 'custom',
                ),
            ),
            'end_date' => array(
                'label'           => esc_html__(BackendStrings::get('red_end_date'), 'divi-divi_amelia'),
                'type'            => 'amelia_date_picker',
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                    'event_to_show'  => 'custom',
                ),
            ),
            'events' => array(
                'label'           => esc_html__(BackendStrings::get('select_event'), 'divi-divi_amelia'),
                'type'            => 'amelia_multi_select',
                'showAllText'     => BackendStrings::get('show_all_events'),
                'options'         => $this->events,
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                ),
            ),
            'tags' => array(
                'label'           => esc_html__(BackendStrings::get('select_tag'), 'divi-divi_amelia'),
                'type'            => 'amelia_multi_select',
                'showAllText'     => BackendStrings::get('show_all_tags'),
                'toggle_slug'     => 'main_content',
                'options'         => $this->tags,
                'option_category' => 'basic_option',
                'brackets'        => true,
                'show_if'         => array(
                    'booking_params' => 'on',
                ),
            ),
            'recurring' => array(
                'label'           => esc_html__(BackendStrings::get('recurring_event'), 'divi-divi_amelia'),
                'type'            => 'yes_no_button',
                'options' => array(
                    'on'  => esc_html__(BackendStrings::get('yes'), 'divi-divi_amelia'),
                    'off' => esc_html__(BackendStrings::get('no'), 'divi-divi_amelia'),
                ),
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                ),
            ),
            'locations' => array(
                'label'           => esc_html__(BackendStrings::get('select_location'), 'divi-divi_amelia'),
                'type'            => 'amelia_multi_select',
                'showAllText'     => BackendStrings::get('show_all_locations'),
                'toggle_slug'     => 'main_content',
                'options'         => $this->locations,
                'option_category' => 'basic_option',
                'show_if'         => array(
                    'booking_params' => 'on',
                ),
            ),
            'trigger' => array(
                'label'           => esc_html__(BackendStrings::get('manually_loading'), 'divi-divi_amelia'),
                'type'            => 'text',
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
                'description'     => BackendStrings::get('manually_loading_description'),
            ),
            'trigger_type' => array(
                'label'           => esc_html__(BackendStrings::get('trigger_type'), 'divi-divi_amelia'),
                'type'            => 'select',
                'options'         => array(
                    'id' => BackendStrings::get('trigger_type_id'),
                    'class' => BackendStrings::get('trigger_type_class')
                ),
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
            ),
            'in_dialog' => array(
                'label'             => esc_html__(BackendStrings::get('in_dialog'), 'divi-divi_amelia'),
                'type'              => 'yes_no_button',
                'options'           => array(
                    'on'  => esc_html__(BackendStrings::get('yes'), 'divi-divi_amelia'),
                    'off' => esc_html__(BackendStrings::get('no'), 'divi-divi_amelia'),
                ),
                'toggle_slug'     => 'main_content',
                'option_category' => 'basic_option',
            ),
        );
    }

    public function checkValues($val)
    {
        if ($val !== null) {
            $val = explode(',', $val);
            if (is_array($val)) {
                $newVals = [];
                foreach ($val as $parameter) {
                    if ($parameter) {
                        $newVals[] = !is_numeric($parameter) ? (strpos($parameter, 'id:') ?  substr(explode('id: ', $parameter)[1], 0, -1) : $parameter) : $parameter;
                    }
                }
                return count($newVals) > 0 ? $newVals : [];
            }
            return [];
        }
        return [];
    }

    public function render($attrs, $content = null, $render_slug = null)
    {
        $preselect =  $this->props['booking_params'];
        $shortcode = '[ameliaeventslistbooking';
        $trigger   = $this->props['trigger'];
        $trigger_type = $this->props['trigger_type'];
        $in_dialog = $this->props['in_dialog'];
        if ($trigger !== null && $trigger !== '') {
            $shortcode .= ' trigger='.$trigger;
        }
        if (!empty($trigger) && !empty($trigger_type)) {
            $shortcode .= ' trigger_type='.$trigger_type;
        }
        if (!empty($trigger) && $in_dialog === 'on') {
            $shortcode .= ' in_dialog=1';
        }
        if ($preselect === 'on') {
            $event = $this->checkValues($this->props['events']);

            // Add event_to_show range logic (only if no specific event is selected)
            if (empty($event) && !empty($this->props['event_to_show']) && $this->props['event_to_show'] !== 'all') {
                if ($this->props['event_to_show'] === 'custom') {
                    $startDate = !empty($this->props['start_date']) ? $this->props['start_date'] : '';
                    $endDate = !empty($this->props['end_date']) ? $this->props['end_date'] : '';

                    $has_valid_start = $startDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate);
                    $has_valid_end = $endDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate);

                    if (!$has_valid_start || !$has_valid_end) {
                        return '';
                    }

                    $shortcode .= ' range="' . $startDate . ' - ' . $endDate . '"';
                } else {
                    $shortcode .= ' range="' . $this->props['event_to_show'] . '"';
                }
            }

            $tag   = $this->props['tags'];
            if ($event && count($event) > 0) {
                $shortcode .= ' event=' . implode(',', $event);
            }
            if ($tag) {
                $shortcode .= ' tag="' . $tag . '"';
            }
            $recurring = $this->props['recurring'];
            if ($recurring === 'on') {
                $shortcode .= ' recurring=1';
            }
            $locations = $this->checkValues($this->props['locations']);
            if ($locations && count($locations) > 0) {
                $shortcode .= ' location=' . implode(',', $locations);
            }
        }
        $shortcode .= ']';

        return do_shortcode($shortcode);
    }
}

new DIVI_EventsList;
