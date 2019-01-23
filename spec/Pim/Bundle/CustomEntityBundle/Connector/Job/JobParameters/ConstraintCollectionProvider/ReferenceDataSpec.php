<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider\ReferenceData;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReferenceDataSpec extends ObjectBehavior
{
    function let(ConstraintCollectionProviderInterface $simpleProvider, Registry $registry)
    {
        $this->beConstructedWith($simpleProvider, $registry, ['a_job_name']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceData::class);
    }

    function it_is_a_constraint_collection_provider()
    {
        $this->shouldImplement(ConstraintCollectionProviderInterface::class);
    }

    function it_only_provides_constraints_for_supported_jobs(
        JobInterface $supportedJob,
        JobInterface $unsupportedJob
    ) {
        $supportedJob->getName()->willReturn('a_job_name');
        $unsupportedJob->getName()->willReturn('another_job_name');

        $this->supports($supportedJob)->shouldReturn(true);
        $this->supports($unsupportedJob)->shouldReturn(false);
    }

    function it_provides_a_constraint_collection(
        $simpleProvider,
        $registry,
        Collection $emptyCollection
    ) {
        $registry->getNames()->willReturn(['custom_entity_A', 'custom_entity_B']);
        $simpleProvider->getConstraintCollection()->willReturn($emptyCollection);

        $constraintCollection = $this->getConstraintCollection();
        $constraintCollection->shouldBeAnInstanceOf(Collection::class);
        $constraintFields = $constraintCollection->fields;
        $constraintFields->shouldHaveCount(1);
        $constraintFields->shouldHaveKey('reference_data_name');
        $constraintFields['reference_data_name']->shouldBeAnInstanceOf(Composite::class);
        $constraintFields['reference_data_name']->constraints->shouldHaveCount(2);
        $constraintFields['reference_data_name']->constraints[0]->shouldBeAnInstanceOf(NotBlank::class);
        $constraintFields['reference_data_name']->constraints[1]->shouldBeAnInstanceOf(Choice::class);
    }
}
