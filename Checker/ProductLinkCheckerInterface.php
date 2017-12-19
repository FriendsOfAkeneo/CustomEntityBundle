<?php

namespace Pim\Bundle\CustomEntityBundle\Checker;

use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * Base implementation for ORM managers
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ProductLinkCheckerInterface
{
    /**
     * Check if the entity is linked to one or more products
     *
     * @param ReferenceDataInterface $entity
     *
     * @return bool
     */
    public function isLinkedToProduct(ReferenceDataInterface $entity);
}
