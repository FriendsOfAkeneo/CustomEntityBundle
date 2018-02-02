<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Metadata;

use Acme\Bundle\CustomBundle\Entity\Pictogram;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ClassMetadataRegistrySpec extends ObjectBehavior
{
    function let(PropertyAccessorInterface $propertyAccessor)
    {
        $this->beConstructedWith($propertyAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassMetadataRegistry::class);
    }

    function it_returns_reflection_class_from_a_class_name()
    {
        $this->getReflectionClass(Pictogram::class)->shouldHaveType(\ReflectionClass::class);
    }

    function it_returns_readable_properties_from_an_object($propertyAccessor)
    {
        $object = new FakeEntity();

        $propertyAccessor->isReadable($object, 'publicProperty')->willReturn(true);
        $propertyAccessor->isReadable($object, 'readableProperty')->willReturn(true);
        $propertyAccessor->isReadable($object, 'unreadableProperty')->willReturn(false);

        $this->getReadableProperties($object)->shouldReturn(['publicProperty', 'readableProperty']);
    }
}

class FakeEntity
{
    public $publicProperty;

    protected $readableProperty;

    protected $unreadableProperty;

    public function getReadableProperty()
    {
        return $this->readableProperty;
    }
}
