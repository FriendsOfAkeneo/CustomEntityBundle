<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry as ConfigurationRegistry;
use Pim\Bundle\CustomEntityBundle\Controller\RestController;

class RestControllerSpec extends ObjectBehavior
{
    public function let(ConfigurationRegistry $configurations)
    {
        $this->beConstructedWith($configurations);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RestController::class);
    }
}
