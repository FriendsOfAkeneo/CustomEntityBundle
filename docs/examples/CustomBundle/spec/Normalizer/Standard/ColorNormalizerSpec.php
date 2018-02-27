<?php

namespace spec\Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Color;
use Acme\Bundle\CustomBundle\Normalizer\Standard\ColorNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ColorNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ColorNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_only_normalizes_colors(Color $color)
    {
        $this->supportsNormalization(new \stdClass(), 'standard')->shouldReturn(false);
        $this->supportsNormalization(Argument::any(), 'standard')->shouldReturn(false);
        $this->supportsNormalization($color, 'standard')->shouldReturn(true);
    }

    function it_only_normalizes_standard_format(Color $color)
    {
        $this->supportsNormalization($color)->shouldReturn(false);
        $this->supportsNormalization($color, 'internal_api')->shouldReturn(false);
        $this->supportsNormalization($color, 'foo')->shouldReturn(false);
    }

    function it_normalizes_a_color(Color $color)
    {
        $color->getId()->willReturn(999);
        $color->getCode()->willReturn('blue');
        $color->getName()->willReturn('Plain blue');
        $color->getHex()->willReturn('#0000FF');
        $color->getRed()->willReturn(0);
        $color->getGreen()->willReturn(0);
        $color->getBlue()->willReturn(255);

        $this->normalize($color, 'standard')->shouldReturn(
            [
                'id'    => 999,
                'code'  => 'blue',
                'name'  => 'Plain blue',
                'hex'   => '#0000FF',
                'red'   => 0,
                'green' => 0,
                'blue'  => 255,
            ]
        );
    }
}
