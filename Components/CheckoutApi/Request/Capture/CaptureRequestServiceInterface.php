<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Capture;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\CaptureRequestStruct;

interface CaptureRequestServiceInterface
{
    public function capturePayment(CaptureRequestStruct $captureRequestStruct): void;
}
