<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
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
        $router->generate(Argument::type('string'), Argument::type('array'))->will(
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
}
