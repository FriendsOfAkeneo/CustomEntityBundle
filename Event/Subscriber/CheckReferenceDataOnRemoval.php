<?php

namespace Pim\Bundle\CustomEntityBundle\Event\Subscriber;

use Akeneo\Component\StorageUtils\Event\RemoveEvent;
use Akeneo\Component\StorageUtils\StorageEvents;
use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;
use Pim\Bundle\CustomEntityBundle\Repository\AttributeRepository;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class CheckReferenceDataOnRemoval implements EventSubscriberInterface
{
    /** @var AttributeRepository */
    protected $attributeRepository;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $pqbFactory;

    /**
     * @param AttributeRepository $attributeRepository
     * @param ProductQueryBuilderFactoryInterface $pqbFactory
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        ProductQueryBuilderFactoryInterface $pqbFactory
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->pqbFactory = $pqbFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [StorageEvents::PRE_REMOVE => 'checkReferenceDataUsage'];
    }

    /**
     * Checks if the reference data is used in a product
     *
     * @param RemoveEvent $event
     */
    public function checkReferenceDataUsage(RemoveEvent $event)
    {
        $referenceData = $event->getSubject();

        $attributes = $this->attributeRepository->findByReferenceData($referenceData);
        foreach ($attributes as $attribute) {
            $pqb = $this->pqbFactory->create();
            $pqb->addFilter($attribute->getCode(), Operators::IN_LIST, [$referenceData->getCode()]);
            if (0 !== $pqb->execute()->count()) {
                throw new NonRemovableEntityException('Reference data "%s" is linked to at least one product');
            }
        }
    }
}
