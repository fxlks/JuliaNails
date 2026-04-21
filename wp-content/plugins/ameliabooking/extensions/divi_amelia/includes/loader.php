<?php

if (!class_exists('ET_Builder_Element')) {
    return;
}

$module_files = glob(__DIR__ . '/modules/*/*.php');

$hidden_modules = ['Search', 'Events', 'Booking', 'Catalog'];

// Check if we're in Divi builder context (admin or visual builder)
$is_builder = is_admin()
    || isset($_GET['et_fb'])
    || (function_exists('et_core_is_fb_enabled') && et_core_is_fb_enabled());

foreach ((array) $module_files as $module_file) {
    if ($module_file && preg_match("/\/modules\/\b([^\/]+)\/\\1\.php$/", $module_file, $matches)) {
        // Skip hidden modules in builder, but load them on frontend for existing pages
        if (in_array($matches[1], $hidden_modules) && $is_builder) {
            continue;
        }
        require_once $module_file;
    }
}
