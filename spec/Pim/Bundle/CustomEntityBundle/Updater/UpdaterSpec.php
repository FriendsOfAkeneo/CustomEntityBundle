<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Updater;

use Akeneo\Component\FileStorage\File\FileStorerInterface;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Updater\Updater;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class UpdaterSpec extends ObjectBehavior
{
    public function let(
        PropertyAccessorInterface $propertyAccessor,
        LocaleRepositoryInterface $localeRepository,
        EntityManagerInterface $em,
        FileStorerInterface $storer
    ) {
        $this->beConstructedWith($propertyAccessor, $localeRepository, $em, $storer, '/tmp');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Updater::class);
        $this->shouldImplement(ObjectUpdaterInterface::class);
    }
}
