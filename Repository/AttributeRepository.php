<?php

namespace Pim\Bundle\CustomEntityBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Model\AttributeInterface;

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
     */
    public function __construct(EntityManagerInterface $em)
    {
        $metadataClass = $em->getClassMetadata(Attribute::class);
        parent::__construct($em, $metadataClass);
    }

    /**
     * Find attributes codes linked to custom entity
     *
     * @return array
     */
    public function findReferenceDataAttributeCodes()
    {
        $codes = $this
            ->createQueryBuilder('a')
            ->select('a.code')
            ->andWhere('a.type IN (:reference_data_simple_select, :reference_data_multi_select)')
            ->setParameters(
                [
                    ':reference_data_simple_select' => AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT,
                    ':reference_data_multi_select'  => AttributeTypes::REFERENCE_DATA_MULTI_SELECT,
                ]
            )
            ->getQuery()
            ->getArrayResult();

        return array_map(
            function ($data) {
                return $data['code'];
            },
            $codes
        );
    }

    /**
     * Find reference data attributes
     *
     * @return array
     */
    public function findReferenceDataAttribute(string $referenceDataCode)
    {
        $attributes = $this
            ->createQueryBuilder('a')
            ->andWhere('a.type IN (:reference_data_simple_select, :reference_data_multi_select)')
            ->setParameters(
                [
                    ':reference_data_simple_select' => AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT,
                    ':reference_data_multi_select'  => AttributeTypes::REFERENCE_DATA_MULTI_SELECT,
                ]
            )
            ->getQuery()
            ->execute();

        return array_filter(
            $attributes,
            function (AttributeInterface $attribute) use ($referenceDataCode) {
                return $referenceDataCode === $attribute->getProperty('reference_data_name');
            }
        );
    }
}
