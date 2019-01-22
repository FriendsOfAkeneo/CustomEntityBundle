<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Event\Subscriber;

use Akeneo\Tool\Component\StorageUtils\Event\RemoveEvent;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Doctrine\ORM\EntityManagerInterface;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Configuration;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\AttributeRepository;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Bundle\CustomEntityBundle\Event\Subscriber\CheckReferenceDataOnRemovalSubscriber;
use Pim\Bundle\CustomEntityBundle\Remover\NonRemovableEntityException;
use Oro\Bundle\PimDataGridBundle\Datasource\DatasourceInterface;
use Oro\Bundle\PimDataGridBundle\Datasource\ResultRecord\Orm\ObjectIdHydrator;
use Oro\Bundle\PimDataGridBundle\Extension\MassAction\Event\MassActionEvent;
use Oro\Bundle\PimDataGridBundle\Extension\MassAction\Event\MassActionEvents;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderFactoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderInterface;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class CheckReferenceDataOnRemovalSubscriberSpec extends ObjectBehavior
{
    function let(
        AttributeRepository $attributeRepository,
        ProductQueryBuilderFactoryInterface $pqbFactory,
        Registry $configRegistry,
        EntityManagerInterface $em
    ) {
        $this->beConstructedWith($attributeRepository, $pqbFactory, $configRegistry, $em);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckReferenceDataOnRemovalSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldHaveType(EventSubscriberInterface::class);
    }

    function it_subscribes_to_pre_remove_events()
    {
        $this->getSubscribedEvents()->shouldHaveKey(StorageEvents::PRE_REMOVE);
        $this->getSubscribedEvents()->shouldHaveKey(MassActionEvents::MASS_DELETE_PRE_HANDLER);
        $this->getSubscribedEvents()->shouldHaveCount(2);
    }

    function it_does_not_check_other_entities_than_reference_data(RemoveEvent $event, AbstractCustomEntity $object)
    {
        $event->getSubject()->willReturn(Argument::not($object));
        $this->checkReferenceDataUsage($event)->shouldReturn(null);
    }

    function it_checks_reference_data_usage(
        RemoveEvent $event,
        AbstractCustomEntity $refData,
        AttributeRepository $attributeRepository,
        AttributeInterface $attribute,
        ProductQueryBuilderFactoryInterface $pqbFactory,
        ProductQueryBuilderInterface $pqb,
        \Countable $countable
    ) {
        $event->getSubject()->willReturn($refData);
        $refData->getCode()->willReturn('green');
        $refData->getCustomEntityName()->willReturn('color');
        $attribute->getCode()->willReturn('main_color');
        $attributeRepository->getAttributesByReferenceDataName('color')->willReturn([$attribute]);

        $pqbFactory->create()->willReturn($pqb);
        $pqb->addFilter('main_color', Operators::IN_LIST, ['green'])->shouldBeCalled();
        $pqb->execute()->willReturn($countable);
        $countable->count()->willReturn(0);

        $this->checkReferenceDataUsage($event)->shouldReturn(null);
    }

    function it_throws_an_exception_when_reference_data_is_used_in_at_least_one_product(
        RemoveEvent $event,
        AbstractCustomEntity $refData,
        AttributeRepository $attributeRepository,
        AttributeInterface $attribute,
        ProductQueryBuilderFactoryInterface $pqbFactory,
        ProductQueryBuilderInterface $pqb,
        \Countable $countable
    ) {
        $event->getSubject()->willReturn($refData);
        $refData->getCode()->willReturn('green');
        $refData->getCustomEntityName()->willReturn('color');
        $attribute->getCode()->willReturn('main_color');
        $attributeRepository->getAttributesByReferenceDataName('color')->willReturn([$attribute]);

        $pqbFactory->create()->willReturn($pqb);
        $pqb->addFilter('main_color', Operators::IN_LIST, ['green'])->shouldBeCalled();
        $pqb->execute()->willReturn($countable);

        $countable->count()->willReturn(1);
        $this
            ->shouldThrow(NonRemovableEntityException::class)
            ->during('checkReferenceDataUsage', [$event]);

        $countable->count()->willReturn(5);
        $this
            ->shouldThrow(NonRemovableEntityException::class)
            ->during('checkReferenceDataUsage', [$event]);
    }

    function it_only_checks_for_reference_data_registry_names(
        MassActionEvent $event,
        DatagridInterface $datagrid,
        $configRegistry
    ) {
        $event->getDatagrid()->willReturn($datagrid);
        $datagrid->getName()->willReturn('product');
        $configRegistry->has('product')->willReturn(false);

        $this->checkReferenceDataIdsUsage($event)->shouldReturn(null);
    }

    function it_checks_many_reference_data_usage(
        MassActionEvent $event,
        DatagridInterface $datagrid,
        DatasourceInterface $datasource,
        Configuration $config,
        AttributeRepository $attributeRepository,
        AttributeInterface $attribute,
        CustomEntityRepository $refDataRepository,
        ProductQueryBuilderFactoryInterface $pqbFactory,
        ProductQueryBuilderInterface $pqb,
        \Countable $countable,
        $configRegistry,
        $em
    ) {
        $configRegistry->has('color')->willReturn(true);
        $configRegistry->get('color')->willReturn($config);
        $config->getEntityClass()->willReturn('MyColorFQCN');

        $event->getDatagrid()->willReturn($datagrid);
        $datagrid->getName()->willReturn('color');
        $datagrid->getDatasource()->willReturn($datasource);
        $datasource->setHydrator(new ObjectIdHydrator())->shouldBeCalled();
        $datasource->getResults()->willReturn([1, 3]);

        $attributeRepository->getAttributesByReferenceDataName('color')->willReturn([$attribute]);
        $attribute->getCode()->willReturn('main_color');
        $em->getRepository('MyColorFQCN')->willReturn($refDataRepository);
        $refDataRepository->findReferenceDataCodesFromIds([1, 3])->willReturn(['green', 'purple']);

        $pqbFactory->create()->willReturn($pqb);
        $pqb->addFilter('main_color', Operators::IN_LIST, ['green', 'purple'])->shouldBeCalled();
        $pqb->execute()->willReturn($countable);

        $countable->count()->willReturn(0);

        $this->checkReferenceDataIdsUsage($event)->shouldReturn(null);
    }
}
