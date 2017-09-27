<?php

namespace Acme\Bundle\CustomBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class AcmeCustomExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form_types.yml');
        $loader->load('normalizers.yml');
        $loader->load('entities.yml');
        $loader->load('repositories.yml');
    }
}
