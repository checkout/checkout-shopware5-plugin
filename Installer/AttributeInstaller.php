<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Installer;

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMappingInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class AttributeInstaller implements InstallerInterface
{
    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(
        CrudService $crudService,
        ModelManager $modelManager
    ) {
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
    }

    public function install(InstallContext $context): void
    {
        $this->updateAttributes();
    }

    public function update(UpdateContext $context): void
    {
        $this->updateAttributes();
    }

    public function uninstall(UninstallContext $context): void
    {
        // nothing todo here
    }

    public function deactivate(DeactivateContext $context): void
    {
        // nothing todo here
    }

    public function activate(ActivateContext $context): void
    {
        // nothing todo here
    }

    private function updateAttributes(): void
    {
        $this->crudService->update(
            's_order_attributes',
            'cko_mandate_reference',
            TypeMappingInterface::TYPE_STRING
        );

        $this->modelManager->generateAttributeModels(['s_order_attributes']);
    }
}