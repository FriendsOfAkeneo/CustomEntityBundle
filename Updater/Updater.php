<?php

namespace Pim\Bundle\CustomEntityBundle\Updater;

use Akeneo\Component\FileStorage\File\FileStorerInterface;
use Akeneo\Component\StorageUtils\Exception\InvalidPropertyException;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Pim\Component\Catalog\FileStorage;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Reference data updater
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Updater implements ObjectUpdaterInterface
{
    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ClassMetadataInfo[] */
    protected $classMetadata = [];

    /** @var FileStorerInterface */
    protected $storer;

    /** @var string */
    protected $tmpStorageDir;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param LocaleRepositoryInterface $localeRepository
     * @param EntityManagerInterface    $em
     * @param FileStorerInterface       $storer
     * @param string                    $tmpStorageDir
     */
    public function __construct(
        PropertyAccessorInterface $propertyAccessor,
        LocaleRepositoryInterface $localeRepository,
        EntityManagerInterface $em,
        FileStorerInterface $storer,
        $tmpStorageDir
    ) {
        $this->propertyAccessor = $propertyAccessor;
        $this->localeRepository = $localeRepository;
        $this->em = $em;
        $this->storer = $storer;
        $this->tmpStorageDir = $tmpStorageDir;
    }

    /**
     * {@inheritdoc}
     */
    public function update($referenceData, array $data, array $options = []): ReferenceDataInterface
    {
        if (!$referenceData instanceof ReferenceDataInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a "%s", "%s" provided',
                    'Pim\Component\ReferenceData\Model\ReferenceDataInterface',
                    ClassUtils::getClass($referenceData)
                )
            );
        }

        foreach ($data as $propertyPath => $value) {
            $this->updateProperty($referenceData, $propertyPath, $value);
        }

        return $referenceData;
    }

    /**
     * @param ReferenceDataInterface $referenceData
     * @param string                 $propertyPath
     * @param mixed                  $value
     */
    protected function updateProperty(ReferenceDataInterface $referenceData, $propertyPath, $value): void
    {
        if ('id' === $propertyPath) {
            return;
        }

        if ('code' === $propertyPath) {
            $referenceData->setCode($value);

            return;
        }

        if (($this->isAssociation($referenceData, 'translations') || $this->isAssociation($referenceData, 'labels'))
            && in_array($propertyPath, ['labels', 'translations'])) {
            $this->updateTranslations($referenceData, $propertyPath, $value);

            return;
        }
        if ($this->isAssociation($referenceData, $propertyPath)) {
            $this->updateAssociatedEntity($referenceData, $propertyPath, $value);
        } else {
            $this->propertyAccessor->setValue($referenceData, $propertyPath, $value);
        }
    }

    /**
     * Updates an entity linked to the reference data
     *
     * @param ReferenceDataInterface $referenceData
     * @param string                 $propertyPath
     * @param mixed                  $value
     *
     * @throws EntityNotFoundException
     */
    protected function updateAssociatedEntity(ReferenceDataInterface $referenceData, $propertyPath, $value): void
    {
        if (null === $value) {
            $this->propertyAccessor->setValue($referenceData, $propertyPath, null);

            return;
        }

        $associationMapping = $this->getAssociationMapping($referenceData, $propertyPath);
        $associationRepo = $this->em->getRepository($associationMapping['targetEntity']);
        $classMetadata = $this->getClassMetadata($referenceData);

        if (is_subclass_of(
            $associationMapping['targetEntity'],
            'Akeneo\Component\FileStorage\Model\FileInfoInterface'
        )) {
            if (empty($value['filePath'])) {
                $this->propertyAccessor->setValue($referenceData, $propertyPath, null);

                return;
            }

            $associatedEntity = $associationRepo->findOneBy([
                'key' => str_replace($this->tmpStorageDir, '', $value['filePath']),
            ]);

            if (null === $associatedEntity) {
                $rawFile = new \SplFileInfo($value['filePath']);

                if (!$rawFile->isFile()) {
                    throw InvalidPropertyException::validPathExpected(
                        'media',
                        static::class,
                        $value['filePath']
                    );
                }

                $associatedEntity = $this->storer->store($rawFile, FileStorage::CATALOG_STORAGE_ALIAS);
            }
        } elseif ($classMetadata->isCollectionValuedAssociation($propertyPath)) {
            $associatedEntity = [];
            if (is_string($value)) {
                $value = array_filter(explode(',', $value));
            }
            foreach ($value as $entityCode) {
                $associatedEntity[] = $associationRepo->findOneBy(['code' => $entityCode]);
            }
        } elseif ('' === $value) {
            $this->propertyAccessor->setValue($referenceData, $propertyPath, null);

            return;
        } else {
            $associatedEntity = $associationRepo->findOneBy(['code' => $value]);
        }

        if (null === $associatedEntity) {
            throw new EntityNotFoundException(
                sprintf('Associated entity "%s" with code "%" not found', $associationMapping['targetEntity'], $value)
            );
        }

        $this->propertyAccessor->setValue($referenceData, $propertyPath, $associatedEntity);
    }

    /**
     * Updates a reference data translation from the translatable reference data
     *
     * @param AbstractTranslatableCustomEntity $referenceData
     * @param string                           $propertyPath
     * @param mixed                            $values
     *
     * @throws \InvalidArgumentException
     */
    protected function updateTranslations(AbstractTranslatableCustomEntity $referenceData, $propertyPath, $values): void
    {
        foreach ($values as $locale => $value) {
            if (!in_array($locale, $this->localeRepository->getActivatedLocaleCodes())) {
                throw new \InvalidArgumentException(
                    sprintf('Locale "%s" is not activated', $locale)
                );
            }
            $translation = $referenceData->getTranslation($locale);
            $translation->setLabel($value);
        }
    }

    /**
     * @param ReferenceDataInterface $referenceData
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata|ClassMetadataInfo
     */
    protected function getClassMetadata(ReferenceDataInterface $referenceData): ClassMetadataInfo
    {
        $entityName = ClassUtils::getClass($referenceData);
        if (!isset($this->classMetadata[$entityName])) {
            $this->classMetadata[$entityName] = $this->em->getClassMetadata($entityName);
        }

        return $this->classMetadata[$entityName];
    }

    /**
     * @param ReferenceDataInterface $referenceData
     *
     * @return array
     */
    protected function getAssociationMappings(ReferenceDataInterface $referenceData): array
    {
        return $this->getClassMetadata($referenceData)->getAssociationMappings();
    }

    /**
     * @param ReferenceDataInterface $referenceData
     * @param string                 $property
     *
     * @return bool
     */
    protected function isAssociation(ReferenceDataInterface $referenceData, $property): bool
    {
        $associationMappings = $this->getAssociationMappings($referenceData);

        return isset($associationMappings[$property]);
    }

    /**
     * @param ReferenceDataInterface $referenceData
     * @param string                 $property
     *
     * @return array
     */
    protected function getAssociationMapping(ReferenceDataInterface $referenceData, $property): array
    {
        $associationMappings = $this->getAssociationMappings($referenceData);

        return $associationMappings[$property];
    }
}
