<?php

declare(strict_types=1);

use CkoCheckoutPayment\Components\CheckoutApi\Builder\ResponseBuilder\PaymentDetailsResponseBuilderServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Actions\PaymentActionsRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Capture\CaptureRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Details\PaymentDetailsRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Refund\RefundRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Request\Void\VoidRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\CaptureRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\RefundRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\VoidRequestStruct;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderServiceInterface;
use Shopware\Models\Order\Order;

class Shopware_Controllers_Backend_CkoCheckoutPayment extends Shopware_Controllers_Backend_Application
{
    protected $model = Order::class;

    /**
     * @var OrderProviderServiceInterface
     */
    private $orderProviderService;

    /**
     * @var CaptureRequestServiceInterface
     */
    private $captureService;

    /**
     * @var VoidRequestServiceInterface
     */
    private $voidService;

    /**
     * @var RefundRequestServiceInterface
     */
    private $refundService;

    /**
     * @var PaymentDetailsRequestServiceInterface
     */
    private $paymentDetailsService;

    /**
     * @var PaymentActionsRequestServiceInterface
     */
    private $paymentActionsService;

    /**
     * @var PaymentDetailsResponseBuilderServiceInterface
     */
    private $paymentDetailsResponseBuilderService;

    /**
     * @var LoggerServiceInterface
     */
    private $loggerService;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->Front()->Plugins()->Json()->setRenderer();

        $this->orderProviderService = $this->get('cko_checkout_payment.components.order_provider.order_provider_service');
        $this->captureService = $this->get('cko_checkout_payment.components.checkout_api.request.capture.capture_request_service');
        $this->voidService = $this->get('cko_checkout_payment.components.checkout_api.request.void.void_request_service');
        $this->refundService = $this->get('cko_checkout_payment.components.checkout_api.request.refund.refund_request_service');
        $this->paymentDetailsService = $this->get('cko_checkout_payment.components.checkout_api.request.details.payment_details_request_service');
        $this->paymentActionsService = $this->get('cko_checkout_payment.components.checkout_api.request.actions.payment_actions_request_service');
        $this->paymentDetailsResponseBuilderService = $this->get('cko_checkout_payment.components.checkout_api.builder.response_builder.payment_details_response_builder_service');
        $this->loggerService = $this->get('cko_checkout_payment.components.logger.logger_service');
    }

    public function getPaymentDetailsAction()
    {
        try {
            $order = $this->orderProviderService->getOrderById((int)$this->Request()->getParam('orderId'));

            $paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));
            $paymentActionsResponse = $this->paymentActionsService->getPaymentActions($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));

            $data = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse, $order->getAttribute()->getCkoMandateReference());

            $this->view->assign([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\RuntimeException $orderNotFoundException) {
            $this->view->assign([
                'success' => false,
            ]);
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());

            $this->view->assign([
                'success' => false,
            ]);
        }
    }

    public function capturePaymentAction()
    {
        try {
            $order = $this->orderProviderService->getOrderById((int)$this->Request()->getParam('orderId'));

            $paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));
            $paymentActionsResponse = $this->paymentActionsService->getPaymentActions($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));

            $data = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

            if (!$data[PaymentDetailsResponseBuilderServiceInterface::IS_CAPTURE_POSSIBLE]) {
                return;
            }

            $captureRequest = new CaptureRequestStruct(
                $this->Request()->getParam('paymentId', ''),
                $this->Request()->getParam('reference', ''),
                $order->getPayment()->getName(),
                (int)$this->Request()->getParam('shopId'),
                (float)$this->Request()->getParam('captureAmount', 0.00),
                (bool)$this->Request()->getParam('isPartialCapture', false)
            );
            $this->captureService->capturePayment($captureRequest);

            $this->view->assign([
                'success' => true,
            ]);
        } catch (\RuntimeException $orderNotFoundException) {
            $this->view->assign([
                'success' => false,
            ]);
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());

            $this->view->assign([
                'success' => false,
                'error' => $checkoutApiRequestException->getMainErrorReason(),
            ]);
        }
    }

    public function voidPaymentAction()
    {
        try {
            $order = $this->orderProviderService->getOrderById((int)$this->Request()->getParam('orderId'));

            $paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));
            $paymentActionsResponse = $this->paymentActionsService->getPaymentActions($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));

            $data = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

            if (!$data[PaymentDetailsResponseBuilderServiceInterface::IS_VOID_POSSIBLE]) {
                return;
            }

            $voidRequest = new VoidRequestStruct(
                $this->Request()->getParam('paymentId', ''),
                $order->getPayment()->getName(),
                (int)$this->Request()->getParam('shopId')
            );
            $this->voidService->voidPayment($voidRequest);

            $this->view->assign([
                'success' => true,
            ]);
        } catch (\RuntimeException $orderNotFoundException) {
            $this->view->assign([
                'success' => false,
            ]);
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());

            $this->view->assign([
                'success' => false,
                'error' => $checkoutApiRequestException->getMainErrorReason(),
            ]);
        }
    }

    public function refundPaymentAction()
    {
        try {
            $paymentDetailsResponse = $this->paymentDetailsService->getPaymentDetails($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));
            $paymentActionsResponse = $this->paymentActionsService->getPaymentActions($this->Request()->getParam('paymentId'), (int)$this->Request()->getParam('shopId'));

            $data = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

            if (!$data[PaymentDetailsResponseBuilderServiceInterface::IS_REFUND_POSSIBLE]) {
                return;
            }

            $refundRequest = new RefundRequestStruct(
                $this->Request()->getParam('paymentId', ''),
                $this->Request()->getParam('reference', ''),
                (int)$this->Request()->getParam('shopId'),
                (float)$this->Request()->getParam('refundAmount', 0.00),
                (bool)$this->Request()->getParam('isPartialRefund', false)
            );
            $this->refundService->refundPayment($refundRequest);

            $this->view->assign([
                'success' => true,
            ]);
        } catch (CheckoutApiRequestException $checkoutApiRequestException) {
            $this->loggerService->error($checkoutApiRequestException->getMessage(), $checkoutApiRequestException->getContext());

            $this->view->assign([
                'success' => false,
                'error' => $checkoutApiRequestException->getMainErrorReason(),
            ]);
        }
    }
}
