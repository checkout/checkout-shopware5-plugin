<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PaymentRequestHandlerCompilerPass implements CompilerPassInterface
{
    private const DEFINITION_NAME = 'cko_checkout_payment.components.checkout_api.request.payment_request_handler_service';
    private const TAGGED_SERVICE_NAME = 'cko_checkout_payment.components.checkout_api.request.payment_request_service';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::DEFINITION_NAME)) {
            return;
        }

        $definition = $container->getDefinition(self::DEFINITION_NAME);
        $taggedServices = $container->findTaggedServiceIds(self::TAGGED_SERVICE_NAME);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addPaymentRequestService', [
                new Reference($id)
            ]);
        }
    }
}