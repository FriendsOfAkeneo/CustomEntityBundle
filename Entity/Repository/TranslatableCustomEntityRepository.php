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
}
