<?php

namespace spec\Pim\Bundle\CustomEntityBundle\EventListener\DataGrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;

class ConfigureCustomEntityGridListenerSpec extends ObjectBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionInterface $indexAction,
        BuildBefore $event,
        DatagridConfiguration $datagridConfig,
        ConfigurationInterface $customEntityConfig
    ) {
        $this->beConstructedWith($actionFactory);
        $indexAction->implement('Pim\Bundle\CustomEntityBundle\Action\IndexActionInterface');
        $indexAction->getConfiguration()->willReturn($customEntityConfig);
        $customEntityConfig->getName()->willReturn('entity');
        $customEntityConfig->getEntityClass()->willReturn('entity_class');
        $actionFactory->getAction('entity', 'index')->willReturn($indexAction);
        $event->getConfig()->willReturn($datagridConfig);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\EventListener\DataGrid\ConfigureCustomEntityGridListener');
    }

    public function it_configures_the_datagrid(
        BuildBefore $event,
        DatagridConfiguration $datagridConfig,
        ActionInterface $indexAction
    ) {
        $datagridConfig->getName()->willReturn('entity');
        $indexAction->getMassActions()->willReturn([]);
        $indexAction->getRowActions()->willReturn([]);
        $datagridConfig->offsetGetByPath('[extends]')->willReturn('custom_entity');
        $datagridConfig->offsetGetByPath('[properties]')->willReturn([]);
        $datagridConfig->offsetGetByPath('[actions]')->willReturn([]);
        $datagridConfig->offsetGetByPath('[mass_actions]')->willReturn([]);
        $datagridConfig->offsetSetByPath("[source]", ["entity" => "entity_class", "type" => "pim_custom_entity"])
                ->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[actions]", [])->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[properties]", ['id' => []])->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[mass_actions]", [])->shouldBeCalled();
        $this->buildBefore($event);
    }

    public function it_configures_row_actions(
        BuildBefore $event,
        DatagridConfiguration $datagridConfig,
        ActionInterface $indexAction,
        ActionFactory $actionFactory,
        ActionInterface $action1,
        ActionInterface $action2
    ) {
        $action1->implement('Pim\Bundle\CustomEntityBundle\Action\GridActionInterface');
        $action2->implement('Pim\Bundle\CustomEntityBundle\Action\GridActionInterface');
        $actionFactory->getAction('entity', 'action1')->willReturn($action1);
        $actionFactory->getAction('entity', 'action2')->willReturn($action2);
        $action1->getGridActionOptions()->willReturn(['action1_key1' => 'action1_value1']);
        $datagridConfig->getName()->willReturn('entity');
        $indexAction->getMassActions()->willReturn([]);
        $indexAction->getRowActions()->willReturn(['action1', 'action2']);
        $datagridConfig->offsetGetByPath('[extends]')->willReturn('custom_entity');
        $datagridConfig->offsetGetByPath('[properties]')->willReturn([]);
        $datagridConfig->offsetGetByPath('[actions]')->willReturn(['action2'=>[]]);
        $datagridConfig->offsetGetByPath('[mass_actions]')->willReturn([]);
        $datagridConfig->offsetSetByPath("[source]", ["entity" => "entity_class", "type" => "pim_custom_entity"])
                ->shouldBeCalled();
        $datagridConfig->offsetSetByPath(
                "[actions]",
                [
                    "action2" => [],
                    "action1" => ["action1_key1" => "action1_value1", "link" => "action1_link"]
                ]
            )->shouldBeCalled();
        $datagridConfig->offsetSetByPath(
            "[properties]",
            [
                'id' => [],
                "action1_link" => ["type" => "custom_entity_url", "route" => "entity/action1", "params" => ["id"]]
            ]
        )->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[mass_actions]", [])->shouldBeCalled();
        $this->buildBefore($event);
    }

    public function it_configures_mass_actions(
        BuildBefore $event,
        DatagridConfiguration $datagridConfig,
        ActionInterface $indexAction,
        ActionFactory $actionFactory,
        ActionInterface $action1,
        ActionInterface $action2
    ) {
        $action1->implement('Pim\Bundle\CustomEntityBundle\Action\GridActionInterface');
        $action2->implement('Pim\Bundle\CustomEntityBundle\Action\GridActionInterface');
        $actionFactory->getAction('entity', 'action1')->willReturn($action1);
        $actionFactory->getAction('entity', 'action2')->willReturn($action2);
        $action1->getGridActionOptions()->willReturn(['action1_key1' => 'action1_value1']);
        $datagridConfig->getName()->willReturn('entity');
        $indexAction->getMassActions()->willReturn(['action1', 'action2']);
        $indexAction->getRowActions()->willReturn([]);
        $datagridConfig->offsetGetByPath('[extends]')->willReturn('custom_entity');
        $datagridConfig->offsetGetByPath('[properties]')->willReturn([]);
        $datagridConfig->offsetGetByPath('[actions]')->willReturn([]);
        $datagridConfig->offsetGetByPath('[mass_actions]')->willReturn(['action2'=>[]]);
        $datagridConfig->offsetSetByPath("[source]", ["entity" => "entity_class", "type" => "pim_custom_entity"])
                ->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[actions]", [])->shouldBeCalled();
        $datagridConfig->offsetSetByPath("[properties]", ['id' => []])->shouldBeCalled();
        $datagridConfig->offsetSetByPath(
                "[mass_actions]",
                [
                    "action2" => [],
                    "action1" => ["action1_key1" => "action1_value1"]
                ]
            )->shouldBeCalled();
        $this->buildBefore($event);
    }

}
