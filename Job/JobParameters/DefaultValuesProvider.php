<?php

namespace Pim\Bundle\CustomEntityBundle\Job\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class DefaultValuesProvider implements DefaultValuesProviderInterface
{
    /** @var DefaultValuesProviderInterface */
    protected $simpleProvider;

    /**
     * @param DefaultValuesProviderInterface $simpleProvider
     */
    public function __construct(DefaultValuesProviderInterface $simpleProvider)
    {
        $this->simpleProvider = $simpleProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        $defaultValues = $this->simpleProvider->getDefaultValues();
        $defaultValues['entity_name'] = null;

        return $defaultValues;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return 'csv_reference_data_import' === $job->getName();
    }
}
