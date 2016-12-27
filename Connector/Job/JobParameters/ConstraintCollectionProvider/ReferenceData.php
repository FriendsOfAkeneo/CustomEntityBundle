<?php

namespace Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Constraint collection provider adding the reference data list as validation constraint
 *
 * @author     Romain Monceau <romain@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceData implements ConstraintCollectionProviderInterface
{
    /** @var ConstraintCollectionProviderInterface */
    protected $simpleProvider;

    /** @var Registry */
    protected $configurationRegistry;

    /** @var string[] */
    protected $supportedJobNames;

    /**
     * @param ConstraintCollectionProviderInterface $simpleProvider
     * @param Registry                              $configurationRegistry
     * @param string[]                              $supportedJobNames
     */
    public function __construct(
        ConstraintCollectionProviderInterface $simpleProvider,
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
    public function getConstraintCollection()
    {
        $referenceDataNames = $this->configurationRegistry->getNames();

        $baseConstraint   = $this->simpleProvider->getConstraintCollection();
        $constraintFields = $baseConstraint->fields;
        $constraintFields['reference_data_name'] = [
            new NotBlank(),
            new Choice(
                [
                    'choices' => array_combine($referenceDataNames, $referenceDataNames),
                    'message' => 'The value must be one of the configured reference datas'
                ]
            )
        ];

        return new Collection(['fields' => $constraintFields]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
