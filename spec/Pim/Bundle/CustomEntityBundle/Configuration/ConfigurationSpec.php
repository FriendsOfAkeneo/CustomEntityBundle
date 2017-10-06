<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConfigurationSpec extends ObjectBehavior
{
    public function let(EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($eventDispatcher, 'entity', 'entity_class');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
        $this->getName()->shouldReturn('entity');
        $this->getEntityClass()->shouldReturn('entity_class');
    }

    public function it_can_register_actions()
    {
        $this->addAction('action1', 'action1_service');
        $this->addAction('action2', 'action2_service', ['action2_options']);
        $this->hasAction('action1')->shouldReturn(true);
        $this->hasAction('action2')->shouldReturn(true);
        $this->hasAction('action3')->shouldReturn(false);
        $this->getAction('action1')->shouldReturn('action1_service');
        $this->getAction('action2')->shouldReturn('action2_service');
        $this->getActionOptions('action1')->shouldReturn([]);
        $this->getActionOptions('action2')->shouldReturn(['action2_options']);
    }
}
