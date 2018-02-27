<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Manager;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry;
use PhpSpec\ObjectBehavior;

class RegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Registry::class);
    }

    function it_adds_a_manager(ManagerInterface $manager)
    {
        $this->add('foo', $manager);
        $this->get('foo')->shouldReturn($manager);
    }

    function it_gets_a_manager_from_configuration(
        ConfigurationInterface $config,
        ManagerInterface $manager
    ) {
        $this->add('foo', $manager);
        $config->getOptions()->willReturn([
            'manager' => 'foo',
        ]);

        $this->getFromConfiguration($config)->shouldReturn($manager);
    }
}
