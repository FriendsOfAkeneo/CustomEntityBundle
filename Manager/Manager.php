<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Bundle\CustomEntityBundle\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * Base implementation for ORM managers
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Manager implements ManagerInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ObjectUpdaterInterface */
    protected $updater;

    /** @var SaverInterface */
    protected $saver;

    /** @var RemoverInterface */
    protected $remover;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $productQueryBuilderFactory;

    /**
     * @param EntityManagerInterface              $em
     * @param ObjectUpdaterInterface              $updater
     * @param SaverInterface                      $saver
     * @param RemoverInterface                    $remover
     * @param ProductQueryBuilderFactoryInterface $productQueryBuilderFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        ObjectUpdaterInterface $updater,
        SaverInterface $saver,
        RemoverInterface $remover,
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory
    ) {
        $this->em = $em;
        $this->updater = $updater;
        $this->saver = $saver;
        $this->remover = $remover;
        $this->productQueryBuilderFactory = $productQueryBuilderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entityClass, array $defaultValues = [], array $options = [])
    {
        $referenceData = new $entityClass();
        $this->updater->update($referenceData, $defaultValues);

        return $referenceData;
    }

    /**
     * {@inheritdoc}
     */
    public function find($entityClass, $id, array $options = [])
    {
        return $this->em->getRepository($entityClass)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, array $options = [])
    {
        $this->saver->save($entity, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity)
    {
        if (!$this->isLinkedToProduct($entity)) {
            $this->remover->remove($entity);
        }
    }

    /**
     * Check if the entity is linked to one or more products
     *
     * @param ReferenceDataInterface|object $entity
     *
     * @return bool
     */
    protected function isLinkedToProduct($entity)
    {
        $metadata = $this->em->getClassMetadata(Attribute::class);
        $repository = new AttributeRepository($this->em, $metadata);
        $attributesCodes = $repository
            ->findReferenceDataAttributeCodesByEntityName($entity->getCode());

        $pqb = $this->productQueryBuilderFactory->create();
        foreach ($attributesCodes as $attributeCode) {
            $pqb->addFilter($attributeCode, Operators::IN_LIST, [$entity->getCode()]);
        }
        $qb = $pqb->getQueryBuilder();
        $result = $qb->getQuery()->execute()->count();

        return $result > 0;
    }
}
