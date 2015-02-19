<?php

namespace Pim\Bundle\CustomEntityBundle\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Sets missing field with the clear_missing option to null
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ClearMissingSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        if (null === $data) {
            return;
        }
        if (is_array($data)) {
            foreach ($form as $name => $child) {
                if (!isset($data[$name]) && $child->getConfig()->getOption('clear_missing')) {
                    $data[$name] = new NullValue;
                }
            }
        }
        if ($form->getConfig()->getOption('clear_missing') && $data instanceof NullValue) {
            $data = null;
        }

        $event->setData($data);
    }
}
