<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CreateActionSpec extends FormActionBehavior
{
    public function let(
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
        ActionInterface $indexAction,
        Session $session,
        FlashBagInterface $flashBag
    ) {
        $this->beConstructedWith($manager, $router, $translator, $templating, $formFactory);
        $this->initializeConfiguration($configuration);
        $this->initializeRouter($router);
        $this->initializeForm($formFactory, $form, $formView, $object);
        $this->initializeRequest($request, $attributes);
        $this->initializeConfigurationForForm($configuration, $indexAction);
        $this->initializeFlashBag($request, $session, $flashBag);
        $this->initializeTranslator($translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Bundle\CustomEntityBundle\Action\CreateAction');
    }

    public function it_displays_forms(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request,
        Entity $object,
        EngineInterface $templating,
        FormView $formView
    ) {
        $request->isMethod('post')->willReturn(false);
        $manager->create('entity_class', [], [])->willReturn($object);
        $object->getId()->willReturn(null);
        $configuration->getActionOptions('create')->willReturn(['form_type' => 'form_type']);
        $configuration->hasAction('remove')->willReturn(false);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:form.html.twig',
            [
                'form' => $formView,
                'formAction' => 'pim_customentity_create?&customEntityName=entity',
                'customEntityName' => 'entity',
                'baseTemplate' => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl' => 'index?&ir_param1=value1'
            ]
        )->willReturn('success');
        $this->execute($request, $configuration)->shouldReturn('success');
    }

    public function it_displays_invalid_forms(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request,
        Entity $object,
        EngineInterface $templating,
        FormView $formView,
        FormInterface $form
    ) {
        $request->isMethod('post')->willReturn(true);
        $form->submit($request)->shouldBeCalled();
        $form->isValid()->willReturn(false);
        $manager->create('entity_class', [], [])->willReturn($object);
        $object->getId()->willReturn(null);
        $configuration->getActionOptions('create')
            ->willReturn(['form_type' => 'form_type']);
        $configuration->hasAction('remove')->willReturn(false);
        $templating->renderResponse(
            'PimCustomEntityBundle:CustomEntity:form.html.twig',
            [
                'form' => $formView,
                'formAction' => 'pim_customentity_create?&customEntityName=entity',
                'customEntityName' => 'entity',
                'baseTemplate' => 'PimCustomEntityBundle::layout.html.twig',
                'indexUrl' => 'index?&ir_param1=value1'
            ]
        )->willReturn('success');
        $this->execute($request, $configuration)->shouldReturn('success');
    }

    public function it_redirects_on_success(
        ManagerInterface $manager,
        ConfigurationInterface $configuration,
        Request $request,
        Entity $object,
        FormInterface $form,
        FlashBagInterface $flashBag
    ) {
        $request->isMethod('post')->willReturn(true);
        $form->submit($request)->shouldBeCalled();
        $form->isValid()->willReturn(true);
        $manager->create('entity_class', [], [])->willReturn($object);
        $manager->save($object)->shouldBeCalled();
        $object->getId()->willReturn(null);
        $configuration->getActionOptions('create')
            ->willReturn(['form_type' => 'form_type']);
        $configuration->hasAction('remove')->willReturn(false);
        $flashBag->add('success', '<flash.entity.created>')->shouldBeCalled();
        $response = $this->execute($request, $configuration);
        $response->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }
}
