<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string[]
     */
    protected $actions = [];

    /**
     * @var array[]
     */
    protected $actionOptions = [];

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $name
     * @param string                   $entityClass
     * @param array                    $options
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $name, $entityClass, array $options = [])
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->name = $name;
        $this->entityClass = $entityClass;
        $resolver = new OptionsResolver;
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Adds an action for the current entity
     *
     * @param string $type
     * @param string $action
     * @param array  $options
     */
    public function addAction($type, $action, array $options = [])
    {
        $this->actions[$type] = $action;
        $this->actionOptions[$type] = $options;
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

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the default options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['manager' => 'default']);
        $event = new ConfigurationEvent($this, $resolver);
        $this->eventDispatcher->dispatch(ConfigurationEvents::CONFIGURE, $event);
    }
}
