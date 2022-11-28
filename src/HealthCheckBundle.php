<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck;

use Eglobal\Healthcheck\DependencyInjection\HealthCheckPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HealthCheckBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new HealthCheckPass());

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.xml');
        $loader->load('console.xml');
        $loader->load('controller.xml');
    }
}
