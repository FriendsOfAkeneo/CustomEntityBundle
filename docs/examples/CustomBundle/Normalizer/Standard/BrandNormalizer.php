<?php

namespace Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Brand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @author    Kevin Rademan <kevin.rademan@locafox.de>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class BrandNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /** @var FabricNormalizer */
    protected $fabricNormalizer;

    /**
     * @param FabricNormalizer $fabricNormalizer
     */
    public function __construct(FabricNormalizer $fabricNormalizer)
    {
        $this->fabricNormalizer = $fabricNormalizer;
    }

    /**
     * @param Brand $entity
     * @param null  $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        $normalizedBrand = [
            'id'     => $entity->getId(),
            'code'   => $entity->getCode(),
            'visual' => $entity->getVisual(),
        ];

        $fabric = $entity->getFabric();
        if (null !== $fabric) {
            $normalizedBrand['fabric'] = $fabric->getCode();
            // Perhaps we should have a seperate normalizer for the dropdown ?
            // $normalizedFabric = $this->fabricNormalizer->normalize($fabric, 'standard');
            // $normalizedBrand['fabric'] = $normalizedFabric;
        }

        return $normalizedBrand;
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
