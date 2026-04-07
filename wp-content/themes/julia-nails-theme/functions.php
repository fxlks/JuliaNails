<?php
function jn_load_resources() {
    wp_enqueue_style('style', get_stylesheet_directory_uri() . '/assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'jn_load_resources');