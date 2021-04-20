<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Frontend;

use CkoCheckoutPayment\Components\CardManagement\CardManagementServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder\KlarnaRequestBuilderServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Details\PaymentDetailsRequestServiceInterface;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GiropayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\IdealPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\PayPalPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\Przelewy24PaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SofortPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\GooglePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\SepaConfiguration;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;

class FrontendCheckoutSubscriber implements SubscriberInterface
{
    private const ACTION_SHIPPING_PAYMENT = 'shippingPayment';
    private const ACTION_CHECKOUT_CONFIRM = 'confirm';
    private const ACTION_CHECKOUT_FINISH = 'finish';

    private const SUBSCRIBE_ACTIONS_CHECKOUT = [
        self::ACTION_SHIPPING_PAYMENT,
        self::ACTION_CHECKOUT_CONFIRM,
        self::ACTION_CHECKOUT_FINISH
    ];

    private const PAYMENT_METHOD_PREFIX = 'cko_';

    private const SNIPPET_SEPA_MANDATE_NAMESPACE = 'frontend/cko_checkout_payment/checkout_sepa/sepa_mandate';

    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    /**
     * @var PaymentSessionServiceInterface
     */
    private $paymentSessionService;

    /**
     * @var PaymentMethodValidatorServiceInterface
     */
    private $paymentMethodValidatorService;

    /**
     * @var OrderProviderServiceInterface
     */
    private $orderProviderService;

    /**
     * @var KlarnaRequestBuilderServiceInterface
     */
    private $klarnaRequestBuilderService;

    /**
     * @var PaymentDetailsRequestServiceInterface
     */
    private $paymentDetailsRequestService;

    /**
     * @var CheckoutApiClientServiceInterface
     */
    private $apiClientService;

    /**
     * @var \Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var \Shopware_Components_Modules
     */
    private $moduleManager;

