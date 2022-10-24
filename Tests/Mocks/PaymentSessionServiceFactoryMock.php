<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceFactory;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;

class PaymentSessionServiceFactoryMock extends PaymentSessionServiceFactory
{
    /**
     * @var PaymentSessionServiceMock
     */
    private $paymentSessionServiceMock;

    public function createPaymentSessionService(): ?PaymentSessionServiceInterface
    {
        if (!$this->paymentSessionServiceMock instanceof PaymentSessionServiceMock) {
            $this->paymentSessionServiceMock = new PaymentSessionServiceMock();
        }

        return $this->paymentSessionServiceMock;
    }

    public function getPaymentSessionServiceMock(): PaymentSessionServiceMock
    {
        return $this->paymentSessionServiceMock;
    }
}