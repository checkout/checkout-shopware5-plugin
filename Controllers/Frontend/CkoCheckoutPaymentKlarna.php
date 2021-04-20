<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder\KlarnaRequestBuilderServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;
use CkoCheckoutPayment\Components\RequestConstants;
use Shopware\Components\BasketSignature\BasketSignatureGeneratorInterface;

class Shopware_Controllers_Frontend_CkoCheckoutPaymentKlarna extends Shopware_Controllers_Frontend_Checkout
{
    private const IS_BASKET_SIGNATURE_VALID = 'isBasketSignatureValid';
    private const KLARNA_DATA = 'klarnaData';

    /**
     * @var PaymentSessionServiceInterface
     */
    private $paymentSessionService;

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    /**
     * @var KlarnaRequestBuilderServiceInterface
     */
    private $klarnaRequestBuilderService;

    /**
     * @var LoggerServiceInterface
     */
    private $loggerService;

    /**
     * @var BasketSignatureGeneratorInterface
     */
    private $basketSignatureGenerator;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->paymentSessionService = $this->get('cko_checkout_payment.components.payment_session.payment_session_service');
        $this->dependencyProviderService = $this->get('cko_checkout_payment.components.dependency_provider.dependency_provider_service');
        $this->klarnaRequestBuilderService = $this->get('cko_checkout_payment.components.checkout_api.builder.request_builder.klarna_request_builder_service');
        $this->loggerService = $this->get('cko_checkout_payment.components.logger.logger_service');
        $this->basketSignatureGenerator = $this->get('basket_signature_generator');
    }

    public function savePaymentDataAction(): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid klarna request.');

            return;
        }

        $token = $this->Request()->getParam(RequestConstants::TOKEN);

        if (empty($token)) {
            $this->loggerService->warning('Invalid klarna payment data given.');

            return;
        }

        $this->paymentSessionService->set(RequestConstants::TOKEN, $token);
    }

    public function checkBasketSignatureAction(): void
    {
        if (!$this->Request()->isPost()) {
            $this->loggerService->warning('Invalid klarna check basket signature request.');

            return;
        }

        $shopId = $this->dependencyProviderService->getShop()->getId();
        $klarnaData = $this->klarnaRequestBuilderService->createKlarnaPaymentInitializeData((array)$this->getBasket(), (array)$this->getUserData(), $shopId);
        $currentUserId = (int)$this->get('session')->get('sUserId');

        $oldBasketSignature = $this->basketSignatureGenerator->generateSignature((array)Shopware()->Session()->sOrderVariables['sBasket'], $currentUserId);
        $currentBasketSignature = $this->basketSignatureGenerator->generateSignature((array)$this->getBasket(), $currentUserId);

        $isBillingAddressSignatureValid = $this->isBillingAddressSignatureValid($this->Request()->getParam('currentBillingAddress'));

        if ($oldBasketSignature === $currentBasketSignature && $isBillingAddressSignatureValid) {
            $this->setJsonResponse([self::IS_BASKET_SIGNATURE_VALID => true]);

            return;
        }

        $this->setJsonResponse([self::IS_BASKET_SIGNATURE_VALID => false, self::KLARNA_DATA => $klarnaData->getRequestData()]);
    }

    private function isBillingAddressSignatureValid(array $givenBillingAddress): bool
    {
        $userData = $this->getUserData();
        $expectedBillingAddress = $this->klarnaRequestBuilderService->createBillingAddress($userData['billingaddress'], $userData['additional']['user'])->getValues();

        return hash('sha256', json_encode($expectedBillingAddress)) === hash('sha256', json_encode($givenBillingAddress));
    }

    private function setJsonResponse(array $data): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody(json_encode($data));
    }
}
