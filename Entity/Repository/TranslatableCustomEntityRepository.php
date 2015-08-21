<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

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
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale=:localeCode')
            ->addSelect('translation');
    }
}
