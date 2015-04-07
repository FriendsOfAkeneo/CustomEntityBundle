<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Pim\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface;
use Pim\Bundle\CatalogBundle\Doctrine\ReferableEntityRepository;

/**
 * Repository for the Color entity
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityRepository extends ReferableEntityRepository implements OptionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOption($id, $collectionId = null, array $options = array())
    {
        return $this->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionId($object)
    {
        return $object->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionLabel($object, $dataLocale)
    {
        return $object->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($dataLocale, $collectionId = null, $search = '', array $options = array())
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id AS id, c.code AS text');
        if ($search) {
            $qb->andWhere(('c.code LIKE :search'))
                ->setParameter('search', "$search%");
        }

        return array(
            'results' => $qb->getQuery()->getArrayResult()
        );
    }
}
