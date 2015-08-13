<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EditActionSpec extends ObjectBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        Request $request,
        ParameterBag $attributes,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormView $formView,
        Entity $object,
        ActionInterface $indexAction
    ) {
        $this->beConstructedWith(
            $actionFactory,
            $eventManager,
            $managerRegistry,
            $router,
            $translator,
            $templating,
            $formFactory
        );

        // initialize configuration
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');

        // initialize manager
        $managerRegistry->getFromConfiguration($configuration)->willReturn($manager);

        // initialize router
        $router->generate(Argument::type('string'), Argument::type('array'), Argument::any())->will(
            function ($arguments) {
                $path = $arguments[0] . '?';
                foreach ($arguments[1] as $key => $value) {
                    $path .= '&' . $key . '=' . $value;
                }

                return $path;
            }
        );

        // initialize form
        $formFactory->create('form_type', $object, ['data_class' => 'entity_class'])->willReturn($form);
        $form->createView()->willReturn($formView);
        $form->getData()->willReturn($object);

        // initialize request
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');

        // initialize configuration for form
        $actionFactory->getAction('entity', 'index')->willReturn($indexAction);
        $configuration->hasAction('index')->willReturn(true);
        $indexAction->getRoute()->willReturn('index');
        $indexAction->getRouteParameters(null)->willReturn(['ir_param1' => 'value1']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\EditAction');
    }

    public function it_displays_forms(
        ManagerInterface $manager,
        ActionEventManager $eventManager,
        ConfigurationInterface $configuration,
        Request $request,
        Entity $object,
        EngineInterface $templating,
        FormView $formView,
        Response $response
    ) {
        $request->isMethod('post')->willReturn(false);
        $manager->find('entity_class', 'id', [])->willReturn($object);
        $object->getId()->willReturn('id');
        $configuration->getActionOptions('edit')->willReturn(['form_type' => 'form_type']);
        $configuration->hasAction('delete')->willReturn(false);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:form.html.twig',
            [
                'form' => $formView,
                'formAction' => 'pim_customentity_edit?&customEntityName=entity&id=id',
                'customEntityName' => 'entity',
                'baseTemplate' => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl' => 'index?&ir_param1=value1',
                'pre_render' => true
            ]
        )->willReturn($response);
        $this->initializeEventManager($eventManager);

        $this->setConfiguration($configuration);
        $this->execute($request)->shouldReturn($response);
    }

    protected function initializeEventManager(ActionEventManager $eventManager)
    {
        $eventManager
            ->dipatchConfigureEvent(
                $this,
                Argument::type('Symfony\Component\OptionsResolver\OptionsResolver')
            )
            ->shouldBeCalled();

        $eventManager->dispatchPreExecuteEvent($this)->shouldBeCalled();

        $eventManager
            ->dispatchPostExecuteEvent($this, Argument::type('Symfony\Component\HttpFoundation\Response'))
            ->will(
                function ($args) {
                    return $args[1];
                }
            )
            ->shouldBeCalled();

        $eventManager
            ->dispatchPreRenderEvent($this, Argument::type('string'), Argument::type('array'))
            ->will(
                function ($args) {
                    $args[2]['pre_render'] = true;

                    return [$args[1], $args[2]];
                }
            );
    }
}
