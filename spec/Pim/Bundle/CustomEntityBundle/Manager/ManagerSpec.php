<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ManagerSpec extends ObjectBehavior
{
    public function let(
        RegistryInterface $doctrine,
        EntityRepository $repository,
        EntityManager $entityManager,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $doctrine->getRepository('entity_class')->willReturn($repository);
        $doctrine->getManager()->willReturn($entityManager);
        $this->beConstructedWith($doctrine, $propertyAccessor);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Manager\Manager');
    }

    public function it_creates_objects(PropertyAccessorInterface $propertyAccessor)
    {
        $propertyAccessor->setValue(Argument::type('stdClass'), 'key1', 'value1')->shouldBeCalled();
        $propertyAccessor->setValue(Argument::type('stdClass'), 'key2', 'value2')->shouldBeCalled();
        $this->create('stdClass', ['key1' => 'value1', 'key2' => 'value2'])->shouldHaveType('stdClass');
    }

    public function it_finds_objects(EntityRepository $repository)
    {
        $repository->find('id')->willReturn('success');
        $this->find('entity_class', 'id')->shouldReturn('success');
    }

    public function it_saves_objects(EntityManager $entityManager)
    {
        $object = new \stdClass;
        $entityManager->persist($object)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();
        $this->save($object);
    }

    public function it_removes_objects(EntityManager $entityManager)
    {
        $object = new \stdClass;
        $entityManager->remove($object)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();
        $this->remove($object);
    }
}
