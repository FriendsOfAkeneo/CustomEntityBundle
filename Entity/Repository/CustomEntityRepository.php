<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Doctrine\ORM\QueryBuilder;
use Pim\Bundle\ReferenceDataBundle\Doctrine\ORM\Repository\ReferenceDataRepository;

/**
 * Repository for the custom entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityRepository extends ReferenceDataRepository
{
    /**
     * Apply mass action parameters on query builder
     *
     * @param QueryBuilder $qb
     * @param bool         $inset
     * @param array        $values
     */
    public function applyMassActionParameters($qb, $inset, $values)
    {
        if ($values) {
            $rootAlias = $qb->getRootAlias();
            $valueWhereCondition =
                $inset
                    ? $qb->expr()->in($rootAlias, $values)
                    : $qb->expr()->notIn($rootAlias, $values);
            $qb->andWhere($valueWhereCondition);
        }

        if (null !== $qb->getDQLPart('where')) {
            $whereParts = $qb->getDQLPart('where')->getParts();
            $qb->resetDQLPart('where');

            foreach ($whereParts as $part) {
                if (!is_string($part) || !strpos($part, 'entityIds')) {
                    $qb->andWhere($part);
                }
            }
        }

        $qb->setParameters(
            $qb->getParameters()->filter(
                function ($parameter) {
                    return $parameter->getName() !== 'entityIds';
                }
            )
        );

        // remove limit of the query
        $qb->setMaxResults(null);
    }

    /**
     * Delete a list of reference ids
     *
     * @param int[] $ids
     *
     * @return int
     * @throws \LogicException
     */
    public function deleteFromIds(array $ids)
    {
        if (empty($ids)) {
            throw new \LogicException('Nothing to remove');
        }

        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete($this->_entityName, 'ref')
            ->where($qb->expr()->in('ref.id', $ids));

        return $qb->getQuery()->execute();
    }
}
