<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\ArrayConverter\FlatToStandard;

use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;

/**
 * @author    Mathias METAYER <mathias.metayer@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements ArrayConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * Converts flat csv array to standard structured array:
     *
     * Before:
     * [
     *      'code'        => 'mycode',
     *      'label-fr_FR' => 'Label en français',
     *      'label-en_US' => 'Label in English',
     *      'other_prop'  => 'other_value',
     * ]
     *
     * After:
     * [
     *      'code'       => 'mycode',
     *      'labels'     => [
     *          'fr_FR' => 'T-shirt super beau',
     *          'en_US' => 'T-shirt very beautiful',
     *      ],
     *      'other_prop' => 'other_value',
     * ]
     */
    public function convert(array $item, array $options = [])
    {
        $convertedItem = [];
        foreach ($item as $field => $data) {
            $convertedItem = $this->convertField($convertedItem, $field, $data);
        }

        return $convertedItem;
    }

    /**
     * @param array $convertedItem
     * @param string $field
     * @param mixed $data
     *
     * @return array
     */
    protected function convertField($convertedItem, $field, $data)
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
