<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

/**
 * Provides methods used by the pim_custom_entity datasource. This interface is optional.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface DatagridAwareRepositoryInterface
{
    /**
     * Creates a query builder for datagrids
     *
     * @param array $config
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createDatagridQueryBuilder(array $config);
}
