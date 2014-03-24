<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
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
     * @param ManagerInterface $manager
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param EngineInterface $templating
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($manager, $router, $translator, $templating);
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(ConfigurationInterface $configuration, OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($configuration, $resolver);
        $resolver->setRequired(['form_type', 'success_message']);
        $resolver->setDefaults(
            [
                'form_options'              => [],
                'template'                  => 'PimCustomEntityBundle:CustomEntity:form.html.twig',
                'redirect_route'            => $configuration->getAction('index')->getRoute(),
                'redirect_route_parameters' => $configuration->getAction('index')->getRouteParameters($configuration)
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Request $request, ConfigurationInterface $configuration, array $options)
    {
        $object = $this->getObject($request, $configuration, $options);
        $form = $this->createForm($configuration, $options, $object);
        if ($request->isMethod('post')) {
            $form->submit($request);
            if ($form->isValid()) {
                $this->manager->save($object);
                $this->addFlash($request, 'success', $options['success_message']);

                return $this->getRedirectResponse($options);
            }
        }

        return $this->renderResponse(
            $configuration,
            $options,
            $this->getTemplateVars($request, $configuration, $form, $options)
        );
    }

    /**
     * Gets the variables that should be present on the template
     * 
     * @param Request $request
     * @param ConfigurationInterface $configuration
     * @param Form $form
     * @param array $options
     */
    protected function getTemplateVars(Request $request, ConfigurationInterface $configuration, Form $form, array $options)
    {
        return [
            'form'       => $form->createView(),
            'formAction' => $this->getActionUrl($configuration, $this->getType(), $form->getData())
        ];
    }

    /**
     * Returns the redirect response in case of success
     * 
     * @param array $options
     * 
     * @return Response
     */
    protected function getRedirectResponse(array $options)
    {
        return new RedirectResponse($this->getRedirectPath($options));
    }

    /**
     * Returns the path to be redirected to in case of success
     * 
     * @param array $options
     * 
     * @return string
     */
    protected function getRedirectPath(array $options)
    {
        return $this->router->generate($options['redirect_route'], $options['redirect_route_parameters']);
    }

    /**
     * Creates the form
     * 
     * @param ConfigurationInterface $configuration
     * @param array $options
     * @param object $object
     * 
     * @return Form
     */
    protected function createForm(ConfigurationInterface $configuration, array $options, $object)
    {
        $options['form_options']['data_class'] = $configuration->getEntityClass();

        return $this->formFactory->create($options['form_type'], $object, $options['form_options']);
    }

    /**
     * Returns the object to use in the form
     * 
     * @param Request $request
     * @param ConfigurationInterface $configuration
     * @param array $options
     * 
     * @return object
     */
    abstract protected function getObject(Request $request, ConfigurationInterface $configuration, array $options);
}
