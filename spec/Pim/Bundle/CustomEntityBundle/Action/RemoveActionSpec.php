<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RemoveActionSpec extends ActionBehavior
{
    public function let(
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        Request $request,
        ParameterBag $attributes,
        ConfigurationInterface $configuration
    ) {
        parent::let($manager, $router, $translator, $request, $attributes, $configuration);
        $this->beConstructedWith($manager, $router, $translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\RemoveAction');
    }

    public function it_removes_objects(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request
    ) {
        $this->testObjectRemoval($manager, $configuration, $request, [], []);
    }

    public function it_accepts_find_options(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request
    ) {
        $this->testObjectRemoval(
            $manager,
            $configuration,
            $request,
            ['find_options' => ['find_options']],
            ['find_options']
        );
    }

    protected function testObjectRemoval(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request,
        $options,
        $findOptions
    ) {
        $object = new \stdClass;
        $configuration->getActionOptions('remove')->willReturn($options);
        $manager->find('entity_class', 'id', $findOptions)->willReturn($object);
        $manager->remove($object)->shouldBeCalled();
        $response = $this->execute($request, $configuration);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(204);
    }
}
