<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Repository for translatable custom entities
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TranslatableCustomEntityRepository extends CustomEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function createDatagridQueryBuilder()
    {
        return parent::createDatagridQueryBuilder()
            ->leftJoin(
                sprintf('%s.translations', $this->getAlias()),
                'translation',
                'WITH',
                'translation.locale=:localeCode'
            )
            ->addSelect('translation');
    }

    /**
     * {@inheritdoc}
     */
    public function findBySearchQB($search, array $options)
    {
        $qb = parent::findBySearchQB($search, $options);

        $qb
            ->leftJoin(
                sprintf('%s.translations', $this->getAlias()),
                'translation',
                'WITH',
                'translation.locale=:localeCode'
            )
            ->setParameter('localeCode', $options['dataLocale']);

        return $qb;
    }

    /**
     * If the column defined as the sorting one belongs to the entity fields, we filter by this field
     * Otherwise, we consider that it's a translation one
     *
     * {@inheritdoc}
     */
    protected function addSortOrder(QueryBuilder $qb)
    {
        $sortOrder = $this->getSortOrderColumn();

        if ($this->getClassMetadata()->hasField($sortOrder)) {
            parent::addSortOrder($qb);
        } else {
            $qb
                ->orderBy(sprintf('translation.%s', $sortOrder))
                ->addOrderBy(sprintf('%s.code', $this->getAlias()));
        }
    }

    /**
     * Adds select in the findBySearch method
     * Used in products datagrid filtering and product edit form
     * This method is used by findBySearch method and it's not recommended to call it from elsewhere
     *
     * {@inheritdoc}
     */
    protected function selectFields(QueryBuilder $qb, array $options)
    {
        $labelProperty = $this->getReferenceDataLabelProperty();

        if ($this->getClassMetadata()->hasField($labelProperty)) {
            parent::selectFields($qb, $options);
        } else {
            $identifierField = isset($options['type']) && 'code' === $options['type'] ? 'code' : 'id';

            $qb
                ->select(
                    sprintf('%s.%s AS id', $this->getAlias(), $identifierField)
                )
                ->addSelect(
                    sprintf(
                        'CASE WHEN translation.%s IS NULL '.
                        'THEN CONCAT(\'[\', %s.code, \']\') ELSE translation.%s END AS text',
                        $labelProperty,
                        $this->getAlias(),
                        $labelProperty
                    )
                );
        }
    }

    /**
     * Adds search on label or code in the findBySearch method
     * Used in products datagrid filtering and product edit form
     * This method is used by findBySearch method and it's not recommended to call it from elsewhere
     *
     * {@inheritdoc}
     */
    protected function addSearchFilter(QueryBuilder $qb, $search)
    {
        $labelProperty = $this->getReferenceDataLabelProperty();

        if ($this->getClassMetadata()->hasField($labelProperty)) {
            parent::addSearchFilter($qb, $search);
        } else {
            $searchDql = sprintf(
                '%s.code LIKE :search OR translation.%s LIKE :search',
                $this->getAlias(),
                $labelProperty
            );
            $qb->andWhere($searchDql)->setParameter('search', "%$search%");
        }
    }
}
