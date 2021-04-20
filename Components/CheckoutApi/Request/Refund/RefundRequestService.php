<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Refund;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Refund;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\RefundRequestStruct;

class RefundRequestService extends AbstractCheckoutPaymentService implements RefundRequestServiceInterface
{
    public function refundPayment(RefundRequestStruct $refundRequestStruct): void
    {
        $refund = new Refund($refundRequestStruct->getPaymentId());
        if ($refundRequestStruct->isPartialRefund()) {
            $refund->reference = $refundRequestStruct->getReference();
            $refund->amount = $this->calculateAmount($refundRequestStruct->getRefundAmount());
        }

        try {
            $this->apiClientService->createClient($refundRequestStruct->getShopId())->payments()->refund($refund);
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }
}
