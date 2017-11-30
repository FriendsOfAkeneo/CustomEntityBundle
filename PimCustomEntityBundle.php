<?php

namespace Pim\Bundle\CustomEntityBundle;

use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\RegisterFormExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Pim Custom Entity Bundle
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimCustomEntityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new DependencyInjection\Compiler\ConfigurationBuilderPass())
            ->addCompilerPass(new DependencyInjection\Compiler\SerializerPass())
            ->addCompilerPass(new DependencyInjection\Compiler\ManagerRegistryPass());

        if (class_exists('PimEnterprise\Bundle\WorkflowBundle\PimEnterpriseWorkflowBundle')) {
            $container->addCompilerPass(new RegisterFormExtensionPass());
        }
    }
}
