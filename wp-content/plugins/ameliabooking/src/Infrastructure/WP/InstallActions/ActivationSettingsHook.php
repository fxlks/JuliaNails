<?php

/**
 * Settings hook for activation
 */

namespace AmeliaBooking\Infrastructure\WP\InstallActions;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Domain\ValueObjects\String\Token;
use AmeliaBooking\Infrastructure\Licence\Licence;
use AmeliaBooking\Infrastructure\WP\SettingsService\SettingsStorage;
use Exception;

/**
 * Class ActivationSettingsHook
 *
 * @package AmeliaBooking\Infrastructure\WP\InstallActions
 */
class ActivationSettingsHook
{
    /**
     * Initialize the plugin
     *
     * @throws Exception
     */
    public static function init()
    {
        self::initDBSettings();

        self::initGeneralSettings();

        self::initCompanySettings();

        self::initNotificationsSettings();

        self::initDaysOffSettings();

        self::initWeekScheduleSettings();

        self::initGoogleCalendarSettings();

        self::initOutlookCalendarSettings();

        self::initPaymentsSettings();

        self::initActivationSettings();

        self::initLabelsSettings();

        self::initRolesSettings();

        self::initAppointmentsSettings();

        self::initWebHooksSettings();

        self::initZoomSettings();

        self::initApiKeySettings();

        self::initLessonSpaceSettings();

        self::initIcsSettings();

        self::initFacebookPixelSettings();

        self::initGoogleAnalyticsSettings();

        self::initGoogleTagSettings();

        self::initMailchimpSettings();

        self::initAppleCalendarSettings();

        self::initPageColumnSettings();

        self::initSocialLoginSettings();

        self::initFeaturesIntegrationsSettings();
    }

    /**
     * @param string $category
     * @param array  $settings
     * @param bool   $replace
     */
    public static function initSettings($category, $settings, $replace = false)
    {
        $settingsService = new SettingsService(new SettingsStorage());

        if (!$settingsService->getCategorySettings($category)) {
            $settingsService->setCategorySettings(
                $category,
                []
            );
        }

        foreach ($settings as $key => $value) {
            if ($replace || null === $settingsService->getSetting($category, $key)) {
                $settingsService->setSetting(
                    $category,
                    $key,
                    $value
                );
            }
        }
    }

    /**
     * Init General Settings
     *
     * @param array $savedSettings
     *
     * @return array
     */
    public static function getDefaultGeneralSettings($savedSettings)
    {
        return [
            'timeSlotLength'                         => 1800,
            'serviceDurationAsSlot'                  => false,
            'bufferTimeInSlot'                       => true,
            'defaultAppointmentStatus'               => 'approved',
            'defaultEventStatus'                     => 'approved',
            'minimumTimeRequirementPriorToBooking'   => 0,
            'minimumTimeRequirementPriorToCanceling' => 0,
            'minimumTimeRequirementPriorToRescheduling' =>
            isset($savedSettings['minimumTimeRequirementPriorToCanceling']) &&
                !isset($savedSettings['minimumTimeRequirementPriorToRescheduling']) ?
                $savedSettings['minimumTimeRequirementPriorToCanceling'] : 0,
            'numberOfDaysAvailableForBooking'        => SettingsService::NUMBER_OF_DAYS_AVAILABLE_FOR_BOOKING,
            'backendSlotsDaysInFuture'               => SettingsService::NUMBER_OF_DAYS_AVAILABLE_FOR_BOOKING,
            'backendSlotsDaysInPast'                 => SettingsService::NUMBER_OF_DAYS_AVAILABLE_FOR_BOOKING,
            'phoneDefaultCountryCode'                => 'auto',
            'ipLocateApiKey'                         => '',
            'requiredPhoneNumberField'               => false,
            'requiredEmailField'                     => true,
            'itemsPerPage'                           => 12,
            'appointmentsPerPage'                    => 100,
            'eventsPerPage'                          => 100,
            'servicesPerPage'                        => 100,
            'customersFilterLimit'                   => 100,
            'eventsFilterLimit'                      => 1000,
            'calendarEmployeesPreselected'           => 0,
            'gMapApiKey'                             => '',
            'addToCalendar'                          => true,
            'defaultPageOnBackend'                   => 'Dashboard',
            'showClientTimeZone'                     => false,
            'redirectUrlAfterAppointment'            => '',
            'customFieldsUploadsPath'                => '',
            'customFieldsAllowedExtensions'          => [
                '.jpg'  => 'image/jpeg',
                '.jpeg' => 'image/jpeg',
                '.png'  => 'image/png',
                '.mp3'  => 'audio/mpeg',
                '.mpeg' => 'video/mpeg',
                '.mp4'  => 'video/mp4',
                '.txt'  => 'text/plain',
                '.csv'  => 'text/plain',
                '.xls'  => 'application/vnd.ms-excel',
                '.pdf'  => 'application/pdf',
                '.doc'  => 'application/msword',
                '.docx' => 'application/msword'
            ],
            'customFieldsBackendValidation'          => false,
            'runInstantPostBookingActions'           => false,
            'sortingPackages'                        => 'nameAsc',
            'sortingServices'                        => 'nameAsc',
            'calendarLocaleSubstitutes'              => [],
            'googleRecaptcha'                        => [
                'invisible' => true,
                'siteKey'   => '',
                'secret'    => '',
            ],
            'usedLanguages'                          => [],
        ];
    }

    /**
     * Get General Settings
     */
    private static function initGeneralSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('general');

        $settings = self::getDefaultGeneralSettings($savedSettings);

        $settings['backLink'] = self::getBackLinkSetting();

        self::initSettings('general', $settings);

