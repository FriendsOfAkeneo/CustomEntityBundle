<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOption;

class CustomOption extends AbstractCustomOption
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_name';
    }
}

class CustomOptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomOption');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOption');
    }
}
