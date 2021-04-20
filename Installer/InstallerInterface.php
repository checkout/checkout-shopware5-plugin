<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Installer;

use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

interface InstallerInterface
{
    public function install(InstallContext $context): void;

    public function update(UpdateContext $context): void;

    public function uninstall(UninstallContext $context): void;

    public function deactivate(DeactivateContext $context): void;

    public function activate(ActivateContext $context): void;
}