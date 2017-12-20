<?php

namespace Pim\Bundle\CustomEntityBundle\Checker;

use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * Checks if an entity is linked to one or more products
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ProductLinkCheckerInterface
{
    /**
     * Find number of products linked to an entity
     *
     * @param ReferenceDataInterface $entity
     *
     * @return bool
     */
    public function isLinkedToProduct(ReferenceDataInterface $entity);
}
