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

    public function it_creates_objects($updater)
    {
        $updater->update(Argument::type('stdClass'), ['key1' => 'value1', 'key2' => 'value2'])->shouldBeCalled();
        $this->create('stdClass', ['key1' => 'value1', 'key2' => 'value2'])->shouldHaveType('stdClass');
    }

    public function it_finds_objects($em, EntityRepository $repository)
    {
        $em->getRepository('entity_class')->willReturn($repository);
        $repository->find('id')->willReturn('success');
        $this->find('entity_class', 'id')->shouldReturn('success');
    }

    public function it_saves_objects($saver)
    {
        $object = new \stdClass();
        $saver->save($object, [])->shouldBeCalled();
        $this->save($object, []);
    }

    public function it_removes_objects($remover)
    {
        $object = new \stdClass();
        $remover->remove($object)->shouldBeCalled();
        $this->remove($object);
    }
}
