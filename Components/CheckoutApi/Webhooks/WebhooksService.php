<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Webhooks;

use Checkout\Models\Events\Event;
use Checkout\Models\Response;
use Checkout\Models\Webhooks\Webhook;
use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use Doctrine\ORM\EntityManager;
use Shopware\Components\Routing\Context;
use Shopware\Models\Shop\Shop;

class WebhooksService
{
    /**
     * @var CheckoutApiClientServiceInterface
     */
    private $apiClientService;

    /**
     * @var LoggerService
     */
    private $loggerService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        CheckoutApiClientServiceInterface $apiClientService,
        LoggerService $loggerService,
        EntityManager $entityManager
    ) {
        $this->apiClientService = $apiClientService;
        $this->loggerService = $loggerService;
        $this->entityManager = $entityManager;
    }

    public function getRegisteredWebhooks(int $shopId): array
    {
        /** @var Response $retrieve */
        $retrieve = $this->apiClientService->createClient($shopId)->webhooks()->retrieve();
        if ($retrieve->getCode() === 200) {
            return $retrieve->getValues()['list'];
        }

        return [];
    }

    public function registerWebhook(int $shopId): void
    {
        $url = $this->getShopUrl($shopId);

        $webhooks = $this->getRegisteredWebhooks($shopId);
        $webhookRegistered = false;

        /** @var Webhook $webhook */
        foreach ($webhooks as $webhook) {
            if ($webhook->getValue('url') === $url) {
                $webhookRegistered = true;
            }
        }

        if ($webhookRegistered) {
            return;
        }

        $newWebhook = new Webhook($url);
        $client = $this->apiClientService->createClient($shopId);

        /** @var Event $events */
        $events = $client->events()->types(['version' => '2.0']);
        $allEvents = [];
        foreach ($events->getValue(['list']) as $event) {
            $allEvents = $event->getValue('event_types');
        }

        $client->webhooks()->register($newWebhook, $allEvents);
    }

    private function getShopUrl(int $shopId): string
    {
        $repository = $this->entityManager->getRepository(Shop::class);
        $shop = $repository->getActiveById($shopId);
        $shop->registerResources();
        $context = Context::createFromShop(
            $shop,
            Shopware()->Container()->get('config')
        );
        Shopware()->Container()->get('router')->setContext($context);

        return Shopware()->Front()->Router()->assemble(['controller' => 'CkoCheckoutWebhook', 'module' => 'frontend']);
    }
}
