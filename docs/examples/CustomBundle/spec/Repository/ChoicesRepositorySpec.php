<?php

namespace spec\Acme\Bundle\CustomBundle\Repository;

use Acme\Bundle\CustomBundle\Repository\ChoicesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Akeneo\Pim\Enrichment\Component\Product\Model\ReferenceDataInterface;

class ChoicesRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $em)
    {
        $this->beConstructedWith($em, 'foo');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChoicesRepository::class);
    }

    function it_gets_choices(
        $em,
        EntityRepository $repo,
        ReferenceDataInterface $customEntity1,
        ReferenceDataInterface $customEntity2
    ) {
        $customEntity1->getId()->willReturn(56);
        $customEntity1->getCode() ->willReturn('foo');
        $customEntity2->getId()->willReturn(44);
        $customEntity2->getCode()->willReturn('bar');
        $repo->findAll()->willReturn([$customEntity1, $customEntity2]);

        $em->getRepository('foo')->willReturn($repo);

        $this->getChoices()->shouldReturn([
            56 => 'foo',
            44 => 'bar',
        ]);
    }
}
