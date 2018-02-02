<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\Flat;

use Akeneo\Component\Localization\Model\TranslationInterface;
use Doctrine\Common\Collections\Collection;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Pim\Bundle\CustomEntityBundle\Metadata\TargetEntityResolver;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a reference data in flat format
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class ReferenceDataNormalizer implements NormalizerInterface
{
    /** @var TargetEntityResolver */
    protected $targetEntityResolver;

    /** @var ClassMetadataRegistry */
    protected $classMetadataRegistry;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var NormalizerInterface */
    protected $transNormalizer;

    /** @var string[] */
    protected $skippedFields = [];

    /**
     * @param TargetEntityResolver $targetEntityResolver
     * @param ClassMetadataRegistry $classMetadataRegistry
     * @param PropertyAccessorInterface $propertyAccessor
     * @param NormalizerInterface $transNormalizer
     */
    public function __construct(
        TargetEntityResolver $targetEntityResolver,
        ClassMetadataRegistry $classMetadataRegistry,
        PropertyAccessorInterface $propertyAccessor,
        NormalizerInterface $transNormalizer
    ) {
        $this->targetEntityResolver = $targetEntityResolver;
        $this->classMetadataRegistry = $classMetadataRegistry;
        $this->propertyAccessor = $propertyAccessor;
        $this->transNormalizer = $transNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $csvData = [];

        $properties = $this->classMetadataRegistry->getReadableProperties($object);
        $properties = array_diff($properties, $this->skippedFields);

        foreach ($properties as $property) {
            $propertyValue = $this->propertyAccessor->getValue($object, $property);

            if (is_object($propertyValue)) {
                $normalizedData = $this->normalizeLinkedObject($object, $property, $propertyValue, $format, $context);
                if (is_array($normalizedData)) {
                    $csvData = array_merge($csvData, $normalizedData);
                }
            } else {
                if (!is_array($propertyValue)) {
                    $csvData[$property] = $propertyValue;
                }
            }
        }

        return $csvData;
    }

    /**
     * Normalizes linked object property
     *
     * @param object $object
     * @param string $property
     * @param string $format
     * @param array $context
     *
     * @return array|null
     */
    protected function normalizeLinkedObject($object, $property, $propertyValue, $format, $context)
    {
        $targetEntityClass = $this->targetEntityResolver->getTargetEntityClass($object, $property);
        $targetReflectionClass = $this->classMetadataRegistry->getReflectionClass($targetEntityClass);

        if ($propertyValue instanceof Collection) {
            // Linked reference data
            if ($targetReflectionClass->implementsInterface(ReferenceDataInterface::class)) {
                $normalizedData = [];
                foreach ($propertyValue as $refData) {
                    $normalizedData[] = $refData->getCode();
                }

                return [$property => implode(',', $normalizedData)];
            } elseif ($targetReflectionClass->implementsInterface(TranslationInterface::class)) {
                $normalizedData = [];
                foreach ($propertyValue as $translation) {
                    $translationData = $this->transNormalizer->normalize($translation, $format);
                    $normalizedData = array_merge($normalizedData, $translationData);
                }

                return $normalizedData;
            }
        } else { // Many-to-One
            if ($targetReflectionClass->implementsInterface(ReferenceDataInterface::class)) {
                return [$property => $propertyValue->getCode()];
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * We do NOT want to call it from Serializer
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return false;
    }

    /**
     * @param array $skippedFields
     */
    public function setSkippedFields(array $skippedFields)
    {
        $this->skippedFields = $skippedFields;
    }
}
