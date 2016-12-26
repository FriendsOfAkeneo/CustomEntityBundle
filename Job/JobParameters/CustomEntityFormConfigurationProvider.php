<?php

namespace Pim\Bundle\CustomEntityBundle\Job\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Configuration;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class CustomEntityFormConfigurationProvider implements FormConfigurationProviderInterface
{
    /** @var FormConfigurationProviderInterface */
    protected $simpleProvider;

    /** @var Registry */
    protected $configurationRegistry;

    /**
     * @param FormConfigurationProviderInterface $simpleProvider
     * @param Registry                           $configurationRegistry
     */
    public function __construct(
        FormConfigurationProviderInterface $simpleProvider,
        Registry $configurationRegistry
    ) {
        $this->simpleProvider        = $simpleProvider;
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormConfiguration()
    {
        $formOptions = [
            'entity_name' => [
                'type' => 'choice',
                'options' => [
                    'choices'  => $this->configurationRegistry->getNames(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'pim_custom_entity.import.csv.entity_name.label',
                    'help'     => 'pim_custom_entity.import.csv.entity_name.help'
                ]
            ]
        ];

        return array_merge($this->simpleProvider->getFormConfiguration(), $formOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return 'csv_custom_entity_import' === $job->getName();
    }
}
