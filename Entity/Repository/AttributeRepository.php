<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\AttributeTypes;

/**
 * Repository for attribute entity
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeRepository extends EntityRepository
{
    /**
     * @param EntityManagerInterface $em
     * @param string $attributeClass
     */
    public function __construct(EntityManagerInterface $em, $attributeClass)
    {
        $metadataClass = $em->getClassMetadata($attributeClass);

        parent::__construct($em, $metadataClass);
    }

    /**
     * Finds attributes codes linked to a reference data class
     * We cannot filter on reference data name as it is stored in an array.
     *
     * @param string $referenceDataName
     *
     * @return array
     */
    public function getAttributesByReferenceDataName($referenceDataName)
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->andWhere($qb->expr()->in('a.type', ':attribute_types'))
            ->setParameter(
                'attribute_types',
                [AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT, AttributeTypes::REFERENCE_DATA_MULTI_SELECT]
            );
        $attributes = $qb->getQuery()->execute();

        foreach ($attributes as $key => $attribute) {
            if ($referenceDataName !== $attribute->getReferenceDataName()) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }
}
