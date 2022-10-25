<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Models\Event;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelManager;

class Shopware_Controllers_Frontend_CkoCheckoutWebhook extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $config;

    /**
     * @var LoggerServiceInterface
     */
    private $logger;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

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

        $this->validateSignature($content);

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

    private function validateSignature(string $content)
    {
        $signature = $this->Request()->getHeader('CKO-Signature');
        $configuration = $this->config->getGeneralConfiguration(null);
        $messageHashForPrivateKey = hash_hmac('sha256', $content, $configuration->getPrivateKey());
        $messageHashForWebhookSignatureKey = hash_hmac('sha256', $content, $configuration->getWebhookSignatureKey());

        if($signature !== $messageHashForWebhookSignatureKey && $signature !== $messageHashForPrivateKey) {
            $this->logger->error(sprintf("Invalid event signature. Expected '%s' | Is '%s' and '%s'", $signature, $messageHashForPrivateKey, $messageHashForWebhookSignatureKey), [self::class]);
            throw new Exception("Invalid hash");
        }
    }
}
