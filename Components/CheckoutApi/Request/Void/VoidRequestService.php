<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Void;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Voids;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\VoidRequestStruct;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;

class VoidRequestService extends AbstractCheckoutPaymentService implements VoidRequestServiceInterface
{
    public function voidPayment(VoidRequestStruct $voidRequestStruct): void
    {
        $void = $this->getVoidModel($voidRequestStruct);

        try {
            $this->apiClientService->createClient($voidRequestStruct->getShopId())->payments()->void($void);
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    private function getVoidModel(VoidRequestStruct $voidRequestStruct): Voids
    {
        if ($voidRequestStruct->getPaymentMethodName() === KlarnaPaymentMethod::NAME) {
            $void = new KlarnaVoid($voidRequestStruct->getPaymentId());
            $void->setIsSandboxMode($this->apiClientService->isSandboxMode($voidRequestStruct->getShopId()));
        } else {
            $void = new Voids($voidRequestStruct->getPaymentId());
        }

        return $void;
    }
}
