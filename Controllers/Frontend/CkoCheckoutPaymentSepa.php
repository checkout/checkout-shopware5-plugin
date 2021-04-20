<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentSepa extends AbstractCheckoutPaymentFrontendController
{
    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid sepa request.');

            return;
        }

        $iban = $this->Request()->getParam(RequestConstants::IBAN);
        $bic = $this->Request()->getParam(RequestConstants::BIC);

        if (empty($iban)) {
            $this->loggerService->warning('Invalid sepa payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::IBAN, $iban);
        $this->paymentSessionService->set(RequestConstants::BIC, $bic);
    }
}
