<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Doctrine\Common\Collections\ArrayCollection;
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
     * Create a query builder used for the datagrid
     *
     * @return QueryBuilder
     */
    public function createDatagridQueryBuilder()
    {
        return $this->createQueryBuilder(
            $this->getAlias()
        );
    }

    /**
     * Applies mass action parameters on the query builder
     *
     * @param QueryBuilder $qb
     * @param bool         $inset
     * @param array        $values
     */
    public function applyMassActionParameters($qb, $inset, $values)
    {
        if ($values) {
            $valueWhereCondition =
                $inset
                    ? $qb->expr()->in($this->getAlias(), $values)
                    : $qb->expr()->notIn($this->getAlias(), $values);
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
     * Used to mass delete reference datas from their ids
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
            ->delete($this->getEntityName(), $this->getAlias())
            ->where(
                $qb->expr()->in(
                    sprintf('%s.id', $this->getAlias()),
                    $ids
                )
            );

        return $qb->getQuery()->execute();
    }

    /**
     * Hydrates reference data from ids for quick export or mass edit features
     *
     * @param array $referenceDataIds
     *
     * @return ArrayCollection
     *
     * @throws \InvalidArgumentException array of ids should not be empty
     */
    public function findByIds(array $referenceDataIds)
    {
        if (empty($referenceDataIds)) {
            throw new \InvalidArgumentException('Array must contain at least one reference data id');
        }

        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->where(
            $qb->expr()->in(
                sprintf('%s.id', $this->getAlias()),
                $referenceDataIds
            )
        );

        return new ArrayCollection($qb->getQuery()->getResult());
    }

    /**
     * {@inheritdoc}
     */
    public function findBySearch($search = null, array $options = [])
    {
        $qb = $this->findBySearchQB($search, $options);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * @param string $search
     * @param array  $options
     *
     * @return QueryBuilder
     */
    protected function findBySearchQB($search, array $options)
    {
        if (null !== $labelProperty = $this->getReferenceDataLabelProperty()) {
            $selectDql = sprintf(
                '%s.%s as id, ' .
                'CASE WHEN %s.%s IS NULL OR %s.%s = \'\' THEN CONCAT(\'[\', %s.code, \']\') ELSE %s.%s END AS text',
                $this->getAlias(),
                isset($options['type']) && 'code' === $options['type'] ? 'code' : 'id',
                $this->getAlias(),
                $labelProperty,
                $this->getAlias(),
                $labelProperty,
                $this->getAlias(),
                $this->getAlias(),
                $labelProperty
            );
        } else {
            $selectDql = sprintf(
                '%s.%s as id, CONCAT(\'[\', %s.code, \']\') as text',
                $this->getAlias(),
                isset($options['type']) && 'code' === $options['type'] ? 'code' : 'id',
                $this->getAlias()
            );
        }

        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select($selectDql);

        // Overridden part - manage with sort order
        $this->addSortOrder($qb);
        // End of overridden part - manage with sort order

        if (null !== $search) {
            $searchDql = sprintf('%s.code LIKE :search', $this->getAlias());
            if (null !== $labelProperty) {
                $searchDql .= sprintf(' OR %s.%s LIKE :search', $this->getAlias(), $labelProperty);
            }
            $qb->andWhere($searchDql)->setParameter('search', "%$search%");
        }

        if (isset($options['limit'])) {
            $qb->setMaxResults((int) $options['limit']);
            if (isset($options['page'])) {
                $qb->setFirstResult((int) $options['limit'] * ((int) $options['page'] - 1));
            }
        }

        return $qb;
    }

    /**
     * Add sort order in the findBySearch method
     * Used in products datagrid filtering and product edit form
     *
     * @param QueryBuilder $qb
     */
    protected function addSortOrder(QueryBuilder $qb)
    {
        $sortOrder = $this->getSortOrderColumn();

        $qb->orderBy(sprintf('%s.%s', $this->getAlias(), $sortOrder));
        $qb->addOrderBy(sprintf('%s.code', $this->getAlias()));
    }

    /**
     * @return string
     */
    protected function getSortOrderColumn()
    {
        $referenceDataClass = $this->getEntityName();

        return $referenceDataClass::getSortOrderColumn();
    }

    /**
     * Duplicate code due to method visibility
     *
     * {@inheritdoc}
     */
    protected function getReferenceDataLabelProperty()
    {
        $referenceDataClass = $this->getEntityName();

        return $referenceDataClass::getLabelProperty();
    }
}
