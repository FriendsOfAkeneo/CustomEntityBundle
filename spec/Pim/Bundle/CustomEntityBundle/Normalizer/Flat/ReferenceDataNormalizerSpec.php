<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer\Flat;

use Acme\Bundle\CustomBundle\Entity\Color;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Pim\Bundle\CustomEntityBundle\Metadata\TargetEntityResolver;
use Pim\Bundle\CustomEntityBundle\Normalizer\Flat\ReferenceDataNormalizer;
use Akeneo\Pim\Enrichment\Component\Product\Model\ReferenceDataInterface;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferenceDataNormalizerSpec extends ObjectBehavior
{
    function let(
        TargetEntityResolver $targetEntityResolver,
        ClassMetadataRegistry $classMetadataRegistry,
        PropertyAccessorInterface $propertyAccessor,
        NormalizerInterface $transNormalizer
    ) {
        $this->beConstructedWith($targetEntityResolver, $classMetadataRegistry, $propertyAccessor, $transNormalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_does_not_support_normalization_from_serializer()
    {
        $this->supportsNormalization(Argument::any(), Argument::any())->shouldReturn(false);
    }

    function it_sets_skipped_fields()
    {
        $this->setSkippedFields(['id'])->shouldReturn(null);
    }

    function it_normalizes_reference_data(\stdClass $object, $classMetadataRegistry, $propertyAccessor)
    {
        $classMetadataRegistry
            ->getReadableProperties($object)
            ->willReturn(['id', 'code']);
        $propertyAccessor->getValue($object, 'id')->willReturn(1);
        $propertyAccessor->getValue($object, 'code')->willReturn('foo');

        $this->normalize($object, Argument::any())->shouldReturn(['id' => 1, 'code' => 'foo']);
    }

    function it_does_not_normalize_skipped_fields(\stdClass $object, $classMetadataRegistry, $propertyAccessor)
    {
        $classMetadataRegistry
            ->getReadableProperties($object)
            ->willReturn(['id', 'code']);
        $propertyAccessor->getValue($object, 'id')->willReturn(1);
        $propertyAccessor->getValue($object, 'code')->willReturn('foo');

        $this->normalize($object, Argument::any())->shouldReturn(['id' => 1, 'code' => 'foo']);
    }

    function it_normalizes_linked_reference_data(
        \stdClass $object,
        ReferenceDataInterface $referenceData,
        \ReflectionClass $reflectionClass,
        $classMetadataRegistry,
        $targetEntityResolver,
        $propertyAccessor
    ) {
        $referenceData->getCode()->willReturn('bar');

        $classMetadataRegistry
            ->getReadableProperties($object)
            ->willReturn(['code', 'ref_data']);
        $classMetadataRegistry->getReflectionClass(ReferenceDataInterface::class)->willReturn($reflectionClass);

        $targetEntityResolver->getTargetEntityClass($object, 'ref_data')->willReturn(ReferenceDataInterface::class);

        $reflectionClass->implementsInterface(ReferenceDataInterface::class)->willReturn(true);

        $propertyAccessor->getValue($object, 'code')->willReturn('foo');
        $propertyAccessor->getValue($object, 'ref_data')->willReturn($referenceData);

        $this->normalize($object, Argument::any())->shouldReturn(['code' => 'foo', 'ref_data' => 'bar']);
    }
}
