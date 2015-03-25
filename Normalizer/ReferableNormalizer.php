<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes referable entities
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferableNormalizer implements NormalizerInterface
{
    /**
     * @var array
     */
    protected $allowedformats;

    /**
     * Constructor
     *
     * @param array $allowedformats
     */
    public function __construct(array $allowedformats)
    {
        $this->allowedformats = $allowedformats;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (array_key_exists('field_name', $context)) {
            return [
                $context['field_name'] => $object->getReference(),
            ];
        } else {
            throw new \LogicException(sprintf('No normalizer found for object of class "%s"', get_class($object)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->allowedformats);
    }
}