        self::setNewSettingsToExistingSettings(
            'general',
            [
                ['backLink', 'url'],
            ],
            $settings
        );
    }

    /**
     * Init DB Settings
     */
    private static function initDBSettings()
    {
        self::initSettings('db', [
            'wpTablesPrefix' => '',
        ]);
    }

    /**
     * Init Company Settings
     */
    private static function initCompanySettings()
    {

        $settings = [
            'pictureFullPath'   => '',
            'pictureThumbPath'  => '',
            'name'              => '',
            'address'           => '',
            'addressComponents' => [],
            'countryCode'       => '',
            'phone'             => '',
            'vat'               => '',
            'countryPhoneIso'   => '',
            'website'           => '',
            'translations'      => '',
        ];

        self::initSettings('company', $settings);
    }

    /**
     * Get Notification Settings
     *
     * @return array
     */
    public static function getDefaultNotificationsSettings()
    {
        return [
            'mailService'          => '',
            'smtpHost'             => '',
            'smtpPort'             => '',
            'smtpSecure'           => 'ssl',
            'smtpUsername'         => '',
            'smtpPassword'         => '',
            'mailgunApiKey'        => '',
            'mailgunDomain'        => '',
            'mailgunEndpoint'      => '',
            'senderName'           => '',
            'replyTo'              => '',
            'senderEmail'          => '',
            'sendAllCF'            => true,
            'smsAlphaSenderId'     => 'Amelia',
            'smsSignedIn'          => false,
            'smsApiToken'          => '',
            'bccEmail'             => '',
            'bccSms'               => '',
            'emptyPackageEmployees' => '',
            'smsBalanceEmail'      => ['enabled' => false, 'minimum' => 0, 'email' => ''],
            'cancelSuccessUrl'     => '',
            'cancelErrorUrl'       => '',
            'approveSuccessUrl'    => '',
            'approveErrorUrl'      => '',
            'rejectSuccessUrl'     => '',
            'rejectErrorUrl'       => '',
            'breakReplacement'     => '<br>',
            'pendingReminder'      => false,
            'sendInvoice'          => false,
            'invoiceFormat'        => 'pdf',
            'whatsAppPhoneID'      => '',
            'whatsAppAccessToken'  => '',
            'whatsAppBusinessID'   => '',
            'whatsAppLanguage'     => '',
            'whatsAppReplyEnabled' => false,
            'whatsAppReplyMsg'     => 'Dear %customer_full_name%,
This message does not have an option for responding. If you need additional information about your booking, please contact us at %company_phone%',
            'whatsAppReplyToken'   => (new Token(null, 20))->getValue(),
        ];
    }

    /**
     * Init Notification Settings
     */
    private static function initNotificationsSettings()
    {
        $settings = self::getDefaultNotificationsSettings();

        self::initSettings('notifications', $settings);
    }

    /**
     * Init Days Off Settings
     */
    private static function initDaysOffSettings()
    {
        self::initSettings('daysOff', []);
    }

    /**
     * Init Work Schedule Settings
     */
    private static function initWeekScheduleSettings()
    {
        self::initSettings(
            'weekSchedule',
            [
                [
                    'day'     => 'Monday',
                    'time'    => ['09:00', '17:00'],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Tuesday',
                    'time'    => ['09:00', '17:00'],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Wednesday',
                    'time'    => ['09:00', '17:00'],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Thursday',
                    'time'    => ['09:00', '17:00'],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Friday',
                    'time'    => ['09:00', '17:00'],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Saturday',
                    'time'    => [],
                    'breaks'  => [],
                    'periods' => []
                ],
                [
                    'day'     => 'Sunday',
                    'time'    => [],
                    'breaks'  => [],
                    'periods' => []
                ]
            ]
        );
    }

    /**
     * Init Google Calendar Settings
     */
    private static function initGoogleCalendarSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('googleCalendar');

        $settings = [
            'clientID'                        => '',
            'clientSecret'                    => '',
            'redirectURI'                     => AMELIA_SITE_URL . '/wp-admin/admin.php?page=wpamelia-employees',
            'showAttendees'                   => false,
            'insertPendingAppointments'       => false,
            'addAttendees'                    => false,
            'sendEventInvitationEmail'        => false,
            'removeGoogleCalendarBusySlots'   => false,
            'maximumNumberOfEventsReturned'   => 50,
            'eventTitle'                      => '%service_name%',
            'eventDescription'                => '',
            'includeBufferTimeGoogleCalendar' => false,
            'status'                          => 'tentative',
            'enableGoogleMeet'                => false,
            'title'                           => [
                'appointment' => $savedSettings && !empty($savedSettings['eventTitle']) ?
                    $savedSettings['eventTitle'] : '%service_name%',
                'event' => '%event_name%'
            ],
            'description'                      => [
                'appointment' => $savedSettings && !empty($savedSettings['eventDescription']) ?
                    $savedSettings['eventDescription'] : '',
                'event' => ''
            ],
            'accessToken'                      => '',
            'googleAccountData'                => null
        ];

        self::initSettings('googleCalendar', $settings);
    }

    /**
     * Init Outlook Calendar Settings
     */
    private static function initOutlookCalendarSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('outlookCalendar');

        $settings = [
            'clientID'                         => '',
            'clientSecret'                     => '',
            'redirectURI'                      => AMELIA_SITE_URL . '/wp-admin/',
            'insertPendingAppointments'        => false,
            'addAttendees'                     => false,
            'sendEventInvitationEmail'         => false,
            'removeOutlookCalendarBusySlots'   => false,
            'maximumNumberOfEventsReturned'    => 50,
            'ignoreAmeliaEvents'               => false,
            'eventTitle'                       => '%service_name%',
            'eventDescription'                 => '',
            'includeBufferTimeOutlookCalendar' => false,
            'enableMicrosoftTeams'             => false,
            'title'                            => [
                'appointment' => $savedSettings && !empty($savedSettings['eventTitle']) ?
                    $savedSettings['eventTitle'] : '%service_name%',
                'event' => '%event_name%'
            ],
            'description'                      => [
                'appointment' => $savedSettings && !empty($savedSettings['eventDescription']) ?
                    $savedSettings['eventDescription'] : '',
                'event' => ''
            ],
            'mailEnabled'                      => false,
            'token'                            => null,
            'accessToken'                      => '',
            'outlookAccountData'               => null
        ];

        self::initSettings('outlookCalendar', $settings);
    }

    /**
     * Init Apple Calendar Settings
     */
    private static function initAppleCalendarSettings()
    {
        $settings = [
            'clientID'                        => '',
            'clientSecret'                    => '',
            'redirectURI'                     => AMELIA_SITE_URL . '/wp-admin/admin.php?page=wpamelia-employees',
            'insertPendingAppointments'       => false,
            'addAttendees'                    => false,
            'removeAppleCalendarBusySlots'    => false,
            'eventTitle'                      => '%service_name%',
            'eventDescription'                => '',
            'includeBufferTimeAppleCalendar' => false,
            'title'                           => [
                'appointment' => '%service_name%',
                'event' => '%event_name%'
            ],
            'description'                      => [
                'appointment' => '',
                'event' => ''
            ],
        ];

        self::initSettings('appleCalendar', $settings);
    }

    /**
     * Init Zoom Settings
     */
    private static function initZoomSettings()
    {
        $settings = [
            'enabled'                     => true,
            'apiKey'                      => '',
            'apiSecret'                   => '',
            'meetingTitle'                => '%reservation_name%',
            'meetingAgenda'               => '%reservation_description%',
            'pendingAppointmentsMeetings' => false,
            's2sEnabled'                  => true,
            'accountId'                   => '',
            'clientId'                    => '',
            'clientSecret'                => '',
            'accessToken'                 => '',
        ];

        self::initSettings('zoom', $settings);
    }


    /**
     * Init Api Key Settings
     */
    private static function initApiKeySettings()
    {
        $settings = [
            'apiKeys' => []
        ];

        self::initSettings('apiKeys', $settings);
    }

    /**
     * Init Lesson Space Settings
     */
    private static function initLessonSpaceSettings()
    {
        $settings = [
            'apiKey'                      => '',
            'spaceNameAppointments'       => '%reservation_name%',
            'spaceNameEvents'             => '%reservation_name%',
            'pendingAppointments'         => false,
            'companyId'                   => ''
        ];

        self::initSettings('lessonSpace', $settings);
    }

    /**
     * Init FacebookPixel Settings
     */
    private static function initFacebookPixelSettings()
    {
        $settings = [
            'id'               => '',
            'tracking' => [
                'appointment' => [],
                'event'       => [],
                'package'     => [],
            ],
        ];

        self::initSettings('facebookPixel', $settings);
    }

    /**
     * Init Google Analytics Settings
     */
    private static function initGoogleAnalyticsSettings()
    {
        $settings = [
            'id'               => '',
            'tracking' => [
                'appointment' => [],
                'event'       => [],
                'package'     => [],
            ],
        ];

        self::initSettings('googleAnalytics', $settings);
    }

    /**
     * Init GoogleTag Settings
     */
    private static function initGoogleTagSettings()
    {
        $settings = [
            'id'               => '',
            'tracking' => [
                'appointment' => [],
                'event'       => [],
                'package'     => [],
            ],
        ];

        self::initSettings('googleTag', $settings);
    }


    /**
     * Init Mailchimp Settings
     */
    private static function initMailchimpSettings()
    {
        $settings = [
            'accessToken'      => null,
            'server'           => null,
            'list'             => null,
            'checkedByDefault' => false
        ];

        self::initSettings('mailchimp', $settings);
    }

    /**
     * Init Page Column Settings
     */
    private static function initPageColumnSettings()
    {
        $settings = self::getDefaultPageColumnSettings();

        self::initSettings('pageColumnSettings', $settings);

        // Handle updates to existing page column settings
        self::updatePageColumnSettings($settings);
    }

    /**
     * Get Default Page Column Settings
     *
     * @return array
     */
    public static function getDefaultPageColumnSettings()
    {
        return [
            "customersPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => false
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "phone",
                    "visible" => true,
                    "width" => 160,
                    "label" => "phone",
                    "sortable" => false
                ],
                [
                    "prop" => "email",
                    "visible" => true,
                    "width" => 240,
                    "label" => "email",
                    "sortable" => false
                ],
                [
                    "prop" => "note",
                    "visible" => true,
                    "width" => 80,
                    "label" => "note",
                    "sortable" => false
                ],
                [
                    "prop" => "lastBooking",
                    "visible" => true,
                    "width" => 240,
                    "label" => "last_booking",
                    "sortable" => false
                ],
                [
                    "prop" => "totalBookings",
                    "visible" => true,
                    "width" => 160,
                    "label" => "total_bookings",
                    "sortable" => false
                ],
                [
                    "prop" => "wordPressUser",
                    "visible" => true,
                    "width" => 240,
                    "label" => "wp_user",
                    "sortable" => false
                ]
            ],

            "employeesPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => false
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ],
                [
                    "prop" => "availability",
                    "visible" => true,
                    "width" => 160,
                    "label" => "red_availability",
                    "sortable" => false
                ],
                [
                    "prop" => "phone",
                    "visible" => true,
                    "width" => 160,
                    "label" => "phone",
                    "sortable" => false
                ],
                [
                    "prop" => "email",
                    "visible" => true,
                    "width" => 240,
                    "label" => "email",
                    "sortable" => false
                ],
                [
                    "prop" => "actions",
                    "visible" => true,
                    "width" => 240,
                    "label" => "red_actions",
                    "sortable" => false
                ],
            ],

            "locationsPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => false
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "address",
                    "visible" => true,
                    "width" => 320,
                    "label" => "address",
                    "sortable" => false
                ],
                [
                    "prop" => "phone",
                    "visible" => true,
                    "width" => 160,
                    "label" => "phone",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ]
            ],

            "bookingsAppointmentsPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "date",
                    "visible" => true,
                    "width" => 160,
                    "label" => "date",
                    "sortable" => false,
                    "required" => true
                ],
                [
                    "prop" => "time",
                    "visible" => true,
                    "width" => 160,
                    "label" => "time",
                    "sortable" => false,
                    "required" => true
                ],
                [
                    "prop" => "customer",
                    "visible" => true,
                    "width" => 320,
                    "label" => "customer",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "service",
                    "visible" => true,
                    "width" => 240,
                    "label" => "service",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "type",
                    "visible" => true,
                    "width" => 160,
                    "label" => "type",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 200,
                    "label" => "status",
                    "sortable" => false
                ],
                [
                    "prop" => "employee",
                    "visible" => true,
                    "width" => 320,
                    "label" => "employee",
                    "sortable" => false
                ],
                [
                    "prop" => "location",
                    "visible" => true,
                    "width" => 240,
                    "label" => "location",
                    "sortable" => false
                ],
                [
                    "prop" => "duration",
                    "visible" => false,
                    "width" => 160,
                    "label" => "duration",
                    "sortable" => false
                ],
                [
                    "prop" => "customerEmail",
                    "visible" => false,
                    "width" => 240,
                    "label" => "customer_email",
                    "sortable" => false
                ],
                [
                    "prop" => "customerPhone",
                    "visible" => false,
                    "width" => 240,
                    "label" => "customer_phone",
                    "sortable" => false
                ],
                [
                    "prop" => "booked",
                    "visible" => false,
                    "width" => 80,
                    "label" => "booked",
                    "sortable" => false
                ],
                [
                    "prop" => "note",
                    "visible" => false,
                    "width" => 80,
                    "label" => "note",
                    "sortable" => false
                ],
                [
                    "prop" => "paidPrice",
                    "visible" => false,
                    "width" => 160,
                    "label" => "paid",
                    "sortable" => false
                ],
                [
                    "prop" => "payment",
                    "visible" => false,
                    "width" => 160,
                    "label" => "payment",
                    "sortable" => false
                ],
                [
                    "prop" => "totalPrice",
                    "visible" => false,
                    "width" => 160,
                    "label" => "total_price",
                    "sortable" => false
                ],
                [
                    "prop" => "hostLink",
                    "visible" => false,
                    "width" => 160,
                    "label" => "red_host_link",
                    "sortable" => false
                ],
                [
                    "prop" => "joinLink",
                    "visible" => false,
                    "width" => 200,
                    "label" => "red_join_link",
                    "sortable" => false
                ],
                [
                    "prop" => "created",
                    "visible" => false,
                    "width" => 160,
                    "label" => "created_on",
                    "sortable" => true,
                    "required" => false
                ],
                [
                    "prop" => "bookingSource",
                    "visible" => false,
                    "width" => 64,
                    "label" => "red_booking_source",
                    "sortable" => false,
                    "showLabel" => false
                ],
            ],

            "bookingsPackagesPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "date",
                    "visible" => true,
                    "width" => 160,
                    "label" => "package_date_purchased",
                    "sortable" => true
                ],
                [
                    "prop" => "customer",
                    "visible" => true,
                    "width" => 320,
                    "label" => "customer",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "package",
                    "visible" => true,
                    "width" => 320,
                    "label" => "package",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 200,
                    "label" => "status",
                    "sortable" => false
                ],
                [
                    "prop" => "appointments",
                    "visible" => true,
                    "width" => 160,
                    "label" => "appointments",
                    "sortable" => false
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 320,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "expirationDate",
                    "visible" => false,
                    "width" => 240,
                    "label" => "expiration_date",
                    "sortable" => false
                ],
                [
                    "prop" => "price",
                    "visible" => false,
                    "width" => 160,
                    "label" => "price",
                    "sortable" => false
                ],
                [
                    "prop" => "paymentStatus",
                    "visible" => false,
                    "width" => 200,
                    "label" => "payment_status",
                    "sortable" => false
                ]
            ],

            "bookingsEventsPage" => [
                [
                    "prop" => "code",
                    "visible" => true,
                    "width" => 120,
                    "label" => "code",
                    "sortable" => false
                ],
                [
                    "prop" => "date",
                    "visible" => true,
                    "width" => 160,
                    "label" => "date",
                    "sortable" => false
                ],
                [
                    "prop" => "time",
                    "visible" => true,
                    "width" => 160,
                    "label" => "time",
                    "sortable" => false
                ],
                [
                    "prop" => "attendee",
                    "visible" => true,
                    "width" => 240,
                    "label" => "attendee",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "event",
                    "visible" => true,
                    "width" => 320,
                    "label" => "event",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 200,
                    "label" => "status",
                    "sortable" => false
                ],
                [
                    "prop" => "spots",
                    "visible" => true,
                    "width" => 80,
                    "label" => "red_booked",
                    "sortable" => false
                ],
                [
                    "prop" => "organizer",
                    "visible" => false,
                    "width" => 240,
                    "label" => "event_organizer",
                    "sortable" => false
                ],
                [
                    "prop" => "staff",
                    "visible" => false,
                    "width" => 240,
                    "label" => "event_staff",
                    "sortable" => false
                ],
                [
                    "prop" => "price",
                    "visible" => false,
                    "width" => 160,
                    "label" => "price",
                    "sortable" => false
                ],
                [
                    "prop" => "paymentStatus",
                    "visible" => false,
                    "width" => 200,
                    "label" => "payment_status",
                    "sortable" => false
                ],
                [
                    "prop" => "created",
                    "visible" => false,
                    "width" => 160,
                    "label" => "created_on",
                    "sortable" => true,
                    "required" => false
                ],
            ],

            "eventsPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "dateTime",
                    "visible" => true,
                    "width" => 240,
                    "label" => "date_time",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 200,
                    "label" => "status",
                    "sortable" => false
                ],
                [
                    "prop" => "spots",
                    "visible" => true,
                    "width" => 80,
                    "label" => "red_booked",
                    "sortable" => false
                ],
                [
                    "prop" => "organizer",
                    "visible" => true,
                    "width" => 240,
                    "label" => "event_organizer",
                    "sortable" => false
                ],
                [
                    "prop" => "staff",
                    "visible" => true,
                    "width" => 240,
                    "label" => "event_staff",
                    "sortable" => false
                ],
                [
                    "prop" => "recurring",
                    "visible" => true,
                    "width" => 120,
                    "label" => "recurring",
                    "sortable" => false
                ],
                [
                    "prop" => "waitingList",
                    "visible" => true,
                    "width" => 200,
                    "label" => "waiting_list",
                    "sortable" => false
                ],
                [
                    "prop" => "bookingOpens",
                    "visible" => true,
                    "width" => 240,
                    "label" => "red_booking_opens",
                    "sortable" => true
                ],
                [
                    "prop" => "bookingCloses",
                    "visible" => true,
                    "width" => 240,
                    "label" => "red_booking_closes",
                    "sortable" => true
                ],
                [
                    "prop" => "visibility",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ],
            ],

            "catalogServicesPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "service",
                    "visible" => true,
                    "width" => 320,
                    "label" => "service",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "duration",
                    "visible" => true,
                    "width" => 160,
                    "label" => "duration",
                    "sortable" => true
                ],
                [
                    "prop" => "price",
                    "visible" => true,
                    "width" => 160,
                    "label" => "price",
                    "sortable" => true
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 320,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "visibility",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ],
            ],

            "catalogPackagesPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "services",
                    "visible" => true,
                    "width" => 200,
                    "label" => "services",
                    "sortable" => true
                ],
                [
                    "prop" => "price",
                    "visible" => true,
                    "width" => 160,
                    "label" => "price",
                    "sortable" => true
                ],
                [
                    "prop" => "duration",
                    "visible" => true,
                    "width" => 160,
                    "label" => "duration",
                    "sortable" => false
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 320,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ]
            ],

            "catalogResourcesPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 320,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "quantity",
                    "visible" => true,
                    "width" => 160,
                    "label" => "quantity",
                    "sortable" => true
                ],
                [
                    "prop" => "services",
                    "visible" => true,
                    "width" => 320,
                    "label" => "services",
                    "sortable" => false
                ],
                [
                    "prop" => "locations",
                    "visible" => true,
                    "width" => 320,
                    "label" => "locations",
                    "sortable" => false
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 320,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "type",
                    "visible" => true,
                    "width" => 200,
                    "label" => "type",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ]
            ],

            "financePaymentsPage" => [
                [
                    "prop" => "id",
                    "visible" => true,
                    "width" => 80,
                    "label" => "id",
                    "sortable" => true
                ],
                [
                    "prop" => "dateTime",
                    "visible" => true,
                    "width" => 160,
                    "label" => "payment_date",
                    "sortable" => true
                ],
                [
                    "prop" => "customer",
                    "visible" => true,
                    "width" => 240,
                    "label" => "customer",
                    "sortable" => false,
                    "required" => true
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 240,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "booking",
                    "visible" => true,
                    "width" => 320,
                    "label" => "booking",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "status",
                    "sortable" => true
                ],
                [
                    "prop" => "amount",
                    "visible" => true,
                    "width" => 160,
                    "label" => "amount",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "payment_method",
                    "visible" => true,
                    "width" => 160,
                    "label" => "payment_method",
                    "sortable" => false
                ],
                [
                    "prop" => "location",
                    "visible" => true,
                    "width" => 320,
                    "label" => "location",
                    "sortable" => false
                ],
            ],
            "financeCouponsPage" => [
                [
                    "prop" => "code",
                    "visible" => true,
                    "width" => 120,
                    "label" => "code",
                    "sortable" => false,
                    "required" => true
                ],
                [
                    "prop" => "discount",
                    "visible" => true,
                    "width" => 120,
                    "label" => "discount_amount",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "deduction",
                    "visible" => true,
                    "width" => 120,
                    "label" => "deduction",
                    "sortable" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ],
                [
                    "prop" => "start_date",
                    "visible" => true,
                    "width" => 240,
                    "label" => "start_date",
                    "sortable" => false
                ],
                [
                    "prop" => "expiration_date",
                    "visible" => true,
                    "width" => 240,
                    "label" => "expiration_date",
                    "sortable" => false
                ],
                [
                    "prop" => "service",
                    "visible" => true,
                    "width" => 320,
                    "label" => "services",
                    "sortable" => false
                ],
                [
                    "prop" => "event",
                    "visible" => true,
                    "width" => 320,
                    "label" => "events",
                    "sortable" => false
                ],
                [
                    "prop" => "package",
                    "visible" => true,
                    "width" => 320,
                    "label" => "packages",
                    "sortable" => false
                ],
                [
                    "prop" => "usage_limit",
                    "visible" => false,
                    "width" => 120,
                    "label" => "usage_limit",
                    "sortable" => false
                ],
                [
                    "prop" => "limit_per_customer",
                    "visible" => false,
                    "width" => 120,
                    "label" => "red_limit_per_customer",
                    "sortable" => false
                ],
                [
                    "prop" => "times_used",
                    "visible" => false,
                    "width" => 120,
                    "label" => "times_used",
                    "sortable" => true
                ],
            ],
            "financeTaxesPage" => [
                [
                    "prop" => "name",
                    "visible" => true,
                    "width" => 240,
                    "label" => "name",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "type",
                    "visible" => true,
                    "width" => 120,
                    "label" => "type",
                    "sortable" => true
                ],
                [
                    "prop" => "rate",
                    "visible" => true,
                    "width" => 80,
                    "label" => "rate",
                    "required" => true
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "visibility",
                    "sortable" => false
                ],
                [
                    "prop" => "service",
                    "visible" => true,
                    "width" => 320,
                    "label" => "services",
                    "sortable" => false
                ],
                [
                    "prop" => "event",
                    "visible" => true,
                    "width" => 320,
                    "label" => "events",
                    "sortable" => false
                ],
                [
                    "prop" => "package",
                    "visible" => true,
                    "width" => 320,
                    "label" => "packages",
                    "sortable" => false
                ],
                [
                    "prop" => "extra",
                    "visible" => true,
                    "width" => 320,
                    "label" => "extras",
                    "sortable" => false
                ],

            ],
            "financeInvoicesPage" => [
                [
                    "prop" => "invoiceNumber",
                    "visible" => true,
                    "width" => 160,
                    "label" => "red_invoice_number",
                    "sortable" => true,
                    "required" => true
                ],
                [
                    "prop" => "customer",
                    "visible" => true,
                    "width" => 240,
                    "label" => "customer",
                    "sortable" => false,
                    "required" => true
                ],
                [
                    "prop" => "dateTime",
                    "visible" => true,
                    "width" => 160,
                    "label" => "red_invoice_date",
                    "sortable" => true
                ],
                [
                    "prop" => "employees",
                    "visible" => true,
                    "width" => 240,
                    "label" => "employees",
                    "sortable" => false
                ],
                [
                    "prop" => "booking",
                    "visible" => true,
                    "width" => 320,
                    "label" => "booking",
                    "sortable" => false
                ],
                [
                    "prop" => "status",
                    "visible" => true,
                    "width" => 160,
                    "label" => "status",
                    "sortable" => true
                ],
                [
                    "prop" => "amount",
                    "visible" => true,
                    "width" => 160,
                    "label" => "total",
                    "sortable" => false
                ],
            ],
        ];
    }

    /**
     * Update Page Column Settings for existing installations
     *
     * @param array $defaultSettings
     */
    private static function updatePageColumnSettings($defaultSettings)
    {
        $settingsService = new SettingsService(new SettingsStorage());
        $savedSettings = $settingsService->getCategorySettings('pageColumnSettings');

        // If no saved settings exist, nothing to update
        if (!$savedSettings) {
            return;
        }

        $needsUpdate = false;
        $updatedSettings = $savedSettings;

        foreach ($defaultSettings as $pageKey => $defaultColumns) {
            // If page doesn't exist in saved settings, add it
            if (!isset($savedSettings[$pageKey])) {
                $updatedSettings[$pageKey] = $defaultColumns;
                $needsUpdate = true;
                continue;
            }

            // Create a map of existing columns by prop for easy lookup, preserving user customizations
            $existingColumnsByProp = [];
            foreach ($savedSettings[$pageKey] as $column) {
                if (isset($column['prop'])) {
                    $existingColumnsByProp[$column['prop']] = $column;
                }
            }

            // Rebuild the columns array in the default order, preserving user customizations
            $mergedColumns = [];
            $addedColumns = [];

            // First, add columns in the default order
            foreach ($defaultColumns as $defaultColumn) {
                if (isset($existingColumnsByProp[$defaultColumn['prop']])) {
                    // Use existing column but merge missing properties from defaults
                    $existingColumn = $existingColumnsByProp[$defaultColumn['prop']];

                    // Check if any properties from default are missing or different in existing column
                    $columnNeedsUpdate = false;
                    foreach ($defaultColumn as $key => $value) {
                        if (!isset($existingColumn[$key])) {
                            // Add missing property
                            $existingColumn[$key] = $value;
                            $columnNeedsUpdate = true;
                        } elseif ($key === 'label' && $existingColumn[$key] !== $value) {
                            // Update label if it has changed in defaults
                            $existingColumn[$key] = $value;
                            $columnNeedsUpdate = true;
                        } elseif ($key === 'sortable' && $existingColumn[$key] !== $value) {
                            // Update sortable if it has changed in defaults
                            $existingColumn[$key] = $value;
                            $columnNeedsUpdate = true;
                        }
                    }

                    if ($columnNeedsUpdate) {
                        $needsUpdate = true;
                    }

                    $mergedColumns[] = $existingColumn;
                    $addedColumns[] = $defaultColumn['prop'];
                } else {
                    // Add new column from defaults
                    $mergedColumns[] = $defaultColumn;
                    $addedColumns[] = $defaultColumn['prop'];
                    $needsUpdate = true;
                }
            }

            // Then, add any existing columns that are not in defaults (user-added columns)
            foreach ($existingColumnsByProp as $prop => $column) {
                if (!in_array($prop, $addedColumns)) {
                    $mergedColumns[] = $column;
                }
            }

            // Check if the order or content changed
            if ($mergedColumns !== $savedSettings[$pageKey]) {
                $updatedSettings[$pageKey] = $mergedColumns;
                $needsUpdate = true;
            }
        }

        // Save updated settings if changes were made
        if ($needsUpdate) {
            $settingsService->setCategorySettings('pageColumnSettings', $updatedSettings);
        }
    }


    /**
     * Init Ics Settings
     */
    private static function initIcsSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('general');

        $settings = [
            'sendIcsAttachment'  => isset($savedSettings['sendIcsAttachment']) ? $savedSettings['sendIcsAttachment'] : false,
            'sendIcsAttachmentPending'  => false,
            'description'        => [
                'appointment'  => '',
                'event'        => '',
                'translations' => [
                    'appointment' => null,
                    'event'       => null,
                ],
            ],
        ];

        self::initSettings('ics', $settings);
    }

    /**
     * Get Payments Settings
     *
     * @param array $savedSettings
     *
     * @return array
     */
    public static function getDefaultPaymentsSettings($savedSettings)
    {
        return [
            'currency'                   => 'USD',
            'symbol'                     => '$',
            'priceSymbolPosition'        => 'before',
            'priceNumberOfDecimals'      => 2,
            'priceSeparator'             => 1,
            'hideCurrencySymbolFrontend' => false,
            'defaultPaymentMethod'       => 'onSite',
            'onSite'                     => true,
            'couponsCaseInsensitive'     => false,
            'paymentLinks'               => [
                'enabled'              => false,
                'changeBookingStatus'  => false,
                'redirectUrl'          => AMELIA_SITE_URL
            ],
            'taxes'                      => [
                'excluded' => true,
            ],
            'payPal'                     => [
                'enabled'         => false,
                'sandboxMode'     => false,
                'liveApiClientId' => '',
                'liveApiSecret'   => '',
                'testApiClientId' => '',
                'testApiSecret'   => '',
                'description'     => [
                    'enabled'     => false,
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => '',
                ],
            ],
            'stripe'                     => [
                'enabled'            => false,
                'testMode'           => false,
                'livePublishableKey' => '',
                'liveSecretKey'      => '',
                'testPublishableKey' => '',
                'testSecretKey'      => '',
                'address'            => false,
                'description'        => [
                    'enabled'     => false,
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => '',
                ],
                'metaData'           => [
                    'enabled'     => false,
                    'appointment' => null,
                    'package'     => null,
                    'event'       => null,
                    'cart'        => '',
                ],
                'manualCapture'   => false,
                'returnUrl'       => '',
                'connect'         => [
                    'enabled'      => false,
                    'method'       => 'transfer',
                    'amount'       => 0,
                    'type'         => 'percentage',
                    'capabilities' => ['card_payments', 'transfers'],
                ],
            ],
            'wc'                         => [
                'enabled'      => false,
                'productId'    => '',
                'onSiteIfFree' => false,
                'page'         => 'cart',
                'dashboard'    => true,
                'checkoutData' => [
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => '',
                    'translations' => [
                        'appointment' => null,
                        'event'       => null,
                        'package'     => null,
                        'cart'        => '',
                    ],
                ],
                'skipCheckoutGetValueProcessing' => isset($savedSettings['wc']['skipCheckoutGetValueProcessing']) ?
                    $savedSettings['wc']['skipCheckoutGetValueProcessing'] : true,
                'skipGetItemDataProcessing'      => !isset($savedSettings['wc']),
                'redirectPage' => 1,
                'bookMultiple' => false,
                'rules'        =>
                isset($savedSettings['wc']['rules']) ? $savedSettings['wc']['rules'] : [
                    'appointment' => [
                        [
                            'order'   => 'pending',
                            'booking' => 'pending',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'on-hold',
                            'booking' => 'default',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'processing',
                            'booking' => 'default',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'completed',
                            'booking' => 'default',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'cancelled',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'failed',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                    ],
                    'package'     => [
                        [
                            'order'   => 'on-hold',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'processing',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'completed',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'cancelled',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'failed',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                    ],
                    'event'       => [
                        [
                            'order'   => 'pending',
                            'booking' => 'pending',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'on-hold',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'processing',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'completed',
                            'booking' => 'approved',
                            'payment' => 'paid',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'cancelled',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                        [
                            'order'   => 'failed',
                            'booking' => 'canceled',
                            'payment' => 'pending',
                            'update'  => false,
                        ],
                    ],
                ],
            ],
            'mollie'           => [
                'enabled'         => false,
                'testMode'        => false,
                'liveApiKey'      => '',
                'testApiKey'      => '',
                'description'        => [
                    'enabled'     => false,
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => '',
                ],
                'metaData'           => [
                    'enabled'     => false,
                    'appointment' => null,
                    'package'     => null,
                    'event'       => null,
                    'cart'        => '',
                ],
                'method'          => [],
                'cancelBooking'   => false
            ],
            'razorpay'         => [
                'enabled'         => false,
                'testMode'        => false,
                'liveKeyId'       => '',
                'liveKeySecret'   => '',
                'testKeyId'       => '',
                'testKeySecret'   => '',
                'description'     => [
                    'enabled'       => false,
                    'appointment'   => '',
                    'package'       => '',
                    'event'         => '',
                    'cart'          => '',
                ],
                'name'            => [
                    'enabled'       => false,
                    'appointment'   => '',
                    'package'       => '',
                    'event'         => '',
                    'cart'          => '',
                ],
                'metaData'       => [
                    'enabled'       => false,
                    'appointment'   => null,
                    'package'       => null,
                    'event'         => null,
                    'cart'          => '',
                ],
            ],
            'square'               => [
                'enabled'            => false,
                'locationId'         => '',
                'accessToken'        => '',
                'testMode'           => false,
                'clientLiveId'       => 'sq0idp-TtDyGP_2RfKYpFzrDqs0lw',
                'clientTestId'       => 'sandbox-sq0idb-Wxnxasx1NMG_ZyvM--JV4Q',
                'countryCode'        => '',
                'description'        => [
                    'enabled'     => false,
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => ''
                ],
                'metaData'           => [
                    'enabled'     => false,
                    'appointment' => null,
                    'package'     => null,
                    'event'       => null,
                    'cart'        => null
                ],
            ],
            'barion' => [
                'enabled'         => false,
                'sandboxMode'     => false,
                'livePOSKey'      => '',
                'sandboxPOSKey'   => '',
                'payeeEmail'      => '',
                'description'     => [
                    'enabled'     => false,
                    'appointment' => '',
                    'package'     => '',
                    'event'       => '',
                    'cart'        => '',
                ],
                'metaData'           => [
                    'enabled'     => false,
                    'appointment' => null,
                    'package'     => null,
                    'event'       => null,
                    'cart'        => null
                ],
            ],
        ];
    }

    /**
     * Init Payments Settings
     */
    private static function initPaymentsSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('payments');

        $settings = self::getDefaultPaymentsSettings($savedSettings);

        self::initSettings('payments', $settings);

        self::setNewSettingsToExistingSettings(
            'payments',
            [
                ['stripe', 'connect'],
                ['stripe', 'connect', 'capabilities'],
                ['stripe', 'description'],
                ['stripe', 'description', 'package'],
                ['stripe', 'description', 'cart'],
                ['stripe', 'metaData'],
                ['stripe', 'metaData', 'package'],
                ['stripe', 'metaData', 'cart'],
                ['stripe', 'manualCapture'],
                ['stripe', 'returnUrl'],
                ['stripe', 'address'],
                ['payPal', 'description'],
                ['payPal', 'description', 'package'],
                ['payPal', 'description', 'cart'],
                ['wc', 'onSiteIfFree'],
                ['wc', 'page'],
                ['wc', 'dashboard'],
                ['wc', 'skipCheckoutGetValueProcessing'],
                ['wc', 'skipGetItemDataProcessing'],
                ['wc', 'rules'],
                ['wc', 'redirectPage'],
                ['wc', 'bookMultiple'],
                ['wc', 'checkoutData'],
                ['wc', 'checkoutData', 'package'],
                ['wc', 'checkoutData', 'cart'],
                ['wc', 'checkoutData', 'translations'],
                ['wc', 'checkoutData', 'translations', 'appointment'],
                ['wc', 'checkoutData', 'translations', 'event'],
                ['wc', 'checkoutData', 'translations', 'package'],
                ['wc', 'checkoutData', 'translations', 'cart'],
                ['razorpay', 'name'],
                ['razorpay', 'description', 'cart'],
                ['razorpay', 'metaData', 'cart'],
                ['razorpay', 'name', 'cart'],
                ['mollie', 'description', 'cart'],
                ['mollie', 'metaData', 'cart'],
                ['mollie', 'cancelBooking'],
                ['square', 'enabled'],
                ['square', 'description', 'cart'],
                ['square', 'metaData', 'cart'],
                ['square', 'locationId'],
                ['square', 'accessToken'],
                ['square', 'testMode'],
                ['square', 'clientLiveId'],
                ['square', 'clientTestId'],
                ['square', 'countryCode'],
                ['barion', 'metaData'],
            ],
            $settings
        );
    }

    /**
     * Init Purchase Code Settings
     */
    private static function initActivationSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings('activation');

        $isNewInstallation = empty($savedSettings);

        $settings = [
            'showActivationSettings'        => true,
            'active'                        => false,
            'purchaseCodeStore'             => '',
            'envatoTokenEmail'              => '',
            'version'                       => '',
            'deleteTables'                  => false,
            'showAmeliaPromoCustomizePopup' => true,
            'showAmeliaSurvey'              => true,
            'showWelcomePage'               => true,
            'stash'                         => false,
            'responseErrorAsConflict'       => $savedSettings ? false : true,
            'hideTipsAndSuggestions'        => false,
            'hideUnavailableFeatures'       => false,
            'licence'                       => '',
            'disableUrlParams'              => $savedSettings ? false : true,
            'enableThriveItems'             => false,
            'customUrl'                     => [
                'enabled'     => false,
                'pluginPath'  => '/wp-content/plugins/ameliabooking/',
                'ajaxPath'    => '/wp-admin/admin-ajax.php',
                'uploadsPath' => '',
            ],
            'v3RelativePath'                => false,
            'v3AsyncLoading'                => false,
            'premiumBannerVisibility'       => true,
            'dismissibleBannerVisibility'   => true,
        ];

        self::initSettings('activation', $settings);

        $savedSettings['showAmeliaPromoCustomizePopup'] = true;

        $savedSettings['isNewInstallation'] = $isNewInstallation;

        self::initSettings('activation', $savedSettings, true);
    }

    /**
     * Init Labels Settings
     */
    private static function initLabelsSettings()
    {
        $settings = [
            'enabled'   => true,
            'employee'  => 'employee',
            'employees' => 'employees',
            'service'   => 'service',
            'services'  => 'services'
        ];

        self::initSettings('labels', $settings);
    }

    /**
     * Init Roles Settings
     *
     * @return array
     */
    public static function getDefaultRolesSettings()
    {
        return [
            'allowConfigureSchedule'      => false,
            'allowConfigureDaysOff'       => false,
            'allowConfigureSpecialDays'   => false,
            'allowConfigureServices'      => false,
            'allowWriteAppointments'      => false,
            'allowWriteCustomers'         => false,
            'automaticallyCreateCustomer' => false,
            'inspectCustomerInfo'         => false,
            'allowCustomerReschedule'     => false,
            'allowCustomerCancelPackages' => true,
            'allowCustomerDeleteProfile'  => false,
            'allowWriteEvents'            => false,
            'allowAdminBookAtAnyTime'     => false,
            'allowAdminBookOverApp'       => false,
            'adminServiceDurationAsSlot'  => false,
            'enabledHttpAuthorization'    => true,
            'customerCabinet'             => [
                'headerJwtSecret' => (new Token(null, 20))->getValue(),
                'urlJwtSecret'    => (new Token(null, 20))->getValue(),
                'tokenValidTime'  => 2592000,
                'pageUrl'         => '',
                'loginEnabled'    => true,
                'filterDate'      => false,
                'translations'    => [],
                'googleRecaptcha' => false,
            ],
            'providerCabinet'             => [
                'headerJwtSecret' => (new Token(null, 20))->getValue(),
                'urlJwtSecret'    => (new Token(null, 20))->getValue(),
                'tokenValidTime'  => 2592000,
                'pageUrl'         => '',
                'loginEnabled'    => true,
                'filterDate'      => false,
                'googleRecaptcha' => false,
            ],
            'urlAttachment'       => [
                'enabled'         => true,
                'headerJwtSecret' => (new Token(null, 20))->getValue(),
                'urlJwtSecret'    => (new Token(null, 20))->getValue(),
                'tokenValidTime'  => 2592000,
                'pageUrl'         => '',
                'loginEnabled'    => true,
                'filterDate'      => false,
            ],
            'limitPerCustomerService' => [
                'enabled'     => false,
                'numberOfApp' => 1,
                'timeFrame'   => 'day',
                'period'      => 1,
                'from'        => 'bookingDate'
            ],
            'limitPerCustomerPackage' => [
                'enabled'     => false,
                'numberOfApp' => 1,
                'timeFrame'   => 'day',
                'period'      => 1,
            ],
            'limitPerCustomerEvent' => [
                'enabled'     => false,
                'numberOfApp' => 1,
                'timeFrame'   => 'day',
                'period'      => 1,
                'from'        => 'bookingDate'
            ],
            'limitPerEmployee' => [
                'enabled'     => false,
                'numberOfApp' => 1,
                'timeFrame'   => 'day',
                'period'      => 1,
            ],
            'providerBadges'  => [
                'counter' => 3,
                'badges'  => [
                    [
                        'id'      => 1,
                        'content' => 'Most Popular',
                        'color'   => '#316bff'
                    ],
                    [
                        'id'      => 2,
                        'content' => 'Top Performer',
                        'color'   => '#06a192'
                    ],
                    [
                        'id'      => 3,
                        'content' => 'Exclusive',
                        'color'   => '#facc15'
                    ],
                ]
            ],
        ];
    }

    /**
     * Init Roles Settings
     */
    private static function initRolesSettings()
    {
        $settings = self::getDefaultRolesSettings();

        self::initSettings('roles', $settings);

        self::setNewSettingsToExistingSettings(
            'roles',
            [
                ['customerCabinet', 'filterDate'],
                ['customerCabinet', 'translations'],
                ['customerCabinet', 'headerJwtSecret'],
                ['customerCabinet', 'urlJwtSecret'],
                ['customerCabinet', 'googleRecaptcha'],
                ['providerCabinet', 'headerJwtSecret'],
                ['providerCabinet', 'urlJwtSecret'],
                ['providerCabinet', 'googleRecaptcha'],
                ['urlAttachment', 'headerJwtSecret'],
                ['urlAttachment', 'urlJwtSecret'],
            ],
            $settings
        );
    }

    /**
     * Get Appointments Settings
     *
     * @return array
     */
    public static function getDefaultAppointmentsSettings()
    {
        return [
            'isGloballyBusySlot'                => false,
            'bookMultipleTimes'                 => false,
            'allowBookingIfPending'             => true,
            'allowBookingIfNotMin'              => true,
            'openedBookingAfterMin'             => false,
            'cartPlaceholders'                  => '<!-- Content --><p>DateTime: %appointment_date_time%</p>',
            'cartPlaceholdersSms'               => 'DateTime: %appointment_date_time%',
            'cartPlaceholdersCustomer'          => '<!-- Content --><p>DateTime: %appointment_date_time%</p>',
            'cartPlaceholdersCustomerSms'       => 'DateTime: %appointment_date_time%',
            'recurringPlaceholders'             => 'DateTime: %appointment_date_time%',
            'recurringPlaceholdersSms'          => 'DateTime: %appointment_date_time%',
            'recurringPlaceholdersCustomer'     => 'DateTime: %appointment_date_time%',
            'recurringPlaceholdersCustomerSms'  => 'DateTime: %appointment_date_time%',
            'packagePlaceholders'               => 'DateTime: %appointment_date_time%',
            'packagePlaceholdersSms'            => 'DateTime: %appointment_date_time%',
            'packagePlaceholdersCustomer'       => 'DateTime: %appointment_date_time%',
            'packagePlaceholdersCustomerSms'    => 'DateTime: %appointment_date_time%',
            'groupAppointmentPlaceholder'       => 'Name: %customer_full_name%',
            'groupEventPlaceholder'             => 'Name: %customer_full_name%',
            'groupAppointmentPlaceholderSms'    => 'Name: %customer_full_name%',
            'groupEventPlaceholderSms'          => 'Name: %customer_full_name%',
            'translations'                      => [
                'cartPlaceholdersCustomer'         => null,
                'cartPlaceholdersCustomerSms'      => null,
                'recurringPlaceholdersCustomer'    => null,
                'recurringPlaceholdersCustomerSms' => null,
                'packagePlaceholdersCustomer'      => null,
                'packagePlaceholdersCustomerSms'   => null,
                'groupAppointmentPlaceholder'      => 'Name: %customer_full_name%',
                'groupEventPlaceholder'            => 'Name: %customer_full_name%',
                'groupAppointmentPlaceholderSms'   => 'Name: %customer_full_name%',
                'groupEventPlaceholderSms'         => 'Name: %customer_full_name%',
            ],
            'waitingListEvents'                 => [
                'addingMethod'                     => 'Manually'
            ],
            'qrCodeEvents'                      => [
                'enabled'                          => false,
            ],
            'waitingListAppointments'           => [
                'enabled'                          => false,
                'redirectUrlDenied'                => ''
            ],
            'pastDaysEvents'                       => 0,
            'employeeSelection'                    => 'random',
            'bringingAnyoneLogic'                  => 'additional',
        ];
    }

    /**
     * Init Appointments Settings
     */
    private static function initAppointmentsSettings()
    {
        $settings = self::getDefaultAppointmentsSettings();

        self::initSettings('appointments', $settings);

        self::setNewSettingsToExistingSettings(
            'appointments',
            [
                ['translations', 'cartPlaceholdersCustomer'],
                ['translations', 'cartPlaceholdersCustomerSms'],
            ],
            $settings
        );
    }

    /**
     * Init Web Hooks Settings
     */
    private static function initWebHooksSettings()
    {
        $settings = [];

        self::initSettings('webHooks', $settings);
    }

    /**
     * get Back Link Setting
     */
    private static function getBackLinkSetting()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $backLinksLabels = [
            'Generated with Amelia - WordPress Booking Plugin',
            'Powered by Amelia - WordPress Booking Plugin',
            'Booking by Amelia  - WordPress Booking Plugin',
            'Powered by Amelia - Appointment and Events Booking Plugin',
            'Powered by Amelia - Appointment and Event Booking Plugin',
            'Powered by Amelia - WordPress Booking Plugin',
            'Generated with Amelia - Appointment and Event Booking Plugin',
            'Booking Enabled by Amelia - Appointment and Event Booking Plugin',
        ];

        $backLinksUrls = [
            'https://wpamelia.com/?utm_source=lite&utm_medium=websites&utm_campaign=powerdby',
            'https://wpamelia.com/demos/?utm_source=lite&utm_medium=website&utm_campaign=powerdby#Features-list',
            'https://wpamelia.com/pricing/?utm_source=lite&utm_medium=website&utm_campaign=powerdby',
            'https://wpamelia.com/documentation/?utm_source=lite&utm_medium=website&utm_campaign=powerdby',
        ];

        return [
            'enabled' => $settingsService->getCategorySettings('general') === null,
            'label'   => $backLinksLabels[rand(0, 7)],
            'url'     => $backLinksUrls[rand(0, 3)],
        ];
    }

    /**
     * Add new settings ti global parent settings
     *
     * @param string $category
     * @param array  $pathsKeys
     * @param array  $initSettings
     */
    private static function setNewSettingsToExistingSettings($category, $pathsKeys, $initSettings)
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $savedSettings = $settingsService->getCategorySettings($category);

        $activationSettings = $settingsService->getCategorySettings('activation');
        $savedVersion = !empty($activationSettings['version']) ? $activationSettings['version'] : '0.0.0';

        $setSettings = false;

        foreach ($pathsKeys as $keys) {
            $current = &$savedSettings;

            $currentInit = &$initSettings;

            foreach ((array)$keys as $key) {
                if (!isset($current[$key])) {
                    $current[$key] = !empty($currentInit[$key]) ? $currentInit[$key] : null;

                    $setSettings = true;

                    continue 2;
                }

                // If the saved value is a JSON-encoded string but the new schema expects
                // a nested array (i.e. there are more keys to traverse), decode it so
                // that subsequent keys can be accessed without a PHP 8 TypeError.
                // This fix is only applied when upgrading from versions before 9.2
                if (version_compare($savedVersion, '9.2', '<') && is_string($current[$key])) {
                    $decoded = json_decode($current[$key], true);
                    if (is_array($decoded)) {
                        $current[$key] = $decoded;
                        $setSettings   = true;
                    } else {
                        // Scalar string with no deeper path possible — skip this path.
                        continue 2;
                    }
                } elseif (!is_array($current[$key]) && !is_null($current[$key])) {
                    // Boolean / int leaf where a nested array is expected — skip.
                    continue 2;
                }

                $current = &$current[$key];

                $currentInit = &$currentInit[$key];
            }
        }

        if ($setSettings) {
            self::initSettings($category, $savedSettings, true);
        }
    }

    /**
     * Init Social Login Settings
     */
    private static function initSocialLoginSettings()
    {
        $settings = [
            'facebookAppId'       => '',
            'facebookAppSecret'   => '',
        ];

        self::initSettings('socialLogin', $settings);
    }

    /**
     * Init Features and Integrations Settings
     */
    private static function initFeaturesIntegrationsSettings()
    {
        $settingsService = new SettingsService(new SettingsStorage());

        $saved = $settingsService->getAllSettingsCategorized();

        $old = empty($saved['activation']['isNewInstallation']);

        $starterAndUp = Licence::getLicence() === 'Developer' ||
            Licence::getLicence() === 'Pro' ||
            Licence::getLicence() === 'Basic' ||
            Licence::getLicence() === 'Starter';

        $basicAndUp = Licence::getLicence() === 'Developer' || Licence::getLicence() === 'Pro' || Licence::getLicence() === 'Basic';

        $proAndUp = Licence::getLicence() === 'Developer' || Licence::getLicence() === 'Pro';

        $settings = [
            'googleCalendar'        => [
                'enabled' => $old &&
                    $basicAndUp &&
                    !empty($saved['googleCalendar']['calendarEnabled']),
            ],
            'appleCalendar'         => [
                'enabled' => $old &&
                    $basicAndUp &&
                    !empty($saved['appleCalendar']['clientID']) &&
                    !empty($saved['appleCalendar']['clientSecret']),
            ],
            'outlookCalendar'       => [
                'enabled' => $old &&
                    $basicAndUp &&
                    !empty($saved['outlookCalendar']['calendarEnabled']),
            ],
            'zoom'                  => [
                'enabled' => $old &&
                    $basicAndUp &&
                    !empty($saved['zoom']['accountId']) &&
                    !empty($saved['zoom']['clientId']) &&
                    !empty($saved['zoom']['clientSecret']),
            ],
            'webhooks'              => [
                'enabled' => $basicAndUp,
            ],
            'facebookPixel'         => [
                'enabled' => $old && $starterAndUp && !empty($saved['facebookPixel']['id']),
            ],
            'googleAnalytics'       => [
                'enabled' => $old && $starterAndUp && !empty($saved['googleAnalytics']['id']),
            ],
            'lessonSpace'           => [
                'enabled' => $old && $starterAndUp && !empty($saved['lessonSpace']['apiKey']),
            ],
            'recaptcha'             => [
                'enabled' => $old &&
                    $starterAndUp &&
                    !empty($saved['general']['googleRecaptcha']['enabled']) &&
                    !empty($saved['general']['googleRecaptcha']['siteKey']) &&
                    !empty($saved['general']['googleRecaptcha']['secret']),
            ],
            'mollie'                => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['mollie']['enabled']),
            ],
            'wc'           => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['wc']['enabled']),
            ],
            'payPal'                => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['payPal']['enabled']),
            ],
            'stripe'                => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['stripe']['enabled']),
            ],
            'razorpay'              => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['razorpay']['enabled']),
            ],
            'square'                => [
                'enabled' => $old && !empty($saved['payments']['square']['enabled']),
            ],
            'barion'                => [
                'enabled' => $old && $basicAndUp && !empty($saved['payments']['barion']['enabled']),
            ],
            'packages'              => [
                'enabled' => $proAndUp,
            ],
            'resources'             => [
                'enabled' => $proAndUp,
            ],
            'customFields'          => [
                'enabled' => $basicAndUp,
            ],
            'coupons'               => [
                'enabled' => $old && $starterAndUp && !empty($saved['payments']['coupons']),
            ],
            'customNotifications'   => [
                'enabled' => $basicAndUp,
            ],
            'tax'                   => [
                'enabled' => $basicAndUp && (!$old || !empty($saved['payments']['taxes']['enabled'])),
            ],
            'invoices'              => [
                'enabled' => $basicAndUp,
            ],
            'whatsapp'              => [
                'enabled' => $old &&
                    $proAndUp &&
                    !empty($saved['notifications']['whatsAppEnabled']) &&
                    !empty($saved['notifications']['whatsAppPhoneID']) &&
                    !empty($saved['notifications']['whatsAppAccessToken']) &&
                    !empty($saved['notifications']['whatsAppBusinessID']),
            ],
            'recurringEvents'       => [
                'enabled' => $basicAndUp,
            ],
            'tickets'               => [
                'enabled' => $basicAndUp,
            ],
            'waitingList'           => [
                'enabled' => $proAndUp && (!$old || !empty($saved['appointments']['waitingListEvents']['enabled'])),
            ],
            'waitingListAppointments' => [
                'enabled' => $proAndUp && (!$old || !empty($saved['appointments']['waitingListAppointments']['enabled'])),
            ],
            'customPricing'         => [
                'enabled' => $basicAndUp,
            ],
            'recurringAppointments' => [
                'enabled' => $basicAndUp,
            ],
            'extras'                => [
                'enabled' => $starterAndUp,
            ],
            'cart'                  => [
                'enabled' => $old && $proAndUp && !empty($saved['payments']['cart']),
            ],
            'timezones'             => [
                'enabled' => $basicAndUp,
            ],
            'depositPayment'        => [
                'enabled' => $basicAndUp,
            ],
            'noShowTag'             => [
                'enabled' => $basicAndUp && (!$old || !empty($saved['roles']['enableNoShowTag'])),
            ],
            'apis'                  => [
                'enabled' => Licence::getLicence() === 'Developer',
            ],
            'buddyboss'             => [
                'enabled' => $basicAndUp && $old,
            ],
            'employeeBadge'         => [
                'enabled' => $basicAndUp,
            ],
            'mailchimp'             => [
                'enabled' => $old && $basicAndUp && !empty($saved['mailchimp']['accessToken']),
            ],
            'googleSocialLogin'     => [
                'enabled' => $old && $basicAndUp && !empty($saved['socialLogin']['enableGoogleLogin']),
            ],
            'facebookSocialLogin'   => [
                'enabled' => $old && $basicAndUp && !empty($saved['socialLogin']['enableFacebookLogin']),
            ],
            'eTickets'              => [
                'enabled' => $proAndUp && (!$old || !empty($saved['appointments']['qrCodeEvents']['enabled'])),
            ],
        ];

        self::initSettings('featuresIntegrations', $settings);
    }
}
