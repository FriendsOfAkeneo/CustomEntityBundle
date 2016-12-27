<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Updater\Updater;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ManagerSpec extends ObjectBehavior
{
    public function let(
        EntityManager $em,
        Updater $updater,
        SaverInterface $saver,
        RemoverInterface $remover
    ) {
        $this->beConstructedWith($em, $updater, $saver, $remover);
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
