<?php

namespace Acme\Bundle\CustomBundle\Normalizer;

use Acme\Bundle\CustomBundle\Entity\Color;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class ColorNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /**
     * @param Color $entity
     * @param null  $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        return [
            'code' => $entity->getCode(),
            'name' => $entity->getName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Color && in_array($format, $this->supportedFormats);
    }
}
