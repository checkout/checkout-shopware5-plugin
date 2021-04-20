<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Capture;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Capture;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\CaptureRequestStruct;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;

class CaptureRequestService extends AbstractCheckoutPaymentService implements CaptureRequestServiceInterface
{
    public function capturePayment(CaptureRequestStruct $captureRequestStruct): void
    {
        $capture = $this->getCaptureModel($captureRequestStruct);

        try {
            $this->apiClientService->createClient($captureRequestStruct->getShopId())->payments()->capture($capture);
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    private function getCaptureModel(CaptureRequestStruct $captureRequestStruct): Capture
    {
        if ($captureRequestStruct->getPaymentMethodName() === KlarnaPaymentMethod::NAME) {
            $capture = new KlarnaCapture($captureRequestStruct->getPaymentId());
            $capture->setIsSandboxMode($this->apiClientService->isSandboxMode($captureRequestStruct->getShopId()));

            if ($captureRequestStruct->isPartialCapture()) {
                $capture->reference = $captureRequestStruct->getReference();
                $capture->amount = $this->calculateAmount($captureRequestStruct->getCaptureAmount());
            }

            $capture->type = KlarnaCapture::TYPE;
            $capture->klarna = new \stdClass();
        } else {
            $capture = new Capture($captureRequestStruct->getPaymentId());

            if ($captureRequestStruct->isPartialCapture()) {
                $capture->reference = $captureRequestStruct->getReference();
                $capture->amount = $this->calculateAmount($captureRequestStruct->getCaptureAmount());
            }
        }

        return $capture;
    }
}
