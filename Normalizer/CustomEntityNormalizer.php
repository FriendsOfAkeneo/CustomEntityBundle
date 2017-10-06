<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
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

    /**
     * @param NormalizerInterface $pimSerializer
     */
    public function __construct(NormalizerInterface $pimSerializer)
    {
        $this->pimSerializer = $pimSerializer;
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

        $meta = [
            'id'               => $entity->getId(),
            'customEntityName' => $context['customEntityName'],
            'form'             => $context['form'],
        ];

        return [
            'data' => $normalizedEntity,
            'meta' => $meta,
        ];
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
