<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class ReferenceData implements DefaultValuesProviderInterface
{
    /** @var DefaultValuesProviderInterface */
    protected $simpleProvider;

    /** @var string[] */
    protected $supportedJobNames;

    /**
     * @param DefaultValuesProviderInterface $simpleProvider
     * @param string[]                       $supportedJobNames
     */
    public function __construct(DefaultValuesProviderInterface $simpleProvider, array $supportedJobNames)
    {
        $this->simpleProvider = $simpleProvider;
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        $defaultValues = $this->simpleProvider->getDefaultValues();
        $defaultValues['reference_data_name'] = null;

        return $defaultValues;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
