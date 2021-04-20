<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\IdealSource;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\IdealPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;

class IdealPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === IdealPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(IdealPaymentMethod::NAME);
        }

        $ideal = $this->createPaymentRequestFromStruct(new IdealSource($paymentRequestStruct->getBic(), $paymentRequestStruct->getPurpose()), $paymentRequestStruct);

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($ideal);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing ideal payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

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
        return !empty($paymentRequestStruct->getBic()) && !empty($paymentRequestStruct->getPurpose());
    }
}
