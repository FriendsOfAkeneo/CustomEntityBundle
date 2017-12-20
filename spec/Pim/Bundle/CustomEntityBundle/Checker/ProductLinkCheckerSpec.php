<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Checker\ProductLinkChecker;
use Pim\Bundle\CustomEntityBundle\Checker\ProductLinkCheckerInterface;
use Pim\Bundle\CustomEntityBundle\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;

class ProductLinkCheckerSpec extends ObjectBehavior
{
    function let(
        EntityManagerInterface $em,
        ProductQueryBuilderFactoryInterface $productQueryBuilderFactory,
        AttributeRepository $attributeRepository
    ) {
        $this->beConstructedWith($em, $productQueryBuilderFactory, $attributeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductLinkChecker::class);
        $this->shouldImplement(ProductLinkCheckerInterface::class);
    }
}
