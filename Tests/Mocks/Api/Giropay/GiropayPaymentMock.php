<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\Giropay;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;

class GiropayPaymentMock
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
            throw new CheckoutException(GiropayPaymentMock::class);
        }

        $payment = new Payment(new IdSource(''), '');
        $payment->currency = 'EUR';
        $payment->id = 'pay_abcabc';
        $payment->action_id = 'act_abcabc';
        $payment->amount = 500;
        $payment->approved = true;
        $payment->status = 'Authorized';
        $payment->response_code = '10000';
        $payment->response_summary = 'Approved';
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
        $payment->processed_on = '2021-10-05T12:13:01Z';
        $payment->reference = 'testReference';
        $payment->http_code = 201;

        return $payment;
    }
}