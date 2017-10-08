<?php

namespace Acme\Bundle\CustomBundle\Normalizer\Standard;

use Acme\Bundle\CustomBundle\Entity\Pictogram;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PictogramNormalizer implements NormalizerInterface
{
    /** @var string[] */
    protected $supportedFormats = ['standard'];

    /**
     * @param Pictogram $entity
     * @param null      $format
     * @param array     $context
     *
     * @return array
     */
    public function normalize($entity, $format = null, array $context = [])
    {
        return [
            'id'     => $entity->getId(),
            'code'   => $entity->getCode(),
            'labels' => $this->getLabels($entity),
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
        return $data instanceof Pictogram && in_array($format, $this->supportedFormats);
    }

    /**
     * @param AbstractTranslatableCustomEntity $entity
     *
     * @return array
     */
    protected function getLabels(AbstractTranslatableCustomEntity $entity): array
    {
        $labels = [];
        foreach ($entity->getTranslations() as $translation) {
            $labels[$translation->getLocale()] = $translation->getLabel();
        }

        return $labels;
    }
}
