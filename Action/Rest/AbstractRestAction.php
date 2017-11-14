<?php

namespace Pim\Bundle\CustomEntityBundle\Action\Rest;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractRestAction implements ActionInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ActionEventManager
     */
    protected $eventManager;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param ActionFactory      $actionFactory
     * @param ActionEventManager $eventManager
     * @param ManagerRegistry    $managerRegistry
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->eventManager->dipatchConfigureEvent($this, $resolver);
        $this->options = $resolver->resolve($configuration->getActionOptions($this->getType()));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }


    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $optionKey
     *
     * @return mixed
     * @throws \LogicException
     */
    protected function getOption($optionKey)
    {
        if (isset($this->options[$optionKey])) {
            return $this->options[$optionKey];
        } else {
            throw new \LogicException(
                sprintf('Option "%s" is not defined', $optionKey)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Request $request): Response
    {
        $this->eventManager->dispatchPreExecuteEvent($this);
        $response = $this->doExecute($request);

        return $this->eventManager->dispatchPostExecuteEvent($this, $response);
    }

    /**
     * Returns the entity of the request
     *
     * @param Request $request
     *
     * @return AbstractCustomEntity
     *
     * @throws NotFoundHttpException
     */
    protected function findEntity(Request $request): AbstractCustomEntity
    {
        $entity = $this->getManager()->find(
            $this->configuration->getEntityClass(),
            $request->attributes->get('id'),
            $this->options['find_options']
        );

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * Normalizes an entity into the internal array format
     *
     * @param AbstractCustomEntity $entity
     *
     * @return array
     */
    protected function normalize(AbstractCustomEntity $entity): array
    {
        $manager = $this->getManager();
        $entityName = $this->configuration->getName();
        $editFormExtension = $this->configuration->getOptions()['edit_form_extension'];
        $context = [
            'customEntityName' => $entityName,
            'form'             => $editFormExtension,
        ];

        $normalized = $manager->normalize($entity, 'internal_api', $context);

        return $normalized;
    }

    /**
     * Returns the custom entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager(): ManagerInterface
    {
        return $this->managerRegistry->getFromConfiguration($this->configuration);
    }


    /**
     * Set the default options
     *
     * @param OptionsResolver $resolver
     */
    protected function setDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['find_options' => []]);
    }

    /**
     * Executes the action
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    abstract protected function doExecute(Request $request): JsonResponse;
}
