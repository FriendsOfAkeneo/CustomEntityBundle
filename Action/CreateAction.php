<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Create action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CreateAction extends AbstractFormAction
{
    /**
     * {@inheritdoc}
     */
    protected function getObject(Request $request, ConfigurationInterface $configuration, array $options)
    {
        return $this->manager->create(
            $configuration->getEntityClass(),
            $options['create_values'],
            $options['create_options']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(ConfigurationInterface $configuration, \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);

        $resolver->setDefaults(
            [
                'create_values' => [],
                'create_options' => [],
                'success_message' => sprintf('flash.%s.created', $configuration->getName())
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return 'pim_customentity_create';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'create';
    }
}
