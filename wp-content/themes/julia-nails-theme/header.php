<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    <header class="main-header">
    <div class="container navbar-flex">
        <div class="site-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="YOUR_LOGO_PATH_HERE.png" alt="Logo">
            </a>
        </div>

        <nav class="main-navigation">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary-menu',
                'container'      => false,
                'menu_class'     => 'nav-links',
            ) );
            ?>
        </nav>

        <div class="header-cta">
            <a href="/booking" class="btn-book">BOOK NOW</a>
        </div>
    </div>
</header>
</head>
<body>


