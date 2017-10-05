<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\CustomEntityNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomEntityNormalizerSpec extends ObjectBehavior
{
    public function let(NormalizerInterface $pimSerializer)
    {
        $this->beConstructedWith($pimSerializer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CustomEntityNormalizer::class);
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_only_internal_format(AbstractCustomEntity $entity)
    {
        $this->supportsNormalization($entity, 'internal_api')->shouldReturn(true);
        $this->supportsNormalization($entity, 'external_api')->shouldReturn(false);
        $this->supportsNormalization($entity, 'standard')->shouldReturn(false);
    }

    public function it_normalizes_objects(NormalizerInterface $pimSerializer, AbstractCustomEntity $entity)
    {
        $entity->getId()->willReturn(666);

        $context = [
            'customEntityName' => 'fooEntity',
            'form'             => 'foo_form_extension',
        ];

        $normalizedEntity = [
            'foo'    => 'bar',
            'labels' => [
                'en_US' => 'foo',
                'fr_FR' => 'bar',
            ],
        ];

        $pimSerializer->normalize($entity, 'standard', $context)
            ->willReturn($normalizedEntity);

        $expected = [
            'data' => $normalizedEntity,
            'meta' => [
                'id'               => 666,
                'customEntityName' => 'fooEntity',
                'form'             => 'foo_form_extension',
            ],
        ];

        $this->normalize($entity, 'standard', $context)->shouldReturn($expected);
    }
}
