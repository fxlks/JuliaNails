<?php

namespace AmeliaBooking\Infrastructure\WP\SettingsService;

use AmeliaBooking\Application\Services\Location\AbstractCurrentLocation;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsStorageInterface;
use AmeliaBooking\Infrastructure\Licence;
use AmeliaBooking\Plugin;

/**
 * Class SettingsStorage
 *
 * @package AmeliaBooking\Infrastructure\WP\SettingsService
 */
class SettingsStorage implements SettingsStorageInterface
{
    /** @var array|mixed */
    private $settingsCache;

    /** @var AbstractCurrentLocation */
    private $locationService;

    private static $wpSettings = [
        'dateFormat'     => 'date_format',
        'timeFormat'     => 'time_format',
        'startOfWeek'    => 'start_of_week',
        'timeZoneString' => 'timezone_string',
        'gmtOffset'      => 'gmt_offset'
    ];

    public function __construct()
    {
        if (!defined('AMELIA_LOCALE')) {
            define('AMELIA_LOCALE', get_user_locale());
        }

        $this->locationService = Licence\ApplicationService::getCurrentLocationService();

        $this->settingsCache = self::getSavedSettings();

        Licence\DataModifier::modifySettings($this->settingsCache);

        foreach (self::$wpSettings as $ameliaSetting => $wpSetting) {
            $this->settingsCache['wordpress'][$ameliaSetting] = get_option($wpSetting);
        }

        $this->settingsCache['wordpress']['locale'] = AMELIA_LOCALE;

        DateTimeService::setTimeZone($this->getAllSettings());
    }

    /**
     * @return array
     */
    private function getSavedSettings()
    {
        return json_decode(get_option('amelia_settings'), true);
    }

    /**
     * @param $settingCategoryKey
     * @param $settingKey
     *
     * @return mixed
     */
    public function getSetting($settingCategoryKey, $settingKey)
    {
        return $this->settingsCache[$settingCategoryKey][$settingKey] ?? null;
    }

    /**
     * @param $settingCategoryKey
     *
     * @return mixed
     */
    public function getCategorySettings($settingCategoryKey)
    {
        return $this->settingsCache[$settingCategoryKey] ?? null;
    }

    /**
     * @return array|mixed|null
     */
    public function getAllSettings()
    {
        $settings = [];

        if (null !== $this->settingsCache) {
            foreach ((array)$this->settingsCache as $settingsCategoryName => $settingsCategory) {
                if ($settingsCategoryName !== 'daysOff') {
                    foreach ((array)$settingsCategory as $settingName => $settingValue) {
                        $settings[$settingName] = $settingValue;
                    }
                }
            }

            return $settings;
        }

        return null;
    }

    /**
     * @return array|mixed|null
     */
    public function getAllSettingsCategorized()
    {
        return $this->settingsCache ?? null;
    }

