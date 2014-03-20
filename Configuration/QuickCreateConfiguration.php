<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

/**
 * CRUD configuration without quick create
 *
 * The following extra options are defined :
 *   - edit_after_create:           Set to true to redirect to the edit page after entity creation
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateConfiguration extends Configuration
{
    /**
     * {@inheritdoc}
     */
    public function getCreateRedirectRoute($entity)
    {
        return $this->options['edit_after_create']
            ? $this->options['edit_route']
            : $this->options['index_route'];
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateRedirectRouteParameters($entity)
    {
        $parameters = array('customEntityName' => $this->getName());
        if ($this->options['edit_after_create']) {
            $parameters['id'] = $entity->getId();
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $optionsResolver)
    {
        parent::setDefaultOptions($optionsResolver);
        $optionsResolver->setDefaults(
            array(
                'create_template' => 'PimCustomEntityBundle:CustomEntity:quickcreate.html.twig',
                'edit_after_create'                 => true,
            )
        );
    }
}
