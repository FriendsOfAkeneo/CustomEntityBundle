<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization;

use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Generic normalizer processor for reference datas
 * Only works for basic reference data
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceDataProcessor implements ItemProcessorInterface
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var string[] */
    protected $skippedFields = ['id', 'created', 'updated', 'locale'];

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param NormalizerInterface $normalizer
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, NormalizerInterface $normalizer) {
        $this->propertyAccessor = $propertyAccessor;
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        $normalizedData = [];

        $refItem = new \ReflectionClass($item);
        foreach ($refItem->getProperties() as $property) {
            if (in_array($property->getName(), $this->skippedFields)) {
                continue;
            }

            $value = $this->normalizer
                ->normalize($this->propertyAccessor->getValue($item, $property->getName()), 'flat');

            if (is_array($value)) {
                $normalizedData = array_merge($normalizedData, $this->normalizeArray($value));
            } else {
                $normalizedData[$property->getName()] = $value;
            }
        }

        return $normalizedData;
    }

    /**
     * @param array $values
     * @return array
     */
    protected function normalizeArray(array $values): array
    {
        $returnValue = [];

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $returnValue = array_merge($returnValue, $this->normalizeArray($value));
            } else {
                $returnValue[$key] = $value;
            }
        }

        return $returnValue;
    }

    /**
     * @param array $skippedFields
     */
    public function setSkippedFields(array $skippedFields)
    {
        $this->skippedFields = $skippedFields;
    }
}
