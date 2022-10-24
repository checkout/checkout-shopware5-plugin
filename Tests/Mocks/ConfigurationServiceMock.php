<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\GeneralConfiguration;
use Shopware\Components\Model\ModelEntity;

class ConfigurationServiceMock implements ConfigurationServiceInterface
{
    public function getGeneralConfiguration(?int $shopId, bool $useFallbackShop = true): GeneralConfiguration
    {
        // TODO: Implement getGeneralConfiguration() method.
    }

    public function getPaymentMethodConfiguration(string $paymentMethodName, ?int $shopId, bool $useFallbackShop = true): ModelEntity
    {
        if ($paymentMethodName === CreditCardPaymentMethod::NAME) {
            return new CreditCardConfigurationMock();
        }
    }

    public function getGooglePayEnvironment(?int $shopId): string
    {
        // TODO: Implement getGooglePayEnvironment() method.
    }

    public function getApplePayEnvironment(?int $shopId): string
    {
        // TODO: Implement getApplePayEnvironment() method.
    }

    public function getGooglePayAllowedCardNetworks(?int $shopId): array
    {
        // TODO: Implement getGooglePayAllowedCardNetworks() method.
    }

    public function getApplePaySupportedNetworks(?int $shopId): array
    {
        // TODO: Implement getApplePaySupportedNetworks() method.
    }

    public function getApplePayMerchantCapabilities(?int $shopId): array
    {
        // TODO: Implement getApplePayMerchantCapabilities() method.
    }

    public function isAutoCaptureEnabled(string $paymentMethodName, ?int $shopId): bool
    {
        // TODO: Implement isAutoCaptureEnabled() method.
    }

    public function isCreditCart3dsEnabled(?int $shopId): bool
    {
        // TODO: Implement isCreditCart3dsEnabled() method.
    }

    public function updateConfiguration(string $paymentMethodName, array $data, int $shopId): void
    {
        // TODO: Implement updateConfiguration() method.
    }
}