<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer\Flat;

use Acme\Bundle\CustomBundle\Entity\PictogramTranslation;
use Akeneo\Component\Localization\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Pim\Bundle\CustomEntityBundle\Normalizer\Flat\TranslationNormalizer;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TranslationNormalizerSpec extends ObjectBehavior
{
    function let(ClassMetadataRegistry $classMetadataRegistry, PropertyAccessorInterface $propertyAccessor)
    {
        $this->beConstructedWith($classMetadataRegistry, $propertyAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TranslationNormalizer::class);
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

    function it_does_not_normalize_skipped_fields(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor
    ) {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'id')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'locale')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'foreignKey')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'label')->shouldBeCalled();

        $this->setSkippedFields(['id', 'locale', 'foreignKey'])->shouldReturn(null);
        $this->normalize($translation, 'csv');
    }

    function it_normalizes_translation(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor
    ) {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'label')->willReturn('foo');

        $this->setSkippedFields(['id', 'locale', 'foreignKey'])->shouldReturn(null);

        $this->normalize($translation, 'csv')->shouldReturn(['label-en_US' => 'foo']);
    }

    function it_normalizes_many_translation_fields(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor
    ) {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'label')->willReturn('foo');
        $propertyAccessor->getValue($translation, 'locale')->willReturn('bar');

        $this->setSkippedFields(['id', 'foreignKey'])->shouldReturn(null);

        $this->normalize($translation, 'csv')->shouldReturn(['label-en_US' => 'foo', 'locale-en_US' => 'bar']);
    }
}
