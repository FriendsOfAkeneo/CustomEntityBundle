<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Internal API normalizer
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
     * {@inheritdoc}
     * @param AbstractCustomEntity $entity
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        $normalizedEntity = [
            'data' => $this->pimSerializer->normalize($entity, 'standard', $context),
        ];

        $normalizedEntity['meta'] = [
            'customEntityName' => $context['customEntityName'],
            'id'               => $entity->getId(),
            'form'             => $context['form'],
        ];

        return $normalizedEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }
}
