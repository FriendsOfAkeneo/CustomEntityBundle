<?php

namespace Pim\Bundle\CustomEntityBundle\Configuration;

use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Configuration for an ORM custom entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Configuration implements ConfigurationInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $name;

    /** @var string */
    protected $entityClass;

    /** @var string[] */
    protected $actions = [];

    /** @var array[] */
    protected $actionOptions = [];

    /** @var array[] */
    protected $options = [];

    /**
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
        $resolver = new OptionsResolver();
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
    public function addAction($type, $action, array $options = []): void
    {
        $this->actions[$type] = $action;
        $this->actionOptions[$type] = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction($type): string
    {
        return $this->actions[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getActionOptions($type): array
    {
        return $this->actionOptions[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction($type): bool
    {
        return isset($this->actions[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Sets the default options
     *
     * @param OptionsResolver $resolver
     */
    protected function setDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'manager'            => 'default',
            'edit_form_extension' => null,
        ]);
        $event = new ConfigurationEvent($this, $resolver);
        $this->eventDispatcher->dispatch(ConfigurationEvents::CONFIGURE, $event);
    }
}
