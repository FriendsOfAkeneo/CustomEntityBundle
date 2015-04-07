<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Abstract action
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractAction implements ActionInterface
{
    /** @var ActionFactory */
    protected $actionFactory;

    /** @var ActionEventManager */
    protected $eventManager;

    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var ConfigurationInterface */
    protected $configuration;

    /** @var array */
    protected $options;

    /**
     * Constructor
     *
     * @param ActionFactory       $actionFactory
     * @param ActionEventManager  $eventManager
     * @param ManagerRegistry     $managerRegistry
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->managerRegistry = $managerRegistry;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $resolver = new OptionsResolver;
        $this->setDefaultOptions($resolver);
        $this->eventManager->dipatchConfigureEvent($this, $resolver);
        $this->options = $resolver->resolve($configuration->getActionOptions($this->getType()));
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->options['route'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters($object = null)
    {
        $parameters = ['customEntityName' => $this->configuration->getName()];
        if ($object && $object->getId()) {
            $parameters['id'] = $object->getId();
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Request $request)
    {
        $this->eventManager->dispatchPreExecuteEvent($this);
        $response = $this->doExecute($request);

        return $this->eventManager->dispatchPostExecuteEvent($this, $response);
    }

    /**
     * Set the default options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['route']);
        $resolver->setDefaults(['find_options' => []]);
    }

    /**
     * Returns the url for a specified action
     *
     * @param string $actionType
     * @param object $object
     * @param array  $parameters
     * @param mixed  $referenceType
     *
     * @return string
     */
    protected function getActionUrl(
        $actionType,
        $object = null,
        $parameters = [],
        $referenceType = Router::ABSOLUTE_PATH
    ) {
        $action = ($actionType === $this->getType())
            ? $this
            : $this->actionFactory->getAction($this->configuration->getName(), $actionType);

        return $this->router->generate(
            $action->getRoute(),
            $parameters + $action->getRouteParameters($object),
            $referenceType
        );
    }

    /**
     * Returns the entity of the request
     *
     * @param Request $request
     *
     * @throws NotFoundHttpException
     * @return object
     */
    protected function findEntity(Request $request)
    {
        $entity = $this->getManager()->find(
            $this->configuration->getEntityClass(),
            $request->attributes->get('id'),
            $this->options['find_options']
        );

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * Adds a flash message
     *
     * @param Request $request
     * @param string  $type
     * @param string  $message
     * @param array   $messageParameters
     */
    protected function addFlash(Request $request, $type, $message, array $messageParameters = [])
    {
        $request->getSession()->getFlashBag()
            ->add($type, $this->translator->trans($message, $messageParameters));
    }

    /**
     * Returns the manager
     *
     * @return \Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface
     */
    protected function getManager()
    {
        return $this->managerRegistry->getFromConfiguration($this->configuration);
    }

    /**
     * Executes the action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    abstract public function doExecute(Request $request);
}
