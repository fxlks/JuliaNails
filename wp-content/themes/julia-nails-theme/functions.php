<?php
function jn_load_resources() {
    wp_enqueue_style('julia-nails-custom', get_template_directory_uri() . '/assets/css/styles.css', array(), '1.0');
    wp_enqueue_style('julia-nails-main', get_stylesheet_uri());
    wp_enqueue_script('tailwind', 'https://cdn.tailwindcss.com', array(), null, false);
}
add_action('wp_enqueue_scripts', 'jn_load_resources');

function add_typekit_fonts() {
    wp_enqueue_style(
        'typekit-fonts', 
        'https://use.typekit.net/wzy5fff.css',
        array(), 
        null
    );
}
add_action('wp_enqueue_scripts', 'add_typekit_fonts');

function julia_nails_register_menus() {
    register_nav_menus(array(
        'primary-menu' => __('Primary Menu', 'julia-nails'),
    ));
}

function julia_nails_theme_setup() {
    add_theme_support('menus');
}
add_action('after_setup_theme', 'julia_nails_theme_setup');
add_action('init', 'julia_nails_register_menus');


