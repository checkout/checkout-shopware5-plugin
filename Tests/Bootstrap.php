<?php

namespace CkoCheckoutPayment\Tests;

require __DIR__.'/../../../../shopware.php';

class CkoCheckoutPaymentTestKernel extends \Shopware\Kernel
{
    private const PLUGIN_NAME = 'CkoCheckoutPayment';

    public static function start(): void
    {
        ini_set('error_reporting', E_ALL | E_STRICT);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        if (!self::isPluginInstalledAndActivated()) {
            die('Error: The plugin is not installed or activated, tests aborted!');
        }

        Shopware()->Loader()->registerNamespace(self::PLUGIN_NAME, __DIR__.'/../');
    }

    private static function isPluginInstalledAndActivated(): bool
    {
        /** @var \Doctrine\DBAL\Connection $db */
        $db = Shopware()->Container()->get('dbal_connection');
        $active = $db->fetchColumn('SELECT active FROM s_core_plugins WHERE name = ?', [self::PLUGIN_NAME]);

        return (bool) $active;
    }
}

CkoCheckoutPaymentTestKernel::start();
