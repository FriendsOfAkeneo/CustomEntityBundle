<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\ReferableNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferableNormalizerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['some_format']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferableNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_only_normalizes_custom_entities(AbstractCustomEntity $customEntity)
    {
        $this->supportsNormalization($customEntity, 'some_format')->shouldReturn(true);
        $this->supportsNormalization(new \stdClass(), 'some_format')->shouldReturn(false);
    }

    function it_throws_execption_if_no_field_name_id_provided(AbstractCustomEntity $customEntity)
    {
        $this->shouldThrow(\LogicException::class)->during(
            'normalize',
            [$customEntity->getWrappedObject(), 'some_format', []]
        );
    }

    function it_normalizes_a_custom_entity(AbstractCustomentity $customEntity)
    {
        $context = ['field_name' => 'foo'];
        $customEntity->getCode()->willReturn('bar');
        $this->normalize($customEntity, 'some_format', $context)->shouldReturn(['foo' => 'bar']);
    }
}
