<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Pim\Bundle\CustomEntityBundle\MassAction\DataGridQueryGenerator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
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
    /** @var RegistryInterface */
    protected $doctrine;

    /** @var DataGridQueryGenerator */
    protected $queryGenerator;

    /** @var Serializer */
    protected $serializer;

    /**
     * Constructor
     *
     * @param ActionFactory          $actionFactory
     * @param ActionEventManager     $eventManager
     * @param ManagerRegistry        $managerRegistry
     * @param RouterInterface        $router
     * @param TranslatorInterface    $translator
     * @param RegistryInterface      $doctrine
     * @param DataGridQueryGenerator $queryGenerator
     * @param Serializer             $serializer
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        TranslatorInterface $translator,
        RegistryInterface $doctrine,
        DataGridQueryGenerator $queryGenerator,
        Serializer $serializer
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry, $router, $translator);
        $this->doctrine = $doctrine;
        $this->queryGenerator = $queryGenerator;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(Request $request)
    {
        if (isset($this->options['limit'])) {
            $count = $this->queryGenerator->getCount($request, $this->configuration->getName());
            if ($count > $this->options['limit']) {
                $this->addFlash(
                    $request,
                    'error',
                    'pim_custom_entity.export.limit_exceeded',
                    ['%limit%' => $this->options['limit']]
                );

                return new RedirectResponse($this->getActionUrl('index'));
            }
        }

        $response = new StreamedResponse(
            function () use ($request) {
                $this->export($request);
            }
        );

        $this->setHttpHeaders($response);

        return $response;
    }

    /**
     * Sets headers in the response
     *
     * @param Response $response
     */
    protected function setHttpHeaders(Response $response)
    {
        $response->headers->set('Content-Type', $this->options['content_type']);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->options['filename']
        );
        $response->headers->set('Content-Disposition', $disposition);
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(['limit']);
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

    /**
     * Outputs serialized entities
     *
     * @param Request $request
     */
    protected function export(Request $request)
    {
        $iterator = $this->queryGenerator
            ->createQueryBuilder(
                $request,
                $this->configuration->getName()
            )
            ->getQuery()
            ->iterate();

        $headersSent = false;
        $manager = $this->doctrine->getManagerForClass($this->configuration->getEntityClass());

        foreach ($iterator as $index => $item) {
            if (!count($item[0])) {
                continue;
            }
            $norm = $this->serializer->normalize(
                $item[0],
                $this->options['serializer_format'],
                $this->options['serializer_context']
            );

            if (!$headersSent) {
                echo $this->serializer->encode(
                    array_keys($norm),
                    $this->options['serializer_format'],
                    $this->options['serializer_context']
                );
                $headersSent = true;
            }

            echo $this->serializer->encode(
                $norm,
                $this->options['serializer_format'],
                $this->options['serializer_context']
            );
            flush();
            if (0 === (($index + 100) % $this->options['batch_size'])) {
                $manager->clear();
            }
        }
    }
}