    /**
     * @var CardManagementServiceInterface
     */
    private $cardManagementService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        DependencyProviderServiceInterface $dependencyProviderService,
        PaymentSessionServiceInterface $paymentSessionService,
        PaymentMethodValidatorServiceInterface $paymentMethodValidatorService,
        OrderProviderServiceInterface $orderProviderService,
        KlarnaRequestBuilderServiceInterface $klarnaRequestBuilderService,
        PaymentDetailsRequestServiceInterface $paymentDetailsRequestService,
        CheckoutApiClientServiceInterface $apiClientService,
        \Shopware_Components_Snippet_Manager $snippetManager,
        \Shopware_Components_Modules $moduleManager,
        CardManagementServiceInterface $cardManagementService
    ) {
        $this->configurationService = $configurationService;
        $this->dependencyProviderService = $dependencyProviderService;
        $this->paymentSessionService = $paymentSessionService;
        $this->paymentMethodValidatorService = $paymentMethodValidatorService;
        $this->orderProviderService = $orderProviderService;
        $this->klarnaRequestBuilderService = $klarnaRequestBuilderService;
        $this->paymentDetailsRequestService = $paymentDetailsRequestService;
        $this->apiClientService = $apiClientService;
        $this->snippetManager = $snippetManager;
        $this->moduleManager = $moduleManager;
        $this->cardManagementService = $cardManagementService;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatchCheckout'
        ];
    }

    public function onPostDispatchCheckout(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->assign('ckoHasPaymentFailed', $this->paymentSessionService->hasPaymentFailed());

        if ($this->paymentSessionService->hasPaymentFailed()) {
            $this->paymentSessionService->setPaymentFailed(false);
        }

        if (!in_array($controller->Request()->getActionName(), self::SUBSCRIBE_ACTIONS_CHECKOUT, true)) {
            return;
        }

        $this->validatePaymentMethodRequiredDetails($args);
        $this->assignPaymentMethodNamesToView($args);

        $this->assignBasketDataToView($args);
        $this->assignGooglePayDataToView($args);
        $this->assignApplePayDataToView($args);
        $this->assignKlarnaDataToView($args);
        $this->assignCreditcardDataToView($args);
        $this->assignSepaDataToView($args);
    }

    private function validatePaymentMethodRequiredDetails(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $currentPaymentMethodName = $user['additional']['payment']['name'];

        if ($controller->Request()->getActionName() !== self::ACTION_CHECKOUT_CONFIRM) {
            return;
        }

        if (!$this->paymentMethodValidatorService->isCheckoutPaymentMethod($currentPaymentMethodName)) {
            return;
        }

        if ($this->paymentMethodValidatorService->isPaymentMethodValid($currentPaymentMethodName)) {
            return;
        }

        // redirect to change payment if required payment details for current payment method are missing
        $controller->redirect(['controller' => 'checkout', 'action' => self::ACTION_SHIPPING_PAYMENT]);
    }

    private function assignPaymentMethodNamesToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $view->assign('googlePayPaymentMethodName', GooglePayPaymentMethod::NAME);
        $view->assign('applePayPaymentMethodName', ApplePayPaymentMethod::NAME);
        $view->assign('klarnaPaymentMethodName', KlarnaPaymentMethod::NAME);
        $view->assign('giropayPaymentMethodName', GiropayPaymentMethod::NAME);
        $view->assign('idealPaymentMethodName', IdealPaymentMethod::NAME);
        $view->assign('sepaPaymentMethodName', SepaPaymentMethod::NAME);
        $view->assign('creditCardPaymentMethodName', CreditCardPaymentMethod::NAME);
        $view->assign('paypalPaymentMethodName', PayPalPaymentMethod::NAME);
        $view->assign('sofortPaymentMethodName', SofortPaymentMethod::NAME);
        $view->assign('przelewy24PaymentMethodName', Przelewy24PaymentMethod::NAME);
    }

    private function assignBasketDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $basket = $view->getAssign('sBasket');
        $currentPaymentMethodId = (int)$view->getAssign('sFormData')['payment'] ?? 0;

        $user = $this->moduleManager->Admin()->sGetUserData();

        $showAdditionalSepaInformation = false;
        if ($user['additional']['payment']['name'] === SepaPaymentMethod::NAME) {
            $showAdditionalSepaInformation = true;
        }

        $shop = $this->dependencyProviderService->getShop();

        try {
            $view->assign('ckoApiPublicKey', $this->apiClientService->getPublicKey($shop->getId()));
        } catch (\RuntimeException $exception) {
            $view->assign('ckoApiPublicKey', '');
        }

        $view->assign('ckoPaymentMethodPrefix', self::PAYMENT_METHOD_PREFIX);
        $view->assign('ckoShowAdditionalSepaInformation', $showAdditionalSepaInformation);
        $view->assign('ckoCurrentCurrency', $basket['sCurrencyName']);
        $view->assign('ckoTotalPrice', $basket['sAmount']);
        $view->assign('ckoCurrentPaymentMethodId', $currentPaymentMethodId);
        $view->assign('ckoUserBillingAddress', $user['billingaddress']);
        $view->assign('ckoUserBillingCountryName', $user['additional']['country']['countryname']);
        $view->assign('ckoUserBillingCountryCode', $user['additional']['country']['countryiso']);
        $view->assign('ckoShopName', $shop->getName());
    }

    private function assignCreditcardDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== CreditCardPaymentMethod::NAME) {
            return;
        }

        foreach ([RequestConstants::TOKEN, RequestConstants::CC_EXPIRY_DATE, RequestConstants::CC_LAST_4] as $constant) {
            $view->assign($constant, $this->paymentSessionService->get($constant));
        }

        $customerId =  (int) $user['additional']['user']['id'];
        $view->assign('ckoCreditcardSaved', $this->cardManagementService->getCards($customerId));

        $isGuestAccount = $user['additional']['user']['accountmode'] === '1';

        $view->assign('ckoIsGuestOrder', $isGuestAccount);

        if($this->paymentSessionService->get(RequestConstants::SOURCE_ID)) {
            $view->assign('ckoSourceId', $this->paymentSessionService->get(RequestConstants::SOURCE_ID));
        }

        if($this->paymentSessionService->get(RequestConstants::TOKEN)
            && $this->paymentSessionService->get(RequestConstants::CC_EXPIRY_DATE)
            && $this->paymentSessionService->get(RequestConstants::CC_LAST_4))
        {
            // CC data has already been entered. We only show the already entered data
            $view->assign('ckoCreditcardIsEntered', true);
        }
    }

    private function assignGooglePayDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== GooglePayPaymentMethod::NAME) {
            return;
        }

        $shop = $this->dependencyProviderService->getShop();
        $shopId = $shop->getId();

        try {
            /** @var GooglePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(GooglePayPaymentMethod::NAME, $shopId);
            $allowedCardNetworks = $this->configurationService->getGooglePayAllowedCardNetworks($shopId);

            $view->assign('ckoGooglePayAllowedCardNetworks', implode(',', $allowedCardNetworks));
            $view->assign('ckoGooglePayButtonColor', $configuration->getButtonColor());
            $view->assign('ckoGooglePayMerchantId', $configuration->getMerchantId());
            $view->assign('ckoGooglePayGatewayMerchantId', $this->apiClientService->getPublicKey($shopId));
            $view->assign('ckoGooglePayEnvironment', $this->configurationService->getGooglePayEnvironment($shopId));
        } catch (\RuntimeException $exception) {
            $view->assign('ckoGooglePayAllowedCardNetworks', '');
            $view->assign('ckoGooglePayButtonColor', '');
            $view->assign('ckoGooglePayMerchantId', '');
            $view->assign('ckoGooglePayGatewayMerchantId', '');
            $view->assign('ckoGooglePayEnvironment', '');
        }
    }

    private function assignApplePayDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== ApplePayPaymentMethod::NAME) {
            return;
        }

        $shop = $this->dependencyProviderService->getShop();
        $shopId = $shop->getId();

        try {
            /** @var ApplePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId);
            $supportedNetworks = $this->configurationService->getApplePaySupportedNetworks($shopId);
            $merchantCapabilities = $this->configurationService->getApplePayMerchantCapabilities($shopId);

            $view->assign('ckoApplePayMerchantId', $configuration->getMerchantId());
            $view->assign('ckoApplePaySupportedNetworks', implode(',', $supportedNetworks));
            $view->assign('ckoApplePayMerchantCapabilities', implode(',', $merchantCapabilities));
            $view->assign('ckoApplePayButtonColor', $configuration->getButtonColor());
        } catch (\RuntimeException $exception) {
            $view->assign('ckoApplePayMerchantId', '');
            $view->assign('ckoApplePaySupportedNetworks', '');
            $view->assign('ckoApplePayMerchantCapabilities', '');
            $view->assign('ckoApplePayButtonColor', '');
        }
    }

    private function assignKlarnaDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== KlarnaPaymentMethod::NAME) {
            return;
        }

        $shop = $this->dependencyProviderService->getShop();

        try {
            $klarnaRequest = $this->klarnaRequestBuilderService->createKlarnaPaymentInitializeData($view->getAssign('sBasket'), $user, $shop->getId());

            $view->assign('ckoKlarnaClientToken', $klarnaRequest->getClientToken());
            $view->assign('ckoKlarnaInstanceId', $klarnaRequest->getInstanceId());
            $view->assign('ckoKlarnaPaymentMethods', $klarnaRequest->getPaymentMethods());
            $view->assign('ckoKlarnaData', $klarnaRequest->getRequestData());
        } catch (\RuntimeException $exception) {
            $view->assign('ckoKlarnaClientToken', '');
            $view->assign('ckoKlarnaInstanceId', '');
            $view->assign('ckoKlarnaPaymentMethods', []);
            $view->assign('ckoKlarnaData', '');
        }
    }

    private function assignSepaDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== SepaPaymentMethod::NAME) {
            return;
        }

        $shop = $this->dependencyProviderService->getShop();

        try {
            /** @var SepaConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(SepaPaymentMethod::NAME, $shop->getId());

            $creditorName = $configuration->getMandateCreditorName();
            $mandateAgreementTextOne = $this->snippetManager->getNamespace(self::SNIPPET_SEPA_MANDATE_NAMESPACE)->get('mandateAgreement/textOne');
            $mandateAgreementTextOne = str_replace('{$bankName}', $creditorName, $mandateAgreementTextOne);

            $view->assign('ckoSepaMandateAgreementTextOne', $mandateAgreementTextOne);
            $view->assign('ckoSepaMandateCreditorName', $creditorName);
            $view->assign('ckoSepaMandateCreditorId', $configuration->getMandateCreditorId());
            $view->assign('ckoSepaMandateCreditorAddressFirst', $configuration->getMandateCreditorAddressFirst());
            $view->assign('ckoSepaMandateCreditorAddressSecond', $configuration->getMandateCreditorAddressSecond());
            $view->assign('ckoSepaMandateCreditorCountry', $configuration->getMandateCreditorCountry());

            $this->assignSepaCheckoutFinishDataToView($args);
        } catch (\RuntimeException $exception) {
            $view->assign('ckoSepaMandateAgreementTextOne', '');
            $view->assign('ckoSepaMandateCreditorName', '');
            $view->assign('ckoSepaMandateCreditorId', '');
            $view->assign('ckoSepaMandateCreditorAddressFirst', '');
            $view->assign('ckoSepaMandateCreditorAddressSecond', '');
            $view->assign('ckoSepaMandateCreditorCountry', '');
        }
    }

    private function assignSepaCheckoutFinishDataToView(\Enlight_Event_EventArgs $args): void
    {
        /** @var Enlight_Controller_Action $controller */
        $controller = $args->getSubject();
        $view = $controller->View();

        if ($controller->Request()->getActionName() !== self::ACTION_CHECKOUT_FINISH) {
            return;
        }

        $user = $this->moduleManager->Admin()->sGetUserData();
        $paymentMethodName = $user['additional']['payment']['name'];

        if ($paymentMethodName !== SepaPaymentMethod::NAME) {
            return;
        }

        $orderVariables = $this->paymentSessionService->getOrderVariables();
        $shop = $this->dependencyProviderService->getShop();

        try {
            $order = $this->orderProviderService->getOrderByNumber((string)$orderVariables['sOrderNumber']);
            $paymentDetails = $this->paymentDetailsRequestService->getPaymentDetails($order->getTransactionId(), $shop->getId());

            $view->assign('ckoSepaMandateReference', $order->getAttribute()->getCkoMandateReference());
            $view->assign('ckoSepaEstimatedDueDate', $paymentDetails->getPlannedDebitDate()->format('d.m.Y'));
        } catch (\Throwable $e) {
            $view->assign('ckoSepaMandateReference', null);
            $view->assign('ckoSepaEstimatedDueDate', null);
        }
    }
}
