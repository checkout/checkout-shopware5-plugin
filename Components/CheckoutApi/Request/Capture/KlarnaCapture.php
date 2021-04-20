<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Capture;

use Checkout\Models\Payments\Capture as BaseCapture;

class KlarnaCapture extends BaseCapture
{
    public const TYPE = 'klarna';

    private const REQUEST_URL_KLARNA_LIVE = 'klarna/orders/{id}/captures';
    private const REQUEST_URL_KLARNA_SANDBOX = 'klarna-external/orders/{id}/captures';

    /**
     * @var bool
     */
    private $isSandboxMode = false;

    public function getEndpoint()
    {
        // php sdk does not currently support capturing payments manually using klarna
        // we are replacing the endpoint url to match correct url if klarna payment

        $requestUrl = self::REQUEST_URL_KLARNA_LIVE;
        if ($this->isSandboxMode) {
            $requestUrl = self::REQUEST_URL_KLARNA_SANDBOX;
        }

        return str_replace('{id}', $this->getId(), $requestUrl);
    }

    public function setIsSandboxMode(bool $isSandboxMode): void
    {
        $this->isSandboxMode = $isSandboxMode;
    }
}