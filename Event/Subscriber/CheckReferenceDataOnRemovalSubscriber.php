<?php

namespace Pim\Bundle\CustomEntityBundle\Event\Subscriber;

use Akeneo\Component\StorageUtils\Event\RemoveEvent;
use Akeneo\Component\StorageUtils\StorageEvents;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\AttributeRepository;
use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;
use Pim\Bundle\DataGridBundle\Datasource\ResultRecord\Orm\ObjectIdHydrator;
use Pim\Bundle\DataGridBundle\Extension\MassAction\Event\MassActionEvent;
use Pim\Bundle\DataGridBundle\Extension\MassAction\Event\MassActionEvents;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Checks if a reference data can be removed or not
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class CheckReferenceDataOnRemovalSubscriber implements EventSubscriberInterface
{
    /** @var AttributeRepository */
    protected $attributeRepository;

    /** @var ProductQueryBuilderFactoryInterface */
    protected $pqbFactory;

    /** @var Registry */
    protected $configRegistry;

    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param AttributeRepository $attributeRepository
     * @param ProductQueryBuilderFactoryInterface $pqbFactory
     * @param Registry $configRegistry
     * @param EntityManagerInterface $em
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        ProductQueryBuilderFactoryInterface $pqbFactory,
        Registry $configRegistry,
        EntityManagerInterface $em
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->pqbFactory = $pqbFactory;
        $this->configRegistry = $configRegistry;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            StorageEvents::PRE_REMOVE => 'checkReferenceDataUsage',
            MassActionEvents::MASS_DELETE_PRE_HANDLER => 'checkReferenceDataIdsUsage'
        ];
    }

    /**
     * Checks if the reference data ids are used in a product
     *
     * @param MassActionEvent $event
     *
     * @return null
     */
    public function checkReferenceDataIdsUsage(MassActionEvent $event)
    {
        $referenceDataName = $event->getDatagrid()->getName();
        if (!$this->configRegistry->has($referenceDataName)) {
            return;
        }
        $entityClass = $this->configRegistry->get($referenceDataName)->getEntityClass();

        $datasource = $event->getDatagrid()->getDatasource();
        $datasource->setHydrator(new ObjectIdHydrator());
        $referenceDataIds = $datasource->getResults();

        $attributes = $this->attributeRepository->getAttributesByReferenceDataName($referenceDataName);
        $referenceDataCodes = $this->em->getRepository($entityClass)->findReferenceDataCodesFromIds($referenceDataIds);

        $this->checkProductLink($attributes, $referenceDataCodes);
    }

    /**
     * Checks if the reference data is used in a product
     *
     * @param RemoveEvent $event
     *
     * @return null
     */
    public function checkReferenceDataUsage(RemoveEvent $event)
    {
        $referenceData = $event->getSubject();
        if (!$referenceData instanceof AbstractCustomEntity) {
            return;
        }

        $referenceDataName = $referenceData->getCustomEntityName();
        $attributes = $this->attributeRepository->getAttributesByReferenceDataName($referenceDataName);
        $this->checkProductLink($attributes, [$referenceData->getCode()]);
    }

    /**
     * Checks if a reference data is linked to a product
     *
     * @param array $attributes
     * @param string[] $referenceDataCode
     *
     * @throws NonRemovableEntityException
     */
    protected function checkProductLink($attributes, array $referenceDataCode)
    {
        foreach ($attributes as $attribute) {
            $pqb = $this->pqbFactory->create();
            $pqb->addFilter($attribute->getCode(), Operators::IN_LIST, $referenceDataCode);
            $count = $pqb->execute()->count();

            if (0 !== $count) {
                throw new NonRemovableEntityException(
                    sprintf(
                        'Reference data cannot be removed. It is linked to %s product(s) with the attribute "%s"',
                        $count,
                        $attribute->getCode()
                    )
                );
            }
        }
    }
}
