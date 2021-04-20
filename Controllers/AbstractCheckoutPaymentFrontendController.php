<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Controllers;

use CkoCheckoutPayment\Components\CardManagement\CardManagementService;
use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Details\PaymentDetailsRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\PaymentRequestHandlerService;
use CkoCheckoutPayment\Components\CheckoutApi\Request\SepaPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Components\Structs\CardStruct;
use Shopware\Models\Order\Status as OrderStatus;

abstract class AbstractCheckoutPaymentFrontendController extends \Shopware_Controllers_Frontend_Payment
{
    /**
     * @var PaymentDetailsRequestServiceInterface
     */
    protected $paymentDetailsService;

    /**
     * @var PaymentSessionServiceInterface
     */
    protected $paymentSessionService;

    /**
     * @var DependencyProviderServiceInterface
     */
    protected $dependencyProviderService;

    /**
     * @var LoggerServiceInterface
     */
    protected $loggerService;

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var PaymentRequestHandlerService
     */
    private $paymentRequestHandler;

    /**
     * @var CardManagementService
     */
    private $cardManagementService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->paymentDetailsService = $this->get('cko_checkout_payment.components.checkout_api.request.details.payment_details_request_service');
        $this->paymentSessionService = $this->get('cko_checkout_payment.components.payment_session.payment_session_service');
        $this->dependencyProviderService = $this->get('cko_checkout_payment.components.dependency_provider.dependency_provider_service');
        $this->loggerService = $this->get('cko_checkout_payment.components.logger.logger_service');
        $this->configurationService = $this->get('cko_checkout_payment.components.configuration.configuration_service');
        $this->paymentRequestHandler = $this->get('cko_checkout_payment.components.checkout_api.request.payment_request_handler_service');
        $this->cardManagementService = $this->get('cko_checkout_payment.components.card_management.card_management_service');
    }

    protected function handlePaymentRequest(PaymentRequestStruct $paymentRequestStruct): void
    {
        $this->paymentSessionService->setPaymentReference($paymentRequestStruct->getReference());

        try {
            $this->paymentSessionService->set(RequestConstants::BASKET_SIGNATURE, $this->persistBasket());

            $paymentResponse = $this->paymentRequestHandler->handlePaymentRequest($paymentRequestStruct);

            if ($responseCode = $paymentResponse->getResponseCode()) {
                $this->paymentSessionService->setResponseCode($responseCode);
            }

            if (!$paymentResponse->isSuccessful()) {
                $this->handleFailedResponse();

                return;
            }

            // set the mandate reference for sepa payment
            if ($paymentResponse->getPaymentSource() !== null && $mandateReference = $paymentResponse->getMandateReference()) {
                $this->paymentSessionService->setMandateReference($mandateReference);
            }

            $shopId = $this->dependencyProviderService->getShop()->getId();

            if (!$this->configurationService->isCreditCart3dsEnabled($shopId) && $paymentRequestStruct->getPaymentMethodName() === CreditCardPaymentMethod::NAME) {
                $this->handlePaymentResponse($paymentResponse->getPaymentId(), $shopId);

                return;
            }

            if ($paymentResponse->getStatus() === CheckoutApiPaymentStatus::API_PAYMENT_PENDING && !$paymentResponse->getRedirectionUrl()) {
                $this->handlePaymentResponse($paymentResponse->getPaymentId(), $shopId);

                return;
            }

            if ($paymentResponse->getRedirectionUrl()) {
                $this->redirect($paymentResponse->getRedirectionUrl());

                return;
            }

            $this->handlePaymentResponse($paymentResponse->getPaymentId(), $shopId);
        } catch (RequiredPaymentDetailsMissingException $requiredPaymentDetailsMissingException) {
            $this->loggerService->error($requiredPaymentDetailsMissingException->getMessage());
            $this->handleFailedResponse();
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());
            $this->handleFailedResponse();
        } catch (\RuntimeException $runtimeException) {
            $this->loggerService->error($runtimeException->getMessage());
            $this->handleFailedResponse();
        }
    }

    protected function handlePaymentResponse(string $paymentId, ?int $shopId): void
    {
        $basketSignature = $this->paymentSessionService->get(RequestConstants::BASKET_SIGNATURE);

        if (empty($basketSignature)) {
            throw new \RuntimeException('basket signature is empty');
        }

        $this->verifyBasketSignatureIsValid($basketSignature);

		$paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($paymentId, $shopId);

		if ($paymentDetailsResponse->getSource()['type'] === 'card' && $this->paymentSessionService->get(RequestConstants::CC_SAVE_CARD) === "true") {
			$this->saveCreditcardSource($paymentDetailsResponse->getSource());
		}

        $this->saveOrder(
            $paymentId,
            $this->paymentSessionService->getPaymentReference(),
            OrderStatus::PAYMENT_STATE_OPEN
        );
        $this->paymentSessionService->clearPaymentSession();

        $this->redirect(['controller' => 'checkout', 'action' => 'finish']);
    }

    public function saveCreditcardSource(array $paymentSource): void
    {
        $customerId = (int)$this->getUser()['additional']['user']['id'];
        $lastFour = (string)$paymentSource['last4'];
        $expiryMonth = (string)$paymentSource['expiry_month'];
        $expiryYear = (string)$paymentSource['expiry_year'];
        $scheme = $paymentSource['scheme'];

        $card = new CardStruct($customerId, $paymentSource['id'], $lastFour, $expiryMonth, $expiryYear, $scheme);
        $this->cardManagementService->saveCard($card);
    }

    public function deleteCreditcardSource($sourceId)
    {
        $customerId = (int)$this->getUser()['additional']['user']['id'];
        $this->cardManagementService->deleteCard($customerId, $sourceId);
    }

    protected function handleFailedResponse(): void
    {
        $this->paymentSessionService->clearPaymentSession();
        $this->paymentSessionService->setPaymentFailed();

        $this->redirect(['controller' => 'checkout', 'action' => 'cart']);
    }

    protected function createDefaultPaymentRequest(): PaymentRequestStruct
    {
        $paymentMethodName = $this->getPaymentShortName();
        $shopId = $this->dependencyProviderService->getShop()->getId();

        return new PaymentRequestStruct(
            $paymentMethodName,
            $this->configurationService->isAutoCaptureEnabled($paymentMethodName, $shopId),
            $this->getAmount(),
            $this->getCurrencyShortName(),
            $this->createPaymentUniqueId(),
            $this->getPurpose(),
            $this->getFinishUrl(),
            $this->getCancelOrFailureUrl(),
            $this->paymentSessionService->get(RequestConstants::BASKET_SIGNATURE),
            $this->getUser(),
            $this->getBasket(),
            $this->paymentSessionService->get(RequestConstants::TOKEN),
            $this->paymentSessionService->get(RequestConstants::SOURCE_ID),
            $this->paymentSessionService->get(RequestConstants::BIC),
            $this->paymentSessionService->get(RequestConstants::IBAN),
            SepaPaymentRequestService::MANDATE_TYPE_SINGLE,
            $this->createGooglePayStruct(),
            $this->createApplePayStruct()
        );
    }

    protected function verifyBasketSignatureIsValid(string $signature): void
    {
        $basket = $this->loadBasketFromSignature($signature);
        $this->verifyBasketSignature($signature, $basket);
    }

    protected function setJsonResponse(array $data): void
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody(json_encode($data));
    }

    private function getPurpose(): string
    {
        // api only accepts purpose up to 27 characters
        $shopName = $this->dependencyProviderService->getShop()->getName();
        if (mb_strlen($shopName) > 27) {
            $shopName = mb_substr($shopName, 0, 27);
        }

        return $shopName;
    }

    private function getFinishUrl(): string
    {
        $router = $this->Front()->Router();

        return $router->assemble(['controller' => 'CkoCheckoutPayment', 'action' => 'return']);
    }

    private function getCancelOrFailureUrl(): string
    {
        $router = $this->Front()->Router();

        return $router->assemble(['controller' => 'CkoCheckoutPayment', 'action' => 'cancel']);
    }

    private function createGooglePayStruct(): GooglePayStruct
    {
        return new GooglePayStruct(
            $this->paymentSessionService->get(RequestConstants::GOOGLE_PAY_SIGNATURE),
            $this->paymentSessionService->get(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION),
            $this->paymentSessionService->get(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE)
        );
    }

    private function createApplePayStruct(): ApplePayStruct
    {
        return new ApplePayStruct(
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_TRANSACTION_ID),
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_PUBLIC_KEY_HASH),
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_EPHEMERAL_PUBLIC_KEY),
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_VERSION),
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_SIGNATURE),
            $this->paymentSessionService->get(RequestConstants::APPLE_PAY_DATA)
        );
    }
}
