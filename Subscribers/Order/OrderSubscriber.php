<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Order;

use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;
use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\AttributeBundle\Service\DataPersisterInterface;
use Shopware\Models\Order\Order;

class OrderSubscriber implements SubscriberInterface
{
    /**
     * @var DataPersisterInterface
     */
    private $dataPersister;

    /**
     * @var OrderProviderServiceInterface
     */
    private $orderProviderService;

    /**
     * @var PaymentSessionServiceInterface
     */
    private $paymentSessionService;

    /**
     * @var LoggerServiceInterface
     */
    private $loggerService;

    public function __construct(
        DataPersisterInterface $dataPersister,
        OrderProviderServiceInterface $orderProviderService,
        PaymentSessionServiceInterface $paymentSessionService,
        LoggerServiceInterface $loggerService
    ) {
        $this->dataPersister = $dataPersister;
        $this->orderProviderService = $orderProviderService;
        $this->paymentSessionService = $paymentSessionService;
        $this->loggerService = $loggerService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware_Modules_Order_SaveOrder_OrderCreated' => 'onOrderCreated'
        ];
    }

    public function onOrderCreated(\Enlight_Event_EventArgs $args): void
    {
        $orderId = (int)$args->get('orderId');

        if (!$orderId) {
            return;
        }

        try {
            $order = $this->orderProviderService->getOrderById($orderId);

            if ($order->getPayment()->getName() === SepaPaymentMethod::NAME) {
                $this->setSepaMandateReference($this->paymentSessionService->getMandateReference(), $order);
            }
        } catch (\Throwable $e) {
            $this->loggerService->error(
                sprintf('order not found for order id: %d', $orderId),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
        }
    }

    private function setSepaMandateReference(string $mandateReference, Order $order): void
    {
        if (!$mandateReference) {
            return;
        }

        try {
            $this->dataPersister->persist(['cko_mandate_reference' => $mandateReference], 's_order_attributes', $order->getId());
        } catch (\Throwable $e) {
            $this->loggerService->error(
                sprintf('updating order attributes has failed for order id: %d', $order->getId()),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'orderId' => $order->getId(),
                    'mandateReference' => $mandateReference
                ]
            );
        }
    }
}