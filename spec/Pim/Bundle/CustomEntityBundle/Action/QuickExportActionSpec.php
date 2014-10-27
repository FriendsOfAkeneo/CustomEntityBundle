<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\AbstractAction;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;

class QuickExportActionSpec extends ActionBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        Request $request,
        ParameterBag $attributes,
        ConfigurationInterface $configuration,
        DataGridQueryGenerator $queryGenerator,
        Serializer $serializer,
        Session $session,
        FlashBag $flashBag
    ) {
        $this->beConstructedWith(
            $actionFactory,
            $eventManager,
            $managerRegistry,
            $router,
            $translator,
            $queryGenerator,
            $serializer
        );
        $this->initializeRequest($request, $attributes);
        $this->initializeFlashBag($request, $session, $flashBag);
        $this->initializeManager($configuration, $managerRegistry, $manager);
        $this->initializeConfiguration($configuration);
        $this->initializeRouter($router);
        $this->initializeTranslator($translator);
        $configuration->getActionOptions('quick_export')->willReturn([]);
        $queryGenerator->getCount($request, 'entity')->willReturn(3);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\QuickExportAction');
    }

    public function it_returns_a_streamed_response(
        Request $request,
        ConfigurationInterface $configuration,
        ActionEventManager $eventManager
    ) {
        $this->initializeEventManager($eventManager);
        $configuration->getActionOptions('quick_export')->willReturn([]);

        $this->setConfiguration($configuration);
        $response = $this->execute($request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\StreamedResponse');
    }

    public function it_accepts_a_limit(
        ActionFactory $actionFactory,
        AbstractAction $indexAction,
        Request $request,
        ConfigurationInterface $configuration,
        ActionEventManager $eventManager,
        FlashBag $flashBag
    ) {
        $actionFactory->getAction('entity', 'index')->willReturn($indexAction);
        $indexAction->getRoute()->willReturn('index_route');
        $indexAction->getRouteParameters(null)->willReturn([]);
        $this->initializeEventManager($eventManager);
        $configuration->getActionOptions('quick_export')->willReturn(['limit' => 2]);

        $this->setConfiguration($configuration);
        $response = $this->execute($request);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
        $response->getTargetUrl()->shouldReturn('index_route?');
        $flashBag->add('error', '<pim_custom_entity.export.limit_exceeded>%limit%=2;')->shouldBeCalled();
    }
}
