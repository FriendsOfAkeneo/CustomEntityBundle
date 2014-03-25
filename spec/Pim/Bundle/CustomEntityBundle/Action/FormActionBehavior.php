<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\FormInterface;

class FormActionBehavior extends ActionBehavior
{
    public function initializeForm(
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormView $formView,
        Entity $object
    ) {
        $formFactory->create('form_type', $object, ['data_class' => 'entity_class'])->willReturn($form);
        $form->createView()->willReturn($formView);
        $form->getData()->willReturn($object);
    }

    public function initializeConfigurationForForm(
        ActionFactory $actionFactory,
        ConfigurationInterface $configuration,
        ActionInterface $indexAction
    ) {
        $actionFactory->getAction('entity', 'index')->willReturn($indexAction);
        $configuration->hasAction('index')->willReturn(true);
        $indexAction->getRoute()->willReturn('index');
        $indexAction->getRouteParameters(null)->willReturn(['ir_param1' => 'value1']);
    }
}

class Entity
{
    public function getId() { }
}
