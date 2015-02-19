<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ActionBehavior extends ObjectBehavior
{
    public function initializeRequest(Request $request, ParameterBag $attributes)
    {
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');
    }

    public function initializeConfiguration(ConfigurationInterface $configuration)
    {
        $configuration->getEntityClass()->willReturn('entity_class');
        $configuration->getName()->willReturn('entity');
    }

    public function initializeRouter(RouterInterface $router)
    {
        $router->generate(Argument::type('string'), Argument::type('array'), Argument::any())->will(
            function ($arguments) {
                $path = $arguments[0] . '?';
                foreach ($arguments[1] as $key => $value) {
                    $path .= '&' . $key . '=' . $value;
                }

                return $path;
            }
        );
    }

    public function initializeFlashBag(
        Request $request,
        Session $session,
        FlashBagInterface $flashBag
    ) {
        $request->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
    }

    public function initializeTranslator(TranslatorInterface $translator)
    {
        $translator->trans(Argument::type('string'), Argument::any())->will(
            function ($arguments) {
                if (!isset($arguments[1])) {
                    $arguments[1] = array();
                }

                $translated = sprintf('<%s>', $arguments[0]);
                foreach ($arguments[1] as $key => $value) {
                    $translated .= sprintf('%s=%s;', $key, $value);
                }

                return $translated;
            }
        );
    }

    public function initializeEventManager(
        ActionEventManager $eventManager
    ) {
        $eventManager->dipatchConfigureEvent(
            $this,
            Argument::type('Symfony\Component\OptionsResolver\OptionsResolverInterface')
        )->shouldBeCalled();
        $eventManager->dispatchPreExecuteEvent($this)->shouldBeCalled();
        $eventManager
            ->dispatchPostExecuteEvent($this, Argument::type('Symfony\Component\HttpFoundation\Response'))
            ->will(
                function ($args) {
                    return $args[1];
                }
            )
            ->shouldBeCalled();
        $eventManager->dispatchPreRenderEvent($this, Argument::type('string'), Argument::type('array'))
            ->will(
                function ($args) {
                    $args[2]['pre_render'] = true;

                    return [$args[1], $args[2]];
                }
            );
    }

    public function initializeManager(
        ConfigurationInterface $configuration,
        Registry $managerRegistry,
        ManagerInterface $manager
    )
    {
        $managerRegistry->getFromConfiguration($configuration)->willReturn($manager);
    }
}
