<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\ConstraintCollectionProvider\QuickCsvExport;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Composite;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class QuickCsvExportSpec extends ObjectBehavior
{
    function let(ConstraintCollectionProviderInterface $simpleProvider)
    {
        $this->beConstructedWith($simpleProvider, ['a_job_name']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(QuickCsvExport::class);
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

    function it_adds_default_constraints($simpleProvider, Collection $emptyCollection)
    {
        $simpleProvider->getConstraintCollection()->willReturn($emptyCollection);

        $collection = $this->getConstraintCollection();

        $collection->shouldBeAnInstanceOf(Collection::class);

        $collection->fields->shouldHaveKey('reference_data');
        $collection->fields['reference_data']->shouldBeAnInstanceOf(Composite::class);
        $collection->fields['reference_data']->constraints->shouldBeArray();
        $collection->fields['reference_data']->constraints->shouldHaveCount(1);
        $collection->fields['reference_data']->constraints[0]->shouldBeAnInstanceOf(NotBlank::class);

        $collection->fields->shouldHaveKey('ids');
        $collection->fields['ids']->shouldBeAnInstanceOf(Composite::class);
        $collection->fields['ids']->constraints->shouldBeArray();
        $collection->fields['ids']->constraints->shouldHaveCount(1);
        $collection->fields['ids']->constraints[0]->shouldBeAnInstanceOf(NotNull::class);
    }
}
