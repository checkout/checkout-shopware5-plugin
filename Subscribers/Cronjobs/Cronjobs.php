<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Cronjobs;

use CkoCheckoutPayment\Components\Webhooks\EventQueueService;
use Enlight\Event\SubscriberInterface;

class Cronjobs implements SubscriberInterface
{

    /**
     * @var EventQueueService
     */
    private $eventQueueService;

    public function __construct(EventQueueService $eventQueueService)
    {
        $this->eventQueueService = $eventQueueService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_CkoProcessEvents' => 'onCronjobProcessEvents'
        ];
    }

    public function onCronjobProcessEvents(\Enlight_Event_EventArgs $args)
    {
        $this->eventQueueService->processQueue();
    }

}
