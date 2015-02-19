<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Registry of configurations
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Registry
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $configurations = array();

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns true if a configuration with the corresponding name exists
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->configurations[$name]);
    }

    /**
     * Get a configuration
     *
     * @param string $name
     *
     * @return ConfigurationInterface
     */
    public function get($name)
    {
        return $this->container->get($this->configurations[$name]);
    }

    /**
     * Add a configuration
     *
     * @param string $name
     * @param string $serviceId
     */
    public function add($name, $serviceId)
    {
        $this->configurations[$name] = $serviceId;
    }
}
