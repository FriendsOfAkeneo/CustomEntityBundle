<?php

namespace Pim\Bundle\CustomEntityBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

class ProductLinkChecker implements ProductLinkCheckerInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $productQueryBuilderFactory;

    /** @var AttributeRepository */
    protected $attributeRepository;

    /**
     * @param EntityManagerInterface              $em
     * @param ProductQueryBuilderFactoryInterface $productQueryBuilderFactory
     * @param AttributeRepository                 $attributeRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory,
        AttributeRepository $attributeRepository
    ) {
        $this->em = $em;
        $this->productQueryBuilderFactory = $productQueryBuilderFactory;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Check if the entity is linked to one or more products
     *
     * @param ReferenceDataInterface $entity
     *
     * @return bool
     */
    public function isLinkedToProduct(ReferenceDataInterface $entity)
    {
        $attributesCodes = $this->attributeRepository->findReferenceDataAttributeCodesByEntityName($entity->getCode());

        foreach ($attributesCodes as $attributeCode) {
            $pqb = $this->productQueryBuilderFactory->create();
            $pqb->addFilter($attributeCode, Operators::IN_LIST, [$entity->getCode()]);
            $qb = $pqb->getQueryBuilder();
            $result = count($qb->getQuery()->execute());
            if ($result > 0) {
                return true;
            }
        }

        return false;
    }
}
