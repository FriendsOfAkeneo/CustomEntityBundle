<?php

namespace Pim\Bundle\CustomEntityBundle\Repository;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\AttributeRepository as BaseRepository;
use Pim\Component\Catalog\AttributeTypes;

/**
 * Repository for attribute entity
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeRepository extends BaseRepository
{
    /**
     * Find attributes codes linked to custom entity
     *
     * @param string $entityName
     *
     * @return array
     */
    public function findReferenceDataAttributeCodesByEntityName($entityName)
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
}
