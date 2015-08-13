<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\AbstractAction;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;

class QuickExportActionSpec extends ObjectBehavior
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
        RegistryInterface $doctrine,
        DataGridQueryGenerator $queryGenerator,
        Serializer $serializer,
        Session $session,
        FlashBag $flashBag,
        EntityManager $em
    ) {
        $this->beConstructedWith(
            $actionFactory,
            $eventManager,
            $managerRegistry,
            $router,
            $translator,
            $doctrine,
            $queryGenerator,
            $serializer
        );

        // initialize configuration
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');

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

        // initialize request
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');

        // initialize flashbag
        $request->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        // initialize translator
        $translator->trans(Argument::type('string'), Argument::any())->will(
            function ($arguments) {
                if (!isset($arguments[1])) {
                    $arguments[1] = array();
                }

                $translated = sprintf('<%s>', $arguments[0]);
                foreach ($arguments[1] as $key => $value) {
                    $translated .= sprintf('%s=%s;', $key, $value);
                }

                return $translated;
            }
        );

        $doctrine->getManagerForClass('class')->willReturn($em);
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

class Entity
{
    public function getId() { }
}
