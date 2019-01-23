<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer;

use Akeneo\Tool\Component\Versioning\Model\VersionInterface;
use Doctrine\Common\Util\ClassUtils;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\CustomEntityNormalizer;
use Pim\Bundle\CustomEntityBundle\Versioning\VersionableInterface;
use Akeneo\Pim\Enrichment\Bundle\StructureVersion\Provider\StructureVersion;
use Akeneo\Tool\Bundle\VersioningBundle\Manager\VersionManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomEntityNormalizerSpec extends ObjectBehavior
{
    public function let(
        NormalizerInterface $pimSerializer,
        VersionManager $versionManager,
        NormalizerInterface $versionNormalizer,
        StructureVersion $structureVersionProvider
    ) {
        $this->beConstructedWith($pimSerializer, $versionManager, $versionNormalizer, $structureVersionProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CustomEntityNormalizer::class);
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_only_internal_format(AbstractCustomEntity $entity)
    {
        $this->supportsNormalization($entity, 'internal_api')->shouldReturn(true);
        $this->supportsNormalization($entity, 'external_api')->shouldReturn(false);
        $this->supportsNormalization($entity, 'standard')->shouldReturn(false);
    }

    public function it_normalizes_objects(NormalizerInterface $pimSerializer, AbstractCustomEntity $entity)
    {
        $entity->getId()->willReturn(666);

        $context = [
            'customEntityName' => 'fooEntity',
            'form'             => 'foo_form_extension',
        ];

        $normalizedEntity = [
            'foo'    => 'bar',
            'labels' => [
                'en_US' => 'foo',
                'fr_FR' => 'bar',
            ],
        ];

        $pimSerializer->normalize($entity, 'standard', $context)
                      ->willReturn($normalizedEntity);

        $expected = array_merge(
            $normalizedEntity,
            [
                'meta' => [
                    'structure_version' => null,
                    'id'                => 666,
                    'customEntityName'  => 'fooEntity',
                    'form'              => 'foo_form_extension',
                ],
            ]
        );

        $this->normalize($entity, 'standard', $context)->shouldReturn($expected);
    }

    function it_normalizes_versionable_custom_entities(
        $pimSerializer,
        $versionManager,
        $versionNormalizer,
        $structureVersionProvider,
        FooEntity $entity,
        VersionInterface $oldestLogEntry,
        VersionInterface $newestLogEntry
    ) {
        $context = [
            'customEntityName' => 'fooEntity',
            'form'             => 'foo_form_extension',
        ];

        $pimSerializer->normalize($entity, 'standard', $context)->willReturn(
            [
                'id'   => 44,
                'code' => 'foo',
                'bar'  => 'baz',
            ]
        );

        $structureVersionProvider
            ->addResource(ClassUtils::getClass($entity->getWrappedObject()))
            ->shouldBeCalled();
        $versionManager->getOldestLogEntry($entity)->willReturn($oldestLogEntry);
        $versionManager->getNewestLogEntry($entity)->willReturn($newestLogEntry);
        $versionNormalizer->normalize($oldestLogEntry, 'internal_api', $context)->willReturn('aaa');
        $versionNormalizer->normalize($newestLogEntry, 'internal_api', $context)->willReturn('bbb');
        $structureVersionProvider->getStructureVersion()->willReturn(11111);

        $entity->getId()->willReturn(44);

        $this->normalize($entity, 'internal_api', $context)->shouldReturn(
            [
                'id'   => 44,
                'code' => 'foo',
                'bar'  => 'baz',
                'meta' => [
                    'structure_version' => 11111,
                    'id'                => 44,
                    'customEntityName' => 'fooEntity',
                    'form'              => 'foo_form_extension',
                    'created'           => 'aaa',
                    'updated'           => 'bbb',
                    'model_type'        => 'fooEntity',
                ],
            ]
        );
    }
}

class FooEntity extends AbstractCustomEntity implements VersionableInterface
{
}
