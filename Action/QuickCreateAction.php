<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

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
    protected function getRedirectResponse($object)
    {
        $response = array(
            'status' => 1,
            'url' => $this->getRedirectPath($object)
        );

        return new Response(json_encode($response));
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(['template' => 'PimCustomEntityBundle:CustomEntity:quickcreate.html.twig']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectPath($object)
    {
        $routeParameters = array_merge(
            $this->options['redirect_route_parameters'],
            $object && $object->getId() !== null ? array('id' => $object->getId()) : array()
        );

        return $this->router->generate(
            $this->options['redirect_route'],
            $routeParameters
        );
    }
}
