<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer;

use Doctrine\Common\Util\ClassUtils;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Pim\Bundle\CustomEntityBundle\Versioning\VersionableInterface;
use Pim\Bundle\EnrichBundle\Provider\StructureVersion\StructureVersionProviderInterface;
use Pim\Bundle\VersioningBundle\Manager\VersionManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Internal API normalizer.
 *
 * Used to generate JSON rest responses.
 * @see       \Pim\Bundle\CustomEntityBundle\Controller\RestController
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomEntityNormalizer implements NormalizerInterface
{
    /** @var array $supportedFormats */
    protected $supportedFormats = ['internal_api'];

    /** @var NormalizerInterface */
    protected $pimSerializer;

    /** @var VersionManager */
    protected $versionManager;

    /** @var NormalizerInterface */
    protected $versionNormalizer;

    /** @var StructureVersionProviderInterface */
    protected $structureVersionprovider;

    /**
     * @param NormalizerInterface $pimSerializer
     * @param VersionManager $versionManager
     * @param NormalizerInterface $versionNormalizer
     * @param StructureVersionProviderInterface $structureVersionProvider
     */
    public function __construct(
        NormalizerInterface $pimSerializer,
        VersionManager $versionManager,
        NormalizerInterface $versionNormalizer,
        StructureVersionProviderInterface $structureVersionProvider
    ) {
        $this->pimSerializer = $pimSerializer;
        $this->versionManager = $versionManager;
        $this->versionNormalizer = $versionNormalizer;
        $this->structureVersionprovider = $structureVersionProvider;
    }

    /**
     * @param AbstractCustomEntity $entity
     * @param null                 $format
     * @param array                $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        $normalizedEntity = $this->pimSerializer->normalize($entity, 'standard', $context);

        $normalizedEntity['meta'] = [
            'structure_version' => null,
            'id'                => $entity->getId(),
            'customEntityName'  => $context['customEntityName'],
            'form'              => $context['form'],
        ];

        if ($entity instanceof VersionableInterface) {
            $this->structureVersionprovider->addResource(ClassUtils::getClass($entity));
            $firstVersion = $this->versionManager->getOldestLogEntry($entity);
            $lastVersion = $this->versionManager->getNewestLogEntry($entity);

            $firstVersion = null !== $firstVersion ?
                $this->versionNormalizer->normalize($firstVersion, 'internal_api', $context) :
                null;
            $lastVersion = null !== $lastVersion ?
                $this->versionNormalizer->normalize($lastVersion, 'internal_api', $context) :
                null;

            $normalizedEntity['meta']['created'] = $firstVersion;
            $normalizedEntity['meta']['updated'] = $lastVersion;
            $normalizedEntity['meta']['structure_version'] = $this->structureVersionprovider->getStructureVersion();
            $normalizedEntity['meta']['model_type'] = $context['customEntityName'];
        }

        return $normalizedEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }

    /**
     * @param AbstractCustomEntity $entity
     *
     * @return array
     */
    protected function getLabels(AbstractCustomEntity $entity): array
    {
        $labels = [];
        if ($entity instanceof AbstractTranslatableCustomEntity) {
            foreach ($entity->getTranslations() as $translation) {
                $labels[$translation->getLocale()] = $translation->getLabel();
            }
        }

        return $labels;
    }
}
