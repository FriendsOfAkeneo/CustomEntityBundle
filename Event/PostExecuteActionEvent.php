<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PostExecuteActionEvent extends ActionEvent
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param ActionInterface $action
     * @param Response        $response
     */
    public function __construct(ActionInterface $action, Response $response)
    {
        parent::__construct($action);

        $this->response = $response;
    }

    /**
     * Returns the response created by the action
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Changes the response returned by the action
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
