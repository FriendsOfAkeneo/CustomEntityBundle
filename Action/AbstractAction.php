<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param ActionFactory       $actionFactory
     * @param ManagerInterface    $manager
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->actionFactory = $actionFactory;
        $this->manager = $manager;
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
     * Set the default options
     *
     * @param ConfigurationInterface   $configuration
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['route']);
        $resolver->setDefaults(['find_options' => []]);
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
     * Returns the url for a specified action
     *
     * @param object $object
     * @param string $actionType
     */
    protected function getActionUrl($actionType, $object = null)
    {
        $action = ($actionType === $this->getType())
            ? $this
            : $this->actionFactory->getAction($this->configuration->getName(), $actionType);

        return $this->router->generate(
            $action->getRoute(),
            $action->getRouteParameters($object)
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
        $entity = $this->manager->find(
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
     * @param type    $type
     * @param type    $message
     */
    protected function addFlash(Request $request, $type, $message)
    {
        $request->getSession()->getFlashBag()
            ->add($type, $this->translator->trans($message));
    }
}
