<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Pim\Bundle\CustomEntityBundle\MassAction\MassUpdater;
use Pim\Bundle\DataGridBundle\Extension\MassAction\MassActionDispatcher;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MassEditAction extends CreateAction
{
    /**
     * @var MassActionDispatcher
     */
    protected $massActionDispatcher;

    /**
     * @var MassUpdater
     */
    protected $massUpdater;

    /**
     * @param ActionFactory          $actionFactory
     * @param ActionEventManager     $eventManager
     * @param ManagerRegistry        $manager
     * @param RouterInterface        $router
     * @param TranslatorInterface    $translator
     * @param EngineInterface        $templating
     * @param FormFactoryInterface   $formFactory
     * @param MassActionDispatcher   $massActionDispatcher
     * @param MassUpdater            $massUpdater
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        MassActionDispatcher $massActionDispatcher,
        MassUpdater $massUpdater
    ) {
        parent::__construct($actionFactory, $eventManager, $manager, $router, $translator, $templating, $formFactory);

        $this->massActionDispatcher = $massActionDispatcher;
        $this->massUpdater          = $massUpdater;
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
    protected function saveForm(Request $request, FormInterface $form)
    {
        $this->massUpdater->updateEntities(
            $this->configuration->getEntityClass(),
            $this->getFormData($form),
            $this->massActionDispatcher->dispatch($request)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVars(Request $request, FormInterface $form)
    {
        $entityIds = $this->massActionDispatcher->dispatch($request);

        return [
            'objectCount' => count($entityIds),
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
     * @param Request $request
     *
     * @return array
     */
    protected function getGridUrlParameters(Request $request)
    {
        $parameters = [];
        foreach (['inset', 'filters', 'values', 'gridName', 'actionName'] as $key) {
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
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(
            [
                'route'               => 'pim_customentity_massedit',
                'template'            => 'PimCustomEntityBundle:CustomEntity:massEdit.html.twig',
                'success_message'     => sprintf('flash.%s.mass_updated', $this->configuration->getName())
            ]
        );
    }
}
