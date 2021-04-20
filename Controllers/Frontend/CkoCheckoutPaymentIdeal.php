<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentIdeal extends AbstractCheckoutPaymentFrontendController
{
    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid ideal request.');

            return;
        }

        $bic = $this->Request()->getParam(RequestConstants::BIC);

        if (empty($bic)) {
            $this->loggerService->warning('Invalid ideal payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::BIC, $bic);
    }
}
