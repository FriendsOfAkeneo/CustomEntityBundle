<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;


/**
 * Configuration for an ORM custom entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var ActionInterface[]
     */
    protected $actions = [];

    /**
     * @var array[]
     */
    protected $actionOptions = [];

    /**
     * Constructor
     * 
     * @param string $name
     * @param string $entityClass
     */
    function __construct($name, $entityClass)
    {
        $this->name = $name;
        $this->entityClass = $entityClass;
    }

    /**
     * Adds an action for the current entity
     * 
     * @param ActionInterface $action
     * @param array $options
     */
    public function addAction(ActionInterface $action, array $options = [])
    {
        $this->actions[$action->getType()] = $action;
        $this->actionOptions[$action->getType()] = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction($type)
    {
        return $this->actions[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getActionOptions($type)
    {
        return $this->actionOptions[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction($type)
    {
        return isset($this->actions[$type]);
    }
}
