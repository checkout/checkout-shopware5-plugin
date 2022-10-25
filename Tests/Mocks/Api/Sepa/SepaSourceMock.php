<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api\Sepa;

use Checkout\Models\Sources\Source;

class SepaSourceMock
{
    public function add(Source $source)
    {
        $source->id = 'testSourceId';
        $source->response_code = '10000';
        $source->response_data = [
            'mandate_reference' => 'testMandateReference'
        ];

        $source->customer = [
          'id' => 'testId',
          'email' => 'test@email.com'
        ];

        $source->http_code = '201';

        return $source;
    }
}