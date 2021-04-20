<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\GooglePayConfiguration;

class Shopware_Controllers_Backend_CkoSetupGooglePay extends Shopware_Controllers_Backend_Application
{
	protected $model = GooglePayConfiguration::class;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->configurationService = $this->get('cko_checkout_payment.components.configuration.configuration_service');
    }

	public function loadConfigurationAction(): void
    {
        try {
            /** @var GooglePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(
                GooglePayPaymentMethod::NAME,
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
}
