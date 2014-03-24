<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerSpec extends ObjectBehavior
{
    public function let(
        ContainerInterface $container,
        Request $request,
        Registry $registry,
        ConfigurationInterface $configuration,
        ActionInterface $action
    ) {
        $this->beConstructedWith($container, $request, $registry);
        $registry->has('entity')->willReturn(true);
        $registry->get('entity')->willReturn($configuration);
        $configuration->hasAction('action')->willReturn(true);
        $configuration->getAction('action')->willReturn('action');
        $container->get('action')->willReturn($action);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Controller\Controller');
    }

    public function it_calls_actions(
        Request $request,
        ConfigurationInterface $configuration,
        ActionInterface $action
    ) {
        $action->execute($request, $configuration)->willReturn('success');
        $this->executeAction('entity', 'action')->shouldReturn('success');
    }

    public function it_throws_404_when_there_is_no_configuration_for_entity(Registry $registry)
    {
        $registry->has('other_entity')->willReturn(false);
        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringExecuteAction('other_entity', 'action');
    }

    public function it_throws_404_when_there_is_no_configuration_for_action(ConfigurationInterface $configuration)
    {
        $configuration->hasAction('other_action')->willReturn(false);
        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringExecuteAction('entity', 'other_action');
    }
}
