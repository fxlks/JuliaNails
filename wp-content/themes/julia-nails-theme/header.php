<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="main-header">
    <div class="container navbar-flex">
        <div class="site-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="http://julianails.local/wp-content/uploads/2026/04/ChatGPT-Image-Apr-20-2026-09_56_21-PM.png" alt="Logo">
            </a>
        </div>

        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>

        <nav class="main-navigation">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary-menu',
                'container'      => false,
                'menu_class'     => 'nav-links',
            ) );
            ?>
            <a href="/booking" class="btn-book mobile-cta">BOOK NOW</a>
        </nav>

        <div class="header-cta desktop-only">
            <a href="/booking" class="btn-book">BOOK NOW</a>
        </div>
    </div>
</header>