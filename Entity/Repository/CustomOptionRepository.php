<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

/**
 * Repository for the Color entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomOptionRepository extends CustomEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function getOptionLabel($object, $dataLocale)
    {
        return $object->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($dataLocale, $collectionId = null, $search = '', array $options = array())
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id AS id, c.label AS text');
        if ($search) {
            $qb->andWhere(('c.label LIKE :search'))
                ->setParameter('search', "$search%");
        }

        return array(
            'results' => $qb->getQuery()->getArrayResult()
        );
    }

}
