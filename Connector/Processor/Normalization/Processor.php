<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization;

use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class Processor implements ItemProcessorInterface
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var string[] */
    protected $skippedFields = ['id', 'created', 'updated'];

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param NormalizerInterface $normalizer
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor, NormalizerInterface $normalizer)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->normalizer       = $normalizer;
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

            $value = $this->propertyAccessor->getValue($item, $property->getName());
            $normalizedData[$property->getName()] = $this->normalizer->normalize($value, 'flat');
        }

        return $normalizedData;
    }

    /**
     * @param array $skippedFields
     */
    public function setSkippedFields(array $skippedFields)
    {
        $this->skippedFields = $skippedFields;
    }
}
