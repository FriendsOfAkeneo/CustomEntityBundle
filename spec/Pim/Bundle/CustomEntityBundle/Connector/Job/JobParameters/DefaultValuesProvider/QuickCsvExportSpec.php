<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider\QuickCsvExport;

class QuickCsvExportSpec extends ObjectBehavior
{
    function let(DefaultValuesProviderInterface $simpleProvider)
    {
        $this->beConstructedWith($simpleProvider, ['a_job_name']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(QuickCsvExport::class);
    }

    function it_is_a_default_values_provider()
    {
        $this->shouldImplement(DefaultValuesProviderInterface::class);
    }

    function it_only_provides_values_for_supported_jobs(
        JobInterface $supportedJob,
        JobInterface $unsupportedJob
    ) {
        $supportedJob->getName()->willReturn('a_job_name');
        $unsupportedJob->getName()->willReturn('another_job_name');

        $this->supports($supportedJob)->shouldReturn(true);
        $this->supports($unsupportedJob)->shouldReturn(false);
    }

    function it_provides_default_values(
        $simpleProvider
    ) {
        $simpleProvider->getDefaultValues()->willReturn([]);
        $this->getDefaultValues()->shouldReturn(
            [
                'reference_data' => null,
                'ids'            => null,
            ]
        );
    }
}
