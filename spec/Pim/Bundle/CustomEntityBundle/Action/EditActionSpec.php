<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EditActionSpec extends FormActionBehavior
{
    public function let(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
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
            $manager,
            $router,
            $translator,
            $templating,
            $formFactory
        );
        $this->initializeConfiguration($configuration);
        $this->initializeRouter($router);
        $this->initializeForm($formFactory, $form, $formView, $object);
        $this->initializeRequest($request, $attributes);
        $this->initializeConfigurationForForm($actionFactory, $configuration, $indexAction);
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
        $configuration->hasAction('remove')->willReturn(false);
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
}
