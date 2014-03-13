<?php

namespace Pim\Bundle\CustomEntityBundle\Controller\Strategy;

/**
 * CRUD strategy with quick create
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateCrudStrategy extends CrudStrategy
{
    protected function getCreateActionResponse(ConfigurationInterface $configuration, Request $request, $entity)
    {
        $response = array(
            'status' => 1,
            'url' => $this->router->generate(
                $configuration->getCreateRedirectRoute($entity),
                $configuration->getCreateRedirectRouteParameters($entity)
            )
        );

        return new Response(json_encode($response));
    }

    protected function getViewVars(\Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface $configuration, \Symfony\Component\HttpFoundation\Request $request)
    {
        $vars = parent::getViewVars($configuration, $request);
        $vars['quickCreate'] = true;

        return $vars;
    }
}
