<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Common interface for actions
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ActionInterface
{
    /**
     * Execute the action
     * 
     * @param Request $request 
     * @param ConfigurationInterface $configuration
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(Request $request, ConfigurationInterface $configuration);

    /**
     * @return string
     */
    public function getRoute();

    /**
     * @return array
     */
    public function getRouteParameters(ConfigurationInterface $configuration, $object = null);

    /**
     * @return string
     */
    public function getType();
}
