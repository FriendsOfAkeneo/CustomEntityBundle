<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActionFactorySpec extends ObjectBehavior
{
    public function let(
        Registry $confRegistry,
        ContainerInterface $container,
        ConfigurationInterface $configuration1,
        ConfigurationInterface $configuration2,
        ActionInterface $action1,
        ActionInterface $action2,
        ActionInterface $action1_1
    ) {
        $this->beConstructedWith($container, $confRegistry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\ActionFactory');
    }

    public function it_returns_actions(
        Registry $confRegistry,
        ContainerInterface $container,
        ConfigurationInterface $configuration1,
        ConfigurationInterface $configuration2,
        ActionInterface $action1,
        ActionInterface $action2,
        ActionInterface $action1_1
    ) {
        $confRegistry->has('entity1')->willReturn(true);
        $confRegistry->get('entity1')->willReturn($configuration1);
        $confRegistry->has('entity2')->willReturn(true);
        $confRegistry->get('entity2')->willReturn($configuration2);
        $confRegistry->has('entity3')->willReturn(false);
        $configuration1->hasAction('action1')->willReturn(true);
        $configuration1->getAction('action1')->willReturn('action1');
        $configuration1->hasAction('action2')->willReturn(true);
        $configuration1->getAction('action2')->willReturn('action2');
        $configuration1->hasAction('action3')->willReturn(false);
        $configuration2->hasAction('action1')->willReturn(true);
        $configuration2->getAction('action1')->willReturn('action1_1');
        $configuration2->hasAction('action2')->willReturn(true);
        $configuration2->getAction('action2')->willReturn('action2');

        $container->get('action1')->shouldBeCalledTimes(1)->willReturn($action1);
        $container->get('action2')->shouldBeCalledTimes(2)->willReturn($action2);
        $container->get('action1_1')->shouldBeCalledTimes(1)->willReturn($action1_1);

        $action1->setConfiguration($configuration1)->shouldBeCalledTimes(1);
        $action2->setConfiguration($configuration1)->shouldBeCalledTimes(1);
        $action1_1->setConfiguration($configuration2)->shouldBeCalledTimes(1);
        $action2->setConfiguration($configuration2)->shouldBeCalledTimes(1);

        $this->getAction('entity1', 'action1')->shouldReturn($action1);
        $this->getAction('entity1', 'action2')->shouldReturn($action2);
        $this->getAction('entity1', 'action3')->shouldReturn(null);
        $this->getAction('entity2', 'action1')->shouldReturn($action1_1);
        $this->getAction('entity2', 'action2')->shouldReturn($action2);
        $this->getAction('entity3', 'action1')->shouldReturn(null);
        
        $this->getAction('entity1', 'action1')->shouldReturn($action1);
        $this->getAction('entity1', 'action2')->shouldReturn($action2);
        $this->getAction('entity1', 'action3')->shouldReturn(null);
        $this->getAction('entity2', 'action1')->shouldReturn($action1_1);
        $this->getAction('entity2', 'action2')->shouldReturn($action2);
        $this->getAction('entity3', 'action1')->shouldReturn(null);
    }
}
