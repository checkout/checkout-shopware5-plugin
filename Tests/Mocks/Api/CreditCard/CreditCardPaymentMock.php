<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\CreditCard;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Details;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\TokenSource;

class CreditCardPaymentMock
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
            throw new CheckoutException(CreditCardPaymentMock::class);
        }

        $payment = new Payment(new TokenSource('testToken'), 'EUR');
        $payment->source = [
            'id' => 'src_abcabc',
            'type' => 'card',
            'billing_address' => [
                'address_line1' => 'test 123',
                'city' => 'testCity',
                'zip' => '012345',
                'country' => 'DE'
            ],
            'expiry_month' => 1,
            'expiry_year' => 2024,
            'scheme' => 'Visa',
            'last4' => '4242',
            'fingerprint' => 'FE7F4E9B5E889A7D76924B0A011B16575C7FD05E4CA0B942C1C9B93766903972',
            'bin' => '424242',
            'card_type' => 'Credit',
            'card_category' => 'Consumer',
            'issuer' => 'JPMORGAN CHASE BANK NA',
            'issuer_country' => 'US',
            'product_id' => 'A',
            'product_type' => 'Visa Traditional',
            'avs_check' => 'S',
            'cvv_check' => 'Y',
            'payouts' => true,
            'fast_funds' => 'd'
        ];
        $payment->id = 'pay_abcabc';
        $payment->action_id = 'act_abcabc';
        $payment->amount = 500;
        $payment->approved = true;
        $payment->status = 'Authorized';
        $payment->auth_code = '123456';
        $payment->eci = '05';
        $payment->scheme_id = '000000000000000';
        $payment->response_code = '10000';
        $payment->response_summary = 'Approved';
        $payment->risk = ['flagged' => false];
        $payment->customer = [
            'id' => 'cus_abcabc',
            'email' => 'test@email.com',
            'name' => 'firstname lastname'
        ];
        $payment->processed_on = '2021-10-04T14:49:48Z';
        $payment->reference = 'testReference';
        $payment->processing = [
            'acquirer_transaction_id' => '1234567890',
            'retrieval_reference_number' => '123456789012'
        ];
        $payment->http_code = 201;

        return $payment;
    }

    public function details()
    {
        $paymentDetails = new Payment(new IdSource(''), 'EUR');
        $paymentDetails->id = 'pay_abcabc';
        $paymentDetails->source = [
            'planned_debit_date' => '2021-10-07'
        ];
        $paymentDetails->reference = 'testReference';
        //$paymentDetails->source = 'src_abcabc';
        $paymentDetails->amount = 500;
        $paymentDetails->approved = true;
        $paymentDetails->status = 'Authorized';
        //$paymentDetails->currency = 'EUR';

        return $paymentDetails;
    }
}