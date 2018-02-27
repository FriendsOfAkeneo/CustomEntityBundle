<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization;

use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Generic normalizer processor for reference datas
 * Only works for basic reference data
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceDataProcessor implements ItemProcessorInterface
{
    /** @var NormalizerInterface */
    protected $normalizer;

    /**
     * @param NormalizerInterface $normalizer
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
        return $this->normalizer->normalize($item, 'csv');
    }
}
