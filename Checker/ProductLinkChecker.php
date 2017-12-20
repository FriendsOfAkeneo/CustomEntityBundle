<?php

namespace Pim\Bundle\CustomEntityBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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
     * {@inheritdoc}
     */
    public function isLinkedToProduct(ReferenceDataInterface $entity)
    {
        $attributesCodes = $this->attributeRepository->findReferenceDataAttributeCodes();

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
