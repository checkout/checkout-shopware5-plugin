<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\DependencyProvider;

use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Plugin\Plugin;
use Shopware\Models\Shop\Repository as ShopRepository;
use Shopware\Models\Shop\Shop;

class DependencyProviderService implements DependencyProviderServiceInterface
{
    private const PLUGIN_NAME = 'CkoCheckoutPayment';
    private const PLUGIN_VERSION_UNKNOWN = 'unknown';

    private const SHOPWARE_VERSION_UNKNOWN = 'unknown';

    private const SCHEME_HTTP_PREFIX = 'http';
    private const SCHEME_HTTPS_PREFIX = 'https';

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var \Shopware_Components_Config
     */
    private $configManager;

    public function __construct(
        ContextServiceInterface $contextService,
        ModelManager $modelManager,
        \Shopware_Components_Config $configManager
    ) {
        $this->contextService = $contextService;
        $this->modelManager = $modelManager;
        $this->configManager = $configManager;
    }

    public function getShop(?int $shopId = null): Shop
    {
        /** @var ShopRepository $shopRepository */
        $shopRepository = $this->modelManager->getRepository(Shop::class);
        /** @var Shop $shop */
        $shop = $shopRepository->findOneBy(['id' => $shopId]);

        if ($shop !== null) {
            return $shop;
        }

        try {
            /** @var Shop $shop */
            $shop = $shopRepository->find($this->contextService->getShopContext()->getShop()->getId());

            return $shop;
        } catch (\Throwable $e) {
            // shop is not available on some modules like backend use the active shop

            return $shopRepository->getActiveDefault();
        }
    }

    public function getShopUrl(?int $shopId = null): string
    {
        $shop = $this->getShop($shopId);

        $scheme = $shop->getSecure() ? self::SCHEME_HTTPS_PREFIX : self::SCHEME_HTTP_PREFIX;
        $shopUrl = sprintf('%s://%s', $scheme, $shop->getHost());
        if ($shop->getBaseUrl()) {
            $shopUrl = sprintf('%s://%s/%s', $scheme, $shop->getHost(), $shop->getBaseUrl());
        }

        return $shopUrl;
    }

    public function getShopwareVersion(): string
    {
        return (string)$this->configManager->get('version', self::SHOPWARE_VERSION_UNKNOWN);
    }

    public function getPluginVersion(): string
    {
        $pluginRepository = $this->modelManager->getRepository(Plugin::class);

        /** @var Plugin $plugin */
        $plugin = $pluginRepository->findOneBy(['name' => self::PLUGIN_NAME]);
        if ($plugin === null) {
            return self::PLUGIN_VERSION_UNKNOWN;
        }

        return $plugin->getVersion();
    }
}
