<?php

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Builds configuration services
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ManagerRegistryPass implements CompilerPassInterface
{
    /**
     * @staticvar string
     */
    const REGISTRY_SERVICE = 'pim_custom_entity.manager.registry';

    /**
     * @staticvar string
     */
    const TAG_NAME = 'pim_custom_entity.manager';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(static::REGISTRY_SERVICE);
        foreach ($container->findTaggedServiceIds(static::TAG_NAME) as $serviceId => $tags) {
            $ref = new Reference($serviceId);
            foreach ($tags as $tag) {
                $definition->addMethodCall(
                    'add',
                    [$tag['alias'], $ref]
                );
            }
        }
    }
}
