<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Event manager for actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionEventManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatches the ActionEvents::CONFIGURE event
     *
     * @param ActionInterface $action
     * @param OptionsResolver $optionsResolver
     */
    public function dipatchConfigureEvent(ActionInterface $action, OptionsResolver $optionsResolver): void
    {
        $event = new ConfigureActionEvent($action, $optionsResolver);
        $this->eventDispatcher->dispatch(ActionEvents::CONFIGURE, $event);
    }

    /**
     * Dispatches the ActionEvents::PRE_EXECUTE event
     *
     * @param ActionInterface $action
     */
    public function dispatchPreExecuteEvent(ActionInterface $action): void
    {
        $event = new ActionEvent($action);
        $this->eventDispatcher->dispatch(ActionEvents::PRE_EXECUTE, $event);
    }

    /**
     * Dispatches the ActionEvents::POST_EXECUTE event, and returns the modified response
     *
     * @param ActionInterface $action
     * @param Response        $response
     *
     * @return Response
     */
    public function dispatchPostExecuteEvent(ActionInterface $action, Response $response): Response
    {
        $event = new PostExecuteActionEvent($action, $response);
        $this->eventDispatcher->dispatch(ActionEvents::POST_EXECUTE, $event);

        return $event->getResponse();
    }

    /**
     * Dispatches the ActionEvents::PRE_RENDER event, and returns an array containing the template ant its parameters
     *
     * @param ActionInterface $action
     * @param string          $template
     * @param array           $templateVars
     *
     * @return array
     */
    public function dispatchPreRenderEvent(ActionInterface $action, $template, array $templateVars)
    {
        $event = new PreRenderActionEvent($action, $template, $templateVars);
        $this->eventDispatcher->dispatch(ActionEvents::PRE_RENDER, $event);

        return [$event->getTemplate(), $event->getTemplateVars()];
    }
}
