<?php

// File generated from our OpenAPI spec
namespace AmeliaVendor\Stripe\Service\TestHelpers;

/**
 * Service factory class for API resources in the TestHelpers namespace.
 *
 * @property ConfirmationTokenService $confirmationTokens
 * @property CustomerService $customers
 * @property Issuing\IssuingServiceFactory $issuing
 * @property RefundService $refunds
 * @property Terminal\TerminalServiceFactory $terminal
 * @property TestClockService $testClocks
 * @property Treasury\TreasuryServiceFactory $treasury
 */
class TestHelpersServiceFactory extends \AmeliaVendor\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = ['confirmationTokens' => ConfirmationTokenService::class, 'customers' => CustomerService::class, 'issuing' => \AmeliaVendor\Stripe\Service\TestHelpers\Issuing\IssuingServiceFactory::class, 'refunds' => RefundService::class, 'terminal' => \AmeliaVendor\Stripe\Service\TestHelpers\Terminal\TerminalServiceFactory::class, 'testClocks' => TestClockService::class, 'treasury' => \AmeliaVendor\Stripe\Service\TestHelpers\Treasury\TreasuryServiceFactory::class];
    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}