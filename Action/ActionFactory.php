<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Registry
     */
    protected $configurationRegistry;

    /**
     * @var ActionInterface[][]
     */
    protected $actions = [];

    /**
     * @param ContainerInterface $container
     * @param Registry           $configurationRegistry
     */
    public function __construct(ContainerInterface $container, Registry $configurationRegistry)
    {
        $this->container = $container;
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * @param string $customEntityName
     * @param string $actionType
     *
     * @return ActionInterface
     */
    public function getAction($customEntityName, $actionType)
    {
        if (isset($this->actions[$customEntityName][$actionType])) {
            return $this->actions[$customEntityName][$actionType];
        }

        if (!$this->configurationRegistry->has($customEntityName)) {
            return null;
        }

        $configuration = $this->configurationRegistry->get($customEntityName);

        if (!$configuration->hasAction($actionType)) {
            return null;
        }

        if (!isset($this->actions[$customEntityName])) {
            $this->actions[$customEntityName] = [];
        }

        $action = $this->container->get($configuration->getAction($actionType));
        $this->actions[$customEntityName][$actionType] = $action;
        $action->setConfiguration($configuration);

        return $action;
    }
}
