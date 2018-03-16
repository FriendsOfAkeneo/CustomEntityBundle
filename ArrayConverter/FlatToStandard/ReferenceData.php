<?php

namespace Pim\Bundle\CustomEntityBundle\ArrayConverter\FlatToStandard;

use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Pim\Component\Connector\ArrayConverter\FieldsRequirementChecker;

class ReferenceData implements ArrayConverterInterface
{
    /** @var FieldsRequirementChecker */
    protected $checker;

    /** @var array */
    protected $fieldsPresence;

    /** @var array */
    protected $fieldsFilling;

    /**
     * @param FieldsRequirementChecker $checker
     * @param array                    $fieldsPresence
     * @param array                    $fieldsFilling
     */
    public function __construct(
        FieldsRequirementChecker $checker,
        array $fieldsPresence = [],
        array $fieldsFilling = []
    ) {
        $this->checker = $checker;
        $this->fieldsPresence = $fieldsPresence;
        $this->fieldsFilling = $fieldsFilling;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $item, array $options = [])
    {
        if (!empty($this->fieldsPresence)) {
            $this->checker->checkFieldsPresence($item, $this->fieldsPresence);
        }

        if (!empty($this->fieldsFilling)) {
            $this->checker->checkFieldsFilling($item, $this->fieldsFilling);
        }

        $convertedItem = ['labels' => [], 'attribute_requirements' => []];

        foreach ($item as $field => $data) {
            $convertedItem = $this->convertField($convertedItem, $field, $data);
        }

        if (empty($convertedItem['labels'])) {
            unset($convertedItem['labels']);
        }

        return $convertedItem;
    }

    /**
     * @param array  $convertedItem
     * @param string $field
     * @param mixed  $data
     *
     * @return array
     */
    protected function convertField(array $convertedItem, $field, $data)
    {
        if (false !== strpos($field, 'label-', 0)) {
            $labelTokens = explode('-', $field);
            $labelLocale = $labelTokens[1];
            $convertedItem['labels'][$labelLocale] = $data;
        } else {
            $convertedItem[$field] = (string) $data;
        }

        return $convertedItem;
    }
}
