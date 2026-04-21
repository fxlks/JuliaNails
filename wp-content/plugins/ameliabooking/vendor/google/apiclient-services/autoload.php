<?php

// For older (pre-2.7.2) verions of google/apiclient
if (
    file_exists(__DIR__ . '/../apiclient/src/Google/Client.php')
    && !class_exists('AmeliaVendor_Google_Client', false)
) {
    require_once(__DIR__ . '/../apiclient/src/Google/Client.php');
    if (
        defined('AmeliaVendor_Google_Client::LIBVER')
        && version_compare(AmeliaVendor_Google_Client::LIBVER, '2.7.2', '<=')
    ) {
        $servicesClassMap = [
            'AmeliaVendor\\Google\\Client' => 'AmeliaVendor_Google_Client',
            'AmeliaVendor\\Google\\Service' => 'AmeliaVendor_Google_Service',
            'AmeliaVendor\\Google\\Service\\Resource' => 'AmeliaVendor_Google_Service_Resource',
            'AmeliaVendor\\Google\\Model' => 'AmeliaVendor_Google_Model',
            'AmeliaVendor\\Google\\Collection' => 'AmeliaVendor_Google_Collection',
        ];
        foreach ($servicesClassMap as $alias => $class) {
            class_alias($class, $alias);
        }
    }
}
spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Google_Service_')) {
        // Autoload the new class, which will also create an alias for the
        // old class by changing underscores to namespaces:
        //     Google_Service_Speech_Resource_Operations
        //      => AmeliaVendor\Google\Service\Speech\Resource\Operations
        $classExists = class_exists($newClass = str_replace('_', '\\', $class));
        if ($classExists) {
            return true;
        }
    }
}, true, true);
