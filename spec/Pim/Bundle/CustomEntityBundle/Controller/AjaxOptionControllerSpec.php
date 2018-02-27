<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Controller\AjaxOptionController;
use Pim\Component\ReferenceData\ConfigurationRegistryInterface;
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
        $this->shouldBeAnInstanceOf('Pim\Bundle\UIBundle\Controller\AjaxOptionController');
    }
}
