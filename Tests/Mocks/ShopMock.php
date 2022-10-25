<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;

class ShopMock extends Shop
{
    public function getId()
    {
        return 1;
    }

    public function getHost()
    {
        return '127.0.0.1';
    }

    public function getActive()
    {
        return true;
    }

    public function getLocale()
    {
        $locale = new Locale();
        $locale->setLanguage('Deutsch');
        $locale->setLocale('de_DE');

        return $locale;
    }
}