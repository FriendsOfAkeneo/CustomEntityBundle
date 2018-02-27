<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization\ReferenceDataProcessor;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferenceDataProcessorSpec extends ObjectBehavior
{
    function let(
        NormalizerInterface $normalizer
    ) {
        $this->beConstructedWith($normalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataProcessor::class);
    }

    function it_processes_an_item(
        $normalizer,
        \stdClass $item
    ) {
        $normalizer->normalize($item, 'csv')->shouldBeCalled();
        $this->process($item);
    }
}
