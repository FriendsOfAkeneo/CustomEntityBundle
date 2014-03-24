<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

/**
 * Common interface for configuration services
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ConfigurationInterface
{
    /**
     * Returns the name of the configuration
     *
     * @return string
     */
    public function getName();

    /**
     * Returns true if an action of the concerned type is registered
     *
     * @param string
     *
     * @return boolean
     */
    public function hasAction($type);

    /**
     * @param string $type
     *
     * @return string
     */
    public function getAction($type);

    /**
     * Returns the options for a specific action type
     *
     * @param string $type
     *
     * @return array
     */
    public function getActionOptions($type);

    /**
     * Returns the class of the managed entity
     *
     * @return string
     */
    public function getEntityClass();
}
