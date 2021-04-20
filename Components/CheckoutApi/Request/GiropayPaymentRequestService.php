<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\GiropaySource;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\GiropayPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;

class GiropayPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === GiropayPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(GiropayPaymentMethod::NAME);
        }

        $giropay = $this->createPaymentRequestFromStruct(new GiropaySource($paymentRequestStruct->getPurpose(), $paymentRequestStruct->getBic()), $paymentRequestStruct);

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($giropay);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing giropay payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $bic = $paymentSessionService->get(RequestConstants::BIC);

        return !empty($bic);
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        return !empty($paymentRequestStruct->getPurpose()) && !empty($paymentRequestStruct->getBic());
    }
}
