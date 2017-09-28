<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ControllerSpec extends ObjectBehavior
{
    public function let(
        RequestStack $requestStack,
        ActionFactory $actionFactory,
        ActionInterface $action,
        Request $request
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $this->beConstructedWith($actionFactory, $requestStack);
        $actionFactory->getAction('entity', 'action')->willReturn($action);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Controller::class);
    }

    public function it_calls_actions(
        Request $request,
        ActionInterface $action
    ) {
        $action->execute($request)->willReturn('success');
        $this->executeAction('entity', 'action')->shouldReturn('success');
    }

    public function it_throws_404_when_there_is_no_configuration_for_entity(ActionFactory $actionFactory)
    {
        $actionFactory->getAction('other_entity', 'action')->willReturn(false);
        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringExecuteAction('other_entity', 'action');
    }

    public function it_throws_404_when_there_is_no_configuration_for_action(ActionFactory $actionFactory)
    {
        $actionFactory->getAction('entity', 'other_action')->willReturn(false);
        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringExecuteAction('entity', 'other_action');
    }
}