    /**
     * Return settings for frontend
     *
     * @return array|mixed
     */
    public function getFrontendSettings()
    {
        $phoneCountryCode = $this->getSetting('general', 'phoneDefaultCountryCode');
        $ipLocateApyKey   = $this->getSetting('general', 'ipLocateApiKey');

        $capabilities           = [];
        $additionalCapabilities = [];
        if (is_admin()) {
            $currentScreenId = get_current_screen()->id;
            $currentScreen   = substr($currentScreenId, strrpos($currentScreenId, '-') + 1);

            $capabilities = [
                'canRead'        => current_user_can('amelia_read_' . $currentScreen),
                'canReadOthers'  => current_user_can('amelia_read_others_' . $currentScreen),
                'canWrite'       => current_user_can('amelia_write_' . $currentScreen),
                'canWriteOthers' => current_user_can('amelia_write_others_' . $currentScreen),
                'canDelete'      => current_user_can('amelia_delete_' . $currentScreen),
                'canWriteStatus' => current_user_can('amelia_write_status_' . $currentScreen),
            ];

            $additionalCapabilities = [
                'canWriteCustomers' => current_user_can('amelia_write_customers'),
            ];
        }

        $wpUser = wp_get_current_user();

        $userType = 'customer';

        if (in_array('administrator', $wpUser->roles, true) || is_super_admin($wpUser->ID)) {
            $userType = 'admin';
        } elseif (in_array('wpamelia-manager', $wpUser->roles, true)) {
            $userType = 'manager';
        } elseif (in_array('wpamelia-provider', $wpUser->roles, true)) {
            $userType = 'provider';
        }

        return [
            'capabilities'           => $capabilities,
            'additionalCapabilities' => $additionalCapabilities,
            'daysOff'                => $this->getCategorySettings('daysOff'),
            'general'                => [
                'itemsPerPage'                              => $this->getSetting('general', 'itemsPerPage'),
                'appointmentsPerPage'                       => $this->getSetting('general', 'appointmentsPerPage'),
                'eventsPerPage'                             => $this->getSetting('general', 'eventsPerPage'),
                'servicesPerPage'                           => $this->getSetting('general', 'servicesPerPage'),
                'customersFilterLimit'                      => $this->getSetting('general', 'customersFilterLimit'),
                'eventsFilterLimit'                         => $this->getSetting(
                    'general',
                    'eventsFilterLimit'
                ) ?: 1000,
                'calendarEmployeesPreselected'              => $this->getSetting(
                    'general',
                    'calendarEmployeesPreselected'
                ),
                'phoneDefaultCountryCode'                   => $phoneCountryCode === 'auto' ?
                    $this->locationService->getCurrentLocationCountryIso($ipLocateApyKey) : $phoneCountryCode,
                'timeSlotLength'                            => $this->getSetting('general', 'timeSlotLength'),
                'serviceDurationAsSlot'                     => $this->getSetting('general', 'serviceDurationAsSlot'),
                'defaultAppointmentStatus'                  => $this->getSetting('general', 'defaultAppointmentStatus'),
                'gMapApiKey'                                => !empty($this->getSetting('general', 'gMapApiKey')),
                'googleClientId'                            => $this->getSetting('googleCalendar', 'clientID'),
                'googleAccessToken'                         => !empty($this->getSetting('googleCalendar', 'accessToken')),
                'googleAccountData'                         => $this->getSetting('googleCalendar', 'googleAccountData'),
                'outlookAccountData'                        => $this->getSetting('outlookCalendar', 'outlookAccountData'),
                'addToCalendar'                             => $this->getSetting('general', 'addToCalendar'),
                'requiredPhoneNumberField'                  => $this->getSetting('general', 'requiredPhoneNumberField'),
                'requiredEmailField'                        => $this->getSetting('general', 'requiredEmailField'),
                'numberOfDaysAvailableForBooking'           => $this->getSetting(
                    'general',
                    'numberOfDaysAvailableForBooking'
                ),
                'minimumTimeRequirementPriorToBooking'      =>
                $this->getSetting('general', 'minimumTimeRequirementPriorToBooking'),
                'minimumTimeRequirementPriorToCanceling'    =>
                $this->getSetting('general', 'minimumTimeRequirementPriorToCanceling'),
                'minimumTimeRequirementPriorToRescheduling' =>
                $this->getSetting('general', 'minimumTimeRequirementPriorToRescheduling'),
                'showClientTimeZone'                        => $this->getSetting(
                    'general',
                    'showClientTimeZone'
                ),
                'redirectUrlAfterAppointment'               => $this->getSetting(
                    'general',
                    'redirectUrlAfterAppointment'
                ),
                'customFieldsUploadsPath'                   => $this->getSetting('general', 'customFieldsUploadsPath'),
                'customFieldsAllowedExtensions'             => $this->getSetting(
                    'general',
                    'customFieldsAllowedExtensions'
                ),
                'runInstantPostBookingActions'              => $this->getSetting(
                    'general',
                    'runInstantPostBookingActions'
                ),
                'sortingPackages'                           => $this->getSetting('general', 'sortingPackages'),
                'backLink'                                  => $this->getSetting('general', 'backLink'),
                'sortingServices'                           => $this->getSetting('general', 'sortingServices'),
                'googleRecaptcha'                           => Licence\Licence::isFeatureEnabledWithLicense(
                    'recaptcha',
                    $this->getSetting('featuresIntegrations', 'recaptcha')
                ) &&
                $this->getSetting('general', 'googleRecaptcha')['siteKey'] &&
                $this->getSetting('general', 'googleRecaptcha')['secret'] ? [
                    'enabled'   => true,
                    'invisible' => $this->getSetting('general', 'googleRecaptcha')['invisible'],
                    'siteKey'   => $this->getSetting('general', 'googleRecaptcha')['siteKey'],
                ] : [
                    'enabled'   => false,
                    'invisible' => true,
                    'siteKey'   => '',
                ],
                'usedLanguages'                             => $this->getSetting('general', 'usedLanguages'),
            ],
            'googleMeet'             => [
                'enabled' => $this->getSetting('googleCalendar', 'enableGoogleMeet'),
            ],
            'microsoftTeams'         => [
                'enabled' => $this->getSetting('outlookCalendar', 'enableMicrosoftTeams'),
            ],
            'googleCalendar'         => [
                'enabled'           =>
                (
                    (
                        $this->getSetting('googleCalendar', 'clientID') &&
                        $this->getSetting('googleCalendar', 'clientSecret')
                    ) ||
                    !empty($this->getSetting('googleCalendar', 'accessToken')) &&
                    $this->getSetting('googleCalendar', 'accessToken') !== 'null'
                ) &&
                Licence\Licence::isFeatureEnabledWithLicense(
                    'googleCalendar',
                    $this->getSetting('featuresIntegrations', 'googleCalendar')
                ),
                'googleMeetEnabled' => $this->getSetting('googleCalendar', 'enableGoogleMeet'),
                'accessToken' => !empty($this->getSetting('googleCalendar', 'accessToken')),
            ],
            'outlookCalendar'        => [
                'enabled'               =>
                    (
                        (
                            $this->getSetting('outlookCalendar', 'clientID') &&
                            $this->getSetting('outlookCalendar', 'clientSecret')
                        ) ||
                        !empty($this->getSetting('outlookCalendar', 'accessToken')) &&
                        $this->getSetting('outlookCalendar', 'accessToken') !== 'null'
                    ) &&
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'outlookCalendar',
                        $this->getSetting('featuresIntegrations', 'outlookCalendar')
                    ),
                'microsoftTeamsEnabled' => $this->getSetting('outlookCalendar', 'enableMicrosoftTeams'),
                'accessToken' => !empty($this->getSetting('outlookCalendar', 'accessToken')),
            ],
            'appleCalendar'          =>
            $this->getSetting('appleCalendar', 'clientID') && $this->getSetting('appleCalendar', 'clientSecret'),
            'zoom'                   => [
                'enabled' => (
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'zoom',
                        $this->getSetting('featuresIntegrations', 'zoom')
                    ) &&
                    $this->getSetting('zoom', 'accountId') &&
                    $this->getSetting('zoom', 'clientId') &&
                    $this->getSetting('zoom', 'clientSecret')
                )
            ],
            'facebookPixel'          => Licence\Licence::isFeatureEnabledWithLicense(
                'facebookPixel',
                $this->getSetting('featuresIntegrations', 'facebookPixel')
            )
                ? $this->getCategorySettings('facebookPixel')
                : array_merge(
                    $this->getCategorySettings('facebookPixel') ?: [],
                    ['id' => '']
                ),
            'googleAnalytics'          => Licence\Licence::isFeatureEnabledWithLicense(
                'googleAnalytics',
                $this->getSetting('featuresIntegrations', 'googleAnalytics')
            )
                ? $this->getCategorySettings('googleAnalytics')
                : array_merge(
                    $this->getCategorySettings('googleAnalytics') ?: [],
                    ['id' => '']
                ),
            'googleTag'          => Licence\Licence::isFeatureEnabledWithLicense(
                'googleTag',
                $this->getSetting('featuresIntegrations', 'googleTag')
            )
                ? $this->getCategorySettings('googleTag')
                : array_merge(
                    $this->getCategorySettings('googleTag') ?: [],
                    ['id' => '']
                ),
            'mailchimp'              => [
                'subscribeFieldVisible' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'mailchimp',
                        $this->getSetting('featuresIntegrations', 'mailchimp')
                    ) &&
                    !empty($this->getSetting('mailchimp', 'accessToken')) &&
                    !empty($this->getSetting('mailchimp', 'list')) &&
                    !empty($this->getSetting('mailchimp', 'server')),
                'checkedByDefault'      => $this->getSetting('mailchimp', 'checkedByDefault'),
            ],
            'lessonSpace'            => [
                'enabled' => Licence\Licence::isFeatureEnabledWithLicense(
                    'lessonSpace',
                    $this->getSetting('featuresIntegrations', 'lessonSpace')
                ) && $this->getSetting('lessonSpace', 'apiKey')
            ],
            'socialLogin'            => [
                'googleLoginEnabled'         => Licence\Licence::isFeatureEnabledWithLicense(
                    'googleSocialLogin',
                    $this->getSetting('featuresIntegrations', 'googleSocialLogin')
                ),
                'facebookLoginEnabled'       => Licence\Licence::isFeatureEnabledWithLicense(
                    'facebookSocialLogin',
                    $this->getSetting('featuresIntegrations', 'facebookSocialLogin')
                ),
                'facebookAppId'              => $this->getSetting('socialLogin', 'facebookAppId'),
                'facebookCredentialsEnabled' => $this->getSetting('socialLogin', 'facebookAppId') &&
                    $this->getSetting('socialLogin', 'facebookAppSecret'),
            ],
            'notifications'          => [
                'senderName'          => $this->getSetting('notifications', 'senderName'),
                'replyTo'             => $this->getSetting('notifications', 'replyTo'),
                'senderEmail'         => $this->getSetting('notifications', 'senderEmail'),
                'invoiceFormat'       => $this->getSetting('notifications', 'invoiceFormat'),
                'sendAllCF'           => $this->getSetting('notifications', 'sendAllCF'),
                'cancelSuccessUrl'    => $this->getSetting('notifications', 'cancelSuccessUrl'),
                'cancelErrorUrl'      => $this->getSetting('notifications', 'cancelErrorUrl'),
                'approveSuccessUrl'   => $this->getSetting('notifications', 'approveSuccessUrl'),
                'approveErrorUrl'     => $this->getSetting('notifications', 'approveErrorUrl'),
                'rejectSuccessUrl'    => $this->getSetting('notifications', 'rejectSuccessUrl'),
                'rejectErrorUrl'      => $this->getSetting('notifications', 'rejectErrorUrl'),
                'smsSignedIn'         => $this->getSetting('notifications', 'smsSignedIn'),
                'bccEmail'            => $this->getSetting('notifications', 'bccEmail'),
                'bccSms'              => $this->getSetting('notifications', 'bccSms'),
                'smsBalanceEmail'     => $this->getSetting('notifications', 'smsBalanceEmail'),
                'whatsAppEnabled'     => Licence\Licence::isFeatureEnabledWithLicense(
                    'whatsapp',
                    $this->getSetting('featuresIntegrations', 'whatsapp')
                ) &&
                !empty($this->getSetting('notifications', 'whatsAppPhoneID')) &&
                !empty($this->getSetting('notifications', 'whatsAppAccessToken')) &&
                !empty($this->getSetting('notifications', 'whatsAppBusinessID')),
            ],
            'payments'               => [
                'currency'                   => $this->getSetting('payments', 'symbol'),
                'currencyCode'               => $this->getSetting('payments', 'currency'),
                'priceSymbolPosition'        => $this->getSetting('payments', 'priceSymbolPosition'),
                'priceNumberOfDecimals'      => $this->getSetting('payments', 'priceNumberOfDecimals'),
                'priceSeparator'             => $this->getSetting('payments', 'priceSeparator'),
                'hideCurrencySymbolFrontend' => $this->getSetting('payments', 'hideCurrencySymbolFrontend'),
                'defaultPaymentMethod'       => $this->getSetting('payments', 'defaultPaymentMethod'),
                'onSite'                     => $this->getSetting('payments', 'onSite'),
                'couponsCaseInsensitive'     => $this->getSetting('payments', 'couponsCaseInsensitive'),
                'coupons'                    => Licence\Licence::isFeatureEnabledWithLicense(
                    'coupons',
                    $this->getSetting('featuresIntegrations', 'coupons')
                ),
                'taxes'                      => array_merge(
                    $this->getSetting('payments', 'taxes'),
                    [
                        'enabled' => Licence\Licence::isFeatureEnabledWithLicense(
                            'tax',
                            $this->getSetting('featuresIntegrations', 'tax')
                        )
                    ]
                ),
                'cart'                       => Licence\Licence::isFeatureEnabledWithLicense(
                    'cart',
                    $this->getSetting('featuresIntegrations', 'cart')
                ),
                'paymentLinks'               => [
                    'enabled'             => $this->getSetting('payments', 'paymentLinks')['enabled'],
                    'changeBookingStatus' => $this->getSetting('payments', 'paymentLinks')['changeBookingStatus'],
                    'redirectUrl'         => $this->getSetting('payments', 'paymentLinks')['redirectUrl']
                ],
                'payPal'                     => [
                    'enabled'         => $this->getSetting('payments', 'payPal')['enabled'],
                    'sandboxMode'     => $this->getSetting('payments', 'payPal')['sandboxMode'],
                    'testApiClientId' => $this->getSetting('payments', 'payPal')['testApiClientId'],
                    'liveApiClientId' => $this->getSetting('payments', 'payPal')['liveApiClientId'],
                ],
                'stripe'                     => [
                    'enabled'            => $this->getSetting('payments', 'stripe')['enabled'],
                    'testMode'           => $this->getSetting('payments', 'stripe')['testMode'],
                    'livePublishableKey' => $this->getSetting('payments', 'stripe')['livePublishableKey'],
                    'testPublishableKey' => $this->getSetting('payments', 'stripe')['testPublishableKey'],
                    'connect'            => $this->getSetting('payments', 'stripe')['connect'],
                    'address'            => $this->getSetting('payments', 'stripe')['address'],
                ],
                'wc'                         => [
                    'enabled'      => $this->getSetting('payments', 'wc')['enabled'],
                    'productId'    => $this->getSetting('payments', 'wc')['productId'],
                    'page'         => $this->getSetting('payments', 'wc')['page'],
                    'onSiteIfFree' => $this->getSetting('payments', 'wc')['onSiteIfFree']
                ],
                'mollie'                     => [
                    'enabled'       => $this->getSetting('payments', 'mollie')['enabled'],
                    'cancelBooking' => $this->getSetting('payments', 'mollie')['cancelBooking'],
                ],
                'square'                     => [
                    'enabled'        => $this->getSetting('payments', 'square')['enabled'],
                    'countryCode'    => $this->getSetting('payments', 'square')['countryCode'],
                    'clientLiveId'   => $this->getSetting('payments', 'square')['clientLiveId'],
                    'clientTestId'   => $this->getSetting('payments', 'square')['clientTestId'],
                    'testMode'       => $this->getSetting('payments', 'square')['testMode'],
                    'locationId'     => $this->getSetting('payments', 'square')['locationId']
                ],
                'razorpay'                   => [
                    'enabled' => $this->getSetting('payments', 'razorpay')['enabled'],
                ],
                'barion'                     => [
                    'enabled'       => $this->getSetting('payments', 'barion')['enabled'],
                ],
            ],
            'role'                   => $userType,
            'weekSchedule'           => $this->getCategorySettings('weekSchedule'),
            'wordpress'              => [
                'dateFormat'  => $this->getSetting('wordpress', 'dateFormat'),
                'timeFormat'  => $this->getSetting('wordpress', 'timeFormat'),
                'startOfWeek' => (int)$this->getSetting('wordpress', 'startOfWeek'),
                'timezone'    => $this->getSetting('wordpress', 'timeZoneString'),
                'locale'      => AMELIA_LOCALE
            ],
            'labels'                 => [
                'enabled' => $this->getSetting('labels', 'enabled')
            ],
            'activation'             => [
                'showAmeliaSurvey'              => $this->getSetting('activation', 'showAmeliaSurvey'),
                'showAmeliaPromoCustomizePopup' => $this->getSetting('activation', 'showAmeliaPromoCustomizePopup'),
                'showActivationSettings'        => $this->getSetting('activation', 'showActivationSettings'),
                'stash'                         => $this->getSetting('activation', 'stash'),
                'disableUrlParams'              => $this->getSetting('activation', 'disableUrlParams'),
                'isNewInstallation'             => $this->getSetting('activation', 'isNewInstallation'),
                'hideUnavailableFeatures'       => $this->getSetting('activation', 'hideUnavailableFeatures'),
                'licence'                       => $this->getSetting('activation', 'licence'),
                'premiumBannerVisibility'       => $this->getSetting('activation', 'premiumBannerVisibility'),
                'dismissibleBannerVisibility'   => $this->getSetting('activation', 'dismissibleBannerVisibility'),
            ],
            'roles'                  => [
                'allowAdminBookAtAnyTime'     => $this->getSetting('roles', 'allowAdminBookAtAnyTime'),
                'allowAdminBookOverApp'       => $this->getSetting('roles', 'allowAdminBookOverApp'),
                'adminServiceDurationAsSlot'  => $this->getSetting('roles', 'adminServiceDurationAsSlot'),
                'allowConfigureSchedule'      => $this->getSetting('roles', 'allowConfigureSchedule'),
                'allowConfigureDaysOff'       => $this->getSetting('roles', 'allowConfigureDaysOff'),
                'allowConfigureSpecialDays'   => $this->getSetting('roles', 'allowConfigureSpecialDays'),
                'allowConfigureServices'      => $this->getSetting('roles', 'allowConfigureServices'),
                'allowWriteAppointments'      => $this->getSetting('roles', 'allowWriteAppointments'),
                'allowWriteCustomers'         => $this->getSetting('roles', 'allowWriteCustomers'),
                'automaticallyCreateCustomer' => $this->getSetting('roles', 'automaticallyCreateCustomer'),
                'inspectCustomerInfo'         => $this->getSetting('roles', 'inspectCustomerInfo'),
                'allowCustomerReschedule'     => $this->getSetting('roles', 'allowCustomerReschedule'),
                'allowCustomerCancelPackages' => $this->getSetting('roles', 'allowCustomerCancelPackages'),
                'allowCustomerDeleteProfile'  => $this->getSetting('roles', 'allowCustomerDeleteProfile'),
                'allowWriteEvents'            => $this->getSetting('roles', 'allowWriteEvents'),
                'customerCabinet'             => [
                    'loginEnabled'    => $this->getSetting('roles', 'customerCabinet')['loginEnabled'],
                    'tokenValidTime'  => $this->getSetting('roles', 'customerCabinet')['tokenValidTime'],
                    'pageUrl'         => $this->getSetting('roles', 'customerCabinet')['pageUrl'],
                    'googleRecaptcha' => Licence\Licence::isFeatureEnabledWithLicense(
                        'recaptcha',
                        $this->getSetting('featuresIntegrations', 'recaptcha')
                    ) &&
                        $this->getSetting('roles', 'customerCabinet')['googleRecaptcha'] &&
                        $this->getSetting('general', 'googleRecaptcha')['siteKey'] &&
                        $this->getSetting('general', 'googleRecaptcha')['secret'],
                ],
                'providerCabinet'             => [
                    'loginEnabled'    => $this->getSetting('roles', 'providerCabinet')['loginEnabled'],
                    'tokenValidTime'  => $this->getSetting('roles', 'providerCabinet')['tokenValidTime'],
                    'googleRecaptcha' => Licence\Licence::isFeatureEnabledWithLicense(
                        'recaptcha',
                        $this->getSetting('featuresIntegrations', 'recaptcha')
                    ) &&
                        $this->getSetting('roles', 'providerCabinet')['googleRecaptcha'] &&
                        $this->getSetting('general', 'googleRecaptcha')['siteKey'] &&
                        $this->getSetting('general', 'googleRecaptcha')['secret'],
                ],
                'providerBadges'              => Licence\Licence::isFeatureEnabledWithLicense(
                    'employeeBadge',
                    $this->getSetting('featuresIntegrations', 'employeeBadge')
                ) ? $this->getSetting('roles', 'providerBadges') : [],
                'limitPerCustomerService'     => $this->getSetting('roles', 'limitPerCustomerService'),
                'limitPerCustomerPackage'     => $this->getSetting('roles', 'limitPerCustomerPackage'),
                'limitPerCustomerEvent'       => $this->getSetting('roles', 'limitPerCustomerEvent'),
                'limitPerEmployee'            => $this->getSetting('roles', 'limitPerEmployee'),
            ],
            'customization'          => $this->getCategorySettings('customization'),
            'customizedData'         => $this->getCategorySettings('customizedData'),
            'appointments'           => $this->getCategorySettings('appointments'),
            'slotDateConstraints'    => [
                'minDate' => DateTimeService::getNowDateTimeObject()
                    ->modify(
                        "+{$this->getSetting('general', 'minimumTimeRequirementPriorToBooking')} seconds"
                    )
                    ->format('Y-m-d H:i:s'),
                'maxDate' => DateTimeService::getNowDateTimeObject()
                    ->modify(
                        "+{$this->getSetting('general', 'numberOfDaysAvailableForBooking')} day"
                    )
                    ->format('Y-m-d H:i:s')
            ],
            'company'                => [
                'email' => $this->getSetting('company', 'email'),
                'phone' => $this->getSetting('company', 'phone'),
            ],
            'pageColumnSettings'     => $this->getCategorySettings('pageColumnSettings'),
            'featuresIntegrations'   => Licence\Licence::filterFeaturesByLicense(
                $this->getCategorySettings('featuresIntegrations')
            ),
        ];
    }

    public function getBackendSettings()
    {
        $capabilities = [];

        if (is_admin()) {
            $entities = [
                'appointments',
                'events',
                'customers',
                'employees',
                'services',
                'packages',
                'resources',
                'finance',
                'coupons',
                'taxes',
                'locations',
                'custom_fields',
                'notifications',
                'settings',
            ];

            foreach ($entities as $entity) {
                $capabilities = array_merge(
                    $capabilities,
                    [
                        'canRead' . ucfirst($entity)        => current_user_can('amelia_read_' . $entity),
                        'canReadOthers' . ucfirst($entity)  => current_user_can('amelia_read_others_' . $entity),
                        'canWrite' . ucfirst($entity)       => current_user_can('amelia_write_' . $entity),
                        'canWriteOthers' . ucfirst($entity) => current_user_can('amelia_write_others_' . $entity),
                        'canDelete' . ucfirst($entity)      => current_user_can('amelia_delete_' . $entity),
                        'canWriteStatus' . ucfirst($entity) => current_user_can('amelia_write_status_' . $entity),
                    ]
                );
            }
        }

        $phoneCountryCode = $this->getSetting('general', 'phoneDefaultCountryCode');
        $ipLocateApyKey   = $this->getSetting('general', 'ipLocateApiKey');

        $wpUser = wp_get_current_user();

        $userType = 'customer';

        if (in_array('administrator', $wpUser->roles, true) || is_super_admin($wpUser->ID)) {
            $userType = 'admin';
        } elseif (in_array('wpamelia-manager', $wpUser->roles, true)) {
            $userType = 'manager';
        } elseif (in_array('wpamelia-provider', $wpUser->roles, true)) {
            $userType = 'provider';
        }

        return [
            'capabilities'         => $capabilities,
            'activation'           => [
                'licence' => $this->getSetting('activation', 'licence'),
                'stash'   => $this->getSetting('activation', 'stash'),
                'hideUnavailableFeatures' => $this->getSetting('activation', 'hideUnavailableFeatures'),
                'hideTipsAndSuggestions'  => $this->getSetting('activation', 'hideTipsAndSuggestions'),
            ],
            'appleCalendar'        => [
                'active' => Licence\Licence::isFeatureEnabledWithLicense(
                    'appleCalendar',
                    $this->getSetting('featuresIntegrations', 'appleCalendar')
                ) &&
                    $this->getSetting('appleCalendar', 'clientID') &&
                    $this->getSetting('appleCalendar', 'clientSecret'),
            ],
            'appointments'         => [
                'cartPlaceholders'                    => $this->getSetting('appointments', 'cartPlaceholders'),
                'cartPlaceholdersCustomer'            => $this->getSetting('appointments', 'cartPlaceholdersCustomer'),
                'cartPlaceholdersCustomerSms'         => $this->getSetting(
                    'appointments',
                    'cartPlaceholdersCustomerSms'
                ),
                'cartPlaceholdersSms'                 => $this->getSetting('appointments', 'cartPlaceholdersSms'),
                'groupAppointmentPlaceholder'         => $this->getSetting(
                    'appointments',
                    'groupAppointmentPlaceholder'
                ),
                'groupAppointmentPlaceholderCustomer' => $this->getSetting(
                    'appointments',
                    'groupAppointmentPlaceholderCustomer'
                ),
                'groupAppointmentPlaceholderSms'      => $this->getSetting(
                    'appointments',
                    'groupAppointmentPlaceholderSms'
                ),
                'groupEventPlaceholder'               => $this->getSetting('appointments', 'groupEventPlaceholder'),
                'groupEventPlaceholderCustomer'       => $this->getSetting(
                    'appointments',
                    'groupEventPlaceholderCustomer'
                ),
                'groupEventPlaceholderSms'            => $this->getSetting('appointments', 'groupEventPlaceholderSms'),
                'packagePlaceholders'                 => $this->getSetting('appointments', 'packagePlaceholders'),
                'packagePlaceholdersCustomer'         => $this->getSetting(
                    'appointments',
                    'packagePlaceholdersCustomer'
                ),
                'packagePlaceholdersCustomerSms'      => $this->getSetting(
                    'appointments',
                    'packagePlaceholdersCustomerSms'
                ),
                'packagePlaceholdersSms'              => $this->getSetting('appointments', 'packagePlaceholdersSms'),
                'recurringPlaceholders'               => $this->getSetting('appointments', 'recurringPlaceholders'),
                'recurringPlaceholdersCustomer'       => $this->getSetting(
                    'appointments',
                    'recurringPlaceholdersCustomer'
                ),
                'recurringPlaceholdersCustomerSms'    => $this->getSetting(
                    'appointments',
                    'recurringPlaceholdersCustomerSms'
                ),
                'recurringPlaceholdersSms'            => $this->getSetting('appointments', 'recurringPlaceholdersSms'),
                'waitingListAppointments'             => $this->getSetting('appointments', 'waitingListAppointments'),
            ],
            'daysOff'              => $this->getCategorySettings('daysOff'),
            'events'               => [
                'waitingListEvents' => [
                    'addingMethod' => $this->getSetting('appointments', 'waitingListEvents')['addingMethod'],
                ],
            ],
            'featuresIntegrations' => Licence\Licence::filterFeaturesByLicense(
                $this->getCategorySettings('featuresIntegrations')
            ),
            'general'              => [
                'customFieldsBackendValidation'             => $this->getSetting('general', 'customFieldsBackendValidation'),
                'customersFilterLimit'                      => $this->getSetting('general', 'customersFilterLimit'),
                'defaultAppointmentStatus'                  => $this->getSetting('general', 'defaultAppointmentStatus'),
                'gMapApiKey'                                => $this->getSetting('general', 'gMapApiKey'),
                'minimumTimeRequirementPriorToBooking'      => $this->getSetting(
                    'general',
                    'minimumTimeRequirementPriorToBooking'
                ),
                'minimumTimeRequirementPriorToCanceling'    => $this->getSetting(
                    'general',
                    'minimumTimeRequirementPriorToCanceling'
                ),
                'minimumTimeRequirementPriorToRescheduling' => $this->getSetting(
                    'general',
                    'minimumTimeRequirementPriorToRescheduling'
                ),
                'numberOfDaysAvailableForBooking'           => $this->getSetting(
                    'general',
                    'numberOfDaysAvailableForBooking'
                ),
                'phoneDefaultCountryCode'                   => $phoneCountryCode === 'auto' ? $this->locationService->getCurrentLocationCountryIso(
                    $ipLocateApyKey
                ) : $phoneCountryCode,
                'redirectUrlAfterAppointment'               => $this->getSetting(
                    'general',
                    'redirectUrlAfterAppointment'
                ),
                'sortingPackages'                           => $this->getSetting('general', 'sortingPackages'),
                'sortingServices'                           => $this->getSetting('general', 'sortingServices'),
                'timeSlotLength'                            => $this->getSetting('general', 'timeSlotLength'),
                'usedLanguages'                             => $this->getSetting('general', 'usedLanguages'),
            ],
            'googleCalendar'       => [
                'active'     => Licence\Licence::isFeatureEnabledWithLicense(
                    'googleCalendar',
                    $this->getSetting('featuresIntegrations', 'googleCalendar')
                ) &&
                    (($this->getSetting('googleCalendar', 'clientID') &&
                        $this->getSetting('googleCalendar', 'clientSecret')) ||
                        $this->getSetting('googleCalendar', 'accessToken')),
                'googleMeet' => $this->getSetting('googleCalendar', 'enableGoogleMeet'),
                'hasAccessToken' => !empty($this->getSetting('googleCalendar', 'accessToken')) &&
                    $this->getSetting('googleCalendar', 'accessToken') !== 'null',

            ],
            'lessonSpace'          => [
                'active' =>
                Licence\Licence::isFeatureEnabledWithLicense(
                    'lessonSpace',
                    $this->getSetting('featuresIntegrations', 'lessonSpace')
                ) && $this->getSetting('lessonSpace', 'apiKey')
            ],
            'socialLogin'            => [
                'googleLoginEnabled'         => Licence\Licence::isFeatureEnabledWithLicense(
                    'googleSocialLogin',
                    $this->getSetting('featuresIntegrations', 'googleSocialLogin')
                ),
                'facebookLoginEnabled'       => Licence\Licence::isFeatureEnabledWithLicense(
                    'facebookSocialLogin',
                    $this->getSetting('featuresIntegrations', 'facebookSocialLogin')
                ),
                'facebookAppId'              => $this->getSetting('socialLogin', 'facebookAppId'),
                'facebookCredentialsEnabled' => $this->getSetting('socialLogin', 'facebookAppId') &&
                    $this->getSetting('socialLogin', 'facebookAppSecret'),
            ],
            'mailchimp'              => [
                'subscribeFieldVisible' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'mailchimp',
                        $this->getSetting('featuresIntegrations', 'mailchimp')
                    ) &&
                    !empty($this->getSetting('mailchimp', 'accessToken')) &&
                    !empty($this->getSetting('mailchimp', 'list')) &&
                    !empty($this->getSetting('mailchimp', 'server')),
                'checkedByDefault'      => $this->getSetting('mailchimp', 'checkedByDefault'),
            ],
            'notifications'        => [
                'sendAllCF'   => $this->getSetting('notifications', 'sendAllCF'),
                'senderEmail' => $this->getSetting('notifications', 'senderEmail'),
                'sms'         => [
                    'signedIn' => $this->getSetting('notifications', 'smsSignedIn'),
                ],
                'whatsApp'    => [
                    'active'  => Licence\Licence::isFeatureEnabledWithLicense(
                        'whatsapp',
                        $this->getSetting('featuresIntegrations', 'whatsapp')
                    )
                        && $this->getSetting('notifications', 'whatsAppPhoneID')
                        && $this->getSetting('notifications', 'whatsAppAccessToken')
                        && $this->getSetting('notifications', 'whatsAppBusinessID'),
                    'phoneId' => $this->getSetting('notifications', 'whatsAppPhoneID'),
                ],
            ],
            'outlookCalendar'       => [
                'active'     => Licence\Licence::isFeatureEnabledWithLicense(
                    'outlookCalendar',
                    $this->getSetting('featuresIntegrations', 'outlookCalendar')
                ) &&
                    (($this->getSetting('outlookCalendar', 'clientID') &&
                      $this->getSetting('outlookCalendar', 'clientSecret')) ||
                      $this->getSetting('outlookCalendar', 'accessToken')),
                'microsoftTeams' => $this->getSetting('outlookCalendar', 'enableMicrosoftTeams'),
                'hasAccessToken' => !empty($this->getSetting('outlookCalendar', 'accessToken')) &&
                    $this->getSetting('outlookCalendar', 'accessToken') !== 'null',

            ],
            'pageColumnSettings'   => $this->getCategorySettings('pageColumnSettings'),
            'payments'             => [
                'barion'                => [
                    'active' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'barion',
                        $this->getSetting('featuresIntegrations', 'barion')
                    ) &&
                        $this->getSetting('payments', 'barion')['enabled'] &&
                        (($this->getSetting('payments', 'barion')['sandboxMode'] && $this->getSetting(
                            'payments',
                            'barion'
                        )['sandboxPOSKey'] && $this->getSetting('payments', 'barion')['payeeEmail']) ||
                            (! $this->getSetting('payments', 'barion')['sandboxMode'] && $this->getSetting(
                                'payments',
                                'barion'
                            )['livePOSKey'] && $this->getSetting('payments', 'barion')['payeeEmail']))
                ],
                'currency'              => $this->getSetting('payments', 'symbol'),
                'defaultPaymentMethod'  => $this->getSetting('payments', 'defaultPaymentMethod'),
                'mollie'                => [
                    'active' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'mollie',
                        $this->getSetting('featuresIntegrations', 'mollie')
                    ) &&
                        $this->getSetting('payments', 'mollie')['enabled'] &&
                        (($this->getSetting('payments', 'mollie')['testMode'] && $this->getSetting(
                            'payments',
                            'mollie'
                        )['testApiKey']) ||
                            (! $this->getSetting('payments', 'mollie')['testMode'] && $this->getSetting(
                                'payments',
                                'mollie'
                            )['liveApiKey']))
                ],
                'onSite'                => $this->getSetting('payments', 'onSite'),
                'taxes'                 => array_merge(
                    $this->getSetting('payments', 'taxes'),
                    [
                        'enabled' => Licence\Licence::isFeatureEnabledWithLicense(
                            'tax',
                            $this->getSetting('featuresIntegrations', 'tax')
                        )
                    ]
                ),
                'paymentLinks'          => [
                    'enabled'             => $this->getSetting('payments', 'paymentLinks')['enabled'],
                    'changeBookingStatus' => $this->getSetting('payments', 'paymentLinks')['changeBookingStatus'],
                    'redirectUrl'         => $this->getSetting('payments', 'paymentLinks')['redirectUrl']
                ],
                'payPal'                => [
                    'active' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'payPal',
                        $this->getSetting('featuresIntegrations', 'payPal')
                    ) &&
                        $this->getSetting('payments', 'payPal')['enabled'] &&
                        (($this->getSetting('payments', 'payPal')['sandboxMode'] && $this->getSetting(
                            'payments',
                            'payPal'
                        )['testApiClientId'] && $this->getSetting('payments', 'payPal')['testApiSecret']) ||
                            (! $this->getSetting('payments', 'payPal')['sandboxMode'] && $this->getSetting(
                                'payments',
                                'payPal'
                            )['liveApiClientId'] && $this->getSetting('payments', 'payPal')['liveApiSecret']))
                ],
                'priceNumberOfDecimals' => $this->getSetting('payments', 'priceNumberOfDecimals'),
                'priceSeparator'        => $this->getSetting('payments', 'priceSeparator'),
                'priceSymbolPosition'   => $this->getSetting('payments', 'priceSymbolPosition'),
                'razorpay'              => [
                    'active' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'razorpay',
                        $this->getSetting('featuresIntegrations', 'razorpay')
                    ) &&
                        $this->getSetting('payments', 'razorpay')['enabled'] &&
                        (($this->getSetting('payments', 'razorpay')['testMode'] && $this->getSetting(
                            'payments',
                            'razorpay'
                        )['testKeyId'] && $this->getSetting('payments', 'razorpay')['testKeySecret']) ||
                            (! $this->getSetting('payments', 'razorpay')['testMode'] && $this->getSetting(
                                'payments',
                                'razorpay'
                            )['liveKeyId'] && $this->getSetting('payments', 'razorpay')['liveKeySecret']))
                ],
                'stripe'                => [
                    'active'  =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'stripe',
                        $this->getSetting('featuresIntegrations', 'stripe')
                    ) &&
                        $this->getSetting('payments', 'stripe')['enabled'] &&
                        (($this->getSetting('payments', 'stripe')['testMode'] && $this->getSetting(
                            'payments',
                            'stripe'
                        )['testPublishableKey'] && $this->getSetting('payments', 'stripe')['testSecretKey']) ||
                            (! $this->getSetting('payments', 'stripe')['testMode'] && $this->getSetting(
                                'payments',
                                'stripe'
                            )['livePublishableKey'] && $this->getSetting('payments', 'stripe')['liveSecretKey'])),
                    'connect' => $this->getSetting('payments', 'stripe')['connect'],
                ],
                'square'                => [
                    'active' =>
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'square',
                        $this->getSetting('featuresIntegrations', 'square')
                    ) &&
                        $this->getSetting('payments', 'square')['enabled'] &&
                        $this->getSetting('payments', 'square')['accessToken'] &&
                        $this->getSetting('payments', 'square')['locationId']
                ],
                'wc'                    => [
                    'active'    => Licence\Licence::isFeatureEnabledWithLicense(
                        'wc',
                        $this->getSetting('featuresIntegrations', 'wc')
                    ) &&
                        $this->getSetting('payments', 'wc')['enabled'],
                    'productId' => $this->getSetting('payments', 'wc')['productId'],
                ]
            ],

            'role'         => $userType,
            'roles'        => [
                'providerBadges'          => Licence\Licence::isFeatureEnabledWithLicense(
                    'employeeBadge',
                    $this->getSetting('featuresIntegrations', 'employeeBadge')
                ) ? $this->getSetting('roles', 'providerBadges') : [],
                'allowCustomerReschedule' => $this->getSetting('roles', 'allowCustomerReschedule'),
                'allowConfigureSchedule'      => $this->getSetting('roles', 'allowConfigureSchedule'),
                'allowConfigureDaysOff'       => $this->getSetting('roles', 'allowConfigureDaysOff'),
                'allowConfigureSpecialDays'   => $this->getSetting('roles', 'allowConfigureSpecialDays'),
                'allowConfigureServices'      => $this->getSetting('roles', 'allowConfigureServices'),
                'allowWriteAppointments'      => $this->getSetting('roles', 'allowWriteAppointments'),
                'allowWriteEvents'            => $this->getSetting('roles', 'allowWriteEvents'),
            ],
            'weekSchedule' => $this->getCategorySettings('weekSchedule'),
            'wordpress'    => [
                'dateFormat'  => $this->getSetting('wordpress', 'dateFormat'),
                'locale'      => AMELIA_LOCALE,
                'startOfWeek' => (int)$this->getSetting('wordpress', 'startOfWeek'),
                'timeFormat'  => $this->getSetting('wordpress', 'timeFormat'),
                'timezone'    => $this->getSetting('wordpress', 'timeZoneString'),
                'gmtOffset'   => $this->getSetting('wordpress', 'gmtOffset'),
            ],
            'zoom'         => [
                'active' => (
                    Licence\Licence::isFeatureEnabledWithLicense(
                        'zoom',
                        $this->getSetting('featuresIntegrations', 'zoom')
                    ) &&
                    $this->getSetting('zoom', 'accountId') &&
                    $this->getSetting('zoom', 'clientId') &&
                    $this->getSetting('zoom', 'clientSecret')
                )
            ],
        ];
    }

    /**
     * @param $settingCategoryKey
     * @param $settingKey
     * @param $settingValue
     *
     * @return mixed|void
     */
    public function setSetting($settingCategoryKey, $settingKey, $settingValue)
    {
        $this->settingsCache[$settingCategoryKey][$settingKey] = $settingValue;
        $settingsCopy                                          = $this->settingsCache;

        unset($settingsCopy['wordpress']);
        update_option('amelia_settings', json_encode($settingsCopy));
    }

    /**
     * @param $settingCategoryKey
     * @param $settingValues
     *
     * @return mixed|void
     */
    public function setCategorySettings($settingCategoryKey, $settingValues)
    {
        $this->settingsCache[$settingCategoryKey] = $settingValues;
        $settingsCopy                             = $this->settingsCache;

        unset($settingsCopy['wordpress']);
        update_option('amelia_settings', json_encode($settingsCopy));
    }

    /**
     * @param array $settings
     *
     * @return mixed|void
     */
    public function setAllSettings($settings)
    {
        foreach ($settings as $settingCategoryKey => $settingValues) {
            $this->settingsCache[$settingCategoryKey] = $settingValues;
        }
        $settingsCopy = $this->settingsCache;

        Licence\DataModifier::restoreSettings($settingsCopy, self::getSavedSettings());

        if (get_option('amelia_show_wpdt_promo') === false) {
            update_option('amelia_show_wpdt_promo', 'yes');
        }

        unset($settingsCopy['wordpress']);
        update_option('amelia_settings', json_encode($settingsCopy));
    }
}
