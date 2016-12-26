<?php

namespace Pim\Bundle\CustomEntityBundle\Job\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class CustomEntityDefaultValuesProvider implements DefaultValuesProviderInterface
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
        $parameters = $this->simpleProvider->getDefaultValues();
        $parameters['entity_name'] = null;

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return 'csv_custom_entity_import' === $job->getName();
    }
}
