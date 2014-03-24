<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for custom entities
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @param Request            $request
     * @param Registry           $registry
     */
    public function __construct(Request $request, Registry $registry)
    {
        $this->request = $request;
        $this->registry = $registry;
    }

    /**
     * Default action
     *
     * @param string $customEntityName
     * @param string $actionType
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function executeAction($customEntityName, $actionType)
    {
        if (!$this->registry->has($customEntityName)) {
            throw new NotFoundHttpException();
        }

        $configuration = $this->registry->get($customEntityName);

        if (!$configuration->hasAction($actionType)) {
            throw new NotFoundHttpException();
        }

        return $configuration->getAction($actionType)->execute($this->request, $configuration);
    }
}
