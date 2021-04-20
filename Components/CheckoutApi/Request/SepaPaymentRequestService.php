<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Sources\Sepa;
use Checkout\Models\Sources\SepaAddress;
use Checkout\Models\Sources\SepaData;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use Shopware\Models\Country\Country;

class SepaPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public const MANDATE_TYPE_SINGLE = 'single';

    private const SEPA_REQUEST = 'sepaRequest';
    private const DETAILS_RESPONSE = 'detailsResponse';

    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === SepaPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(SepaPaymentMethod::NAME);
        }

        $user = $paymentRequestStruct->getUser();
        $billingAddress = $user['billingaddress'];

        $sepaAddress = $this->createSepaAddress($billingAddress);
        $sepaData = $this->createSepaData($billingAddress, $paymentRequestStruct);

        try {
			$sepa = $this->sendSepaSourceRequest($sepaAddress, $sepaData, $user, $paymentRequestStruct);
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
			$paymentRequest = $client->payments()->request($sepa[self::SEPA_REQUEST]);
            $paymentResponse = new PaymentResponseStruct($paymentRequest, $sepa[self::DETAILS_RESPONSE]);

            $this->loggerService->info(sprintf('Processing sepa payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $iban = $paymentSessionService->get(RequestConstants::IBAN);

        return !empty($iban);
    }

    private function createSepaAddress(array $billingAddress): SepaAddress
    {
        $country = $this->getBillingAddressCountry((int)$billingAddress['countryId']);

        return new SepaAddress(
            $billingAddress['street'],
            $billingAddress['city'],
            $billingAddress['zipcode'],
            $country->getIso()
        );
    }

    private function createSepaData(array $billingAddress, PaymentRequestStruct $paymentRequestStruct): SepaData
    {
        return new SepaData(
            $billingAddress['firstname'],
            $billingAddress['lastname'],
            $paymentRequestStruct->getIban(),
            $paymentRequestStruct->getBic(),
            $paymentRequestStruct->getPurpose(),
            $paymentRequestStruct->getMandate()
        );
    }

    private function getBillingAddressCountry(int $countryId): Country
    {
        try {
            return $this->getCountryById($countryId);
        } catch (\RuntimeException $runtimeException) {
            throw new RequiredPaymentDetailsMissingException(
                sprintf(
                    'country id %d for klarna address not found',
                    $countryId
                )
            );
        }
    }

    private function sendSepaSourceRequest(
        SepaAddress $sepaAddress,
        SepaData $sepaData,
        array $user,
        PaymentRequestStruct $paymentRequestStruct
    ): array
    {
        /** @var Sepa $paymentSource */
        $source = new Sepa($sepaAddress, $sepaData);
        $source->customer = $this->createCreateCustomerData($user);

        $client = $this->createApiClient();
        $details = $client->sources()->add($source);

        $sepa = new Payment(new IdSource($details->getId()), $paymentRequestStruct->getCurrency());
        $sepa->amount = $this->calculateAmount($paymentRequestStruct->getAmount());
        $sepa->reference = $paymentRequestStruct->getReference();

        return [self::SEPA_REQUEST => $sepa, self::DETAILS_RESPONSE => $details];
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        if (empty($paymentRequestStruct->getIban())) {
            return false;
        }

        if (empty($paymentRequestStruct->getPurpose())) {
            return false;
        }

        if (empty($paymentRequestStruct->getMandate())) {
            return false;
        }

        return true;
    }
}
