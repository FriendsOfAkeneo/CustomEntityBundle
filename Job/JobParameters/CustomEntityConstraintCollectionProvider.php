<?php

namespace Pim\Bundle\CustomEntityBundle\Job\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Romain Monceau <romain@akeneo.com>
 */
class CustomEntityConstraintCollectionProvider implements ConstraintCollectionProviderInterface
{
    /** @var ConstraintCollectionProviderInterface */
    protected $simpleProvider;

    /** @var Registry */
    protected $configurationRegistry;

    /**
     * @param ConstraintCollectionProviderInterface $simpleProvider
     */
    public function __construct(
        ConstraintCollectionProviderInterface $simpleProvider,
        Registry $configurationRegistry
    ) {
        $this->simpleProvider        = $simpleProvider;
        $this->configurationRegistry = $configurationRegistry;
    }

    /**
     * @return Collection
     */
    public function getConstraintCollection()
    {
        $referenceDataNames = $this->configurationRegistry->getNames();

        $baseConstraint   = $this->simpleProvider->getConstraintCollection();
        $constraintFields = $baseConstraint->fields;
        $constraintFields['entity_name'] = [
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
        return 'csv_custom_entity_import' === $job->getName();
    }
}
