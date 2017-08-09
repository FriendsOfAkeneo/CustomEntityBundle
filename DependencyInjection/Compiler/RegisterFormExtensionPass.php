<?php

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Compiler pass to load form extension configuration only for EE
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class RegisterFormExtensionPass implements CompilerPassInterface
{
    /** @staticvar string */
    const PROVIDER_ID = 'pim_enrich.provider.form_extension';

    /**
     * {@inheritdoc}
     *
     * @see \Pim\Bundle\EnrichBundle\DependencyInjection\Compiler\RegisterFormExtensionsPass
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(static::PROVIDER_ID)) {
            return;
        }
        $providerDefinition = $container->getDefinition(static::PROVIDER_ID);

        $extensionConfig = [];
        $attributeFields = [];
        $filepaths = [
            __DIR__ .'/../../Resources/config/form_extensions_ee/reference_data_csv_permissions.yml',
        ];

        foreach ($filepaths as $filepath) {
            $config = Yaml::parse(file_get_contents($filepath));
            if (isset($config['extensions']) && is_array($config['extensions'])) {
                $extensionConfig = array_replace_recursive($extensionConfig, $config['extensions']);
            }
            if (isset($config['attribute_fields']) && is_array($config['attribute_fields'])) {
                $attributeFields = array_merge($attributeFields, $config['attribute_fields']);
            }

            $container->addResource(new FileResource($filepath));

            foreach ($extensionConfig as $code => $extension) {
                $providerDefinition->addMethodCall('addExtension', [$code, $extension]);
            }

            foreach ($attributeFields as $attributeType => $module) {
                $providerDefinition->addMethodCall('addAttributeField', [$attributeType, $module]);
            }
        }
    }
}
