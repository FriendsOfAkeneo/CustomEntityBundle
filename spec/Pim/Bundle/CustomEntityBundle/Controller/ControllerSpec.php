<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        ActionInterface $action,
        Response $response
    ) {
        $action->execute($request)->willReturn($response);
        $this->executeAction('entity', 'action')->shouldReturn($response);
    }

    public function it_throws_404_when_there_is_no_configuration_for_entity(ActionFactory $actionFactory)
    {
        $actionFactory->getAction('other_entity', 'action')->willReturn(false);
        $this->shouldThrow(NotFoundHttpException::class)
            ->duringExecuteAction('other_entity', 'action');
    }

    public function it_throws_404_when_there_is_no_configuration_for_action(ActionFactory $actionFactory)
    {
        $actionFactory->getAction('entity', 'other_action')->willReturn(false);
        $this->shouldThrow(NotFoundHttpException::class)
            ->duringExecuteAction('entity', 'other_action');
    }
}
