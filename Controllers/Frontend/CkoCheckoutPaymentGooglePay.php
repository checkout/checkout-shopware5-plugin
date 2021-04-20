<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentGooglePay extends AbstractCheckoutPaymentFrontendController
{
    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid google pay request.');

            return;
        }

        $signature = $this->Request()->getParam(RequestConstants::GOOGLE_PAY_SIGNATURE);
        $protocolVersion = $this->Request()->getParam(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION);
        $signedMessage = $this->Request()->getParam(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE);

        if (empty($signature) || empty($protocolVersion) || empty($signedMessage)) {
            $this->loggerService->warning('Invalid google pay payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::GOOGLE_PAY_SIGNATURE, $signature);
        $this->paymentSessionService->set(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION, $protocolVersion);
        $this->paymentSessionService->set(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE, $signedMessage);
    }
}
