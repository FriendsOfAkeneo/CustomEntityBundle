<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

/**
 * CRUD configuration without quick create
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateConfiguration extends Configuration
{
    protected function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $optionsResolver)
    {
        parent::setDefaultOptions($optionsResolver);
        $optionsResolver->setDefaults(
            array(
                'create_template' => 'PimCustomEntityBundle:CustomEntity:quickcreate.html.twig',
            )
        );
    }
}
