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
     * Sets the configuration
     *
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * Returns the configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration();

    /**
     * Execute the action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(Request $request);

    /**
     * @return string
     */
    public function getRoute();

    /**
     * @param mixed
     *
     * @return array
     */
    public function getRouteParameters($object = null);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getOptions();
}
