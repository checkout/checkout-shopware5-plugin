<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\GooglePay;

use Checkout\Models\Tokens\GooglePay;

class GooglePayTokenMock
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
            return null;
        }

        return new GooglePay('testProtocolVersion', 'testSignature', 'testSignedMessage');
    }
}