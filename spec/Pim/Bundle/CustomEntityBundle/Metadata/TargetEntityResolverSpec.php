<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Metadata;

use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Pim\Bundle\CustomEntityBundle\Metadata\TargetEntityResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class TargetEntityResolverSpec extends ObjectBehavior
{
    function let(EntityManagerInterface $em)
    {
        $this->beConstructedWith($em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TargetEntityResolver::class);
    }

    function it_returns_a_target_entity_class_name_from_its_property($em, ClassMetadata $classMetadata)
    {
        $object = new FakeEntity();

        $em->getClassMetadata(FakeEntity::class)->willReturn($classMetadata);
        $classMetadata->hasAssociation('foo')->willReturn(true);
        $classMetadata->getAssociationTargetClass('foo')->willReturn('bar');

        $this->getTargetEntityClass($object, 'foo')->shouldReturn('bar');
    }

    function it_throws_a_logic_exception_if_the_association_has_no_target($em, ClassMetadata $classMetadata)
    {
        $object = new \stdClass();

        $em->getClassMetadata(\stdClass::class)->willReturn($classMetadata);
        $classMetadata->hasAssociation('foo')->willReturn(false);

        $this->shouldThrow('\LogicException')->during('getTargetEntityClass', [$object, 'foo']);
    }
}
