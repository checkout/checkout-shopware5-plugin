<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentCreditcard extends AbstractCheckoutPaymentFrontendController
{
    public function selectSourceAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid credit card select source request.');

            return;
        }

        $sourceId = $this->Request()->getParam(RequestConstants::SOURCE_ID);

        $this->paymentSessionService->clearPaymentSession();
        $this->paymentSessionService->set(RequestConstants::SOURCE_ID, $sourceId);
    }

    public function deleteSourceAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid credit card delete source request.');

            return;
        }

        $sourceId = $this->Request()->getParam(RequestConstants::SOURCE_ID);
        $this->deleteCreditcardSource($sourceId);
    }

    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid credit card request.');

            return;
        }

        $token = $this->Request()->getParam(RequestConstants::TOKEN);
        $expiryDate = $this->Request()->getParam(RequestConstants::CC_EXPIRY_DATE);
        $last4 = $this->Request()->getParam(RequestConstants::CC_LAST_4);
        $saveCard = $this->Request()->getParam(RequestConstants::CC_SAVE_CARD);

        if (empty($token) || empty($expiryDate) || empty($last4)) {
            $this->loggerService->warning('Invalid credit card payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::TOKEN, $token);
        $this->paymentSessionService->set(RequestConstants::CC_EXPIRY_DATE, $expiryDate);
        $this->paymentSessionService->set(RequestConstants::CC_LAST_4, $last4);
        $this->paymentSessionService->set(RequestConstants::CC_SAVE_CARD, $saveCard);
    }
}
