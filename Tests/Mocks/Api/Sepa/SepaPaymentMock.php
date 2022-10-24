<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\Sepa;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;

class SepaPaymentMock
{
    /**
     * @var bool
     */
    private $shouldThrowApiException;

    public function __construct(bool $shouldThrowApiException)
    {
        $this->shouldThrowApiException = $shouldThrowApiException;
    }

    public function request()
    {
        if ($this->shouldThrowApiException) {
            throw new CheckoutException(SepaPaymentMock::class);
        }

        $payment = new Payment(new IdSource(''), 'EUR');
        $payment->id = 'pay_abcabc';
        $payment->status = 'Pending';
        $payment->reference = 'testReference';

        $payment->customer = [
            'id' => 'cus_abcabc',
            'email' => 'test@email.com',
            'name' => 'test test'
        ];

        $payment->http_code = 202;

        return $payment;
    }
}