<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class IndexActionSpec extends ActionBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        EngineInterface $templating
    ) {
        $this->beConstructedWith($actionFactory, $manager, $router, $translator, $templating);
        $this->initializeConfiguration($configuration);
        $this->initializeRouter($router);
        $configuration->hasAction('index')->willReturn(true);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\IndexAction');
    }

    public function it_displays_a_grid(
        ConfigurationInterface $configuration,
        Request $request,
        EngineInterface $templating
    ) {
        $configuration->getActionOptions('index')->willReturn([]);
        $configuration->hasAction('create')->willReturn(false);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:index.html.twig',
            [
                'customEntityName' => 'entity',
                'baseTemplate'     => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity'
            ]
        )->willReturn('success');

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn('success');
    }

    public function it_displays_a_grid_with_an_add_button(
        ActionFactory $actionFactory,
        ConfigurationInterface $configuration,
        Request $request,
        EngineInterface $templating,
        ActionInterface $createAction
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
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity'
            ]
        )->willReturn('success');

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn('success');
    }

    public function it_supports_options(
        ActionFactory $actionFactory,
        ConfigurationInterface $configuration,
        Request $request,
        EngineInterface $templating,
        ActionInterface $createAction
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
                'indexUrl'         => 'pim_customentity_index?&customEntityName=entity'
            ]
        )->willReturn('success');

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn('success');
    }
}
