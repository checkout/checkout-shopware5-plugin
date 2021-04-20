<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\ApiClient;

use Checkout\CheckoutApi;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;

class CheckoutApiClientService implements CheckoutApiClientServiceInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function createClient(?int $shopId): CheckoutApi
    {
        $configuration = $this->configurationService->getGeneralConfiguration($shopId);

        return new CheckoutApi($configuration->getPrivateKey(), $configuration->isSandboxModeEnabled(), $configuration->getPublicKey());
    }

    public function getPublicKey(?int $shopId): ?string
    {
        $configuration = $this->configurationService->getGeneralConfiguration($shopId);

        return $configuration->getPublicKey();
    }

    public function isSandboxMode(?int $shopId): bool
    {
        $configuration = $this->configurationService->getGeneralConfiguration($shopId);

        return $configuration->isSandboxModeEnabled();
    }
}
