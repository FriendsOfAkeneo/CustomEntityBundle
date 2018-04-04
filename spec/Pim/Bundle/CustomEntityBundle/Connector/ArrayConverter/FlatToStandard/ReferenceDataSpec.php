<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\ArrayConverter\FlatToStandard;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\ArrayConverter\FlatToStandard\ReferenceData;
use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Pim\Component\Connector\ArrayConverter\FieldsRequirementChecker;
use Pim\Component\Connector\Exception\StructureArrayConversionException;
use Prophecy\Argument;

class ReferenceDataSpec extends ObjectBehavior
{
    function let(FieldsRequirementChecker $checker)
    {
        $this->beConstructedWith($checker, ['code'], ['code']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceData::class);
    }

    function it_is_an_array_converter()
    {
        $this->shouldImplement(ArrayConverterInterface::class);
    }

    function it_does_not_update_non_label_fields()
    {
        $item = [
            'code'           => 'my_code',
            'stringProp'     => 'string',
            'array_property' => [
                'item1',
                'item2',
            ],
            'boolProp'       => false,
            'numProp'        => 42,
        ];

        $this->convert($item, [])->shouldReturn($item);
    }

    function it_converts_label_translations()
    {
        $item = [
            'code'        => 'my_code',
            'label-en_US' => 'An English label',
            'foo'         => 'bar',
            'label-fr_FR' => 'Un label en français',
            'label-es_ES' => 'Un label en español',
        ];

        $this->convert($item, [])->shouldReturn(
            [
                'code'   => 'my_code',
                'labels' => [
                    'en_US' => 'An English label',
                    'fr_FR' => 'Un label en français',
                    'es_ES' => 'Un label en español',
                ],
                'foo'    => 'bar',
            ]
        );
    }
}
