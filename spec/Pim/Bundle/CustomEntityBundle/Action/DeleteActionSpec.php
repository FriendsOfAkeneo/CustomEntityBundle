<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DeleteActionSpec extends ActionBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        Request $request,
        ParameterBag $attributes,
        ConfigurationInterface $configuration
    ) {
        $this->beConstructedWith($actionFactory, $eventManager, $manager, $router, $translator);
        $this->initializeRequest($request, $attributes);
        $this->initializeConfiguration($configuration);
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
}
