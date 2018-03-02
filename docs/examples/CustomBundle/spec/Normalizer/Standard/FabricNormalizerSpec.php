<?php

namespace spec\Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Fabric;
use Acme\Bundle\CustomBundle\Normalizer\Standard\FabricNormalizer;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FabricNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FabricNormalizer::class);
    }

    function it_is_a_normalizer()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    function it_only_normalizes_fabrics(Fabric $fabric)
    {
        $this->supportsNormalization(new \stdClass(), 'standard')->shouldReturn(false);
        $this->supportsNormalization(Argument::any(), 'standard')->shouldReturn(false);
        $this->supportsNormalization($fabric, 'standard')->shouldReturn(true);
    }

    function it_only_normalizes_standard_format(Fabric $fabric)
    {
        $this->supportsNormalization($fabric)->shouldReturn(false);
        $this->supportsNormalization($fabric, 'internal_api')->shouldReturn(false);
        $this->supportsNormalization($fabric, 'foo')->shouldReturn(false);
    }

    function it_normalizes_a_fabric(
        Fabric $fabric,
        Collection $colors,
        ReferenceDataInterface $red,
        ReferenceDataInterface $blue
    ) {
        $fabric->getId()->willReturn(999);
        $fabric->getCode()->willReturn('my_fabric');
        $fabric->getName()->willReturn('My fabric');
        $fabric->getAlternativeName()->willReturn('My fabric\'s alternative name');

        $red->getCode()->willReturn('red');
        $blue->getCode()->willReturn('blue');
        $iterator = new \ArrayIterator([$red->getWrappedObject(), $blue->getWrappedObject()]);
        $colors->getIterator()->willReturn($iterator);
        $fabric->getColors()->willReturn($colors);

        $this->normalize($fabric, 'standard')->shouldReturn(
            [
                'id'              => 999,
                'code'            => 'my_fabric',
                'name'            => 'My fabric',
                'alternativeName' => 'My fabric\'s alternative name',
                'colors'          => [
                    'red',
                    'blue',
                ],
            ]
        );
    }
}
