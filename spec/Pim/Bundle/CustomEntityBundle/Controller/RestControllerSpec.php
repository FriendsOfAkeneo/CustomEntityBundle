<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry as ConfigurationRegistry;
use Pim\Bundle\CustomEntityBundle\Controller\RestController;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestControllerSpec extends ObjectBehavior
{
    public function let(
        ConfigurationRegistry $configurations,
        ManagerRegistry $managers,
        ValidatorInterface $validator
    ) {
        $this->beConstructedWith($configurations, $managers, $validator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RestController::class);
    }
}
