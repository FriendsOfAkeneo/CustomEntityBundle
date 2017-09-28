<?php

namespace Acme\Bundle\CustomBundle\Normalizer;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class BrandNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['csv', 'flat'];

    public function normalize($entity, $format = null, array $context = [])
    {
        return [
            'id'   => $entity->getId(),
            'code' => $entity->getCode(),
        ];
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Brand && in_array($format, $this->supportedFormats);
    }
}
