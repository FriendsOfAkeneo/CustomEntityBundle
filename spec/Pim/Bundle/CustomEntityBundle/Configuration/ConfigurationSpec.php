<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;

class ConfigurationSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('entity', 'entity_class');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Configuration\Configuration');
        $this->getName()->shouldReturn('entity');
        $this->getEntityClass()->shouldReturn('entity_class');
    }

    public function it_can_register_actions(ActionInterface $action1, ActionInterface $action2)
    {
        $action1->getType()->willReturn('action1');
        $action2->getType()->willReturn('action2');
        $this->addAction($action1);
        $this->addAction($action2, ['action2_options']);
        $this->hasAction('action1')->shouldReturn(true);
        $this->hasAction('action2')->shouldReturn(true);
        $this->hasAction('action3')->shouldReturn(false);
        $this->getAction('action1')->shouldReturn($action1);
        $this->getAction('action2')->shouldReturn($action2);
        $this->getActionOptions('action1')->shouldReturn([]);
        $this->getActionOptions('action2')->shouldReturn(['action2_options']);
    }
}
