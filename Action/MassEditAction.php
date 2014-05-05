<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Pim\Bundle\CustomEntityBundle\MassAction\MassUpdater;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Batch edit action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MassEditAction extends CreateAction implements GridActionInterface
{
    /**
     * @var DataGridQueryGenerator
     */
    protected $queryGenerator;

    /**
     * @var MassUpdater
     */
    protected $massUpdater;

    /**
     * Constructor
     *
     * @param ActionFactory          $actionFactory
     * @param ActionEventManager     $eventManager
     * @param ManagerInterface       $manager
     * @param RouterInterface        $router
     * @param TranslatorInterface    $translator
     * @param EngineInterface        $templating
     * @param FormFactoryInterface   $formFactory
     * @param DataGridQueryGenerator $queryGenerator
     * @param MassUpdater            $massUpdater
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        DataGridQueryGenerator $queryGenerator,
        MassUpdater $massUpdater
    ) {
        parent::__construct($actionFactory, $eventManager, $manager, $router, $translator, $templating, $formFactory);
        $this->queryGenerator = $queryGenerator;
        $this->massUpdater = $massUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'mass_edit';
    }

    /**
     * {@inheritdoc}
     */
    public function getGridActionOptions()
    {
        return $this->options['grid_action_options'] + [
            'route'             => $this->getRoute(),
            'route_parameters'  => $this->getRouteParameters()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function saveForm(Request $request, FormInterface $form)
    {
        $this->massUpdater->updateEntities(
            $this->configuration->getEntityClass(),
            $this->getFormData($form),
            $this->queryGenerator->getIds($request, $this->configuration->getName())
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVars(Request $request, FormInterface $form)
    {
        return [
            'objectCount' => $this->queryGenerator->getCount($request, $this->configuration->getName()),
            'formAction' => $this->getActionUrl(
                $this->getType(),
                $form->getData(),
                $this->getGridUrlParameters($request)
            )
        ] + parent::getTemplateVars($request, $form);
    }

    /**
     * Returns an array containing the grid url parameters
     *
     * @param  Request $request
     * @return array
     */
    protected function getGridUrlParameters(Request $request)
    {
        $parameters = [];
        foreach (['inset', 'filters', 'values'] as $key) {
            $parameters[$key] = $request->get($key);
        }

        return $parameters;
    }

    /**
     * Returns the data of the form
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormData(FormInterface $form)
    {
        $data = [];
        foreach ($form as $key => $field) {
            if ($field->getConfig()->getMapped()) {
                $data[$key] = $field->getData();
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'grid_action_options' => [
                    'type' => 'redirect',
                    'label'=> 'Mass Edit',
                    'icon' => 'edit',
                ],
                'route'               => 'pim_customentity_massedit',
                'template'            => 'PimCustomEntityBundle:CustomEntity:massEdit.html.twig',
                'success_message'     => sprintf('flash.%s.mass_updated', $this->configuration->getName())
            ]
        );
    }
}
