<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Controller\RestController;

class RestControllerSpec extends ObjectBehavior
{
    public function let(
        Registry $registry
    ) {
        $this->beConstructedWith($registry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RestController::class);
    }
}
