<?php

declare(strict_types=1);

namespace CkoCheckoutPayment;

use CkoCheckoutPayment\Components\DependencyInjection\PaymentMethodValidatorCompilerPass;
use CkoCheckoutPayment\Components\DependencyInjection\PaymentRequestHandlerCompilerPass;
use CkoCheckoutPayment\Installer\AttributeInstaller;
use CkoCheckoutPayment\Installer\DocumentInstaller;
use CkoCheckoutPayment\Installer\PaymentMethodInstaller;
use CkoCheckoutPayment\Installer\SchemaInstaller;
use Doctrine\DBAL\Connection;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\PaymentInstaller;
use Symfony\Component\DependencyInjection\ContainerBuilder;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class CkoCheckoutPayment extends Plugin
{
    private const PLUGIN_MANAGER_NAMESPACE = 'backend/cko_checkout_payment/pluginmanager';

    public function build(ContainerBuilder $container)
    {
        $container->setParameter('cko_checkout_payment.plugin_name', $this->getName());
        $container->setParameter('cko_checkout_payment.plugin_dir', $this->getPath());

        $container->addCompilerPass(new PaymentMethodValidatorCompilerPass());
        $container->addCompilerPass(new PaymentRequestHandlerCompilerPass());

        parent::build($container);
    }

    public function install(InstallContext $context)
    {
        $this->getPaymentMethodInstaller()->install($context);
        $this->getAttributeInstaller()->install($context);
        $this->getSchemaInstaller()->install($context);
        $this->getDocumentInstaller()->install($context);
    }

    public function update(UpdateContext $context)
    {
        $this->getPaymentMethodInstaller()->update($context);
        $this->getAttributeInstaller()->update($context);
        $this->getSchemaInstaller()->update($context);
        $this->getDocumentInstaller()->update($context);

		$context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    public function uninstall(UninstallContext $context)
    {
        $this->getPaymentMethodInstaller()->uninstall($context);
        $this->getAttributeInstaller()->uninstall($context);
        $this->getSchemaInstaller()->uninstall($context);
        $this->getDocumentInstaller()->uninstall($context);
    }

    public function deactivate(DeactivateContext $context)
    {
        $this->getPaymentMethodInstaller()->deactivate($context);
        $this->getAttributeInstaller()->deactivate($context);
        $this->getSchemaInstaller()->deactivate($context);
        $this->getDocumentInstaller()->deactivate($context);

        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    public function activate(ActivateContext $context)
    {
        $this->getPaymentMethodInstaller()->activate($context);
        $this->getAttributeInstaller()->activate($context);
        $this->getSchemaInstaller()->activate($context);
        $this->getDocumentInstaller()->activate($context);

        /** @var \Enlight_Components_Snippet_Manager $snippetManager */
        $snippetManager = $this->container->get('snippets');
        $snippetNamespace = $snippetManager->getNamespace(self::PLUGIN_MANAGER_NAMESPACE);

        $context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
        $context->scheduleMessage($snippetNamespace->get('reload/message'));
    }

    private function getPaymentMethodInstaller(): PaymentMethodInstaller
    {
        /** @var PaymentInstaller $paymentInstaller */
        $paymentInstaller = $this->container->get('shopware.plugin_payment_installer');
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        return new PaymentMethodInstaller($paymentInstaller, $modelManager);
    }

    private function getAttributeInstaller(): AttributeInstaller
    {
        /** @var CrudService $crudService */
        $crudService = $this->container->get('shopware_attribute.crud_service');
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        return new AttributeInstaller($crudService, $modelManager);
    }

    private function getSchemaInstaller(): SchemaInstaller
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');

        return new SchemaInstaller($modelManager);
    }

    private function getDocumentInstaller(): DocumentInstaller
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->container->get('models');
        /** @var Connection $connection */
        $connection = $this->container->get('dbal_connection');
        /** @var \Shopware_Components_Translation $translationService */
        $translationService = $this->container->get('translation');

        return new DocumentInstaller($modelManager, $connection, $translationService);
    }
}
