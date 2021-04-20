<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\TokenSource;
use Checkout\Models\Tokens\ApplePay;
use Checkout\Models\Tokens\ApplePayHeader;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;

class ApplePayPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === ApplePayPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(ApplePayPaymentMethod::NAME);
        }

        $applePayStruct = $paymentRequestStruct->getApplePayStruct();

        $applePayHeader = $this->createApplePayHeader($applePayStruct);
        $applePay = $this->createApplePay($applePayStruct, $applePayHeader);

        try {
            $client = $this->createApiClient();
            $token = $client->tokens()->request($applePay);

            if (!$token) {
                throw new RequiredPaymentDetailsMissingException(ApplePayPaymentMethod::NAME);
            }

            $payment = $this->createPaymentRequestFromStruct(new TokenSource($token->getId()), $paymentRequestStruct);

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($payment);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing apple pay payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $requiredPaymentDataValues = [
            $paymentSessionService->get(RequestConstants::APPLE_PAY_TRANSACTION_ID),
            $paymentSessionService->get(RequestConstants::APPLE_PAY_PUBLIC_KEY_HASH),
            $paymentSessionService->get(RequestConstants::APPLE_PAY_EPHEMERAL_PUBLIC_KEY),
            $paymentSessionService->get(RequestConstants::APPLE_PAY_VERSION),
            $paymentSessionService->get(RequestConstants::APPLE_PAY_SIGNATURE),
            $paymentSessionService->get(RequestConstants::APPLE_PAY_DATA)
        ];

        foreach ($requiredPaymentDataValues as $requiredPaymentDataValue) {
            if (empty($requiredPaymentDataValue)) {
                return false;
            }
        }

        return true;
    }

    private function createApplePayHeader(ApplePayStruct $applePayStruct): ApplePayHeader
    {
        return new ApplePayHeader(
            $applePayStruct->getTransactionId(),
            $applePayStruct->getPublicKeyHash(),
            $applePayStruct->getEphemeralPublicKey()
        );
    }

    private function createApplePay(ApplePayStruct $applePayStruct, ApplePayHeader $applePayHeader): ApplePay
    {
        return new ApplePay(
            $applePayStruct->getVersion(),
            $applePayStruct->getSignature(),
            $applePayStruct->getData(),
            $applePayHeader
        );
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        $applePayStruct = $paymentRequestStruct->getApplePayStruct();

        $requiredPaymentDataValues = [
            $applePayStruct->getTransactionId(),
            $applePayStruct->getPublicKeyHash(),
            $applePayStruct->getEphemeralPublicKey(),
            $applePayStruct->getVersion(),
            $applePayStruct->getSignature(),
            $applePayStruct->getData()
        ];

        foreach ($requiredPaymentDataValues as $requiredPaymentDataValue) {
            if (empty($requiredPaymentDataValue)) {
                return false;
            }
        }

        return true;
    }
}
