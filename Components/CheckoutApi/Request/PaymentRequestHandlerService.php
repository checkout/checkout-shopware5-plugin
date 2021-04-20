<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;

class PaymentRequestHandlerService
{
    /**
     * @var PaymentRequestServiceInterface[]
     */
    private $paymentRequestServices;

    /**
     * @throws CheckoutApiRequestException
     * @throws RequiredPaymentDetailsMissingException
     */
    public function handlePaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        foreach ($this->paymentRequestServices as $paymentRequestService) {
            if ($paymentRequestService->supportsPaymentRequest($paymentRequestStruct->getPaymentMethodName())) {
                return $paymentRequestService->sendPaymentRequest($paymentRequestStruct);
            }
        }

        throw new \RuntimeException(sprintf('Sending payment request for payment method %s is not supported.', $paymentRequestStruct->getPaymentMethodName()));
    }

    public function addPaymentRequestService(PaymentRequestServiceInterface $paymentRequestService): void
    {
        $this->paymentRequestServices[] = $paymentRequestService;
    }
}
