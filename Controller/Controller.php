<?php

namespace Pim\Bundle\CustomEntityBundle\Controller;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
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
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param ActionFactory $actionFactory
     * @param Request       $request
     */
    public function __construct(ActionFactory $actionFactory, Request $request)
    {
        $this->actionFactory = $actionFactory;
        $this->request = $request;
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
        $action = $this->actionFactory->getAction($customEntityName, $actionType);
        if (!$action) {
            throw new NotFoundHttpException(
                sprintf('No action found for type "%s"', $actionType)
            );
        }

        return $action->execute($this->request);
    }
}
