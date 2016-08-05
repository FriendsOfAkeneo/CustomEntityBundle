<?php

namespace Pim\Bundle\CustomEntityBundle\MassEditConnector\Processor;

use Pim\Bundle\EnrichBundle\Connector\Processor\AbstractProcessor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Processor to quick export reference datas
 *
 * @author Romain Monceau <romain@akeneo.com>
 */
class ReferenceDataToFlatArrayProcessor extends AbstractProcessor
{
    /** @var NormalizerInterface */
    protected $normalizer;

    /**
     * @param NormalizerInterface                 $normalizer
     */
    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        return $this->normalizer->normalize($item, 'flat');
    }
}
