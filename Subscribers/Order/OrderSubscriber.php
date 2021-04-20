<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Order;

use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;
use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\AttributeBundle\Service\DataPersisterInterface;

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
     * @var PaymentMethodValidatorServiceInterface
     */
    private $paymentMethodValidatorService;

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
        PaymentMethodValidatorServiceInterface $paymentMethodValidatorService,
        PaymentSessionServiceInterface $paymentSessionService,
        LoggerServiceInterface $loggerService
    ) {
        $this->dataPersister = $dataPersister;
        $this->orderProviderService = $orderProviderService;
        $this->paymentMethodValidatorService = $paymentMethodValidatorService;
        $this->paymentSessionService = $paymentSessionService;
        $this->loggerService = $loggerService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Shopware_Modules_Order_SaveOrder_OrderCreated' => 'onOrderCreated'
        ];
    }

    public function onOrderCreated(\Enlight_Event_EventArgs $args)
    {
        $orderId = (int)$args->get('orderId');
        $mandateReference = $this->paymentSessionService->getMandateReference();

        if (!$orderId || !$mandateReference) {
            return;
        }

        try {
            $order = $this->orderProviderService->getOrderById((int)$args->get('orderId'));

            if ($order->getPayment()->getName() !== SepaPaymentMethod::NAME) {
                return;
            }

            $this->dataPersister->persist(['cko_mandate_reference' => $mandateReference], 's_order_attributes', $order->getId());
        } catch (\Throwable $e) {
            $this->loggerService->error(
                sprintf('updating order attributes has failed for order id: %d', $orderId),
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'mandateReference' => $mandateReference
                ]
            );
        }
    }
}