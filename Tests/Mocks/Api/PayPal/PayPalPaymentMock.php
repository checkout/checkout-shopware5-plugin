<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\PayPal;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;

class PayPalPaymentMock
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
            throw new CheckoutException(PayPalPaymentMock::class);
        }

        $payment = new Payment(new IdSource(''), '');
        $payment->id = 'pay_abcabc';
        $payment->status = 'Pending';
        $payment->reference = 'testReference';
        $payment->customer = [
            'id' => 'cus_abcabc',
            'email' => 'test@email.com',
            'name' => 'firstname lastname'
        ];
        $payment->_links = [
            'self' => [
                'href' => 'https://api.sandbox.checkout.com/payments/pay_abcabc'
            ],
            'redirect' => [
                'href' => 'https://api.sandbox.checkout.com/payments/pay_abcabc'
            ]
        ];
        $payment->http_code = 202;

        return $payment;
    }
}