<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\BancontactSource;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;
use Shopware\Models\Country\Country;

class BancontactPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === BancontactPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        $user = $paymentRequestStruct->getUser();
        $billingAddress = $user['billingaddress'];

        $country = $this->getBillingAddressCountry((int)$billingAddress['countryID']);
        $accountHolder = sprintf('%s %s', $billingAddress['firstname'], $billingAddress['lastname']);
        $bancontact = $this->createPaymentRequestFromStruct(new BancontactSource($accountHolder, $country->getIso()), $paymentRequestStruct);

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($bancontact);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing ban contact payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        // ban contact do not need any other data before the request

        return true;
    }

    private function getBillingAddressCountry(int $countryId): Country
    {
        try {
            return $this->getCountryById($countryId);
        } catch (\RuntimeException $runtimeException) {
            throw new RequiredPaymentDetailsMissingException(
                sprintf(
                    'country id %d for ban contact address not found',
                    $countryId
                )
            );
        }
    }
}
