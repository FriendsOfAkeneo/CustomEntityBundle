<?php

namespace Pim\Bundle\CustomEntityBundle\Extension\Formatter\Property\ProductValue;

use Pim\Bundle\DataGridBundle\Extension\Formatter\Property\ProductValue\FieldProperty;

/**
 * Formatter for simple CustomOption product values
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SimpleCustomOptionProperty extends FieldProperty
{
    /**
     * {@inheritdoc}
     */
    protected function convertValue($value)
    {
        $data = $this->getBackendData($value);

        return $data['label'] ?: sprintf('[%s]', $data['code']);
    }
}
