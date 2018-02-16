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
        PropertyAccessorInterface $propertyAccessor,
        NormalizerInterface $normalizer
    ) {
        $this->beConstructedWith($propertyAccessor, $normalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataProcessor::class);
    }

    function it_processes_an_item(
        $propertyAccessor,
        $normalizer,
        \stdClass $linkedObject
    ) {
        $foo = new class() {
            private $id;
            public $stringProp;
            protected $boolProp;
            protected $dateProp;
            protected $linkedObjectProp;
            private $nullProp;
            private $created;
            priVate $updated;
        };

        $item = new $foo();

        $propertyAccessor->getValue($item, 'id')->shouldNotBeCalled();
        $propertyAccessor->getValue($item, 'created')->shouldNotBeCalled();
        $propertyAccessor->getValue($item, 'updated')->shouldNotBeCalled();

        $propertyAccessor->getValue($item, 'stringProp')->willReturn('a string');
        $propertyAccessor->getValue($item, 'boolProp')->willReturn(true);
        $propertyAccessor->getValue($item, 'dateProp')->willReturn(new \DateTime());
        $propertyAccessor->getValue($item, 'nullProp')->willReturn(null);
        $propertyAccessor->getValue($item, 'linkedObjectProp')->willReturn($linkedObject);

        $normalizer->normalize(Argument::type('scalar'), 'flat')->willReturnArgument(0);
        $normalizer->normalize(Argument::type(\DateTime::class), 'flat')->willReturn('02/19/2018');
        $normalizer->normalize(null, 'flat')->willReturn(null);
        $normalizer->normalize($linkedObject, 'flat')->willReturn(
            [
                'foo'  => 'bar',
                'test' => false,
            ]
        );

        $result = $this->process($item);

        $result->shouldNotHaveKey('id');
        $result->shouldNotHaveKey('created');
        $result->shouldNotHaveKey('updatred');

        $result->shouldHaveKeyWithValue('stringProp', 'a string');
        $result->shouldHaveKeyWithValue('boolProp', true);
        $result->shouldHaveKeyWithValue('dateProp', '02/19/2018');
        $result->shouldHaveKeyWithValue('nullProp', null);
        $result->shouldHaveKeyWithValue(
            'linkedObjectProp',
            [
                'foo'  => 'bar',
                'test' => false,
            ]
        );
    }
}
