<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Installer;

use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\EpsPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GiropayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\IdealPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\PaymentMethodInterface;
use CkoCheckoutPayment\Components\PaymentMethods\PayPalPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\Przelewy24PaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SofortPaymentMethod;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\PaymentInstaller;

class PaymentMethodInstaller implements InstallerInterface
{
    private const PAYMENT_METHODS = [
        BancontactPaymentMethod::class,
        CreditCardPaymentMethod::class,
        EpsPaymentMethod::class,
        GiropayPaymentMethod::class,
        GooglePayPaymentMethod::class,
        ApplePayPaymentMethod::class,
        SofortPaymentMethod::class,
        IdealPaymentMethod::class,
        SepaPaymentMethod::class,
        KlarnaPaymentMethod::class,
        PayPalPaymentMethod::class,
        Przelewy24PaymentMethod::class
    ];

    /**
     * @var PaymentInstaller
     */
    private $paymentInstaller;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(
        PaymentInstaller $paymentInstaller,
        ModelManager $modelManager
    ) {
        $this->paymentInstaller = $paymentInstaller;
        $this->modelManager = $modelManager;
    }

    public function install(InstallContext $context): void
    {
        $this->upsertPaymentMethods(self::PAYMENT_METHODS, $context);
    }

    public function update(UpdateContext $context): void
    {
        // nothing todo here
    }

    public function uninstall(UninstallContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    public function deactivate(DeactivateContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), false);
    }

    public function activate(ActivateContext $context): void
    {
        $this->setActiveFlag($context->getPlugin()->getPayments(), true);
    }

    private function upsertPaymentMethods(array $paymentMethods, InstallContext $context): void
    {
        foreach ($paymentMethods as $paymentMethodClass) {
            /** @var PaymentMethodInterface $paymentMethod */
            $paymentMethod = new $paymentMethodClass();

            if (!$paymentMethod instanceof PaymentMethodInterface) {
                continue;
            }

            $options = [
                'name' => $paymentMethod->getName(),
                'description' => $paymentMethod->getDescription(),
                'action' => $paymentMethod->getAction(),
                'active' => $paymentMethod->isActive(),
                'position' => $paymentMethod->getPosition(),
                'additionalDescription' => $paymentMethod->getAdditionalDescription()
            ];
            $this->paymentInstaller->createOrUpdate($context->getPlugin()->getName(), $options);
        }
    }

    private function setActiveFlag($payments, bool $active): void
    {
        foreach ($payments as $payment) {
            $payment->setActive($active);
        }

        $this->modelManager->flush();
    }
}