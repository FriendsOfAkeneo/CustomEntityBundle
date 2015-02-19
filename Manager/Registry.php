<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;

/**
 * Registry for managers
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Registry
{
    /**
     * @var ManagerInterface[]
     */
    protected $managers = [];

    /**
     * Add a manager for a class name
     *
     * @param string           $key
     * @param ManagerInterface $manager
     */
    public function add($key, ManagerInterface $manager)
    {
        $this->managers[$key] = $manager;
    }

    /**
     * Returns a manager
     *
     * @param string $key
     *
     * @return ManagerInterface
     */
    public function get($key)
    {
        return $this->managers[$key];
    }

    /**
     * Returns a manager for a configuration
     *
     * @param ConfigurationInterface $configuration
     *
     * @return ManagerInterface
     */
    public function getFromConfiguration(ConfigurationInterface $configuration)
    {
        $options = $configuration->getOptions();

        return $this->get($options['manager']);
    }
}
