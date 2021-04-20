<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\TokenSource;
use Checkout\Models\Tokens\GooglePay;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;

class GooglePayPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === GooglePayPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(GooglePayPaymentMethod::NAME);
        }

        $googlePayStruct = $paymentRequestStruct->getGooglePayStruct();

        try {
            $client = $this->createApiClient();

            $token = $this->getToken($googlePayStruct);
            $payment = $this->createPaymentRequestFromStruct(new TokenSource($token), $paymentRequestStruct);

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($payment);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing google pay payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $signature = $paymentSessionService->get(RequestConstants::GOOGLE_PAY_SIGNATURE);
        $protocolVersion = $paymentSessionService->get(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION);
        $signedMessage = $paymentSessionService->get(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE);

        if (empty($signature) || empty($protocolVersion) || empty($signedMessage)) {
            return false;
        }

        return true;
    }

    private function getToken(GooglePayStruct $googlePayStruct): string
    {
        $client = $this->createApiClient();
        $token = $client->tokens()->request(
            new GooglePay(
                $googlePayStruct->getProtocolVersion(),
                $googlePayStruct->getSignature(),
                $googlePayStruct->getSignedMessage()
            )
        );

        if (!$token) {
            throw new RequiredPaymentDetailsMissingException(GooglePayPaymentMethod::NAME);
        }

        return $token;
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        $googlePayStruct = $paymentRequestStruct->getGooglePayStruct();

        if (empty($googlePayStruct->getProtocolVersion())) {
            return false;
        }

        if (empty($googlePayStruct->getSignature())) {
            return false;
        }

        if (empty($googlePayStruct->getSignedMessage())) {
            return false;
        }

        return true;
    }
}
