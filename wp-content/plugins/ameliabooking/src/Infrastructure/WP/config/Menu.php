<?php

namespace AmeliaBooking\Infrastructure\WP\config;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Licence\Licence;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;

/**
 * Class Menu
 */
class Menu
{
    private SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        $defaultPageOnBackend = $this->settingsService->getSetting(
            'general',
            'defaultPageOnBackend'
        );

        $menuItems = [
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('dashboard'),
                'menuTitle'  => BackendStrings::get('dashboard'),
                'capability' => 'amelia_read_dashboard',
                'menuSlug'   => 'wpamelia-dashboard',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('calendar'),
                'menuTitle'  => BackendStrings::get('calendar'),
                'capability' => 'amelia_read_calendar',
                'menuSlug'   => 'wpamelia-calendar',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('bookings'),
                'menuTitle'  => BackendStrings::get('bookings'),
                'capability' => 'amelia_read_appointments',
                'menuSlug'   => 'wpamelia-bookings',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('events'),
                'menuTitle'  => BackendStrings::get('events'),
                'capability' => 'amelia_read_events',
                'menuSlug'   => 'wpamelia-events',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => Licence::getLicence() === 'Lite'
                    ? BackendStrings::get('employee')
                    : BackendStrings::get('employees'),
                'menuTitle'  => Licence::getLicence() === 'Lite'
                    ? BackendStrings::get('employee')
                    : BackendStrings::get('employees'),
                'capability' => 'amelia_read_employees',
                'menuSlug'   => 'wpamelia-employees',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('red_catalog'),
                'menuTitle'  => BackendStrings::get('red_catalog'),
                'capability' => 'amelia_read_services',
                'menuSlug'   => 'wpamelia-catalog',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('locations'),
                'menuTitle'  => BackendStrings::get('locations'),
                'capability' => 'amelia_read_locations',
                'menuSlug'   => 'wpamelia-locations',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('customers'),
                'menuTitle'  => BackendStrings::get('customers'),
                'capability' => 'amelia_read_customers',
                'menuSlug'   => 'wpamelia-customers',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('finance'),
                'menuTitle'  => BackendStrings::get('finance'),
                'capability' => 'amelia_read_finance',
                'menuSlug'   => 'wpamelia-finance',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('red_notifications'),
                'menuTitle'  => BackendStrings::get('red_notifications'),
                'capability' => 'amelia_read_notifications',
                'menuSlug'   => 'wpamelia-notifications',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('customize'),
                'menuTitle'  => BackendStrings::get('customize'),
                'capability' => 'amelia_read_customize',
                'menuSlug'   => 'wpamelia-customize',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('custom_fields_title'),
                'menuTitle'  => BackendStrings::get('custom_fields_title'),
                'capability' => 'amelia_read_custom_fields',
                'menuSlug'   => 'wpamelia-customfields',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('red_features_integrations'),
                'menuTitle'  => BackendStrings::get('red_features_integrations'),
                'capability' => 'amelia_read_settings',
                'menuSlug'   => 'wpamelia-features-integrations',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('settings'),
                'menuTitle'  => BackendStrings::get('settings'),
                'capability' => 'amelia_read_settings',
                'menuSlug'   => 'wpamelia-settings',
            ],
            [
                'parentSlug' => 'amelia',
                'pageTitle'  => BackendStrings::get('whats_new'),
                'menuTitle'  => BackendStrings::get('whats_new'),
                'capability' => 'amelia_read_whats_new',
                'menuSlug'   => 'wpamelia-whats-new',
            ],
        ];

        $defaultPageKey = array_search($defaultPageOnBackend, array_column($menuItems, 'pageTitle'), true);
        $defaultPagesElement = array_splice($menuItems, $defaultPageKey, 1);

        return array_merge(
            $defaultPagesElement,
            $menuItems,
        );
    }
}
