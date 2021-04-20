<?php declare(strict_types=1);


namespace CkoCheckoutPayment\Components\Webhooks;


use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Models\Event;
use Monolog\Logger;
use Shopware\Components\Model\ModelManager;

class EventQueueService
{
    /**
     * @var ModelManager
     */
    private $modelManager;
    /**
     * @var WebhooksService
     */
    private $webhookService;
    /**
     * @var LoggerService
     */
    private $logger;

    public function __construct(LoggerService $logger,ModelManager $modelManager, WebhooksService $webhooksService)
    {
        $this->logger = $logger;
        $this->modelManager = $modelManager;
        $this->webhookService = $webhooksService;
    }

    public function processQueue()
    {
        $eventRepo = $this->modelManager->getRepository(Event::class);
        $unprocessedEvents = $eventRepo->findBy(['isProcessed' => false], ['createdOn' => 'ASC']);

        /** @var Event $event */
        foreach ($unprocessedEvents as $event) {
            switch ($event->getType()) {
                case CheckoutApiPaymentStatus::WEBHOOK_PAYMENT_APPROVED:
                    $this->webhookService->handlePaymentApproved($event->getPaymentId());
                    break;

                case CheckoutApiPaymentStatus::WEBHOOK_PAYMENT_CAPTURED:
                    $this->webhookService->handlePaymentCaptured($event->getPaymentId());
                    break;

                case CheckoutApiPaymentStatus::WEBHOOK_PAYMENT_REFUNDED:
                    $this->webhookService->handlePaymentRefunded($event->getPaymentId());
                    break;

                default:
                    $this->logger->warning("Unknown event type: {$event->getType()}", [self::class]);
            }

            $event->setIsProcessed(true);
        }
        $this->modelManager->flush();
    }

}
