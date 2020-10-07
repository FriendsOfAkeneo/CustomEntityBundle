<?php

namespace Pim\Bundle\CustomEntityBundle\Event\Subscriber;

use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderFactoryInterface;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Tool\Component\StorageUtils\Event\RemoveEvent;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Akeneo\UserManagement\Bundle\Context\UserContext;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\PimDataGridBundle\Datasource\ResultRecord\Orm\ObjectIdHydrator;
use Oro\Bundle\PimDataGridBundle\Extension\MassAction\Event\MassActionEvent;
use Oro\Bundle\PimDataGridBundle\Extension\MassAction\Event\MassActionEvents;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\AttributeRepository;
use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;
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

    /** @var LocaleRepositoryInterface */
    private $localeRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

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
        EntityManagerInterface $em,
        LocaleRepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->pqbFactory = $pqbFactory;
        $this->configRegistry = $configRegistry;
        $this->em = $em;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
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
            $channelCodes = $this->channelRepository->getChannelCodes();
            $localeCodes = $this->localeRepository->getActivatedLocaleCodes();

            if ($attribute->isScopable() && $attribute->isLocalizable()) {
                //loop channels and locales
                foreach ($channelCodes as $channelCode) {
                    foreach ($localeCodes as $localeCode) {
                        $context = [
                            'scope' => $channelCode,
                            'locale' => $localeCode,
                        ];

                        $this->executeQueryWithFilter($referenceDataCode, $attribute, $context);
                    }
                }

                return;
            }

            if ($attribute->isScopable() ) {
                //loop channels
                foreach ($channelCodes as $channelCode) {
                    $context['scope'] = $channelCode;
                    $this->executeQueryWithFilter($referenceDataCode, $attribute, $context);
                }

                return;
            }

            if ($attribute->isLocalizable()) {
                //loop locales
                foreach ($localeCodes as $localeCode) {
                    $context['locale'] = $localeCode;
                    $this->executeQueryWithFilter($referenceDataCode, $attribute, $context);
                }

                return;
            }

            $this->executeQueryWithFilter($referenceDataCode, $attribute);
        }
    }

    private function executeQueryWithFilter($referenceDataCode, AttributeInterface $attribute, $context = [])
    {
        $pqb = $this->pqbFactory->create();
        $pqb->addFilter($attribute->getCode(), Operators::IN_LIST, $referenceDataCode, $context);
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
