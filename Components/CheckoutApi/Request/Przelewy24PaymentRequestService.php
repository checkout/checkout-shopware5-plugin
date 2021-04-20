<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Models\Przelewy24Source;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\Przelewy24PaymentMethod;

class Przelewy24PaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === Przelewy24PaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(Przelewy24PaymentMethod::NAME);
        }

        $billingAddress = $paymentRequestStruct->getUser()['billingaddress'];
        $additonal = $paymentRequestStruct->getUser()['additional'];
        $username = sprintf("%s %s", $billingAddress['firstname'], $billingAddress['lastname']);
        $email = $additonal['user']['email'];

        $przelewy24 = $this->createPaymentRequestFromStruct(
            new Przelewy24Source(
                $paymentRequestStruct->getPurpose(),
                $username,
                $email,
                $this->getCountryById($billingAddress['country']['id'])->getIso()
            ),
            $paymentRequestStruct
        );

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($przelewy24);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing Przelewy24 payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        return !empty($paymentRequestStruct->getPurpose());
    }

    public function isPaymentSessionValid(): bool
    {
        // p24 do not need any other data before the request

        return true;
    }
}
