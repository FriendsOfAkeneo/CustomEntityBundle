<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\ArrayConverter\FlatToStandard;

use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Pim\Component\Connector\ArrayConverter\FieldsRequirementChecker;

/**
 * @author    Marie Minasyan <marie.minasyan@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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

        $convertedItem = [];
        foreach ($item as $field => $data) {
            $convertedItem = $this->convertField($convertedItem, $field, $data);
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
            $convertedItem[$field] = $data;
        }

        return $convertedItem;
    }
}
