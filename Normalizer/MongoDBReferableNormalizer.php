<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer;

use Pim\Bundle\CatalogBundle\Model\ReferableInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOption;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomOption;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize an attribute option to store it as mongodb_json
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MongoDBReferableNormalizer implements NormalizerInterface
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
        $data = [
            'id'   => $object->getId(),
            'code' => $object->getReference()
        ];

        $values = [];
        if ($object instanceof AbstractCustomOption) {
            $data['label'] = $object->getLabel();
        }
        if ($object instanceof AbstractTranslatableCustomOption) {
            foreach ($object->getTranslations() as $translation) {
                $data[$translation->getLocale()] = [
                    'value'  => $translation->getLabel(),
                    'label' => $translation->getLocale()
                ];
            }
        }

        $data['optionValues'] = $values;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ReferableInterface && 'mongodb_json' === $format;
    }
}
