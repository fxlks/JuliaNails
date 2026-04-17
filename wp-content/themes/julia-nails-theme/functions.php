<?php
function jn_load_resources() {
    wp_enqueue_style('style', get_stylesheet_directory_uri() . '/assets/css/style.css');
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
    register_nav_menus( array(
        'primary-menu' => __( 'Primary Menu', 'julia-nails' ),
    ) );
}
add_action( 'init', 'julia_nails_register_menus' );
function julia_nails_scripts() {
    // This links the styles.css file inside your assets/css folder
    wp_enqueue_style( 'julia-nails-custom', get_template_directory_uri() . '/assets/css/styles.css', array(), '1.0' );
    
    // This links the main style.css in your root folder (if you use it)
    wp_enqueue_style( 'julia-nails-main', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'julia_nails_scripts' );