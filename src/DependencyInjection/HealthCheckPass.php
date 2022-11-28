<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HealthCheckPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CheckersContainer::class)) {
            return;
        }

        $definition = $container->findDefinition(CheckersContainer::class);
        $taggedServices = $container->findTaggedServiceIds('fx.healthcheck.checker');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('add', [$attributes['service'], new Reference($id)]);
            }
        }
    }
}
