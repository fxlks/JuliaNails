<?php

namespace AmeliaBooking\Infrastructure\Licence;

use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\WP\SettingsService\SettingsStorage;

/**
 * Class Licence
 *
 * @package AmeliaBooking\Infrastructure\Licence
 */
class Licence extends Lite\Licence
{
    /**
     * License hierarchy mapping - higher numbers mean higher access levels
     */
    private static $licenseHierarchy = [
        LicenceConstants::LITE => 1,
        LicenceConstants::STARTER => 2,
        LicenceConstants::BASIC => 3,
        LicenceConstants::PRO => 4,
        LicenceConstants::DEVELOPER => 5
    ];

    /**
     * Feature to minimum required license mapping
     * Maps feature codes to the minimum license level required to access them
     */
    private static $featureLicenseRequirements = [
        'apis' => LicenceConstants::DEVELOPER,
        'appleCalendar' => LicenceConstants::BASIC,
        'barion' => LicenceConstants::BASIC,
        'buddyboss' => LicenceConstants::BASIC,
        'cart' => LicenceConstants::PRO,
        'coupons' => LicenceConstants::STARTER,
        'customFields' => LicenceConstants::BASIC,
        'customNotifications' => LicenceConstants::BASIC,
        'customPricing' => LicenceConstants::BASIC,
        'depositPayment' => LicenceConstants::BASIC,
        'employeeBadge' => LicenceConstants::BASIC,
        'eTickets' => LicenceConstants::PRO,
        'extras' => LicenceConstants::STARTER,
        'facebookPixel' => LicenceConstants::STARTER,
        'facebookSocialLogin' => LicenceConstants::BASIC,
        'googleAnalytics' => LicenceConstants::STARTER,
        'googleCalendar' => LicenceConstants::BASIC,
        'googleSocialLogin' => LicenceConstants::BASIC,
        'invoices' => LicenceConstants::BASIC,
        'lessonSpace' => LicenceConstants::STARTER,
        'locations' => LicenceConstants::BASIC,
        'mailchimp' => LicenceConstants::BASIC,
        'mollie' => LicenceConstants::BASIC,
        'mycred' => LicenceConstants::BASIC,
        'noShowTag' => LicenceConstants::BASIC,
        'outlookCalendar' => LicenceConstants::BASIC,
        'packages' => LicenceConstants::PRO,
        'payPal' => LicenceConstants::BASIC,
        'razorpay' => LicenceConstants::BASIC,
        'recaptcha' => LicenceConstants::STARTER,
        'recurringAppointments' => LicenceConstants::BASIC,
        'recurringEvents' => LicenceConstants::BASIC,
        'resources' => LicenceConstants::PRO,
        'square' => LicenceConstants::LITE,
        'stripe' => LicenceConstants::BASIC,
        'tax' => LicenceConstants::BASIC,
        'tickets' => LicenceConstants::BASIC,
        'timezones' => LicenceConstants::BASIC,
        'waitingList' => LicenceConstants::PRO,
        'waitingListAppointments' => LicenceConstants::PRO,
        'wc' => LicenceConstants::BASIC,
        'webhooks' => LicenceConstants::BASIC,
        'whatsapp' => LicenceConstants::PRO,
        'zoom' => LicenceConstants::BASIC,
    ];

    /**
     * Map of license names to their corresponding class names
     */
    private static $licenseClassMap = [
        LicenceConstants::LITE => 'AmeliaBooking\Infrastructure\Licence\Lite\Licence',
        LicenceConstants::STARTER => 'AmeliaBooking\Infrastructure\Licence\Lite\Licence',
        LicenceConstants::BASIC => 'AmeliaBooking\Infrastructure\Licence\Lite\Licence',
        LicenceConstants::PRO => 'AmeliaBooking\Infrastructure\Licence\Lite\Licence',
        LicenceConstants::DEVELOPER => 'AmeliaBooking\Infrastructure\Licence\Lite\Licence',
    ];

    /**
     * Get the appropriate license class based on settings (only in development mode)
     *
     * @return string The fully qualified class name of the license
     */
    private static function getLicenseClass()
    {
        // In production, always use the parent class (Lite)
        if (AMELIA_PRODUCTION) {
            return 'AmeliaBooking\Infrastructure\Licence\Lite\Licence';
        }

        // In development, get the license from settings
        $settingsService = new SettingsService(new SettingsStorage());
        $currentLicense = $settingsService->getSetting('activation', 'licence');
        $currentLicense = !empty($currentLicense) ? $currentLicense : LicenceConstants::DEVELOPER;

        // Return the appropriate license class
        return self::$licenseClassMap[$currentLicense] ?? 'AmeliaBooking\Infrastructure\Licence\Lite\Licence';
    }

