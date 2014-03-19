<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Pim\Bundle\CatalogBundle\Entity\Repository\ReferableEntityRepository;

/**
 * Repository for translatable custom entities
 * 
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TranslatableCustomEntityRepository extends ReferableEntityRepository
{
    /**
     * Creates a query builder for datagrids
     * 
     * @param string $alias
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createDatagridQueryBuilder($alias = 'o')
    {
        return $this->createQueryBuilder($alias)
            ->leftJoin("$alias.translations", 'translation')
            ->select("$alias, translation");
    }
}
