<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Reader\Database;

use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\StepExecution;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Connector\Reader\Database\ReferenceDataReader;
use Pim\Component\Connector\Reader\Database\AbstractReader;

class ReferenceDataReaderSpec extends ObjectBehavior
{
    function let(
        Registry $confRegistry,
        EntityManagerInterface $em,
        StepExecution $stepExecution
    ) {
        $this->beConstructedWith($confRegistry, $em);
        $this->setStepExecution($stepExecution);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReferenceDataReader::class);
    }

    function it_is_an_abstract_reader()
    {
        $this->shouldBeAnInstanceOf(AbstractReader::class);
    }

    function it_reads_reference_data_from_database(
        $confRegistry,
        $em,
        $stepExecution,
        JobParameters $jobParameters,
        EntityRepository $repo,
        ConfigurationInterface $config
    ) {
        $bar = function($id = null) {
            return new class($id) {
                private $id;
                public function __construct($id)
                {
                    $this->id = $id;
                }
                public function getId()
                {
                    return $this->id;
                }
            };
        };

        $jobParameters->get('reference_data_name')->willReturn('some_custom_entity');
        $stepExecution->getJobParameters()->willReturn($jobParameters);
        $config->getEntityClass()->willReturn('Foo\Bar');
        $confRegistry->get('some_custom_entity')->willReturn($config);

        $repo->findAll()->willReturn([$bar(42), $bar(56)]);
        $em->getRepository('Foo\Bar')->willReturn($repo);

        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(2);

        $result = $this->read();
        $result->shouldHaveType(get_class($bar()));
        $result->getid()->shouldReturn(42);

        $result = $this->read();
        $result->shouldHaveType(get_class($bar()));
        $result->getid()->shouldReturn(56);

        $this->read()->shouldReturn(null);
    }
}
