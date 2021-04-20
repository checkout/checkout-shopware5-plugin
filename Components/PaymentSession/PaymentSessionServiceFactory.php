<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentSession;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentSessionServiceFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createPaymentSessionService(): ?PaymentSessionServiceInterface
    {
        try {
            $this->container->has('session');

            /** @var PaymentSessionServiceInterface $paymentSession */
            $paymentSession = $this->container->get('cko_checkout_payment.components.payment_session.payment_session_service');

            return $paymentSession;
        } catch (\Throwable $e) {
            // session is not available on backend or other modules

            return null;
        }
    }
}