    /**
     * Checks if the current license grants access to the specified license level.
     *
     * For example, if the user's license is 'Lite' and the function is called with 'Basic', it will return true.
     * If the user's license is 'Lite' and the function is called with 'Lite', it will return false.
     *
     * Usage examples:
     * - Licence::hasLicenseAccess(LicenseConstants::BASIC)
     * - Licence::hasLicenseAccess(LicenseConstants::PRO)
     *
     * @param string $requiredLicense The license level required for access (use class constants)
     * @return bool True if current license has access, false otherwise
     */
    public static function hasLicenseAccess($requiredLicense)
    {
        $currentLicense = self::$licence;

        if (!AMELIA_PRODUCTION) {
            /** @var SettingsService $settingsService */
            $settingsService = new SettingsService(new SettingsStorage());

            $currentLicense = $settingsService->getSetting('activation', 'licence');
            $currentLicense = !empty($currentLicense) ? $currentLicense : 'Lite';
        }

        // If either license is not in our hierarchy, fall back to exact match
        if (!isset(self::$licenseHierarchy[$currentLicense]) || !isset(self::$licenseHierarchy[$requiredLicense])) {
            return $currentLicense === $requiredLicense;
        }

        // Check if current license level is greater than or equal to required level
        return self::$licenseHierarchy[$currentLicense] >= self::$licenseHierarchy[$requiredLicense];
    }

    /**
     * Checks if the current license has access to a specific feature.
     *
     * @param string $featureCode The feature code (e.g., 'googleCalendar', 'packages', etc.)
     * @return bool True if current license has access to the feature, false otherwise
     */
    public static function hasFeatureAccess($featureCode)
    {
        // If feature doesn't have a license requirement, assume it's available to all
        if (!isset(self::$featureLicenseRequirements[$featureCode])) {
            return true;
        }

        $requiredLicense = self::$featureLicenseRequirements[$featureCode];
        return self::hasLicenseAccess($requiredLicense);
    }

    /**
     * Features that are not stored in featuresIntegrations settings
     * These are license-gated features without an on/off toggle
     */
    private static $nonToggleableFeatures = ['locations'];

    /**
     * Checks if a feature should be shown in the UI (menu, pages, etc.)
     *
     * Logic for regular features:
     * 1. If feature is enabled in settings → show it
     * 2. If feature is disabled in settings:
     *    - If user has license access → hide it (don't show disabled features they can enable)
     *    - If user doesn't have license access → check hideUnavailableFeatures setting
     *
     * Logic for non-toggleable features (locations):
     * - These don't have an on/off toggle in featuresIntegrations
     * - If user has license access → always show
     * - If user doesn't have license access → check hideUnavailableFeatures setting
     *
     * Note: For Lite license, unavailable features are always shown (hideUnavailableFeatures is ignored)
     *
     * @param string $featureCode The feature code (e.g., 'packages', 'customFields', 'locations')
     * @return bool True if feature should be shown, false if it should be hidden
     */
    public static function shouldShowFeature($featureCode)
    {
        $settingsService = new SettingsService(new SettingsStorage());

        // Special handling for non-toggleable features (locations)
        // These are not stored in featuresIntegrations and only have license restrictions
        if (in_array($featureCode, self::$nonToggleableFeatures)) {
            // If user has license access, always show it
            if (self::hasFeatureAccess($featureCode)) {
                return true;
            }

            // User doesn't have license access
            // Check hideUnavailableFeatures setting (Lite always shows unavailable features)
            $hideUnavailableFeatures = self::getHideUnavailableFeatures($settingsService);
            return !$hideUnavailableFeatures;
        }

        // Standard feature handling (stored in featuresIntegrations)
        // Check if feature is enabled in settings
        $isFeatureEnabled = $settingsService->isFeatureEnabled($featureCode);

        // If feature is enabled in settings, always show it
        if ($isFeatureEnabled) {
            return true;
        }

        // Feature is disabled in settings
        // Check if user has license access to this feature
        $hasLicenseAccess = self::hasFeatureAccess($featureCode);

        // If user has license access, don't show disabled features
        if ($hasLicenseAccess) {
            return false;
        }

        // User doesn't have license access to this disabled feature
        // Check hideUnavailableFeatures setting (Lite always shows unavailable features)
        $hideUnavailableFeatures = self::getHideUnavailableFeatures($settingsService);

        // If hideUnavailableFeatures is false/null, show locked features
        // If hideUnavailableFeatures is true, hide locked features
        return !$hideUnavailableFeatures;
    }

    /**
     * Get the effective hideUnavailableFeatures value
     * For Lite license, always return false (always show unavailable features)
     * For other licenses, use the setting value
     *
     * @param SettingsService $settingsService
     * @return bool
     */
    private static function getHideUnavailableFeatures($settingsService)
    {
        // For Lite license, always show unavailable features
        if (self::getLicence() === LicenceConstants::LITE) {
            return false;
        }

        return (bool) $settingsService->getSetting('activation', 'hideUnavailableFeatures');
    }

