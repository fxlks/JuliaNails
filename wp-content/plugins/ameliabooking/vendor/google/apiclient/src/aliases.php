<?php

if (class_exists('AmeliaVendor_Google_Client', false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}

$classMap = [
    'AmeliaVendor\\Google\\Client' => 'AmeliaVendor_Google_Client',
    'AmeliaVendor\\Google\\Service' => 'AmeliaVendor_Google_Service',
    'AmeliaVendor\\Google\\AccessToken\\Revoke' => 'AmeliaVendor_Google_AccessToken_Revoke',
    'AmeliaVendor\\Google\\AccessToken\\Verify' => 'AmeliaVendor_Google_AccessToken_Verify',
    'AmeliaVendor\\Google\\Model' => 'AmeliaVendor_Google_Model',
    'AmeliaVendor\\Google\\Utils\\UriTemplate' => 'AmeliaVendor_Google_Utils_UriTemplate',
    'AmeliaVendor\\Google\\AuthHandler\\Guzzle6AuthHandler' => 'AmeliaVendor_Google_AuthHandler_Guzzle6AuthHandler',
    'AmeliaVendor\\Google\\AuthHandler\\Guzzle7AuthHandler' => 'AmeliaVendor_Google_AuthHandler_Guzzle7AuthHandler',
    'AmeliaVendor\\Google\\AuthHandler\\AuthHandlerFactory' => 'AmeliaVendor_Google_AuthHandler_AuthHandlerFactory',
    'AmeliaVendor\\Google\\Http\\Batch' => 'AmeliaVendor_Google_Http_Batch',
    'AmeliaVendor\\Google\\Http\\MediaFileUpload' => 'AmeliaVendor_Google_Http_MediaFileUpload',
    'AmeliaVendor\\Google\\Http\\REST' => 'AmeliaVendor_Google_Http_REST',
    'AmeliaVendor\\Google\\Task\\Retryable' => 'Google_Task_Retryable',
    'AmeliaVendor\\Google\\Task\\Exception' => 'AmeliaVendor_Google_Task_Exception',
    'AmeliaVendor\\Google\\Task\\Runner' => 'AmeliaVendor_Google_Task_Runner',
    'AmeliaVendor\\Google\\Collection' => 'AmeliaVendor_Google_Collection',
    'AmeliaVendor\\Google\\Service\\Exception' => 'AmeliaVendor_Google_Service_Exception',
    'AmeliaVendor\\Google\\Service\\Resource' => 'AmeliaVendor_Google_Service_Resource',
    'AmeliaVendor\\Google\\Exception' => 'AmeliaVendor_Google_Exception',
];

foreach ($classMap as $class => $alias) {
    class_alias($class, $alias);
}

/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class AmeliaVendor_Google_Task_Composer extends \AmeliaVendor\Google\Task\Composer
{
}

/** @phpstan-ignore-next-line */
if (\false) {
    class AmeliaVendor_Google_AccessToken_Revoke extends \AmeliaVendor\Google\AccessToken\Revoke
    {
    }
    class AmeliaVendor_Google_AccessToken_Verify extends \AmeliaVendor\Google\AccessToken\Verify
    {
    }
    class AmeliaVendor_Google_AuthHandler_AuthHandlerFactory extends \AmeliaVendor\Google\AuthHandler\AuthHandlerFactory
    {
    }
    class AmeliaVendor_Google_AuthHandler_Guzzle6AuthHandler extends \AmeliaVendor\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    class AmeliaVendor_Google_AuthHandler_Guzzle7AuthHandler extends \AmeliaVendor\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    class AmeliaVendor_Google_Client extends \AmeliaVendor\Google\Client
    {
    }
    class AmeliaVendor_Google_Collection extends \AmeliaVendor\Google\Collection
    {
    }
    class AmeliaVendor_Google_Exception extends \AmeliaVendor\Google\Exception
    {
    }
    class AmeliaVendor_Google_Http_Batch extends \AmeliaVendor\Google\Http\Batch
    {
    }
    class AmeliaVendor_Google_Http_MediaFileUpload extends \AmeliaVendor\Google\Http\MediaFileUpload
    {
    }
    class AmeliaVendor_Google_Http_REST extends \AmeliaVendor\Google\Http\REST
    {
    }
    class AmeliaVendor_Google_Model extends \AmeliaVendor\Google\Model
    {
    }
    class AmeliaVendor_Google_Service extends \AmeliaVendor\Google\Service
    {
    }
    class AmeliaVendor_Google_Service_Exception extends \AmeliaVendor\Google\Service\Exception
    {
    }
    class AmeliaVendor_Google_Service_Resource extends \AmeliaVendor\Google\Service\Resource
    {
    }
    class AmeliaVendor_Google_Task_Exception extends \AmeliaVendor\Google\Task\Exception
    {
    }
    interface Google_Task_Retryable extends \AmeliaVendor\Google\Task\Retryable
    {
    }
    class AmeliaVendor_Google_Task_Runner extends \AmeliaVendor\Google\Task\Runner
    {
    }
    class AmeliaVendor_Google_Utils_UriTemplate extends \AmeliaVendor\Google\Utils\UriTemplate
    {
    }
}
