<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\CardManagement\CardManagementServiceInterface;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;

class Shopware_Controllers_Backend_CkoSetupCreditCard extends Shopware_Controllers_Backend_Application
{
    protected $model = CreditCardConfiguration::class;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var CardManagementServiceInterface
     */
    private $cardManagementService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->configurationService = $this->get('cko_checkout_payment.components.configuration.configuration_service');
        $this->cardManagementService = $this->get('cko_checkout_payment.components.card_management.card_management_service');
    }

    public function loadConfigurationAction(): void
    {
        try {
            /** @var CreditCardConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(
                CreditCardPaymentMethod::NAME,
                (int)$this->Request()->getParam('shopId'),
                false
            );

            $this->View()->assign([
                'success' => true,
                'configuration' => $configuration->toArray()
            ]);
        } catch (\RuntimeException $exception) {
            // catch exception so it will possible to save new configuration

            $this->View()->assign([
                'success' => true,
                'configuration' => []
            ]);
        }
    }

    public function updateAction()
    {
        parent::updateAction();

        $saveCardOptionEnabled = (bool)$this->Request()->getParam('saveCardOptionEnabled');

        if (!$saveCardOptionEnabled) {
            $this->cardManagementService->deleteAllCards();
        }
    }
}
