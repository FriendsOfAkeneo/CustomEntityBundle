<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\FormConfigurationProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;

/**
 * Form configuration provider adding the reference data list to the job profile configurable fields
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements FormConfigurationProviderInterface
{
    /** @var FormConfigurationProviderInterface */
    protected $simpleProvider;

    /** @var Registry */
    protected $configurationRegistry;

    /** @var string[] */
    protected $supportedJobNames;

    /**
     * @param FormConfigurationProviderInterface $simpleProvider
     * @param Registry                           $configurationRegistry
     * @param string[]                           $supportedJobNames
     */
    public function __construct(
        FormConfigurationProviderInterface $simpleProvider,
        Registry $configurationRegistry,
        array $supportedJobNames
    ) {
        $this->simpleProvider        = $simpleProvider;
        $this->configurationRegistry = $configurationRegistry;
        $this->supportedJobNames     = $supportedJobNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormConfiguration()
    {
        $referenceDataNames = $this->configurationRegistry->getNames();

        $formOptions = [
            'reference_data_name' => [
                'type' => 'choice',
                'options' => [
                    'choices'  => array_combine($referenceDataNames, $referenceDataNames),
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
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
