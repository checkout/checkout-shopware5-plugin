<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Controllers\AbstractCheckoutPaymentFrontendController;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentGiropay extends AbstractCheckoutPaymentFrontendController
{
    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid giropay pay request.');

            return;
        }

        $bic = $this->Request()->getParam(RequestConstants::BIC);

        if (empty($bic)) {
            $this->loggerService->warning('Invalid giropay payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::BIC, $bic);
    }
}