    /**
     * Checks if a feature is locked (visible but not accessible due to license restrictions)
     *
     * A feature is locked when:
     * - It should be shown (shouldShowFeature returns true)
     * - BUT the user doesn't have license access to it
     *
     * For non-toggleable features (locations):
     * - Locked = visible but no license access
     *
     * For regular features:
     * - Locked = visible but not enabled (which implies no license access since
     *   users with license access who have disabled features won't see them)
     *
     * @param string $featureCode The feature code (e.g., 'packages', 'customFields', 'locations')
     * @return bool True if feature is locked, false otherwise
     */
    public static function isFeatureLocked($featureCode)
    {
        // Feature must be visible to be considered "locked"
        if (!self::shouldShowFeature($featureCode)) {
            return false;
        }

        // Feature is locked if user doesn't have license access
        return !self::hasFeatureAccess($featureCode);
    }

    /**
     * Filters feature settings array to disable features not available in current license.
     * Preserves the original structure but sets 'enabled' to false for unavailable features.
     *
     * @param array $featuresIntegrations Array of feature settings
     * @return array Filtered feature settings with license checks applied
     */
    public static function filterFeaturesByLicense($featuresIntegrations)
    {
        if (!is_array($featuresIntegrations)) {
            return $featuresIntegrations;
        }

        foreach ($featuresIntegrations as $featureCode => $featureSettings) {
            // Check if this feature has license restrictions and if user has access
            if (!self::hasFeatureAccess($featureCode)) {
                // Feature is not available in current license - force disable it
                if (is_array($featureSettings) && isset($featureSettings['enabled'])) {
                    $featuresIntegrations[$featureCode]['enabled'] = false;
                }
            }
        }

        return $featuresIntegrations;
    }

    /**
     * Checks if a feature is both enabled in settings AND available in current license.
     * This is a convenience method for use in SettingsStorage and other places.
     *
     * @param string $featureCode The feature code (e.g., 'googleCalendar', 'packages')
     * @param array|null $featureSettings The feature settings array from database
     * @return bool True if feature is enabled and license has access, false otherwise
     */
    public static function isFeatureEnabledWithLicense($featureCode, $featureSettings)
    {
        // Check if feature settings exist and are valid
        if (!is_array($featureSettings) || !isset($featureSettings['enabled'])) {
            return false;
        }

        // Feature must be enabled in settings AND license must have access to it
        return $featureSettings['enabled'] && self::hasFeatureAccess($featureCode);
    }

    /**
     * Override getCommands to delegate to the appropriate license class in development mode
     *
     * @param \AmeliaBooking\Infrastructure\Common\Container $c
     * @return array
     */
    public static function getCommands($c)
    {
        // In production, use normal inheritance (class extends were changed by build scripts)
        if (AMELIA_PRODUCTION) {
            return parent::getCommands($c);
        }

        // In development, dynamically load the appropriate license class
        $licenseClass = self::getLicenseClass();
        return $licenseClass::getCommands($c);
    }

    /**
     * Override setRoutes to delegate to the appropriate license class in development mode
     *
     * @param \Slim\App $app
     * @param \AmeliaBooking\Infrastructure\Common\Container $container
     * @return void
     */
    public static function setRoutes($app, $container)
    {
        // In production, use normal inheritance (class extends were changed by build scripts)
        if (AMELIA_PRODUCTION) {
            parent::setRoutes($app, $container);
            return;
        }

        // In development, dynamically load the appropriate license class
        $licenseClass = self::getLicenseClass();
        $licenseClass::setRoutes($app, $container);
    }

    /**
     * Override getPaddleUrl to delegate to the appropriate license class in development mode
     *
     * @return string
     */
    public static function getPaddleUrl()
    {
        // In production, use normal inheritance (class extends were changed by build scripts)
        if (AMELIA_PRODUCTION) {
            return parent::getPaddleUrl();
        }

        // In development, dynamically load the appropriate license class
        $licenseClass = self::getLicenseClass();
        return $licenseClass::getPaddleUrl();
    }

    /**
     * Get the current license name
     * This is kept for backward compatibility but should use hasLicenseAccess() instead
     *
     * @return string
     */
    public static function getLicence()
    {
        if (!AMELIA_PRODUCTION) {
            $settingsService = new SettingsService(new SettingsStorage());
            $currentLicense = $settingsService->getSetting('activation', 'licence');
            return !empty($currentLicense) ? $currentLicense : LicenceConstants::DEVELOPER;
        }

        return self::$licence;
    }

    /**
     * Check if current license is premium
     * This is kept for backward compatibility but should use hasLicenseAccess() instead
     *
     * @return bool
     */
    public static function isPremium()
    {
        if (!AMELIA_PRODUCTION) {
            $settingsService = new SettingsService(new SettingsStorage());
            $currentLicense = $settingsService->getSetting('activation', 'licence');
            $currentLicense = !empty($currentLicense) ? $currentLicense : LicenceConstants::DEVELOPER;

            // Lite is the only non-premium license
            return $currentLicense !== LicenceConstants::LITE;
        }

        return self::$premium;
    }
}
