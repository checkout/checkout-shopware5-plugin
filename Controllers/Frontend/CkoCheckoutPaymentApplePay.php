<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\ApplePay\Exception\MerchantValidationFailedException;
use CkoCheckoutPayment\Components\ApplePay\MerchantValidationServiceInterface;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentApplePay extends AbstractCheckoutPaymentFrontendController
{
    /**
     * @var MerchantValidationServiceInterface
     */
    private $merchantValidationService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->merchantValidationService = $this->get('cko_checkout_payment.components.apple_pay.merchant_validation_service');
    }

    public function savePaymentDataAction(): void
    {
        if (!$this->Request()->isPost()) {
            $this->setJsonResponse(['success' => false]);

            return;
        }

        $transactionId = $this->Request()->getParam(RequestConstants::APPLE_PAY_TRANSACTION_ID);
        $publicKeyHash = $this->Request()->getParam(RequestConstants::APPLE_PAY_PUBLIC_KEY_HASH);
        $ephemeralPublicKey = $this->Request()->getParam(RequestConstants::APPLE_PAY_EPHEMERAL_PUBLIC_KEY);
        $version = $this->Request()->getParam(RequestConstants::APPLE_PAY_VERSION);
        $signature = $this->Request()->getParam(RequestConstants::APPLE_PAY_SIGNATURE);
        $data = $this->Request()->getParam(RequestConstants::APPLE_PAY_DATA);

        $requiredPaymentData = [
            RequestConstants::APPLE_PAY_TRANSACTION_ID => $transactionId,
            RequestConstants::APPLE_PAY_PUBLIC_KEY_HASH => $publicKeyHash,
            RequestConstants::APPLE_PAY_EPHEMERAL_PUBLIC_KEY => $ephemeralPublicKey,
            RequestConstants::APPLE_PAY_VERSION => $version,
            RequestConstants::APPLE_PAY_SIGNATURE => $signature,
            RequestConstants::APPLE_PAY_DATA => $data
        ];

        foreach ($requiredPaymentData as $key => $value) {
            if (empty($value)) {
                $this->setJsonResponse(['success' => false]);

                return;
            }

            $this->paymentSessionService->set($key, $value);
        }

        $this->setJsonResponse(['success' => true]);
    }

    public function validateMerchantAction(): void
    {
        $url = $this->Request()->getParam('url');
        if (!$url) {
            $this->setJsonResponse(['success' => false]);
        }

        try {
            $response = $this->merchantValidationService->validateMerchant($url, $this->dependencyProviderService->getShop()->getId());

            $this->setJsonResponse([
                'success' => true,
                'merchantSession' => $response
            ]);
        } catch (MerchantValidationFailedException $merchantValidationFailedException) {
            $this->loggerService->error($merchantValidationFailedException->getMessage());

            $this->setJsonResponse([
                'success' => false,
                'merchantSession' => null
            ]);
        }
    }
}
