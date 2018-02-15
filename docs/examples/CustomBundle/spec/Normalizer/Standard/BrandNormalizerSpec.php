<?php

namespace spec\Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Normalizer\Standard\BrandNormalizer;
use Acme\Bundle\CustomBundle\Normalizer\Standard\FabricNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BrandNormalizerSpec extends ObjectBehavior
{
    function let(FabricNormalizer $fabricNormalizer)
    {
        $this->beConstructedWith($fabricNormalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BrandNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldBeAnInstanceOf(NormalizerInterface::class);
    }

    function it_only_normalizes_brands(Brand $brand)
    {
        $this->supportsNormalization(new \stdClass(), 'standard')->shouldReturn(false);
        $this->supportsNormalization(Argument::any(), 'standard')->shouldReturn(false);
        $this->supportsNormalization($brand, 'standard')->shouldReturn(true);
    }

    function it_only_normalizes_standard_format(Brand $brand)
    {
        $this->supportsNormalization($brand)->shouldReturn(false);
        $this->supportsNormalization($brand, 'internal_api')->shouldReturn(false);
        $this->supportsNormalization($brand, 'foo')->shouldReturn(false);
    }

    function it_normalizes_a_brand(Brand $brand, Fabric $fabric)
    {
        $brand->getId()->willReturn(999);
        $brand->getCode()->willReturn('foo');
        $brand->getFabric()->willReturn(null);

        $this->normalize($brand, 'standard')->shouldReturn(
            [
                'id'   => 999,
                'code' => 'foo',
            ]
        );

        $fabric->getCode()->willReturn('bar');
        $brand->getFabric()->willReturn($fabric);

        $this->normalize($brand, 'standard')->shouldReturn(
            [
                'id'     => 999,
                'code'   => 'foo',
                'fabric' => 'bar',
            ]
        );
    }
}
