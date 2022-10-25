<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\ApplePay;

use Checkout\Models\Payments\TokenSource;

class ApplePayTokenMock
{
    /**
     * @var bool
     */
    private $shouldThrowTokenException;

    public function __construct(bool $shouldThrowTokenException)
    {
        $this->shouldThrowTokenException = $shouldThrowTokenException;
    }

    public function request()
    {
        if ($this->shouldThrowTokenException) {
            return null;
        }

        $token = new TokenSource('tok_abcabc');
        $token->type = 'applepay';
        $token->expires_on = '2021-10-21T10:48:35Z';
        $token->expiry_month = 8;
        $token->expiry_year = 2023;
        $token->scheme = 'Visa';
        $token->last4 = '1234';
        $token->bin = '123456';
        $token->card_type = 'Debit';
        $token->card_category = 'Consumer';
        $token->issuer = 'HSBC BANK PLC';
        $token->issuer_country = 'GB';
        $token->product_id = 'F';
        $token->product_type = 'Visa Classic';

        return $token;
    }
}