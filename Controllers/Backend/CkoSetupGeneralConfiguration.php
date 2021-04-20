<?php

declare(strict_types=1);

use Checkout\Library\Exceptions\CheckoutException;
use CkoCheckoutPayment\Components\CheckoutApi\Webhooks\WebhooksService;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Models\Configuration\GeneralConfiguration;

class Shopware_Controllers_Backend_CkoSetupGeneralConfiguration extends Shopware_Controllers_Backend_Application
{
	protected $model = GeneralConfiguration::class;

    private const SNIPPET_NAMESPACE = 'backend/cko_setup/controller/general_configuration';

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var WebhooksService
     */
    private $webhookService;

    /**
     * @var \Enlight_Components_Snippet_Manager
     */
    private $snippetManager;

	public function preDispatch()
    {
        parent::preDispatch();

        $this->configurationService = $this->get('cko_checkout_payment.components.configuration.configuration_service');
        $this->webhookService = $this->get('cko_checkout_payment.components.checkout_api.webhooks.webhooks_service');
        $this->snippetManager = $this->get('snippets');
    }

    public function loadConfigurationAction(): void
    {
        try {
            $configuration = $this->configurationService->getGeneralConfiguration(
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

    public function checkApiCredentialsAction(): void
    {
        $snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);

        try {
            $this->webhookService->getRegisteredWebhooks((int)$this->Request()->getParam('shopId'));

            $this->View()->assign([
                'success' => true,
                'message' => $snippetNamespace->get('notification/growl/checkApiCredentials/successMessage')
            ]);
        } catch (CheckoutException $exception) {
            $message = sprintf(
                '%s: %s',
                $snippetNamespace->get('notification/growl/checkApiCredentials/errorMessage'),
                $exception->getMessage()
            );

            $this->View()->assign([
                'success' => false,
                'message' => $message
            ]);
        }
    }

    public function registerWebhooksAction(): void
    {
        $snippetNamespace = $this->snippetManager->getNamespace(self::SNIPPET_NAMESPACE);

        try {
            $this->webhookService->registerWebhook((int)$this->Request()->getParam('shopId'));

            $this->View()->assign([
                'success' => true,
                'message' => $snippetNamespace->get('notification/growl/registerWebhook/registrationSuccessMessage')
            ]);
        } catch (CheckoutException $exception) {
            $message = sprintf(
                '%s: %s',
                $snippetNamespace->get('notification/growl/registerWebhook/registrationErrorMessage'),
                $exception->getMessage()
            );

            $this->View()->assign([
                'success' => false,
                'message' => $message
            ]);
        }
    }
}
