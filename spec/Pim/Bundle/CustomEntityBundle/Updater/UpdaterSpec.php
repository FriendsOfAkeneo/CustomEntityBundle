<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Updater;

use Akeneo\Component\FileStorage\File\FileStorerInterface;
use Akeneo\Component\Localization\Model\AbstractTranslation;
use Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Pim\Bundle\CustomEntityBundle\Updater\Updater;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Prophecy\Argument;
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

    public function it_updates_code(
        ReferenceDataInterface $referenceData
    ) {
        $referenceData->setCode('foo')->shouldBeCalled();
        $this->update($referenceData, ['code' => 'foo']);
    }

    public function it_updates_associated_entities(
        $propertyAccessor,
        $em,
        ReferenceDataInterface $referenceData,
        ClassMetadata $classMetadata,
        EntityRepository $associationRepository,
        \stdClass $associatedEntity,
        \stdClass $otherEntity
    ) {
        $em->getClassMetadata(Argument::any())->willReturn($classMetadata);
        $classMetadata->getAssociationMappings()->willReturn([
            'foo' => [
                'targetEntity' => 'Foo\Bar\Baz',
            ],
            'bar' => [
                'targetEntity' => 'Foo\Bar\Baz',
            ],
        ]);
        $classMetadata->isCollectionValuedAssociation('foo')->willReturn(false);
        $classMetadata->isCollectionValuedAssociation('bar')->willReturn(true);

        $em->getRepository('Foo\Bar\Baz')->willReturn($associationRepository);
        $associationRepository->findOneBy(['code' => 'bar'])->willReturn($associatedEntity);
        $associationRepository->findOneBy(['code' => 'baz'])->willReturn($otherEntity);

        $propertyAccessor->setValue($referenceData, 'foo', $associatedEntity)->shouldBeCalled();
        $propertyAccessor->setValue($referenceData, 'bar', [$associatedEntity, $otherEntity])->shouldBeCalled();

        $this->update($referenceData, [
            'foo' => 'bar',
            'bar' => ['bar', 'baz'],
        ]);
    }

    function it_can_remove_an_association(
        $propertyAccessor,
        $em,
        ReferenceDataInterface $referenceData,
        ClassMetadata $classMetadata
    ) {
        $em->getClassMetadata(Argument::any())->willReturn($classMetadata);
        $classMetadata->getAssociationMappings()->willReturn(
            [
                'foo' => [
                    'targetEntity' => 'Foo\Bar\Baz',
                ],
            ]
        );
        $classMetadata->isCollectionValuedAssociation('foo')->willReturn(false);
        $propertyAccessor->setValue($referenceData, 'foo', null)->shouldBeCalled();

        $this->shouldNotThrow(\Exception::class)->during(
            'update',
            [
                $referenceData,
                ['foo' => null],
            ]
        );
    }

    function it_updates_translations_and_associated_entities_at_the_same_time(
        $propertyAccessor,
        $em,
        $localeRepository,
        AbstractTranslatableCustomEntity $referenceData,
        LabelTranslation $translationEn,
        LabelTranslation $translationFr,
        ClassMetadata $classMetadata,
        EntityRepository $associationRepository,
        \stdClass $associatedEntity
    ) {
        $data = [
            'foo'    => 'related_entity_code',
            'labels' => [
                'en_US' => 'English label',
                'fr_FR' => 'label français',
            ],
        ];

        $localeRepository->getActivatedLocaleCodes()->willReturn(['en_US', 'fr_FR']);
        $em->getClassMetadata(Argument::any())->willReturn($classMetadata);
        $classMetadata->getAssociationMappings()->willReturn(
            [
                'foo'          => [
                    'targetEntity' => 'Another\Custom\Entity',
                ],
                'translations' => [
                    'targetEntity' => 'Foo\Bar\BazTranslation',
                ],
            ]
        );
        $classMetadata->isCollectionValuedAssociation('foo')->willReturn(false);

        $em->getRepository('Another\Custom\Entity')->willReturn($associationRepository);
        $associationRepository->findOneBy(['code' => 'related_entity_code'])->willReturn($associatedEntity);

        $referenceData->getTranslation('en_US')->willReturn($translationEn);
        $referenceData->getTranslation('fr_FR')->willReturn($translationFr);

        $translationEn->setLabel('English label')->shouldBeCalled();
        $translationFr->setLabel('label français')->shouldBeCalled();

        $propertyAccessor->setValue($referenceData, 'foo', $associatedEntity)->shouldBeCalled();

        $this->update($referenceData, $data);
    }
}

abstract class Labeltranslation extends AbstractTranslation
{
    abstract public function getLabel();

    abstract public function setLabel($label);
}
