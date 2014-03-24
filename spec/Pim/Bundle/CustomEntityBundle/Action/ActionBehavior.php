<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

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
}
