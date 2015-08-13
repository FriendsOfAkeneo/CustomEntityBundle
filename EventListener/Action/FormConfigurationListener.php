<?php

namespace Pim\Bundle\CustomEntityBundle\EventListener\Action;

use Pim\Bundle\CustomEntityBundle\Action\AbstractFormAction;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigureActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Form listener for actions
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FormConfigurationListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigurationEvents::CONFIGURE => 'setConfigurationOptions',
            ActionEvents::CONFIGURE        => 'setActionOptions'
        ];
    }

    /**
     * Adds options to the actions
     *
     * @param ConfigureEvent $event
     */
    public function setConfigurationOptions(ConfigurationEvent $event)
    {
        $event->getOptionsResolver()->setDefined(
            [
                'form_type',
                'form_options',
                'form_template'
            ]
        );
    }

    /**
     * Adds options to the actions
     *
     * @param ConfigureActionEvent $event
     *
     * @return null
     */
    public function setActionOptions(ConfigureActionEvent $event)
    {
        if (!($event->getAction() instanceof AbstractFormAction)) {
            return;
        }

        $confOptions = $event->getAction()->getConfiguration()->getOptions();
        $resolver = $event->getOptionsResolver();
        if (isset($confOptions['form_type'])) {
            $resolver->setDefaults(['form_type' => $confOptions['form_type']]);
        }
        if (isset($confOptions['form_options'])) {
            $resolver->setDefaults(['form_options' => $confOptions['form_options']]);
        }
        if (isset($confOptions['form_template'])) {
            $resolver->setDefaults(['template' => $confOptions['form_template']]);
        }
    }
}
