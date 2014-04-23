<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue;

/**
 * Formatter for simple TranslatableCustomOption product values
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MultipleTranslatableCustomOptionProperty extends SimpleTranslatableCustomOptionProperty
{
    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        $data = $this->getBackendData($value);
        $optionValues = [];
        foreach ($data as $option) {
            $optionValues[] = $this->getLabel($option);
        }
        $result = implode(', ', $optionValues);

        return $result;
    }
}
