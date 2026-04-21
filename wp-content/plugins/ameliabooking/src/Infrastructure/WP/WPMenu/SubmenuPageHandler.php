<?php

namespace AmeliaBooking\Infrastructure\WP\WPMenu;

use AmeliaBooking\Application\Services\Helper\HelperService;
use AmeliaBooking\Domain\Services\DateTime\DateTimeService;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\WP\Integrations\WooCommerce\WooCommerceService;
use AmeliaBooking\Infrastructure\WP\Translations\BackendStrings;
use AmeliaBooking\Infrastructure\Licence\Licence;

/**
 * Renders menu pages
 */
class SubmenuPageHandler
{
    /** @var SettingsService $settingsService */
    private $settingsService;

    /**
     * SubmenuPageHandler constructor.
     *
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Submenu page render function
     *
     * @param $page
     */
    public function render($page)
    {

        $this->renderRedesign($page);
    }

    // TODO - Redesign: Finish & Refactor method
    private function renderRedesign($page)
    {
        $testFlagFileExists = file_exists(AMELIA_PATH . '/test_mode.flag');
        define('AMELIA_TEST', $testFlagFileExists);

        $isTestEnv = defined('AMELIA_TEST') && AMELIA_TEST;
        $isDev     = defined('AMELIA_DEV') && AMELIA_DEV && !$isTestEnv;
        $scriptId  = $isDev ? 'amelia_dev_main_script' : 'amelia_prod_main_script';

        // Enqueue V3 scripts for customize page
        if ($page === 'wpamelia-customize') {
            $this->enqueueV3Scripts();
        }

        if ($isDev) {
            wp_enqueue_script(
                'amelia_dev_vite_client',
                'http://localhost:5173/@vite/client',
                [],
                null,
                false
            );

            wp_enqueue_script(
                $scriptId,
                'http://localhost:5173/src/main.ts',
                [],
                null,
                true
            );
        } else {
            wp_enqueue_script(
                $scriptId,
                AMELIA_URL . 'redesign/dist/index.js',
                [],
                AMELIA_VERSION,
                true
            );

            wp_enqueue_style(
                'amelia_prod_main_style',
                AMELIA_URL . 'redesign/dist/index.css',
                [],
                AMELIA_VERSION
            );
        }

        // WordPress enqueue
        wp_enqueue_media();

        $wcSettings = $this->settingsService->getSetting('payments', 'wc');

        if ($wcSettings['enabled'] && WooCommerceService::isEnabled()) {
            wp_localize_script(
                $scriptId,
                'wpAmeliaWcProducts',
                WooCommerceService::getInitialProducts()
            );
        }

        // Settings Localization
        wp_localize_script(
            $scriptId,
            'wpAmeliaSettings',
            $this->settingsService->getBackendSettings()
        );

        // Strings Localization
        wp_localize_script(
            $scriptId,
            'wpAmeliaLabels',
            BackendStrings::getAllStrings(),
        );

        // Paddle
        if (in_array($page, ['wpamelia-notifications', 'wpamelia-settings'])) {
            wp_enqueue_script('amelia_paddle', Licence::getPaddleUrl());
        }

        // Include the generic page template
        include AMELIA_PATH . '/view/backend/redesign/page.php';
    }

    /**
     * Enqueue V3 scripts for embedding in redesign app
     */
    private function enqueueV3Scripts()
    {
        $scriptId = AMELIA_DEV ? 'amelia_booking_scripts_dev_vite' : 'amelia_booking_script_index';

        if (AMELIA_DEV) {
            wp_enqueue_script(
                'amelia_booking_scripts_dev_vite',
                'http://localhost:3000/@vite/client',
                [],
                null,
                false
            );

            wp_enqueue_script(
                'amelia_booking_scripts_dev_main',
                'http://localhost:3000/src/assets/js/admin/admin.js',
                [],
                null,
                true
            );
        } else {
            wp_enqueue_script(
                $scriptId,
                AMELIA_URL . 'v3/public/assets/admin.daae4f75.js',
                [],
                AMELIA_VERSION,
                true
            );
        }

        wp_localize_script(
            $scriptId,
            'localeLanguage',
            [AMELIA_LOCALE]
        );

        wp_localize_script(
            $scriptId,
            'wpAmeliaLanguages',
            HelperService::getLanguages()
        );

        // Settings Localization
        wp_localize_script(
            $scriptId,
            'wpAmeliaSettings',
            $this->settingsService->getFrontendSettings()
        );

        // Labels
        wp_localize_script(
            $scriptId,
            'wpAmeliaLabels',
            BackendStrings::getAllStrings(),
        );

        wp_localize_script(
            $scriptId,
            'wpAmeliaTimeZone',
            [DateTimeService::getTimeZone()->getName()]
        );

        wp_localize_script(
            $scriptId,
            'wpAmeliaUrls',
            [
                'wpAmeliaUseUploadsAmeliaPath' => AMELIA_UPLOADS_FILES_PATH_USE,
                'wpAmeliaPluginURL'            => AMELIA_URL,
                'wpAmeliaPluginAjaxURL'        => AMELIA_ACTION_URL
            ]
        );
    }
}
