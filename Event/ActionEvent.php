<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;

/**
 * Basic event for actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionEvent
{
    /**
     * @var ActionInterface
     */
    protected $action;

    /**
     * Constructor
     *
     * @param \Pim\Bundle\CustomEntityBundle\Action\ActionInterface $action
     */
    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * Returns the action launched by the event
     *
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }
}
