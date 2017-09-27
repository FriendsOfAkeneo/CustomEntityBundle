<?php

namespace Acme\Bundle\CustomBundle\Normalizer;

use Acme\Bundle\CustomBundle\Entity\Fabric;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class FabricNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /**
     * @param Fabric $entity
     * @param null   $format
     * @param array  $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        return [
            'code'            => $entity->getCode(),
            'name'            => $entity->getName(),
            'alternativeName' => $entity->getAlternativeName(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Fabric && in_array($format, $this->supportedFormats);
    }
}
