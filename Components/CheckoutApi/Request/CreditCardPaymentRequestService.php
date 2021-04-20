<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\BillingDescriptor;
use Checkout\Models\Payments\IdSource;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\ThreeDs;
use Checkout\Models\Payments\TokenSource;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;

class CreditCardPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === CreditCardPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(CreditCardPaymentMethod::NAME);
        }

        if ($paymentRequestStruct->getToken()) {
            $paymentSource = new TokenSource($paymentRequestStruct->getToken());
        } else {
            $paymentSource = new IdSource($paymentRequestStruct->getSourceId());
        }

        $user = $paymentRequestStruct->getUser();
        $billingAddress = $user['billingaddress'];
        $paymentSource->billing_address = $this->createBillingAddress($billingAddress);

        $payment = $this->createPaymentRequestFromStruct($paymentSource, $paymentRequestStruct);

        $shopId = $this->getShopId();
        /** @var CreditCardConfiguration $configuration */
        $configuration = $this->configurationService->getPaymentMethodConfiguration(CreditCardPaymentMethod::NAME, $shopId);

        $payment->threeDs = new ThreeDs((bool)$configuration->isThreeDsEnabled());
        $payment->threeDs->attempt_n3d = (bool)$configuration->isN3dAttemptEnabled();

        if ($this->neededDynamicBillingDescriptorFieldsValid()) {
            $payment->billing_descriptor = new BillingDescriptor(
                $configuration->getDynamicBillingDescriptorName(),
                $configuration->getDynamicBillingDescriptorCity()
            );
        }

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($payment);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing credit card payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $token = $paymentSessionService->get(RequestConstants::TOKEN);
        $sourceId = $paymentSessionService->get(RequestConstants::SOURCE_ID);

        return !empty($token) || !empty($sourceId);
    }

    private function neededDynamicBillingDescriptorFieldsValid(): bool
    {
        $shopId = $this->getShopId();
        /** @var CreditCardConfiguration $configuration */
        $configuration = $this->configurationService->getPaymentMethodConfiguration(CreditCardPaymentMethod::NAME, $shopId);

        if (!$configuration->isDynamicBillingDescriptorEnabled()) {
            return false;
        }

        return !empty($configuration->getDynamicBillingDescriptorName()) && !empty($configuration->getDynamicBillingDescriptorCity());
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        return !empty($paymentRequestStruct->getToken()) || !empty($paymentRequestStruct->getSourceId());
    }
}
