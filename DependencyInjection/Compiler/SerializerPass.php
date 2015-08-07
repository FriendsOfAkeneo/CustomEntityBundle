<?php

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Duplicates the pim serializer
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 */
class SerializerPass implements CompilerPassInterface
{
    /**
     * @staticvar string
     */
    const TAG_NAME = 'pim_serializer';

    /**
     * @staticvar integer The default priority for services
     */
    const DEFAULT_PRIORITY = 100;

    /**
     * @var string
     */
    protected $tagPrefix;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds(static::TAG_NAME) as $serviceId => $tags) {
            $serializerTags = [];
            foreach ($tags as $tag) {
                $serializerTags[] = isset($tag['alias']) ? $tag['alias'] : $serviceId;
            }
            $this->processService($container, $serviceId, $serializerTags);
        }
    }

    /**
     * process a serializer service
     *
     * @param ContainerBuilder $container
     * @param string           $serviceId
     * @param array            $tags
     */
    public function processService(ContainerBuilder $container, $serviceId, array $tags)
    {
        $tagArguments = [];
        // Looks for all the services tagged "serializer.normalizer" and adds them to the Serializer service
        $tagArguments[0] = $this->findAndSortTaggedServices(
            $container,
            array_map(
                function ($tag) {
                    return sprintf("%s.normalizer", $tag);
                },
                $tags
            )
        );

        // Looks for all the services tagged "serializer.encoders" and adds them to the Serializer service
        $tagArguments[1] = $this->findAndSortTaggedServices(
            $container,
            array_map(
                function ($tag) {
                    return sprintf("%s.encoder", $tag);
                },
                $tags
            )
        );

        $definition = $container->getDefinition($serviceId);
        $arguments = $definition->getArguments();
        foreach ($tagArguments as $index => $argument) {
            if (isset($arguments[$index])) {
                $arguments[$index] = array_merge($arguments[$index], $argument);
            } else {
                $arguments[$index] = $argument;
            }
        }

        $definition->setArguments($arguments);
    }

    /**
     * Returns an array of service references for a specified tag name
     *
     * @param ContainerBuilder $container
     * @param array            $tagNames
     *
     * @return Reference[]
     */
    protected function findAndSortTaggedServices(ContainerBuilder $container, $tagNames)
    {
        $sortedServices = [];
        foreach ($tagNames as $tagName) {
            $services = $container->findTaggedServiceIds($tagName);
            foreach ($services as $serviceId => $tags) {
                foreach ($tags as $tag) {
                    $priority = isset($tag['priority']) ? $tag['priority'] : self::DEFAULT_PRIORITY;
                    $sortedServices[$priority][] = new Reference($serviceId);
                }
            }
        }

        krsort($sortedServices);

        // Flatten the array
        return call_user_func_array('array_merge', $sortedServices);
    }
}
