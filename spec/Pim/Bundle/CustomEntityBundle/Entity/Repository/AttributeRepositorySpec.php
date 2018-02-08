<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\AttributeRepository;

/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class AttributeRepositorySpec extends ObjectBehavior
{
    function let(EntityManagerInterface $em, ClassMetadata $classMetadata)
    {
        $em->getClassMetadata('foo')->willReturn($classMetadata);

        $this->beConstructedWith($em, 'foo');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeRepository::class);
    }

    function it_is_an_entity_repository()
    {
        $this->shouldHaveType(EntityRepository::class);
    }
}
