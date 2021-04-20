<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Void;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\VoidRequestStruct;

interface VoidRequestServiceInterface
{
    public function voidPayment(VoidRequestStruct $voidRequestStruct): void;
}
