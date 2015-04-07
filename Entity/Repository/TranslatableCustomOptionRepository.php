<?php

namespace Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Pim\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface;

/**
 * Repository for translatable custom options
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TranslatableCustomOptionRepository extends TranslatableCustomEntityRepository implements OptionRepositoryInterface
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
        $object->setLocale($dataLocale);

        return $object->getTranslation()->getLabel() ?: sprintf('[%s]', $object->getCode());
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($dataLocale, $collectionId = null, $search = '', array $options = array())
    {
        $qb = $this->createOptionsQueryBuilder($dataLocale, $collectionId, $search, $options);

        return array(
            'results' => array_map(
                function ($option) {
                    if (!$option['text']) {
                        $option['text'] = $option['code'];
                    }
                    unset($option['code']);

                    return $option;
                },
                $qb->getQuery()->getArrayResult()
            )
        );
    }

    /**
     * Creates a query builder for options
     *
     * @param string $dataLocale
     * @param mixed  $collectionId
     * @param string $search
     * @param array  $options
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createOptionsQueryBuilder($dataLocale, $collectionId, $search, array $options)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id AS id, c.code, t.label AS text')
            ->leftJoin('c.translations', 't', 'WITH', 't.locale=:locale')
            ->setParameter('locale', $dataLocale);
        if ($search) {
            $qb->andWhere(('c.code LIKE :search OR t.label LIKE :search'))
                ->setParameter('search', "$search%");
        }

        return $qb;
    }
}
