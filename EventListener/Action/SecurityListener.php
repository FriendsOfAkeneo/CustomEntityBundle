<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener\Action;

use Oro\Bundle\SecurityBundle\SecurityFacade;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigureActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\PreRenderActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var SecurityFacade
     */
    protected $securityFacade;

    /**
     * @param ActionFactory  $actionFactory
     * @param SecurityFacade $securityFacade
     */
    public function __construct(ActionFactory $actionFactory, SecurityFacade $securityFacade)
    {
        $this->actionFactory = $actionFactory;
        $this->securityFacade = $securityFacade;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigurationEvents::CONFIGURE => 'setConfigurationOptions',
            ActionEvents::CONFIGURE => 'setDefaultOptions',
            ActionEvents::PRE_EXECUTE => 'checkGranted',
            ActionEvents::PRE_RENDER => 'removeCreateLink'
        ];
    }

    /**
     * Adds options to the configuration
     *
     * @param ConfigurationEvent $event
     */
    public function setConfigurationOptions(ConfigurationEvent $event)
    {
        $resolver = $event->getOptionsResolver();
        $resolver->setDefined(['acl', 'acl_prefix']);
        $resolver->setDefaults(['acl_separator' => '_']);
    }

    /**
     * Adds options to the actions
     *
     * @param ConfigureActionEvent $event
     */
    public function setDefaultOptions(ConfigureActionEvent $event)
    {
        $resolver = $event->getOptionsResolver();

        $options = $event->getAction()->getConfiguration()->getOptions();
        if (isset($options['acl'])) {
            $resolver->setDefaults(['acl' => $options['acl']]);
        } else {
            $resolver->setDefined(['acl']);
        }
        $resolver->setDefined(['acl_suffix']);
        $resolver->setNormalizer(
            'acl',
            function ($actionOptions, $acl) use ($options) {
                if (null === $acl && isset($options['acl_prefix']) && isset($actionOptions['acl_suffix'])) {
                    return $options['acl_prefix'] . $options['acl_separator'] .
                        $actionOptions['acl_suffix'];
                }

                return $acl;
            }
        );
    }

    /**
     * Throws an exception if ACL is set and is not granted
     *
     * @param ActionEvent $event
     *
     * @throws AccessDeniedHttpException
     */
    public function checkGranted(ActionEvent $event)
    {
        $options = $event->getAction()->getOptions();
        if (isset($options['acl']) && !$this->securityFacade->isGranted($options['acl'])) {
            throw new AccessDeniedHttpException;
        }
    }

    /**
     * Removes the create link if no ACLS
     *
     * @param  PreRenderActionEvent $event
     * @return type
     */
    public function removeCreateLink(PreRenderActionEvent $event)
    {
        $action = $event->getAction();
        if ('index' != $action->getType()) {
            return;
        }

        $vars = $event->getTemplateVars();
        if (!isset($vars['createUrl'])) {
            return;
        }

        $customEntityName = $action->getConfiguration()->getName();
        $createAction = $this->actionFactory->getAction($customEntityName, 'create');
        $options = $createAction->getOptions();
        if (isset($options['acl']) && !$this->securityFacade->isGranted($options['acl'])) {
            unset($vars['createUrl']);
            unset($vars['quickCreate']);
            $event->setTemplateVars($vars);
        }
    }
}
