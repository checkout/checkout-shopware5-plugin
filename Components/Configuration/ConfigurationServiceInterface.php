<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\Configuration;

use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\PayPalPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SofortPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;
use CkoCheckoutPayment\Models\Configuration\GeneralConfiguration;
use CkoCheckoutPayment\Models\Configuration\GooglePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\PayPalConfiguration;
use CkoCheckoutPayment\Models\Configuration\SepaConfiguration;
use CkoCheckoutPayment\Models\Configuration\SofortConfiguration;
use Shopware\Components\Model\ModelEntity;

interface ConfigurationServiceInterface
{
    public const PAYMENT_METHODS = [
        ApplePayPaymentMethod::NAME => ApplePayConfiguration::class,
        GooglePayPaymentMethod::NAME => GooglePayConfiguration::class,
        CreditCardPaymentMethod::NAME => CreditCardConfiguration::class,
        PayPalPaymentMethod::NAME => PayPalConfiguration::class,
        SepaPaymentMethod::NAME => SepaConfiguration::class,
        SofortPaymentMethod::NAME => SofortConfiguration::class
    ];

    public function getGeneralConfiguration(?int $shopId, bool $useFallbackShop = true): GeneralConfiguration;

    public function getPaymentMethodConfiguration(string $paymentMethodName, ?int $shopId, bool $useFallbackShop = true): ModelEntity;

    public function getGooglePayEnvironment(?int $shopId): string;

    public function getApplePayEnvironment(?int $shopId): string;

    public function getGooglePayAllowedCardNetworks(?int $shopId): array;

    public function getApplePaySupportedNetworks(?int $shopId): array;

    public function getApplePayMerchantCapabilities(?int $shopId): array;

    public function isAutoCaptureEnabled(string $paymentMethodName, ?int $shopId): bool;

    public function isCreditCart3dsEnabled(?int $shopId): bool;

    public function updateConfiguration(string $paymentMethodName, array $data, int $shopId): void;
}
