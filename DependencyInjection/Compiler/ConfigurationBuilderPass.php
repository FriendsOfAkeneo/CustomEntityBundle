<?php

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

/**
 * Builds configuration services
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigurationBuilderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configurations = [];
        $bundles = $container->getParameter('kernel.bundles');
        $configTree = $this->getConfigTree();
        foreach ($bundles as $bundle) {
            $reflection = new \ReflectionClass($bundle);
            $path = sprintf('%s/Resources/config/custom_entities.yml', dirname($reflection->getFileName()));
            if (file_exists($path)) {
                $container->addResource(new FileResource($path));
                $configurations += $this->parseConfigFile($configTree, $path);
            }
        }

        foreach ($configurations as $name => $configuration) {
            if ($configuration['abstract']) {
                continue;
            }

            $serviceConfiguration =  $this->getMergedConfiguration($configuration, $configurations);
            $this->addService($container, $name, $serviceConfiguration);
        }
    }

    /**
     * Adds a configuration service to the DIC
     *
     * @param ContainerBuilder $container
     * @param string           $name
     * @param array            $configuration
     */
    protected function addService(ContainerBuilder $container, $name, array $configuration)
    {
        $definition = new Definition(
            $configuration['class'],
            [
                new Reference('event_dispatcher'),
                $name,
                $configuration['entity_class'],
                $configuration['options']
            ]
        );
        foreach ($configuration['actions'] as $type => $options) {
            if (null === $options || !$options['enabled']) {
                continue;
            }
            $service = $options['service'];
            unset($options['service'], $options['enabled']);
            $definition->addMethodCall('addAction', [$type, $service, $options]);
        }
        $serviceName = sprintf('pim_custom_entity.configuration.%s', $name);
        $container->addDefinitions([$serviceName => $definition]);
        $container->getDefinition('pim_custom_entity.configuration.registry')
            ->addMethodCall('add', [$name, $serviceName]);
    }

    /**
     * Gets a configuration merged with its parents
     *
     * @param array $configuration
     * @param array $configurations
     *
     * @return array
     */
    protected function getMergedConfiguration(array $configuration, array $configurations)
    {
        foreach (array_keys($configuration['actions']) as $actionType) {
            $configuration['actions'][$actionType] = $configuration['actions'][$actionType] + ['enabled' => true];
        }

        if (!$configuration['extends']) {
            return $configuration;
        }

        $parentConfiguration = $this->getMergedConfiguration(
            $configurations[$configuration['extends']],
            $configurations
        );
        if (!$configuration['class']) {
            $configuration['class'] = $parentConfiguration['class'];
        }
        $configuration['options'] = $configuration['options'] + $parentConfiguration['options'];
        foreach ($parentConfiguration['actions'] as $actionName => $actionConfiguration) {
            if (isset($configuration['actions'][$actionName])) {
                $configuration['actions'][$actionName] = $configuration['actions'][$actionName] + $actionConfiguration;
            } elseif (!array_key_exists($actionName, $configuration['actions'])) {
                $configuration['actions'][$actionName] = $actionConfiguration;
            }
        }

        return $configuration;
    }

    /**
     * Parses a config file and returns an array
     *
     * @param NodeInterface $configTree
     * @param string        $file
     *
     * @return array
     */
    protected function parseConfigFile(NodeInterface $configTree, $file)
    {
        $yamlParser = new Parser();
        $config = $yamlParser->parse(file_get_contents($file));
        $processor = new Processor();

        return $processor->process($configTree, $config);
    }

    /**
     * Returns the configuration definition
     *
     * @return NodeInterface
     */
    protected function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('custom_entities');
        $root
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('name')->end()
                    ->scalarNode('class')->defaultNull()->end()
                    ->scalarNode('entity_class')->defaultNull()->end()
                    ->scalarNode('extends')->defaultValue('default')->end()
                    ->arrayNode('options')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->booleanNode('abstract')->defaultFalse()->end()
                    ->arrayNode('actions')
                        ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
