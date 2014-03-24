<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Action;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ActionBehavior extends ObjectBehavior
{
    public function let(
        ManagerInterface $manager,
        RouterInterface $router,
        TranslatorInterface $translator,
        Request $request,
        ParameterBag $attributes,
        ConfigurationInterface $configuration
    ) {
        $request->attributes = $attributes;
        $attributes->get('id')->willReturn('id');
        $configuration->getEntityClass()->willReturn('entity_class');
    }
}
