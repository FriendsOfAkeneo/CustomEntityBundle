<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer\Standard;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\Standard\MinimalStandardNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MinimalStandardNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MinimalStandardNormalizer::class);
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_normalizes_objects(AbstractCustomEntity $entity)
    {
        $entity->getId()->willReturn(666);
        $entity->getCode()->willReturn('foobar');

        $expected = [
            'id'   => 666,
            'code' => 'foobar',
        ];

        $this->normalize($entity)->shouldReturn($expected);
    }

    public function it_supports_only_internal_format(AbstractCustomEntity $entity)
    {
        $this->supportsNormalization($entity, 'internal_api')->shouldReturn(false);
        $this->supportsNormalization($entity, 'external_api')->shouldReturn(false);
        $this->supportsNormalization($entity, 'standard')->shouldReturn(true);
    }
}
