<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Core;

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\PayPalPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorServiceInterface;
use Enlight\Event\SubscriberInterface;

class PaymentMeansSubscriber implements SubscriberInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    /**
     * @var PaymentMethodValidatorServiceInterface
     */
    private $paymentMethodValidatorService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        DependencyProviderServiceInterface $dependencyProviderService,
        PaymentMethodValidatorServiceInterface $paymentMethodValidatorService
    ) {
        $this->configurationService = $configurationService;
        $this->dependencyProviderService = $dependencyProviderService;
        $this->paymentMethodValidatorService = $paymentMethodValidatorService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware_Modules_Admin_GetPaymentMeans_DataFilter' => 'onFilterPaymentMeans',
        ];
    }

    public function onFilterPaymentMeans(\Enlight_Event_EventArgs $args): void
    {
        /** @var array $availableMethods */
        $availablePaymentMethods = $args->getReturn();
        $filteredPaymentMethods = $this->getFilteredPaymentMethods($availablePaymentMethods);
        $shopId = $this->dependencyProviderService->getShop()->getId();

        foreach ($filteredPaymentMethods as $key => $filteredPaymentMethod) {
            try {
                $this->configurationService->getGeneralConfiguration($shopId);

                foreach (array_keys(ConfigurationServiceInterface::PAYMENT_METHODS) as $configurationPaymentMethod) {
                    if ($filteredPaymentMethod['name'] === $configurationPaymentMethod) {
                        $this->configurationService->getPaymentMethodConfiguration($configurationPaymentMethod, $shopId);
                    }
                }
            } catch (\RuntimeException $exception) {
                unset($availablePaymentMethods[$key]);
            }
        }

        $args->setReturn($availablePaymentMethods);
    }

    private function getFilteredPaymentMethods(array $paymentMethods): array
    {
        return array_filter($paymentMethods, function ($paymentMethod): bool {
            return $this->paymentMethodValidatorService->isCheckoutPaymentMethod($paymentMethod['name']);
        });
    }
}