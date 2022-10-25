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

        // fix for Fatal error: Uncaught TypeError: date() expects parameter 2 to be integer, string given in vendor/phpunit/php-code-coverage/src/Report/Html/Facade.php:63
        // only happens with php 7.2 and phpunit code coverage driver
        $_SERVER['REQUEST_TIME'] = (int) $_SERVER['REQUEST_TIME'];
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
