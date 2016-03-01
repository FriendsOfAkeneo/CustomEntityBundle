<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

class CustomEntity extends AbstractCustomEntity
{
    public function getCustomEntityName()
    {
        return 'custom_entity_name';
    }
}

class CustomEntitySpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomEntity');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity');
    }
}
