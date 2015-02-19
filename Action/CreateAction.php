<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    protected function getObject(Request $request)
    {
        return $this->getManager()->create(
            $this->configuration->getEntityClass(),
            $this->options['create_values'],
            $this->options['create_options']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            [
                'route'           => 'pim_customentity_create',
                'create_values'   => [],
                'create_options'  => [],
                'success_message' => sprintf('flash.%s.created', $this->configuration->getName())
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'create';
    }
}
