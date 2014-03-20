<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

/**
 * Description of LocaleAwareRepositoryInterface
 * 
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
interface DatagridAwareRepositoryInterface
{
    /**
     * Creates a query builder for datagrids
     *
     * @param string $alias
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createDatagridQueryBuilder($alias = 'o');
}
