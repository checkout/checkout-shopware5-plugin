<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\Webhooks\WebhooksService;
use CkoCheckoutPayment\Models\Event;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_CkoCheckoutWebhook extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var WebhooksService
     */
    private $webhookService;

    /**
     * @var ConfigurationServiceInterface
     */
    private $config;

    /**
     * @var LoggerServiceInterface
     */
    private $logger;
    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $modelManager;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $this->webhookService = $this->get('cko_checkout_payment.components.webhooks.webhooks_service');
        $this->config = $this->get('cko_checkout_payment.components.configuration.configuration_service');
        $this->modelManager = $this->get('models');
        $this->logger = $this->get('cko_checkout_payment.components.logger.logger_service');
    }

    public function getWhitelistedCSRFActions()
    {
        return [
            'index',
        ];
    }

    public function indexAction()
    {
        //TODO: integrate some way of parsing events in the SDK
        $content = $this->Request()->getContent();
        $event = json_decode($content, true);

        $signature = $this->Request()->getHeader('CKO-Signature');
        $messageHash = hash_hmac('sha256', $content, $this->config->getGeneralConfiguration(null)->getPrivateKey());

        if ($signature !== $messageHash) {
            $this->logger->error(sprintf("Invalid event signature. Got '%s' is '%s'", $signature, $messageHash), [self::class]);
            throw new Exception("Invalid hash");
        }

        $eventId = $event['id'];
        $eventType = $event['type'];
        $eventData = $event['data'];
        $paymentId = $eventData['id'];

        $this->logger->info("Incoming event: {$eventId} - {$eventType}", [self::class]);

        $savedEvent = new Event();
        $savedEvent->setPaymentId($paymentId);
        $savedEvent->setEventId($eventId);
        $savedEvent->setCreatedOn(new DateTime($event['created_on']));
        $savedEvent->setType($eventType);
        $savedEvent->setData(json_encode($eventData));

        $this->modelManager->persist($savedEvent);
        $this->modelManager->flush();


        return $this->Response()->setBody('');
    }
}
