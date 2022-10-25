<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\Klarna;

use Checkout\Models\Sources\Source;
use Checkout\Library\Exceptions\CheckoutException;

class KlarnaSourceMock
{
    /**
     * @var bool
     */
    private $shouldThrowSourceException;

    public function __construct(bool $shouldThrowSourceException)
    {
        $this->shouldThrowSourceException = $shouldThrowSourceException;
    }

    public function add(Source $source)
    {
        if ($this->shouldThrowSourceException) {
            throw new CheckoutException(KlarnaSourceMock::class);
        }

        $source->type = 'klarna';
        $source->session_id = 'kcs_abcabc';
        $source->client_token = 'testToken';
        $source->payment_method_categories = [
            [
                'identifier' => 'pay_later',
                'name' => 'Rechnung',
                'asset_urls' => [
                    'descriptive' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg',
                    'standard' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg'
                ]
            ],
            [
                'identifier' => 'pay_over_time',
                'name' => 'Ratenkauf',
                'asset_urls' => [
                    'descriptive' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg',
                    'standard' => 'https://x.klarnacdn.net/payment-method/assets/badges/generic/klarna.svg'
                ]
            ]
        ];

        $source->_links = [
            'self' => [
                'href' => 'https://api.sandbox.checkout.com/klarna-external/credit-sessions/kcs_abcabc'
            ]
        ];
        $source->http_code = '201';

        return $source;
    }
}