<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\Configuration;

use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;
use CkoCheckoutPayment\Models\Configuration\GeneralConfiguration;
use CkoCheckoutPayment\Models\Configuration\GooglePayConfiguration;
use Shopware\Components\Model\ModelEntity;
use Shopware\Components\Model\ModelManager;

class ConfigurationService implements ConfigurationServiceInterface
{
    private const GOOGLE_PAY_ENV_TEST = 'test';
    private const GOOGLE_PAY_ENV_PRODUCTION = 'production';

    private const APPLE_PAY_ENV_TEST = 'sandbox';
    private const APPLE_PAY_ENV_PRODUCTION = 'live';

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(
        DependencyProviderServiceInterface $dependencyProviderService,
        ModelManager $modelManager
    ) {
        $this->modelManager = $modelManager;
        $this->dependencyProviderService = $dependencyProviderService;
    }

    public function getGeneralConfiguration(?int $shopId, bool $useFallbackShop = true): GeneralConfiguration
    {
        /** @var GeneralConfiguration $configuration */
        $configuration = $this->getConfiguration(GeneralConfiguration::class, $shopId, $useFallbackShop);

        return $configuration;
    }

    public function getPaymentMethodConfiguration(string $paymentMethodName, ?int $shopId, bool $useFallbackShop = true): ModelEntity
    {
        foreach (self::PAYMENT_METHODS as $name => $entityClass) {
            if ($paymentMethodName === $name) {
                return $this->getConfiguration($entityClass, $shopId, $useFallbackShop);
            }
        }

        throw new \RuntimeException(
            sprintf(
                'payment method %s has no configuration entity',
                $paymentMethodName
            )
        );
    }

    public function getGooglePayEnvironment(?int $shopId): string
    {
        $configuration = $this->getGeneralConfiguration($shopId);

        if ($configuration->isSandboxModeEnabled()) {
            return strtoupper(self::GOOGLE_PAY_ENV_TEST);
        }

        return strtoupper(self::GOOGLE_PAY_ENV_PRODUCTION);
    }

    public function getApplePayEnvironment(?int $shopId): string
    {
        $configuration = $this->getGeneralConfiguration($shopId);

        if ($configuration->isSandboxModeEnabled()) {
            return self::APPLE_PAY_ENV_TEST;
        }

        return self::APPLE_PAY_ENV_PRODUCTION;
    }

    public function getGooglePayAllowedCardNetworks(?int $shopId): array
    {
        /** @var GooglePayConfiguration $configuration */
        $configuration = $this->getPaymentMethodConfiguration(GooglePayPaymentMethod::NAME, $shopId);

        $allowedCardNetworks = [];

        if ($configuration->isAllowedCardNetworksVisaEnabled()) {
            $allowedCardNetworks[] = GooglePayConfiguration::NETWORK_VISA;
        }

        if ($configuration->isAllowedCardNetworksMastercardEnabled()) {
            $allowedCardNetworks[] = GooglePayConfiguration::NETWORK_MASTERCARD;
        }

        return $allowedCardNetworks;
    }

    public function getApplePaySupportedNetworks(?int $shopId): array
    {
        /** @var ApplePayConfiguration $configuration */
        $configuration = $this->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId);

        $supportedNetworks = [];

        if ($configuration->isSupportedNetworksAmexEnabled()) {
            $supportedNetworks[] = ApplePayConfiguration::NETWORK_AMEX;
        }

        if ($configuration->isSupportedNetworksMastercardEnabled()) {
            $supportedNetworks[] = ApplePayConfiguration::NETWORK_MASTERCARD;
        }

        if ($configuration->isSupportedNetworksVisaEnabled()) {
            $supportedNetworks[] = ApplePayConfiguration::NETWORK_VISA;
        }

        return $supportedNetworks;
    }

    public function getApplePayMerchantCapabilities(?int $shopId): array
    {
        /** @var ApplePayConfiguration $configuration */
        $configuration = $this->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId);

        $merchantCapabilities = [];

        if ($configuration->isMerchantCapabilitiesCreditEnabled()) {
            $merchantCapabilities[] = ApplePayConfiguration::CAPABILITIES_CREDIT;
        }

        if ($configuration->isMerchantCapabilitiesDebitEnabled()) {
            $merchantCapabilities[] = ApplePayConfiguration::CAPABILITIES_DEBIT;
        }

        if ($configuration->isMerchantCapabilities3dsEnabled()) {
            $merchantCapabilities[] = ApplePayConfiguration::CAPABILITIES_3DS;
        }

        return $merchantCapabilities;
    }

    public function isAutoCaptureEnabled(string $paymentMethodName, ?int $shopId): bool
    {
        try {
            $configuration = $this->getPaymentMethodConfiguration($paymentMethodName, $shopId);

            if (method_exists($configuration, 'isAutoCaptureEnabled')) {
                return $configuration->isAutoCaptureEnabled();
            }

            return false;
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    public function isCreditCart3dsEnabled(?int $shopId): bool
    {
        try {
            /** @var CreditCardConfiguration $configuration */
            $configuration = $this->getPaymentMethodConfiguration(CreditCardPaymentMethod::NAME, $shopId);

            return $configuration->isThreeDsEnabled();
        } catch (\RuntimeException $exception) {
            return false;
        }
    }

    public function updateConfiguration(string $paymentMethodName, array $data, int $shopId): void
    {
        $configuration = $this->getPaymentMethodConfiguration($paymentMethodName, $shopId, false);
        $configuration->fromArray($data);

        $this->modelManager->flush();
    }

    private function getConfiguration(string $entityClass, ?int $shopId, bool $useFallbackShop): ModelEntity
    {
        $entityRepository = $this->modelManager->getRepository($entityClass);

        /** @var ModelEntity $configuration */
        $configuration = $entityRepository->findOneBy(['shopId' => $shopId]);

        if ($configuration === null && $useFallbackShop) {
            return $this->getFallbackConfiguration($entityClass, $shopId);
        }

        if ($configuration === null) {
            throw new \RuntimeException(
                sprintf(
                    'no configuration for shop id %d found unable to load configuration.',
                    $shopId
                )
            );
        }

        return $configuration;
    }

    private function getFallbackConfiguration(string $entityClass, ?int $shopId): ModelEntity
    {
        $entityRepository = $this->modelManager->getRepository($entityClass);

        if ($entityRepository->count([]) > 0) {
            // try to load fallback configuration if shop was found

            return $entityRepository->findAll()[0];
        }

        throw new \RuntimeException(
            sprintf(
                'no configuration for shop id %d found unable to load fallback configuration.',
                $shopId
            )
        );
    }
}
