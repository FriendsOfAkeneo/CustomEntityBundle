<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Resolver\FQCNResolver;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class FQCNResolverSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $container->getParameter(
            Argument::containingString('pim_catalog.entity.')
        )->willThrow(
            InvalidArgumentException::class
        );
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FQCNResolver::class);
    }

    function it_resolves_fqcn_for_custom_entities($container)
    {
        $container->getParameter('pim_custom_entity.entity.baz.class')->willReturn('Foo\Bar\Baz');
        $this->getFQCN('baz')->shouldReturn('Foo\Bar\Baz');
    }

    function it_returns_null_for_an_unknown_parameter($container)
    {
        $container->getParameter('pim_custom_entity.entity.baz.class')->willThrow(InvalidArgumentException::class);
        $this->getFQCN('baz')->shouldReturn(null);
    }
}
