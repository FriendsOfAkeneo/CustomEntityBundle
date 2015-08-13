<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DeleteActionSpec extends ObjectBehavior
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
        ConfigurationInterface $configuration
    ) {
        $this->beConstructedWith($actionFactory, $eventManager, $managerRegistry, $router, $translator);

        // initialize request
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');

        // initialize manager
        $managerRegistry->getFromConfiguration($configuration)->willReturn($manager);

        // initialize configuration
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\DeleteAction');
    }

    public function it_deletes_objects(
        ManagerInterface $manager,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request
    ) {
        $this->testObjectRemoval($manager, $eventManager, $configuration, $request, [], []);
    }

    public function it_accepts_find_options(
        ManagerInterface $manager,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request
    ) {
        $this->testObjectRemoval(
            $manager,
            $eventManager,
            $configuration,
            $request,
            ['find_options' => ['find_options']],
            ['find_options']
        );
    }

    public function it_throws_a_404_if_no_entity_found(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request
    ) {
        $configuration->getActionOptions('delete')->willReturn([]);
        $manager->find('entity_class', 'id', [])->willReturn(null);
        $this->setConfiguration($configuration);
        $this->shouldThrow('Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
            ->duringExecute($request, $configuration);
    }

    public function testObjectRemoval(
        ManagerInterface $manager,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request,
        $options,
        $findOptions
    ) {
        $object = new \stdClass;
        $configuration->getActionOptions('delete')->willReturn($options);
        $this->setConfiguration($configuration);
        $manager->find('entity_class', 'id', $findOptions)->willReturn($object);
        $manager->remove($object)->shouldBeCalled();
        $this->initializeEventManager($eventManager);

        $response = $this->execute($request, $configuration);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(204);
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
