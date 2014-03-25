<?php

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Constructor
     *
     * @param ManagerInterface    $manager
     * @param RouterInterface     $router
     * @param TranslatorInterface $translator
     */
    public function __construct(ManagerInterface $manager, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Request $request, ConfigurationInterface $configuration)
    {
        $resolver = new OptionsResolver;
        $this->setDefaultOptions($configuration, $resolver);
        $options = $resolver->resolve($configuration->getActionOptions($this->getType()));

        return $this->doExecute($request, $configuration, $options);
    }

    /**
     * Set the default options
     *
     * @param ConfigurationInterface   $configuration
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(ConfigurationInterface $configuration, OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            ['find_options' => []]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters(ConfigurationInterface $configuration, $object = null)
    {
        $parameters = ['customEntityName' => $configuration->getName()];
        if ($object && $object->getId()) {
            $parameters['id'] = $object->getId();
        }

        return $parameters;
    }

    /**
     *
     * @param object $object
     * @param string $actionType
     */
    protected function getActionUrl(ConfigurationInterface $configuration, $actionType, $object = null)
    {
        $action = ($actionType === $this->getType()) ? $this : $configuration->getAction($actionType);

        return $this->router->generate(
            $action->getRoute(),
            $action->getRouteParameters($configuration, $object)
        );
    }

    /**
     * Returns the entity of the request
     *
     * @param Request                $request
     * @param ConfigurationInterface $configuration
     * @param array                  $options
     *
     * @throws NotFoundHttpException
     * @return object
     */
    protected function findEntity(Request $request, ConfigurationInterface $configuration, array $options)
    {
        $entity = $this->manager->find(
            $configuration->getEntityClass(),
            $request->attributes->get('id'),
            $options['find_options']
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

    /**
     * Execute the action. Override to implement your own logic
     *
     * @param Request                $request
     * @param ConfigurationInterface $configuration
     * @param array                  $options
     *
     * @return Response
     */
    abstract protected function doExecute(Request $request, ConfigurationInterface $configuration, array $options);
}
