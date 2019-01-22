<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Controller\AjaxOptionController;
use Akeneo\Pim\Structure\Component\ReferenceData\ConfigurationRegistryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AjaxOptionControllerSpec extends ObjectBehavior
{
    function let(RegistryInterface $doctrine, ConfigurationRegistryInterface $registry)
    {
        $this->beConstructedWith($doctrine, $registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AjaxOptionController::class);
        $this->shouldBeAnInstanceOf('Akeneo\Platform\Bundle\UIBundle\Controller\AjaxOptionController');
    }
}
