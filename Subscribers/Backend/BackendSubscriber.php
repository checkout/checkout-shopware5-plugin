<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Subscribers\Backend;

use Enlight\Event\SubscriberInterface;

class BackendSubscriber implements SubscriberInterface
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
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Order' => 'onPostDispatchOrderLoadCheckoutExtension',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_CkoSetup' => 'onPostDispatchOrderLoadCheckoutExtension'
        ];
    }

    public function onPostDispatchOrderLoadCheckoutExtension(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Order $subject */
        $subject = $args->getSubject();
        $request = $subject->Request();
        $view = $subject->View();

        $view->addTemplateDir($this->pluginDir.'/Resources/views');

        if ($request->getActionName() === 'index') {
            $view->extendsTemplate('backend/cko_setup/app.js');
            $view->extendsTemplate('backend/cko_checkout_payment/app.js');
        }

        if ($request->getActionName() === 'load') {
            $view->extendsTemplate('backend/cko_checkout_payment/view/detail/window.js');
        }
    }
}
