<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\SepaConfiguration;

class Shopware_Controllers_Backend_CkoSetupSepa extends Shopware_Controllers_Backend_Application
{
	protected $model = SepaConfiguration::class;

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
            /** @var SepaConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(
                SepaPaymentMethod::NAME,
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
