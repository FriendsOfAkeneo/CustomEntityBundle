<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Akeneo\Bundle\BatchBundle\Launcher\JobLauncherInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Pim\Bundle\DataGridBundle\Adapter\GridFilterAdapterInterface;
use Pim\Bundle\DataGridBundle\Extension\MassAction\MassActionDispatcher;
use Pim\Bundle\ImportExportBundle\Entity\Repository\JobInstanceRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Quick export action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class QuickExportAction extends AbstractAction implements GridActionInterface
{
    /**
     * @var MassActionDispatcher
     */
    protected $massActionDispatcher;

    /**
     * @var JobInstanceRepository
     */
    protected $jobInstanceRepo;

    /**
     * @var JobLauncherInterface
     */
    protected $jobLauncher;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param ActionFactory          $actionFactory
     * @param ActionEventManager     $eventManager
     * @param ManagerRegistry        $managerRegistry
     * @param RouterInterface        $router
     * @param TranslatorInterface    $translator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        TranslatorInterface $translator,
        MassActionDispatcher $massActionDispatcher,
        JobInstanceRepository $jobInstanceRepo,
        JobLauncherInterface $jobLauncher,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry, $router, $translator);

        $this->massActionDispatcher = $massActionDispatcher;
        $this->jobInstanceRepo      = $jobInstanceRepo;
        $this->jobLauncher          = $jobLauncher;
        $this->tokenStorage         = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        $jobInstance = $this->jobInstanceRepo->findOneBy(['code' => 'reference_data_quick_export']);
        if (null === $jobInstance) {
            throw new \Exception(
                'The job instance "reference_data_quick_export" does not exist. Please contact your administrator'
            );
        }

        $rawConfiguration = addslashes(
            json_encode(
                [
                    'filters'     => $this->getFilters($request),
                ]
            )
        );

        $this->jobLauncher->launch($jobInstance, $this->getUser(), $rawConfiguration);

        return new Response();
    }

    /**
     * Get datagrid filters
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getFilters(Request $request)
    {
        $referenceDatas = $this->massActionDispatcher->dispatch($request);
        $referenceDataIds = [];
        foreach ($referenceDatas as $referenceData) {
            $referenceDataIds[] = $referenceData->getId();
        }

        $filters = [
            ['field' => 'id', 'operator' => 'IN', 'value' => $referenceDataIds]
        ];

        return $filters;
    }

    /**
     * Get a user from the Security Context
     *
     * @return UserInterface|null
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    protected function getUser()
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token || !is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['limit']);
        $resolver->setDefaults(
            [
                'route'               => 'pim_customentity_quickexport',
                'format'              => 'csv',
                'content_type'        => 'text/csv',
                'filename'            => 'export.csv',
                'serializer_format'   => 'csv',
                'serializer_context'  => [],
                'batch_size'          => 1,
                'grid_action_options' => [
                    'type' => 'export',
                    'frontend_type' => 'export',
                    'label'=> 'Quick export',
                    'icon' => 'download',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'quick_export';
    }

    /**
     * {@inheritdoc}
     */
    public function getGridActionOptions()
    {
        return $this->options['grid_action_options'] + [
            'route'            => $this->getRoute(),
            'route_parameters' => $this->getRouteParameters()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters($object = null)
    {
        return parent::getRouteParameters($object) + [
            '_format'      => $this->options['format'],
            '_contentType' => $this->options['content_type']
        ];
    }
}
