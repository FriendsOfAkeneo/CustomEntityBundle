<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue;

use Pim\Bundle\DataGridBundle\Extension\Formatter\Property\ProductValue\FieldProperty;

/**
 * Formatter for simple TranslatableCustomOption product values
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SimpleTranslatableCustomOptionProperty extends FieldProperty
{
    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        return $this->getLabel($this->getBackendData($value));
    }

    /**
     * Returns the label for an option
     *
     * @param array $option
     *
     * @return string
     */
    protected function getLabel($option)
    {
        if (isset($option['translations']) && count($option['translations']) === 1) {
            $optionValue = current($option['translations']);
            if ($optionValue['label']) {
                return $optionValue['label'];
            }
        }

        return $option['code'] ? sprintf('[%s]', $option['code']) : '';
    }
}
