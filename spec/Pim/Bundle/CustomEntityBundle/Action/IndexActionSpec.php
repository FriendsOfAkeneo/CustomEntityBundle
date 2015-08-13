<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IndexActionSpec extends ObjectBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        EngineInterface $templating
    ) {
        $this->beConstructedWith($actionFactory, $eventManager, $managerRegistry, $router, $translator, $templating);

        // initialize configuration
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');
        $configuration->hasAction('index')->willReturn(true);

        // initialize manager
        $managerRegistry->getFromConfiguration($configuration)->willReturn($manager);

        // initialize router
        $router->generate(Argument::type('string'), Argument::type('array'), Argument::any())->will(
            function ($arguments) {
                $path = $arguments[0] . '?';
                foreach ($arguments[1] as $key => $value) {
                    $path .= '&' . $key . '=' . $value;
                }

                return $path;
            }
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\IndexAction');
    }

    public function it_displays_a_grid(
        ConfigurationInterface $configuration,
        ActionEventManager $eventManager,
        Request $request,
        EngineInterface $templating,
        Response $response
    ) {
        $configuration->getActionOptions('index')->willReturn([]);
        $configuration->hasAction('create')->willReturn(false);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:index.html.twig',
            [
                'customEntityName' => 'entity',
                'baseTemplate'     => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity',
                'pre_render'       => true
            ]
        )->willReturn($response);
        $this->initializeEventManager($eventManager);

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn($response);
    }

    public function it_displays_a_grid_with_an_add_button(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request,
        EngineInterface $templating,
        ActionInterface $createAction,
        Response $response
    ) {
        $configuration->getActionOptions('index')->willReturn([]);
        $configuration->hasAction('create')->willReturn(true);
        $actionFactory->getAction('entity', 'create')->willReturn($createAction);
        $createAction->getRoute()->willReturn('create_route');
        $createAction->getRouteParameters(null)->willReturn(['cr_param1' => 'value1']);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:index.html.twig',
            [
                'createUrl'        => 'create_route?&cr_param1=value1',
                'quickCreate'      => false,
                'customEntityName' => 'entity',
                'baseTemplate'     => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity',
                'pre_render'       => true
            ]
        )->willReturn($response);
        $this->initializeEventManager($eventManager);

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn($response);
    }

    public function it_supports_options(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request,
        EngineInterface $templating,
        ActionInterface $createAction,
        Response $response
    ) {
        $configuration->getActionOptions('index')->willReturn(
            [
                'quick_create' => true,
                'template' => 'template',
                'base_template' => 'base_template'
            ]
        );
        $configuration->hasAction('create')->willReturn(true);
        $actionFactory->getAction('entity', 'create')->willReturn($createAction);
        $createAction->getRoute()->willReturn('create_route');
        $createAction->getRouteParameters(null)->willReturn(['cr_param1' => 'value1']);
        $templating->renderResponse(
            'template',
            [
                'createUrl'        => 'create_route?&cr_param1=value1',
                'quickCreate'      => true,
                'customEntityName' => 'entity',
                'baseTemplate'     => 'base_template',
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity',
                'pre_render'       => true
            ]
        )->willReturn($response);
        $this->initializeEventManager($eventManager);

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn($response);
    }

    protected function initializeEventManager(ActionEventManager $eventManager)
    {
        $eventManager
            ->dipatchConfigureEvent(
                $this,
                Argument::type('Symfony\Component\OptionsResolver\OptionsResolver')
            )
            ->shouldBeCalled();

        $eventManager->dispatchPreExecuteEvent($this)->shouldBeCalled();

        $eventManager
            ->dispatchPostExecuteEvent($this, Argument::type('Symfony\Component\HttpFoundation\Response'))
            ->will(
                function ($args) {
                    return $args[1];
                }
            )
            ->shouldBeCalled();

        $eventManager
            ->dispatchPreRenderEvent($this, Argument::type('string'), Argument::type('array'))
            ->will(
                function ($args) {
                    $args[2]['pre_render'] = true;

                    return [$args[1], $args[2]];
                }
            );
    }
}
