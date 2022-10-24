<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Installer;

use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;
use CkoCheckoutPayment\Models\Configuration\GeneralConfiguration;
use CkoCheckoutPayment\Models\Configuration\GooglePayConfiguration;
use CkoCheckoutPayment\Models\Configuration\PayPalConfiguration;
use CkoCheckoutPayment\Models\Configuration\SepaConfiguration;
use CkoCheckoutPayment\Models\Configuration\SofortConfiguration;
use CkoCheckoutPayment\Models\Event;
use CkoCheckoutPayment\Models\StoredCard;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Model\ModelManager;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;

class SchemaInstaller implements InstallerInterface
{
    /**
     * @var ModelManager
     */
    private $modelManager;

    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
    }

    public function install(InstallContext $context): void
    {
        $this->renameOldTablesWithOldPrefix();
        $this->updateSchema();
    }

    public function update(UpdateContext $context): void
    {
        $this->renameOldTablesWithOldPrefix();
        $this->updateSchema();
    }

    public function uninstall(UninstallContext $context): void
    {
        if ($context->keepUserData()) {
            return;
        }

        $this->dropSchema();
    }

    public function deactivate(DeactivateContext $context): void
    {
        // nothing todo here
    }

    public function activate(ActivateContext $context): void
    {
        // nothing todo here
    }

    private function updateSchema(): void
    {
        $tool = new SchemaTool($this->modelManager);

        $schemas = $this->getSchemas();

        /** @var MySqlSchemaManager $schemaManager */
        $schemaManager = $this->modelManager->getConnection()->getSchemaManager();
        foreach ($schemas as $class) {
            if (!$schemaManager->tablesExist($class->getTableName())) {
                $tool->createSchema([$class]);
            } else {
                $tool->updateSchema([$class], true); //true - saveMode and not delete other schemas
            }
        }
    }

    private function renameOldTablesWithOldPrefix(): void
    {
        $connection = $this->modelManager->getConnection();
        /** @var MySqlSchemaManager $schemaManager */
        $schemaManager = $connection->getSchemaManager();

        $tableNames = [
            's_plugin_checkout_events' => 's_plugin_cko_events',
            's_plugin_checkout_stored_cards' => 's_plugin_cko_stored_cards'
        ];

        foreach ($tableNames as $oldTableName => $newTableName) {
            if ($schemaManager->tablesExist([$oldTableName])) {
                try {
                    $connection->exec(sprintf('ALTER TABLE `%s` RENAME TO `%s`', $oldTableName, $newTableName));
                } catch (DBALException $dbalException) {
                    // ignore database errors
                }
            }
        }
    }

    private function dropSchema(): void
    {
        $tool = new SchemaTool($this->modelManager);

        $schemas = $this->getSchemas();

        /** @var MySqlSchemaManager $schemaManager */
        $schemaManager = $this->modelManager->getConnection()->getSchemaManager();
        foreach ($schemas as $class) {
            if ($schemaManager->tablesExist($class->getTableName())) {
                $tool->dropSchema([$class]);
            }
        }
    }

    private function getSchemas(): array
    {
        return [
            $this->modelManager->getClassMetadata(StoredCard::class),
            $this->modelManager->getClassMetadata(Event::class),
            $this->modelManager->getClassMetadata(GeneralConfiguration::class),
            $this->modelManager->getClassMetadata(ApplePayConfiguration::class),
            $this->modelManager->getClassMetadata(GooglePayConfiguration::class),
            $this->modelManager->getClassMetadata(SepaConfiguration::class),
            $this->modelManager->getClassMetadata(SofortConfiguration::class),
            $this->modelManager->getClassMetadata(CreditCardConfiguration::class),
            $this->modelManager->getClassMetadata(PayPalConfiguration::class)
        ];
    }
}
