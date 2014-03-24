<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Quick create action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateAction extends CreateAction
{
    /**
     * {@inheritdoc}
     */
    protected function getRedirectResponse(array $options)
    {
        $response = array(
            'status' => 1,
            'url' => $this->getRedirectPath($options)
        );

        return new Response(json_encode($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(ConfigurationInterface $configuration, OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);

        $resolver->setDefaults(['template' => 'PimCustomEntityBundle:CustomEntity:quickcreate.html.twig']);
    }
}
