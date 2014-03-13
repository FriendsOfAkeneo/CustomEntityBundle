<?php

namespace Pim\Bundle\CustomEntityBundle\Controller\Strategy;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD strategy with quick create
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickCreateCrudStrategy extends CrudStrategy
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function getViewVars(ConfigurationInterface $configuration, Request $request)
    {
        $vars = parent::getViewVars($configuration, $request);
        $vars['quickCreate'] = true;

        return $vars;
    }
}
