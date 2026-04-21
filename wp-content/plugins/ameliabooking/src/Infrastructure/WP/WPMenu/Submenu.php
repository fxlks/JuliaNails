<?php

namespace AmeliaBooking\Infrastructure\WP\WPMenu;

use AmeliaBooking\Infrastructure\Licence\Licence;

/**
 * Class Submenu
 *
 * @package AmeliaBooking\Infrastructure\WPMenu
 */
class Submenu
{
    /** @var SubmenuPageHandler $submenuHandler */
    private $submenuHandler;

    /** @var  array $menu */
    private $menu;

    /**
     * Submenu constructor.
     *
     * @param SubmenuPageHandler $submenuHandler
     * @param array              $menu
     */
    public function __construct($submenuHandler, $menu)
    {
        $this->submenuHandler = $submenuHandler;

        $this->menu = $menu;
    }

    /**
     * Add options in WP menu
     */
    public function addOptionsPages()
    {
        add_menu_page(
            'Amelia Booking',
            'Amelia',
            'amelia_read_menu',
            'amelia',
            '',
            AMELIA_URL . 'public/img/amelia-logo-admin-icon.svg'
        );

        foreach ($this->menu as $menu) {
            $this->handleMenuItem($menu);
        }

        $this->addSubmenuPage(
            'amelia',
            'Welcome',
            'Welcome',
            'amelia_read_menu',
            'wpamelia-welcome',
            function () {
                $this->submenuHandler->render('wpamelia-welcome');
            }
        );

        remove_submenu_page('amelia', 'amelia');

        add_action('admin_head', function () {
            remove_submenu_page('amelia', 'wpamelia-welcome');
        });

        if (!Licence::isPremium() && current_user_can('manage_options')) {
            $this->addUpgradeMenuItem();
        }
    }

    private function addUpgradeMenuItem()
    {
        $upgradeUrl = 'https://wpamelia.com/pricing/?utm_source=lite&utm_medium=dashboard&utm_content=amelia&utm_campaign=amelia-utm';

        $this->addSubmenuPage(
            'amelia',
            '',
            esc_html__('Upgrade', 'wpamelia'),
            'amelia_read_menu',
            'wpamelia-upgrade',
            '__return_null'
        );

        add_action('admin_init', function () use ($upgradeUrl) {
            $page = isset($_GET['page']) ? sanitize_key($_GET['page']) : '';

            if ('wpamelia-upgrade' === $page) {
                wp_redirect($upgradeUrl);
                exit;
            }
        });

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(
                'amelia-admin-menu',
                AMELIA_URL . 'public/css/backend/admin-menu.css',
                [],
                AMELIA_VERSION
            );

            wp_enqueue_script(
                'amelia-admin-menu',
                AMELIA_URL . 'public/js/backend/admin-menu.js',
                ['jquery'],
                AMELIA_VERSION,
                true
            );
        });
    }

    /**
     * Get the inline SVG diamond icon for locked features
     *
     * @return string
     */
    private function getDiamondIcon()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" '
            . 'style="width: 17px; height: 16px; margin-left: 8px; vertical-align: middle; display: inline-block;">'
            // phpcs:ignore Generic.Files.LineLength
            . '<path fill="currentColor" d="M17.166 1c.113 0 .308-.006.498.046.149.04.29.107.415.195.16.114.28.27.352.357l4.84 5.869c.077.094.162.197.228.291.054.076.113.17.16.284l.041.122.037.178c.025.18.013.363-.037.54a1.32 1.32 0 0 1-.2.405 5.2 5.2 0 0 1-.23.291L13.265 21.712c-.11.133-.222.27-.327.377a1.316 1.316 0 0 1-.486.33 1.305 1.305 0 0 1-.903 0 1.327 1.327 0 0 1-.487-.33c-.105-.107-.217-.244-.327-.377L.73 9.577c-.078-.094-.163-.196-.23-.29a1.32 1.32 0 0 1-.2-.406 1.32 1.32 0 0 1 0-.717l.042-.122c.046-.114.105-.208.159-.284.066-.094.15-.197.229-.291l4.84-5.87c.072-.087.19-.242.35-.356l.098-.062c.099-.058.206-.103.317-.133.19-.052.386-.046.499-.046h10.332ZM12 19.198l3.416-9.925H8.584L12 19.198Zm-1.725-.402L6.997 9.273H2.422l7.853 9.523Zm3.449 0 7.854-9.523h-4.575l-3.28 9.523ZM17.01 7.773h4.568l-4.305-5.221-.043-.052h-1.959l1.739 5.273Zm-8.44 0h6.86L13.692 2.5H10.31L8.57 7.773ZM6.77 2.5l-.043.052-4.305 5.22H6.99L8.73 2.5H6.77Z"/>'
            . '</svg>';
    }

    public function handleMenuItem(array $menu)
    {
        if (!isset($menu['menuSlug'])) {
            return;
        }

        // Hide locations if it shouldn't be shown based on license and hideUnavailableFeatures setting
        if ($menu['menuSlug'] === 'wpamelia-locations' && !Licence::shouldShowFeature('locations')) {
            return;
        }

        // Hide custom fields if it shouldn't be shown based on license and hideUnavailableFeatures setting
        if ($menu['menuSlug'] === 'wpamelia-customfields' && !Licence::shouldShowFeature('customFields')) {
            return;
        }

        // Add diamond icon for locked features
        if ($menu['menuSlug'] === 'wpamelia-locations' && Licence::isFeatureLocked('locations')) {
            $menu['menuTitle'] = '<span style="display: inline-flex; align-items: center;">'
                . $menu['menuTitle'] . $this->getDiamondIcon() . '</span>';
        }

        if ($menu['menuSlug'] === 'wpamelia-customfields' && Licence::isFeatureLocked('customFields')) {
            $menu['menuTitle'] = '<span style="display: inline-flex; align-items: center;">'
                . $menu['menuTitle'] . $this->getDiamondIcon() . '</span>';
        }

        $this->addSubmenuPage(
            $menu['parentSlug'],
            $menu['pageTitle'],
            $menu['menuTitle'],
            $menu['capability'],
            $menu['menuSlug'],
            function () use ($menu) {
                $this->submenuHandler->render($menu['menuSlug']);
            }
        );
    }

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     *
     * @param        $parentSlug
     * @param        $pageTitle
     * @param        $menuTitle
     * @param        $capability
     * @param        $menuSlug
     * @param string $function
     */
    private function addSubmenuPage($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $function = '')
    {
        add_submenu_page(
            $parentSlug,
            $pageTitle,
            $menuTitle,
            $capability,
            $menuSlug,
            $function
        );
    }
}
