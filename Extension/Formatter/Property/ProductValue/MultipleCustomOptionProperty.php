<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue;

use Pim\Bundle\DataGridBundle\Extension\Formatter\Property\ProductValue\FieldProperty;

/**
 * Formatter for multiple CustomOption product values
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultipleCustomOptionProperty extends FieldProperty
{
    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        $data = $this->getBackendData($value);
        $optionValues = [];
        foreach ($data as $option) {
            $optionValues[] = $option['label'] ?: sprintf('[%s]', $option['code']);
        }
        $result = implode(', ', $optionValues);

        return $result;
    }
}
