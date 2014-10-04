<?php

namespace GescanPim\Bundle\ConnectorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

class GescanPimConnectorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('reader.yml');
        $loader->load('processor.yml');
        $loader->load('repositories.yml');
        $loader->load('writer.yml');
         $loader->load('services.yml');
    }
}

