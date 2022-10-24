<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPayment extends AbstractCheckoutPaymentFrontendController
{
    private const SESSION_ID = 'cko-session-id';

    public function indexAction(): void
    {
        $this->redirect(['action' => 'payment', 'forceSecure' => true]);
    }

    public function paymentAction(): void
    {
        $paymentRequest = $this->createDefaultPaymentRequest();
        $this->handlePaymentRequest($paymentRequest);
    }

    public function returnAction(): void
    {
        try {
            $sessionId = $this->Request()->getParam(self::SESSION_ID);

            if (empty($sessionId)) {
                throw new \RuntimeException('cko session id is empty');
            }

            $shopId = $this->dependencyProviderService->getShop()->getId();
            $paymentRequestStruct = $this->createDefaultPaymentRequest();
            $paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($sessionId, $shopId);

            $this->handlePaymentResponse($paymentDetailsResponse->getPaymentId(), $shopId, $paymentRequestStruct);
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());
            $this->handleFailedResponse();
        } catch (\RuntimeException $runtimeException) {
            $this->loggerService->error($runtimeException->getMessage());
            $this->handleFailedResponse();
        }
    }

    public function cancelAction(): void
    {
        $this->handleFailedResponse();
    }
}
