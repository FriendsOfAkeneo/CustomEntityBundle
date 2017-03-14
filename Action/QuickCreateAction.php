<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateAction extends CreateAction
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function doExecute(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return new RedirectResponse($this->getRedirectPath(null));
        }

        return parent::doExecute($request);
    }

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
    protected function setDefaultOptions(OptionsResolver $resolver)
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
