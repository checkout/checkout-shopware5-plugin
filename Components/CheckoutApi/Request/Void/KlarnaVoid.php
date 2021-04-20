<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Void;

use Checkout\Models\Payments\Voids as BaseVoid;

class KlarnaVoid extends BaseVoid
{
    private const REQUEST_URL_KLARNA_LIVE = 'klarna/orders/{id}/voids';
    private const REQUEST_URL_KLARNA_SANDBOX = 'klarna-external/orders/{id}/voids';

    /**
     * @var bool
     */
    private $isSandboxMode = false;

    public function getEndpoint()
    {
        // php sdk does not currently support voiding payments manually using klarna
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