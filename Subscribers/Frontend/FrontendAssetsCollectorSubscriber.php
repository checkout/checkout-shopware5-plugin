<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Frontend;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

class FrontendAssetsCollectorSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;

    public function __construct(string $pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDirs',
            'Theme_Compiler_Collect_Plugin_Less' => 'addLessFiles',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'addJsFiles',
        ];
    }

    public function onCollectTemplateDirs(\Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->pluginDir . '/Resources/views';

        $args->setReturn($dirs);
    }

    public function addLessFiles(): ArrayCollection
    {
        $less = new LessDefinition(
            [],
            [
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/cko-checkout-payment-base.less',
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/cko-checkout-payment-creditcard.less',
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/cko-checkout-payment-google-pay.less',
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/cko-checkout-payment-apple-pay.less',
                $this->pluginDir . '/Resources/views/frontend/_public/src/less/cko-checkout-payment-sepa.less',
            ],
            __DIR__ . '/../'
        );

        return new ArrayCollection([$less]);
    }

    public function addJsFiles(): ArrayCollection
    {
        $jsFiles = [
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-base.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-creditcard.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-giropay.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-google-pay.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-apple-pay.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-ideal.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-klarna.js',
            $this->pluginDir . '/Resources/views/frontend/_public/src/js/jquery.cko-checkout-payment-sepa.js'
        ];

        return new ArrayCollection($jsFiles);
    }
}
