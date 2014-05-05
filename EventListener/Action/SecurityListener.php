<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener\Action;

use Oro\Bundle\SecurityBundle\SecurityFacade;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigureActionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Compont\EventDispatcher\EventSubscriberInterface;

/**
 * ACL listener for actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SecurityListener implements EventSubscriberInterface
{
    /**
     * @var SecurityFacade
     */
    protected $securityFacade;

    /**
     * Constructor
     *
     * @param SecurityFacade $securityFacade
     */
    public function __construct(SecurityFacade $securityFacade)
    {
        $this->securityFacade = $securityFacade;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ActionEvents::CONFIGURE   => 'setDefaultOptions',
            ActionEvents::PRE_EXECUTE => 'checkGranted'
        ];
    }

    /**
     * Adds options to the actions
     *
     * @param ConfigureActionEvent $event
     */
    public function setDefaultOptions(ConfigureActionEvent $event)
    {
        $event->getOptionsResolver()->setOptional(['acl']);
    }

    /**
     * Throws an exception if ACL is set and is not granted
     *
     * @param ActionEvent $event
     *
     * @throws UnauthorizedHttpException
     */
    public function checkGranted(ActionEvent $event)
    {
        $options = $event->getAction()->getOptions();

        if (isset($options['acl']) && !$this->securityFacade->isGranted($options['acl'])) {
            throw new UnauthorizedHttpException;
        }
    }
}
