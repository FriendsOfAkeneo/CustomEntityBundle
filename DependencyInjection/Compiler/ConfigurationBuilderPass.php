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
     * @param type             $name
     * @param array            $configuration
     */
    protected function addService(ContainerBuilder $container, $name, array $configuration)
    {
        $definition = new Definition(
            $configuration['class'],
            [ $name, $configuration['entity_class']]
        );
        $definition->setPublic(false);
        foreach ($configuration['actions'] as $type => $options) {
            if (null === $options) {
                continue;
            }
            $service = new Reference($options['service']);
            unset($options['service']);
            $definition->addMethodCall('addAction', [$service, $options]);
        }
        $serviceName = sprintf('pim_custom_entity_bundle.configuration.%s', $name);
        $container->addDefinitions([$serviceName => $definition]);
        $container->getDefinition('pim_custom_entity.configuration.registry')
            ->addMethodCall('add', [new Reference($serviceName)]);
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
        if (!$configuration['parent']) {
            return $configuration;
        }

        $parentConfiguration = $this->getMergedConfiguration(
            $configurations[$configuration['parent']],
            $configurations
        );
        if (!$configuration['class']) {
            $configuration['class'] = $parentConfiguration['class'];
        }
        foreach ($parentConfiguration['actions'] as $actionName => $actionConfiguration) {
            if (isset($configuration['actions'][$actionName])) {
                $configuration['actions'][$actionName] = $configuration['actions'][$actionName] + $actionConfiguration;
            } elseif(!array_key_exists($actionName, $configuration['actions'])) {
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
                    ->scalarNode('parent')->defaultValue('default')->end()
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
