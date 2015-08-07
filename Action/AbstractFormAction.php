<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractFormAction extends AbstractViewableAction
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param ActionFactory        $actionFactory
     * @param ActionEventManager   $eventManager
     * @param ManagerRegistry      $managerRegistry
     * @param RouterInterface      $router
     * @param TranslatorInterface  $translator
     * @param EngineInterface      $templating
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry, $router, $translator, $templating);
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setRequired(['form_type', 'success_message']);
        $action = $this->actionFactory->getAction($this->configuration->getName(), 'index');
        $resolver->setDefaults(
            [
                'form_options'              => [],
                'template'                  => 'PimCustomEntityBundle:CustomEntity:form.html.twig',
                'redirect_route'            => $action->getRoute(),
                'redirect_route_parameters' => $action->getRouteParameters(),
                'save_options'              => []
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        $object = $this->getObject($request);
        $form = $this->createForm($request, $object);
        if ($request->isMethod('post')) {
            $form->submit($request);
            if ($form->isValid()) {
                $this->saveForm($request, $form);
                $this->addFlash($request, 'success', $this->options['success_message']);

                return $this->getRedirectResponse($object);
            }
        }

        return $this->renderResponse(
            $this->getTemplateVars($request, $form)
        );
    }

    /**
     * Saves the object
     *
     * @param Request       $request
     * @param FormInterface $form
     */
    protected function saveForm(Request $request, FormInterface $form)
    {
        $this->getManager()->save($form->getData(), $this->options['save_options']);
    }

    /**
     * Gets the variables that should be present on the template
     *
     * @param Request $request
     * @param Form    $form
     */
    protected function getTemplateVars(Request $request, FormInterface $form)
    {
        return [
            'form'       => $form->createView(),
            'formAction' => $this->getActionUrl($this->getType(), $form->getData())
        ];
    }

    /**
     * Returns the redirect response in case of success
     *
     * @param object $object
     *
     * @return Response
     */
    protected function getRedirectResponse($object)
    {
        return new RedirectResponse($this->getRedirectPath($object));
    }

    /**
     * Returns the path to be redirected to in case of success
     *
     * @param object $object
     *
     * @return string
     */
    protected function getRedirectPath($object)
    {
        return $this->router->generate(
            $this->options['redirect_route'],
            $this->options['redirect_route_parameters']
        );
    }

    /**
     * Creates the form
     *
     * @param Request $request
     * @param object  $object
     *
     * @return Form
     */
    protected function createForm(Request $request, $object)
    {
        return $this->formFactory->create(
            $this->options['form_type'],
            $object,
            $this->getFormOptions($request, $object)
        );
    }

    /**
     * Returns the options of the form
     *
     * @param Request $request
     * @param object  $object
     *
     * @return array
     */
    protected function getFormOptions(Request $request, $object)
    {
        return ['data_class' => $this->configuration->getEntityClass()] + $this->options['form_options'];
    }

    /**
     * Returns the object to use in the form
     *
     * @param Request $request
     *
     * @return object
     */
    abstract protected function getObject(Request $request);
}
